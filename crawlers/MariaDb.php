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

/**
 * MariaDB - Version Crawler
 */
class MariaDb extends VersionCrawler
{

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

                    /**
                     *   *** WARNING ***
                     *
                     * The links are not consistent, because of folder and filename changes in the
                     * release archive. Also some versions are missing in their archive.
                     * Windows releases are available from v5.1.49
                     *
                     * - http://archive.mariadb.org/mariadb-5.1.49/kvm-zip-winxp-x86/mariadb-noinstall-....
                     * - http://archive.mariadb.org/mariadb-5.2.6/win2008r2-vs2010-i386/mariadb-5.2.6-win32.zip
                     * - http://archive.mariadb.org/mariadb-5.5.27/windows/mariadb-5.5.27-win32.zip
                     * - http://archive.mariadb.org/mariadb-5.5.28/win32-packages/mariadb-5.5.28-win32.zip
                     */

                    $filename = 'mariadb-' . $version . '-win32.zip';
                    $folder = 'win32-packages';

                    if (version_compare($version, '5.1.49', '<=')) {
                        $folder   = 'kvm-zip-winxp-x86';
                        $filename = 'mariadb-noinstall-' . $version . '-win32.zip';
                    } elseif (version_compare($version, '5.2.6', '<=')) {
                        $folder = 'win2008r2-vs2010-i386';
                    } elseif (version_compare($version, '5.5.23', '<=')) {
                        $folder = 'win2008r2-vs2010-i386-packages';
                    } elseif (version_compare($version, '5.5.27', '<=')) {
                        $folder = 'windows';
                    } elseif (version_compare($version, '5.5.28', '>=')) { // 5.5.28 and above
                        $folder = 'win32-packages';
                    }

                    /**
                     * Download Mirrors
                     *
                     * http://archive.mariadb.org/mariadb-5.5.38/win32-packages/mariadb-5.5.38-win32.zip
                     * http://mirrors.n-ix.net/mariadb/mariadb-5.5.32/win32-packages/mariadb-5.5.32-win32.zip
                     * http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-5.5.38/win32-packages/mariadb-5.5.38-win32.zip
                     */

                    if (version_compare($version, $this->registry['mariadb']['latest']['version'], '>=')) {
                        return array(
                            'version' => $version,
                            'url'     => 'http://ftp.hosteurope.de/mirror/archive.mariadb.org/mariadb-' . $version . '/' . $folder . '/' . $filename
                        );
                    }
                }
            });
    }

}
