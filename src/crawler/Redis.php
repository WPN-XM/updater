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
    public $name = 'redis';

    // https://github.com/MSOpenTech/redis/releases/latest
    public $url = 'https://github.com/tporadowski/redis/releases/latest';

    private $dl_url_template = 'https://github.com/tporadowski/redis/releases/download/%release_version%/Redis-x64-%version%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * The download URL for a file looks like this:
             * https://github.com/MSOpenTech/redis/releases/download/win-2.8.21/redis-x64-2.8.21.zip
             * New download URL is:
             * https://github.com/tporadowski/redis/releases
             * Full URL:
             * https://github.com/tporadowski/redis/releases/download/v4.0.2.3-alpha/Redis-x64-4.0.2.3.zip
             */
            if (preg_match("#/download/(.*)/Redis-x64-(\d+.\d+.\d+.\d+).zip#i", $node->attr('href'), $matches)) {
                $release_version = $matches[1];
                $version = $matches[2];
                $url = str_replace(['%release_version%', '%version%'], [$release_version, $version], $this->dl_url_template);
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => $url,
                    );
                }
            }
        });
    }
}
