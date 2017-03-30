<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class Registry
{
    /**
     * Writes the registry array to a php file for (re-)inclusion.
     * e.g.
     *  $registry = include 'registry.php';
     *
     * @param $registry The registry array.
     */
    public static function writeRegistry(array $registry)
    {
        // registry file header
        $content = "<?php\n";
        $content .= "   /**\n";
        $content .= "    * WPИ-XM Server Stack\n";
        $content .= "    * Copyright © 2010 - " . date("Y") . " Jens-André Koch <jakoch@web.de>\n";
        $content .= "    * https://wpn-xm.org/\n";
        $content .= "    *\n";
        $content .= "    * This source file is subject to the terms of the MIT license.\n";
        $content .= "    * For full copyright and license information, view the bundled LICENSE file.\n";
        $content .= "    */\n";
        $content .= "\n";
        $content .= "   /**\n";
        $content .= "    * WPИ-XM Server Stack - Software Registry\n";
        $content .= "    * ---------------------------------------\n";
        $content .= "    * Last Update " . date(DATE_RFC2822) . ".\n";
        $content .= "    * Do not edit manually!\n";
        $content .= "    */\n";
        $content .= "\n return ";

        // formatting
        $registry = Registry::sort($registry);
        $content .= Registry::prettyPrint($registry);
        $content .= ";\n";

        // write new registry
        return (bool) file_put_contents(DATA_DIR . 'registry/wpnxm-software-registry.php', $content);
    }

    public static function getArrayForNewComponent($component, $shorthand, $url, $version, $website, $phpversion)
    {
        $version = (string) $version;

        /* TODO support PHP Extension inserts

        $bitsize = (false !== strpos($component, 'x64')) ? 'x64' : '';

        // array structure for PHP Extensions must take "PHP Version" and "Bitsize" into account
        if (strpos($shorthand, 'phpext_') !== false) {
            return array(
                'name'    => $component,
                'website' => $website,
                $version  => array(
                    $bitsize => array(
                        $phpversion => $url,
                    ),
                ),
                'latest'  => array(
                    'version' => $version,
                    'url'     => array(
                        $bitsize => array(
                            $phpversion => $url,
                        ),
                    ),
                ),
            );
        }*/

        return array(
            'name'    => $component,
            'website' => $website,
            $version  => $url,
            'latest'  => array(
                'version' => $version,
                'url'     => $url,
            ),
        );
    }

    /**
     * Add latest version scan of component to the main software component array.
     *
     * @param $name Name of Software Component
     * @param $latestVersion Registry subset of the software component, which should be added to the main array.
     */
    public static function addLatestVersionToRegistry($name, array $latestVersion, array $registry)
    {
        if (isset($latestVersion['url']) === true and isset($latestVersion['version']) === true) {
            // the array contains only one element
            // create [latest] sub-array
            $registry[$name]['latest']['url']     = $latestVersion['url'];
            $registry[$name]['latest']['version'] = $latestVersion['version'];

            // create [version] => [url] relationship
            $registry[$name][$latestVersion['version']] = $latestVersion['url'];

            unset($latestVersion);
        } else {

            // if there are multiple versions, sort version numbers from low to high
            if(count($latestVersion) > 1) {
                $latestVersion = Version::sortByVersion($latestVersion);
            }

            // add the last array item of multiple elements (the one with the highest version number)
            // insert the last array item as [latest][version] => [url]
            $registry[$name]['latest'] = array_pop($latestVersion);

            // insert the last array item also as a pure [version] => [url] relationship
            $registry[$name][$registry[$name]['latest']['version']] = $registry[$name]['latest']['url'];
        }

        // added remaining versions (array items); if any, as pure [version] => [url] relationships.
        if (isset($latestVersion) && count($latestVersion) > 0) {
            foreach ($latestVersion as $new_version_entry) {
                $registry[$name][$new_version_entry['version']] = $new_version_entry['url'];
            }
        }

        return static::sort($registry);
    }

    public static function clearOldScans()
    {
        $scans = glob(DATA_DIR . 'scans\*.php');
        if (count($scans) > 0) {
            foreach ($scans as $file) {
                unlink($file);
            }
        }
    }

    /**
     * @param $component Component Registry Shorthand (e.g. "phpext_xdebug", not "xdebug").
     * @param $registry The registry.
     */
    public static function writeRegistrySubset($component, $registry)
    {
        return (bool) file_put_contents(
            DATA_DIR . 'scans\latest-version-' . strtolower($component) . '.php',
            sprintf("<?php\nreturn %s;", self::prettyPrint($registry))
        );
    }

    public static function addLatestVersionScansIntoRegistry(array $registry, $forComponent = '')
    {
        $scans = glob(DATA_DIR . 'scans\*.php');

        // nothing to do, return early
        if (count($scans) === 0) {
            return false;
        }

        $forComponent = strtolower($forComponent);

        foreach ($scans as $i => $file) {
            $subset    = include $file;
            preg_match('#latest-version-(.*).php#i', $file, $matches);
            $component = $matches[1];

            // add the registry subset only for a specific component
            if (isset($forComponent) && ($forComponent === $component)) {
                printf('Adding Scan/Subset for "%s".' . PHP_EOL, $component);
                $registry[$component] = $subset;

                return $registry;
            } elseif (isset($forComponent) && ($forComponent !== $component)) {
                // skip to the next component, if forComponent is used, but not found yet
                continue;
            } else {
                // forComponent not set = add all
                $registry[$component] = $subset;
            }
        }

        return $registry;
    }

    public static function load()
    {
        $registryFile = REGISTRY_DIR . '\wpnxm-software-registry.php';

        if(!is_file($registryFile)) {
            throw new RuntimeException('The software registry file "'.$registryFile.'" was not found.');
        }

        return include $registryFile;
    }

    public static function sort(array $registry)
    {
        // sort registry (software components in alphabetical order)
        ksort($registry);

        // sort registry (version numbers in lower-to-higher order)
        // maintain "name" and "website" keys on top, then versions, then "latest" key on bottom.
        foreach ($registry as $component => $array)
        {
            // sort by version number
            // but version_compare does not seem to work on x.y.z{alpha} version numbers (1.2.3c)
            if ($component === 'openssl' or $component === 'openssl-x64') {
                uksort($array, 'strnatcmp');
            } else {
                uksort($array, 'version_compare');
            }

            ArrayUtil::move_key_to_bottom($array, 'latest');

            ArrayUtil::move_key_to_top($array, 'website');
            ArrayUtil::move_key_to_top($array, 'name');

            // reassign the sorted array
            $registry[$component] = $array;
        }

        return $registry;
    }

    /**
     * Pretty prints the registry.
     *
     * @param  array  $registry
     * @return string
     */
    public static function prettyPrint(array $registry)
    {
        ksort($registry);

        $content = var_export($registry, true);
        $content = str_replace('array (', 'array(', $content);
        $content = preg_replace('/\n\s+array/', 'array', $content);

        return ArrayUtil::removeTrailingSpaces($content);
    }

    /**
     * Git commits and pushes the latest changes to the
     * wpnxm software registry with specified commit message.
     *
     * @param string $commitMessage Optional Commit Message
     */
    public static function gitCommitAndPush($commitMessage = '', $doGitPush = false)
    {
        echo '<pre>';

        // setup path to git
        $git = '"git" ';
        passthru($git . '--version');

        // switch to the git submodule "registry"
        chdir(DATA_DIR . 'registry');
        //echo 'Switched to Registry folder: ' . getcwd() . NL;

        // make sure we are on the "master" branch and not in "detached head" state
        echo NL . 'Switching branch to "master":' . NL;
        passthru($git . 'checkout master');

        echo NL . 'Pulling possible changes:' . NL;
        passthru($git . 'pull');

        //echo NL . 'Staging current changes' . NL;
        //exec("git add .; git add -u .");

        echo NL . 'Committing current changes "' . $commitMessage . '"' . NL;
        passthru($git . 'commit -m "' . $commitMessage . '" -- wpnxm-software-registry.php');

        echo NL . 'You might "git push" now.' . NL;

        if($doGitPush === true) {
            echo NL . 'Pushing commit to remote server:' . NL;
            passthru($git . 'push');
        }

        echo '</pre>';
    }    
}