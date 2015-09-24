<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;

/**
 * This updates all components of all installation registry to their latest version.
 */
class UpdateComponents extends ActionBase
{
    public $registry;
    
    function __construct()
    {    
        $this->registry = Registry::load();   
        
        Registry::clearOldScans();
    }

    function __invoke()
    {
        $nextRegistries = glob(REGISTRY_DIR . '*-next-*.json');

        if (empty($nextRegistries) === true) {
            exit('No "next" JSON registries found. Create installers for the next version.');
        }

        echo 'Update all components to their latest version.<br>';

        $downloadFilenames = $this->loadDownloadDescriptionFile();

        foreach ($nextRegistries as $file) {
            $filename        = basename($file);
            echo '<br>Processing Installer: "' . $filename . '":<br>';
            $components      = json_decode(file_get_contents($file), true);
            $version_updated = false;
            for ($i = 0; $i < count($components); ++$i) {
                $componentName = $components[$i][0];
                $url           = $components[$i][1];
                $version       = $components[$i][3];

                if (!isset($downloadFilenames[$componentName])) {
                    echo 'The download description file has no value for the Component "' . $componentName . '"<br>';
                } else {
                    // update the download filename with the value of the download description file
                    // in case the registry contains a different (old) value
                    $downloadFilename = $downloadFilenames[$componentName];

                    if ($components[$i][2] !== $downloadFilename) {
                        $components[$i][2] = $downloadFilename;
                    }
                }

                $latestVersion = $this->getLatestVersionForComponent($componentName, $filename);

                if (version_compare($version, $latestVersion, '<') === true) {
                    $components[$i][3] = $latestVersion;
                    if (false !== strpos($url, $version)) { // if the url has a version appended, update it too
                        $components[$i][1] = str_replace($version, $latestVersion, $url);
                    }
                    echo 'Updated "' . $componentName . '" from v' . $version . ' to v' . $latestVersion . '.<br>';
                    $version_updated = true;
                }
            }
            if ($version_updated === true) {
                Registry::write($file, $components);
            } else {
                echo 'The installer registry is up-to-date.<br>';
            }
        }

        echo '<pre>You might "git commit/push":<br>updated installer registries of "next" version</pre>';
    }
    
    function loadDownloadDescriptionFile()
    {
        $descriptionFile = DATA_DIR . 'downloadFilenames.php';
        
        if(!is_file($descriptionFile)) {
            throw new RuntimeException('The download description file "'.$descriptionFile.'" was not found.');
        }
        
        return include $descriptionFile;
    }

    /**
     * Return the PHP version of a registry file.
     *
     * @param string  A filename, e.g. registry filename, like "full-next-php5.6-w64.json".
     * @param string $file
     * @return string PHP Version.
     */
    function getPHPVersionFromFilename($file)
    {
        preg_match("/-php(.*)-/", $file, $matches);

        return $matches[1];
    }

    /**
     * Return the latest version for a component.
     * Takes the PHP major.minor.latest version constraint into account.
     *
     * @param string $component
     * @param string $filename
     * @return string version
     */
    function getLatestVersionForComponent($component, $filename)
    {
        // latest version of PHP means "latest version for PHP5.4, PHP5.5, PHP5.6"
        // we have to raise the PHP version, respecting the major.minor version constraint
        if ($component === 'php' || $component === 'php-x64' || $component === "php-qa" || $component === "php-qa-x64") {
            $minVersionConstraint = $this->getPHPVersionFromFilename($filename); // 5.4, 5.5
            $maxVersionConstraint = $minVersionConstraint . '.99'; // 5.4.99, 5.5.99
            return $this->getLatestVersion($component, $minVersionConstraint, $maxVersionConstraint);
        }

        return $this->getLatestVersion($component);
    }

    /**
     * @param string $component
     * @param string $minConstraint
     * @param string $maxConstraint
     */
    function getLatestVersion($component, $minConstraint = null, $maxConstraint = null)
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

}
