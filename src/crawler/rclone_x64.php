<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * Version Crawler for
 * Rclone is a command line program to sync 
 * files and directories to and from cloud storages.
 *
 * Website: http://rclone.org/
 * Github:  https://github.com/ncw/rclone
 */
class RClone_x64 extends VersionCrawler
{
    public $name = 'rclone-x64';
    public $url  = 'https://github.com/ncw/rclone/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {            
            if (preg_match("#/ncw/rclone/releases/download/v(\d+\.\d+)/rclone#", $node->attr('href'), $matches)) {               
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // https://github.com/ncw/rclone/releases/download/v1.36/rclone-v1.36-windows-amd64.zip
                        'url' => 'https://github.com/ncw/rclone/releases/download/v' . $version . '/rclone-v' . $version . '-windows-amd64.zip',
                    );
                }
            }
        });
    }
}
