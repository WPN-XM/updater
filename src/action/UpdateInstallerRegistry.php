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
use WPNXM\Updater\Registry;

class UpdateInstallerRegistry extends ActionBase
{

    public function __invoke()
    {
        $installer    = filter_input(INPUT_POST, 'installer', FILTER_SANITIZE_STRING);
        $registryJson = filter_input(INPUT_POST, 'registry-json', FILTER_SANITIZE_STRING);

        $file              = DATA_DIR . '\registry\\' . $installer . '.json';
        $registryJson      = html_entity_decode($registryJson, ENT_COMPAT, 'UTF-8'); // fix the JSON.stringify quotes &#34;
        $installerRegistry = json_decode($registryJson, true);
        $downloadFilenames = include DATA_DIR . '\downloadFilenames.php';

        $data = array();

        foreach ($installerRegistry as $component => $version)
        {
            $url = 'http://wpn-xm.org/get.php?s=' . $component . '&v=' . $version;

            // special handling for PHP components
            if (in_array($component, ['php', 'php-x64', 'php-qa-x64', 'php-qa']) === true) {
                $php_version = substr($installerRegistry[$component], 0, 3); // get only major.minor, e.g. "5.4", not "5.4.2"

                $bitsize = (false !== strpos($component, 'x64')) ? 'x64' : ''; // empty bitsize defaults to x86, see website "get.php"
            }

            // special handling for PHP Extensions (which depend on a specific PHP version and bitsize)
            if (false !== strpos($component, 'phpext_')) {
                $url .= '&p=' . $php_version;
                $url .= ($bitsize !== '') ? '&bitsize=' . $bitsize : '';
            }

            $downloadFilename = $downloadFilenames[$component];

            $data[] = array($component, $url, $downloadFilename, $version);
        }

        #var_dump($installer, $registryJson, $installerRegistry, $file, $data);

        return Registry::write($file, $data);
    }

}
