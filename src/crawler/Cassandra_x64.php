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
 * Apache Cassandra - Version Crawler
 *
 * Apache Cassandra is an open source distributed database management system
 * designed to handle large amounts of data across many commodity servers,
 * providing high availability with no single point of failure.
 *
 * We use the DataStax Community Edition of Apache Cassandra.
 *
 * Website:       http://cassandra.apache.org/
 *                http://www.datastax.com/
 *                http://www.planetcassandra.org/cassandra/
 * Downloads:     http://cassandra.apache.org/download/
 */
class Cassandra_x64 extends VersionCrawler
{
    public $name = 'cassandra-x64';

    // We could scrape the table: http://www.planetcassandra.org/cassandra/
    // Or scan the folder:
    public $url = 'http://downloads.datastax.com/community/';

    public function crawlVersion()
    {
        // Download URL: http://downloads.datastax.com/community/datastax-community-64bit_2.2.3.msi

        // filter all a href's with "part of string" match (*)
        return $this->filter('a[href*="datastax-community-64bit"]')->each(function ($node) {

            if (preg_match("#datastax-community-64bit_(\d+\.\d+\.\d+).msi#i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://downloads.datastax.com/community/datastax-community-64bit_' . $version . '.msi',
                    );
                }
            }
        });
    }
}
