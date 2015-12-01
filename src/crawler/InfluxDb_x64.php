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
 * InfluxDb - Version Crawler
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
class InfluxDb_x64 extends VersionCrawler
{
    public $name = 'influxdb-x64';

    public $url = 'https://influxdb.com/download/index.html';

    public function crawlVersion()
    {
        // Download URL: https://s3.amazonaws.com/influxdb/influxdb_0.9.4.2_amd64.msi

        // filter all a href's with "end of string" match ($)
        return $this->filter('a[href$="amd64.msi"]')->each(function ($node) {

            if (preg_match("#influxdb_(\d+\.\d+(\.\d+)*)_amd64.msi#i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->registry['influxdb-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://s3.amazonaws.com/influxdb/influxdb_' . $version . '_amd64.msi',
                    );
                }
            }
        });
    }
}
