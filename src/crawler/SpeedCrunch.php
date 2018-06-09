<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * SpeedCrunch - Version Crawler
 *
 * SpeedCrunch is a high-precision scientific calculator 
 * featuring a fast, keyboard-driven user interface. 
 * It is free and open-source software, licensed under the GPL.
 *
 * Website: http://speedcrunch.org/
 * Source:  https://bitbucket.org/heldercorreia/speedcrunch
 */
class SpeedCrunch extends VersionCrawler
{
    public $name = 'speedcrunch';

    public $url = 'https://bitbucket.org/heldercorreia/speedcrunch/downloads/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            // https://bitbucket.org/heldercorreia/speedcrunch/downloads/SpeedCrunch-0.12-win32.zip
            
            if (preg_match("#/downloads/SpeedCrunch-(\d+\.\d+(\.\d+)*)#", $node->attr('href'), $matches))
            {
                $version = $matches[1];

                $download_url = 'https://bitbucket.org/heldercorreia/speedcrunch/downloads/SpeedCrunch-' . $version . '-win32.zip';

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $download_url,
                    );
                }
            }
        });
    }
}
