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
 * Greenshot - Version Crawler
 *
 * GraphViz - Screenshot Tool
 *
 * Website:       http://getgreenshot.org
 * Downloads:     http://getgreenshot.org/downloads/
 */
class Greenshot extends VersionCrawler
{
    public $name = 'greenshot';

    // http://getgreenshot.org/downloads/
    public $url = 'https://github.com/greenshot/greenshot/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->text();
            //
            // WARNING: Filename is inconsistent across versions
            //
            // Greenshot-NO-INSTALLER-1.2.10.6-RELEASE.zip
            //
            if (preg_match("#(\d+\.\d+.\d+(.\d+)?)(-RELEASE)?.zip#i", $url, $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,                       
                        'url'     => 'https://github.com/greenshot/greenshot/releases/download/Greenshot-RELEASE-' . $version . '/Greenshot-NO-INSTALLER-' . $version . '-RELEASE.zip'
                    );
                }
            }
        });
    }
}
