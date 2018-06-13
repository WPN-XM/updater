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
 * MongoDb - Version Crawler
 */
class mongodb extends VersionCrawler
{
    public $name = 'mongodb';

    // formerly http://www.mongodb.org/downloads
    // warning: do not use http://dl.mongodb.org/dl/win32/ - use instead: https://www.mongodb.org/dl/win32/
    public $url = 'https://www.mongodb.org/dl/win32/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // no RC versions!
            if (preg_match("#win32-i386-(\d+\.\d+(\.\d+)*).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://downloads.mongodb.org/win32/mongodb-win32-i386-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
