<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
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
        // backup current registry
        rename(
            DATA_DIR . 'registry/wpnxm-software-registry.php',
            DATA_DIR . 'registry/wpnxm-software-registry-backup-' . date("dmy-His") . '.php'
        );

        // registry file header
        $content = "<?php\n";
        $content .= "   /**\n";
        $content .= "    * WPИ-XM Server Stack\n";
        $content .= "    * Copyright © 2010 - " . date("Y") . " Jens-André Koch <jakoch@web.de>\n";
        $content .= "    * http://wpn-xm.org/\n";
        $content .= "    *\n";
        $content .= "    * This source file is subject to the terms of the MIT license.\n";
        $content .= "    * For full copyright and license information, view the bundled LICENSE file.\n";
        $content .= "    */\n";
        $content .= "\n";
        $content .= "   /**\n";
        $content .= "    * WPN-XM Software Registry\n";
        $content .= "    * ------------------------\n";
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
        }

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
                $latestVersion = static::sortArrayByVersion($latestVersion);
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

    public static function sortArrayByVersion($array)
    {
        $sort = function ($versionA, $versionB) {
            return version_compare($versionA['version'], $versionB['version']);
        };
        usort($array, $sort);

        return $array;
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
        // load software components registry
        $registry = include dirname(__DIR__) . '\data\registry\wpnxm-software-registry.php';

        // ensure registry array is available
        if (!is_array($registry)) {
            header("HTTP/1.0 404 Not Found");
        }

        return $registry;
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

            // move 'latest' to the bottom of the arary
            self::move_to_bottom($array, 'latest');

            // move 'name' and 'website' to the top of the array
            self::move_to_top($array, 'website');
            self::move_to_top($array, 'name');

            // reassign the sorted array
            $registry[$component] = $array;
        }

        return $registry;
    }

    /**
     * This works on the array and moves the key to the top.
     *
     * @param array  $array
     * @param string $key
     */
    private static function move_to_top(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $temp  = array($key => $array[$key]);
            unset($array[$key]);
            $array = $temp + $array;
        }
    }

    /**
     * This works on the array and moves the key to the bottom.
     *
     * @param array  $array
     * @param string $key
     */
    private static function move_to_bottom(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $value       = $array[$key];
            unset($array[$key]);
            $array[$key] = $value;
        }
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
    public static function gitCommitAndPush($commitMessage = '')
    {
        // setup path to git
        $git = '"C:\Program Files (x86)\Git\bin\git" ';
        //passthru($git . '--version');

        echo '<pre>';

        // switch to the git submodule "registry"
        chdir(DATA_DIR . 'registry');
        //echo 'Switched to Registry folder: ' . getcwd() . NL;

        echo NL . 'Pulling possible changes:' . NL;
        passthru($git . 'pull');

        //echo NL . 'Staging current changes' . NL;
        //exec("git add .; git add -u .");

        echo NL . 'Committing current changes "' . $commitMessage . '"' . NL;
        passthru($git . 'commit -m "' . $commitMessage . '" -- wpnxm-software-registry.php');

        echo NL . 'You might "git push" now.' . NL;


        //echo NL . 'Push commit to remote server' . NL;
        //passthru($git . 'push');

        //echo '<a href="#" class="btn btn-lg btn-primary">'
        //   . '<span class="glyphicon glyphicon-save"></span> Git Push</a>';

        echo '</pre>';
    }

    /**
     * Writes the registry as JSON to the installer registry file.
     *
     * @param string $file
     * @param array  $registry
     */
    public static function write($file, $registry)
    {
        asort($registry);

        $json        = json_encode($registry);
        $json_pretty = \WPNXM\Updater\JsonUtil::prettyPrintCompact($json);
        $json_table  = \WPNXM\Updater\JsonUtil::prettyPrintTableFormat($json_pretty);

        file_put_contents($file, $json_table);

        echo 'Updated or Created Installer Registry "' . $file . '"<br />';
    }

    public static function reduceArrayToContainOnlyVersions($array)
    {
        unset($array['website'], $array['latest'], $array['name']);
        $array = array_reverse($array); // latest version first
        return $array;
    }
}