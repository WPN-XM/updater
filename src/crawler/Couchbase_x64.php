<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
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
class Couchbase_x64 extends VersionCrawler
{
    public $name = 'couchbase-x64';

    public $url = 'http://www.couchbase.com/nosql-databases/downloads';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->extract('data-url')[0];
            // http://packages.couchbase.com/releases/4.1.0-dp/couchbase-server_4.1.0-dp-windows_amd64.exe
            if (preg_match("#/releases/(\d+\.\d+.\d+)-dp/couchbase-server_(\d+\.\d+.\d+)-dp-windows_amd64.exe#i", $url, $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['couchbase-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://packages.couchbase.com/releases/' . $version . '-dp/couchbase-server_' . $version . '-dp-windows_amd64.exe'
                    );
                }
            }
        });
    }
}
