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
 * usql - Version Crawler
 *
 * A universal command-line interface for PostgreSQL, MySQL, 
 * Oracle Database, SQLite3, Microsoft SQL Server, 
 * and many other databases including NoSQL and non-relational databases!
 *
 * https://github.com/xo/usql
 */
class usql_x64 extends VersionCrawler
{
    public $name = 'usql-x64';

    public $url = 'https://github.com/xo/usql/releases/latest';

    private $dl_url = 'https://github.com/xo/usql/releases/download/v%version%/usql-%$version%-windows-amd64.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                // https://github.com/xo/usql/releases/download/v0.7.0/usql-0.7.0-windows-amd64.zip                
                if (preg_match("#/download/v(\d+\.\d+.\d+)/usql-#", $node->attr('href'), $matches)) {
                    $version = $matches[1];
                    $download_file = str_replace('%version%', $version, $this->dl_url);
                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
