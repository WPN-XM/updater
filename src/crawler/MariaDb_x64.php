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
 * MariaDB (x86_64) - Version Crawler
 */
class MariaDb_x64 extends VersionCrawler
{
    public $name = 'mariadb-x64';

    // http://ftp.hosteurope.de/mirror/archive.mariadb.org/
    public $url = 'http://archive.mariadb.org/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#mariadb-(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    // skip all versions below v5.1.49, because this is the first one with a windows release folder
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
                            'url'     => 'http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-' . $version . '/winx64-packages/mariadb-' . $version . '-winx64.zip',
                        );
                    }
                }
            });
    }
}
