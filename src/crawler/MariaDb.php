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
 * MariaDB - Version Crawler
 */
class MariaDb extends VersionCrawler
{
    public $name = 'mariadb';

    // http://ftp.hosteurope.de/mirror/archive.mariadb.org/
    public $url = 'http://archive.mariadb.org/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#mariadb-(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    // skip all versions below v'5.5.28'
                    if (version_compare($version, '5.5.28') <= 0) {
                        $version = '0.0.0';
                    };

                    /*
                     * Download Mirrors
                     *
                     * Archive Server:
                     * http://archive.mariadb.org/mariadb-5.5.38/win32-packages/mariadb-5.5.38-win32.zip
                     *
                     * Mirror Origin:
                     * http://ftp.osuosl.org/pub/mariadb/*
                     *
                     * Mirror:
                     * http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-5.5.38/win32-packages/mariadb-5.5.38-win32.zip
                      */

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-' . $version . '/win32-packages/mariadb-' . $version . '-win32.zip',
                        );
                    }
                }
            });
    }
}
