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
 * Version Crawler for RedisDesktopManager
 *
 * Website: https://redisdesktop.com
 * Github:  https://github.com/uglide/RedisDesktopManager
 */
class RedisDesktopManager extends VersionCrawler
{
    public $name = 'redisdesktopmanager';

    public $url = 'https://github.com/uglide/RedisDesktopManager/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * The download URL for a file looks like this:
             * https://github.com/uglide/RedisDesktopManager/releases/download/0.8.8/redis-desktop-manager-0.8.8.384.exe
             */
            if (preg_match("#download/(\d+\.\d+.\d+)/redis-desktop-manager-(\d+\.\d+.\d+.\d+).exe#i", $node->attr('href'), $matches)) {
                $folder_version = $matches[1];
                $version = $matches[2];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://github.com/uglide/RedisDesktopManager/releases/download/' . $folder_version . '/redis-desktop-manager-' . $version . '.exe',
                    );
                }
            }
        });
    }
}
