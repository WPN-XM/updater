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
 * Pretty prints all installation wizard registries.
 */
class PrettyPrintRegistries extends ActionBase
{
    public $registry;

    public function __construct()
    {
        $this->registry = Registry::load();

        Registry::clearOldScans();
    }

    public function __invoke()
    {
        $nextRegistries = $this->getInstallerRegistriesOfNextVersion();

        echo 'Pretty printing all installation wizard registries.<br>';

        foreach ($nextRegistries as $file) {
            $filename        = basename($file);
            echo '<br>Processing Installer: "' . $filename . '":<br>';
            $components      = json_decode(file_get_contents($file), true);
            Registry::write($file, $components);
        }

        echo '<pre>You might "git commit/push":<br>pretty printed registries</pre>';
    }

    public function getInstallerRegistriesOfNextVersion()
    {
        $nextRegistries = glob(REGISTRY_DIR . '*-next-*.json');

        if (empty($nextRegistries) === true) {
            throw new \Exception('No "next" JSON registries found. Create installers for the next version.');
        }

        return $nextRegistries;
    }

    /**
     * Return the PHP version of a registry file.
     *
     * @param string  A filename, e.g. registry filename, like "full-next-php5.6-w64.json".
     * @param string $file
     * @return string PHP Version.
     */
    public function getPHPVersionFromFilename($file)
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
    public function getLatestVersionForComponent($component, $filename)
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

}
