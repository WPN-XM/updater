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
 * Version Crawler for
 *
 * Robo3t (formerly RoboMongo) - A Shell-centric cross-platform MongoDB management tool.
 *
 * Website:   http://robomongo.org/
 * Downloads: https://robomongo.org/download 
 *            down - http://robomongo.org/download.html
 *            down - http://download.robomongo.org/
 *            down - http://app.robomongo.org/download.html
 */
class robo3t extends VersionCrawler
{
    public $name = 'robo3t'; // robo3t
    public $url = 'https://robomongo.org/download';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // Old URLs (up to v1.0.0)
            // https://download.robomongo.org/0.9.0-rc4/windows/robomongo-0.9.0-rc4-windows-x86_64-8c830b6.exe
            // https://download.robomongo.org/1.0.0/windows/robomongo-1.0.0-windows-x86_64-89f24ea.exe
            // New URL (starting with v1.1)
            // https://download.robomongo.org/1.1.1/windows/robo3t-1.1.1-windows-x86_64-c93c6b0.exe 
            if (preg_match("#robo3t-(\d+.\d+.\d+(-rc\d)?)-windows-x86_64-(.*)\.exe#i", $node->attr('href'), $matches)) {
                $version = $matches[2];
                $hash    = $matches[3]; // why did you add a hash?
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://download.robomongo.org/' . $version . '/windows/robo3t-' . $version . '-windows-x86_64-' . $hash . '.exe',
                    );
                }
            }
        });
    }
}
