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
 * HeidiSQL - Version Crawler
 * 
 * Website:         http://heidisql.com/
 * Downloads:       http://www.heidisql.com/download.php?download=portable
 * Direct Download: http://www.heidisql.com/downloads/releases/HeidiSQL_9.3_Portable.zip
 */
class HeidiSql extends VersionCrawler
{
    public $name = 'heidisql';
    public $url = 'http://www.heidisql.com/download.php';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#HeidiSQL_(\d+\.\d+(\.\d+)*)_Portable.zip#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://www.heidisql.com/downloads/releases/HeidiSQL_' . $version . '_Portable.zip',
                    );
                }
            }
        });
    }
}
