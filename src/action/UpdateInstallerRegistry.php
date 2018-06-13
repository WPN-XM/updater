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
use WPNXM\Updater\DownloadFilenames;
use WPNXM\Updater\InstallerRegistries;
use WPNXM\Updater\InstallerRegistry;

/**
 * This accepts a POST request from ShowVersionMatrix with new registry data in JSON format.
 */
class UpdateInstallerRegistry extends ActionBase
{

    public function __invoke()
    {
        $installer    = filter_input(INPUT_POST, 'installer', FILTER_SANITIZE_STRING);
        $registryJson = filter_input(INPUT_POST, 'registry-json', FILTER_SANITIZE_STRING);

        $file              = InstallerRegistries::getFilePath($installer);
        $downloadFilenames = DownloadFilenames::load();
        $registryJson      = html_entity_decode($registryJson, ENT_COMPAT, 'UTF-8'); // fix the JSON.stringify quotes &#34;
        $installerRegistry = json_decode($registryJson, true);

        $registry = array(); 

        foreach ($installerRegistry as $component => $version)
        {
            $url = 'http://wpn-xm.org/get.php?s=' . $component . '&v=' . $version;

            // special handling for PHP components
            if (in_array($component, ['php', 'php-x64', 'php-qa-x64', 'php-qa'])) {
                // get only major.minor, e.g. "5.4", not "5.4.2"
                $php_version = substr($installerRegistry[$component], 0, 3);

                // an empty bitsize equals "x86" (default value in "get.php")
                $bitsize = (false !== strpos($component, 'x64')) ? 'x64' : '';
            }

            // special handling for PHP Extensions (they depend on a specific PHP version and bitsize)
            if (false !== strpos($component, 'phpext_')) {
                $url .= '&p=' . $php_version;
                $url .= ($bitsize != '') ? '&bitsize=' . $bitsize : '';
            }

            $downloadFilename = $downloadFilenames[$component];

            $registry[] = array($component, $url, $downloadFilename, $version);
        }

        #var_dump($installer, $registryJson, $installerRegistry, $file, $registry);

        return InstallerRegistry::write($file, $registry);
    }

}
