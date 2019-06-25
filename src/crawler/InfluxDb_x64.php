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
 * InfluxDb - Version Crawler
 *
 * Apache Cassandra is an open source distributed database management system
 * designed to handle large amounts of data across many commodity servers,
 * providing high availability with no single point of failure.
 *
 * Website:       https://www.influxdata.com/
 * Downloads:     https://influxdb.com/download/
 *                https://portal.influxdata.com/downloads/  
 */
class InfluxDb_x64 extends VersionCrawler
{
    public $name = 'influxdb-x64';

    public $url = 'https://influxdb.com/download/index.html';

    public function crawlVersion()
    {
        // Download URL: https://s3.amazonaws.com/influxdb/influxdb_0.9.4.2_amd64.msi
        // https://dl.influxdata.com/influxdb/releases/influxdb-1.7.6_windows_amd64.zip

        // filter all a href's with "end of string" match ($)
        return $this->filter('a[href$="amd64.msi"]')->each(function ($node) {

            if (preg_match("#influxdb_(\d+\.\d+(\.\d+)*)_amd64.msi#i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://s3.amazonaws.com/influxdb/influxdb_' . $version . '_amd64.msi',
                    );
                }
            }
        });
    }
}
