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
 * Couchbase - Version Crawler
 *
 * Couchbase is a NoSQL database.
 *
 * Website:       http://www.couchbase.com/
 * Download Repo: http://www.couchbase.com/nosql-databases/downloads
 */
class Couchbase extends VersionCrawler
{
    public $name = 'couchbase';

    public $url = 'http://www.couchbase.com/nosql-databases/downloads';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->extract('data-url')[0];
            // http://packages.couchbase.com/releases/4.1.0-dp/couchbase-server_4.1.0-dp-windows_x86.exe
            if (preg_match("#/releases/(\d+\.\d+.\d+)-dp/couchbase-server_(\d+\.\d+.\d+)-dp-windows_x86.exe#i", $url, $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://packages.couchbase.com/releases/' . $version . '-dp/couchbase-server_' . $version . '-dp-windows_x86.exe'
                    );
                }
            }
        });
    }
}
