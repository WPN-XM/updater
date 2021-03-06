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
 * phpMemcachedAdmin - Version Crawler
 */
class phpmemcachedadmin extends VersionCrawler
{
    /**
     * WARNING
     * The project name is "phpmemcacheadmin", while the filename is "phpmemcachedadmin" (with D).
     * In WPN-XM the name is "phpmemcachedadmin" (with D).
     *
     * @var string
     */
    public $url = 'http://code.google.com/p/phpmemcacheadmin/downloads/list';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // phpMemcachedAdmin-1.2.2-r262.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(r)?(\d+))?#", $node->attr('href'), $matches)) {
                $version_long = $matches[0]; // 1.2.3-r123
                $version = $matches[1]; // 1.2.3
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://phpmemcacheadmin.googlecode.com/files/phpMemcachedAdmin-' . $version_long . '.zip',
                    );
                }
            }
        });
    }
}
