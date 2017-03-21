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
 * Version Crawler for Redis
 *
 * Website: http://redis.io/
 * Github:  https://github.com/antirez/redis
 *
 * The windows port is maintained by MSOpenTech over at
 * Github:  https://github.com/MSOpenTech/redis
 */
class Redis extends VersionCrawler
{
    public $url = 'https://github.com/MSOpenTech/redis/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * The download URL for a file looks like this:
             * https://github.com/MSOpenTech/redis/releases/download/win-2.8.21/redis-x64-2.8.21.zip
             */
            if (preg_match("#download/win-(\d+\.\d+.\d+)/Redis-x64-(\d+\.\d+.\d+).zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['redis']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://github.com/MSOpenTech/redis/releases/download/win-' . $version . '/redis-x64-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
