<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * MariaDB (x86_64) - Version Crawler
 */
class MariaDb_x64 extends VersionCrawler
{
    public $name = 'mariadb-x64';

    public $url = 'http://archive.mariadb.org/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#mariadb-(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    // skip all versions below v5.1.49, because this is the first one with a windows release folder
                    if (version_compare($version, '5.1.48') <= 0) {
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
                     * Mirrors:
                     * http://mirrors.n-ix.net/mariadb/mariadb-5.5.32/win32-packages/mariadb-5.5.32-win32.zip
                     * http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-5.5.38/win32-packages/mariadb-5.5.38-win32.zip
                     * http://mirror3.layerjet.com/mariadb/mariadb-10.0.17/win32-packages/mariadb-10.0.17-win32.zip
                     * http://ams2.mirrors.digitalocean.com/mariadb/mariadb-10.1.5/win32-packages/mariadb-10.1.5-win32.zip
                     * SF: http://mirror.jmu.edu/pub/mariadb/*
                     */

                    if (version_compare($version, $this->registry['mariadb-x64']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'http://mirror.jmu.edu/pub/mariadb/mariadb-' . $version . '/winx64-packages/mariadb-' . $version . '-winx64.zip',
                        );
                    }
                }
            });
    }
}
