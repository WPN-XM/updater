<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\InstallerRegistries;
use WPNXM\Updater\DownloadFilenames;
use WPNXM\Updater\InstallerRegistry;
use WPNXM\Updater\Registry;
use WPNXM\Updater\Version;
//use WPNXM\Updater\View;

/**
 * This updates all components of all installation registry to their latest version.
 */
class UpdateComponents extends ActionBase
{
    public $registry;

    public function __construct()
    {
        $this->registry = Registry::load();

        Registry::clearOldScans();
    }

    public function __invoke()
    {
        $html = '';

        $nextRegistries = InstallerRegistries::getRegistriesOfNextRelease();

        $downloadFilenames = DownloadFilenames::load();

        foreach ($nextRegistries as $file)
        {
            $filename        = basename($file);

            $html .= '<br>Processing Installer <strong>' . $filename . '</strong>:&nbsp;<br>';
            
            $registry      = json_decode(file_get_contents($file), true);
            $version_updated = false;            
            $number_of_components = count($registry);

            for ($i = 0; $i < $number_of_components; ++$i) {
                $componentName = $registry[$i][0];
                $url           = $registry[$i][1];
                $version       = $registry[$i][3];

                if (!isset($downloadFilenames[$componentName])) {
                    throw new \Exception('The download description file has no value for the Component "' . $componentName . '"<br>');
                }

                /**
                 * Synchronize the "download filename" (registry key)
                 * with the value of the download description file (/registry/downloadFilenames.php),
                 * but only in case the registry contains a different (old) value.
                 */
                $downloadFilename = $downloadFilenames[$componentName];
                if ($registry[$i][2] !== $downloadFilename) {
                    $registry[$i][2] = $downloadFilename;
                }

                /**
                 * Raise version to latest version
                 */
                $latestVersion = $this->getLatestVersionForComponent($componentName, $filename);


                if (Version::compare($componentName, $version, $latestVersion) === true) {
                    // update the version number (idx 3)
                    $registry[$i][3] = $latestVersion;
                    // if the url (idx 1) has a version appended, update it too
                    if (false !== strpos($url, $version)) {
                        $registry[$i][1] = str_replace($version, $latestVersion, $url);
                    }

                    $html .= 'Updated "' . $componentName . '" from v' . $version . ' to v' . $latestVersion . '.<br>';
                    $version_updated = true;
                }
            }

            if ($version_updated === true) {
                InstallerRegistry::write($file, $registry);   
            } else {
                $html .= 'The installer registry is up-to-date.';
            }
        }

        /* View */

        $view = new View();
        $view->data['html'] = $html;
        $view->render();
    }


    /**
     * Return the latest version for a component.
     * Takes the PHP major.minor.latest version constraint into account.
     *
     * @param string $component
     * @param string $filename
     * @return string version
     */
    public function getLatestVersionForComponent($component, $filename)
    {
        // For PHP releases, we determine the latest version of the minor release series,
        // by using the major.minor version number and a min/max patch level range.
        if ($component === 'php' || $component === 'php-x64' || $component === "php-qa" || $component === "php-qa-x64") {
            $minVersionConstraint = InstallerRegistries::getPHPVersionFromFilename($filename); // 5.4, 5.5
            $maxVersionConstraint = $minVersionConstraint . '.99'; // 5.4.99, 5.5.99

            return $this->getLatestVersion($component, $minVersionConstraint, $maxVersionConstraint);
        }

        // For PHP extensions, we determine the latest version based on phpversion and bitsize constraints.
        if (stristr($component, 'phpext_') !== false) {
            $constraints = InstallerRegistries::getConstraintsFromFilename($filename);

            return $this->getLatestVersionPHPExtension($component, $constraints);
        }

        return $this->getLatestVersion($component);
    }

    /**
     * @param string $component
     * @param string $minConstraint
     * @param string $maxConstraint
     */
    public function getLatestVersion($component, $minConstraint = null, $maxConstraint = null)
    {
        if (isset($component) === false) {
            throw new RuntimeException('No component provided.');
        }

        if (isset($this->registry[$component]) === false) {
            throw new RuntimeException('The component "' . $component . '" was not found in the registry.');
        }

        if ($minConstraint === null && $maxConstraint === null) {
            return $this->registry[$component]['latest']['version'];
        }

        // determine latest version for a component given a min/max constraint

        $software = $this->registry[$component];

        // remove all non-version stuff
        unset($software['name'], $software['latest'], $software['website']);
        // the array is already sorted.
        // get rid of (version => url) and use (idx => version)
        $software = array_keys($software);
        // reverse array, in order to have the highest version number on top.
        $software = array_reverse($software);
        // reduce array to values in constraint range
        foreach ($software as $url => $version) {
            if (version_compare($version, $minConstraint, '>=') === true && version_compare($version, $maxConstraint, '<') === true) {
                #echo 'Version v' . $version . ' is greater v' . $minConstraint . '(MinConstraint) and smaller v' . $maxConstraint . '(MaxConstraint).<br>';
            } else {
                unset($software[$url]);
            }
        }
        // pop off the first element
        $latestVersion = array_shift($software);

        return $latestVersion;
    }

    public function getLatestVersionPHPExtension($component, $constraints)
    {
        $software = $this->registry[$component];

        // remove all non-version stuff
        unset($software['name'], $software['latest'], $software['website']);

        // the array is already sorted.
        // reverse array, in order to have the highest version number on top.
        $versions = array_reverse($software);

        // reduce array to values in constraint range
        foreach ($versions as $version => $bitsize) {
            if(isset($bitsize[ $constraints['bitsize'] ])) {
                if(isset($bitsize[ $constraints['bitsize'] ][ $constraints['phpversion'] ])) {
                    //var_dump($component, $constraints, $versions, $version);
                    return $version;
                }
            }
        }
    }   

}
