<?php
   /**
    * WPИ-XM Server Stack
    * Jens-André Koch © 2010 - onwards
    * http://wpn-xm.org/
    *
    *        _\|/_
    *        (o o)
    +-----oOO-{_}-OOo------------------------------------------------------------------+
    |                                                                                  |
    |    LICENSE                                                                       |
    |                                                                                  |
    |    WPИ-XM Serverstack is free software; you can redistribute it and/or modify    |
    |    it under the terms of the GNU General Public License as published by          |
    |    the Free Software Foundation; either version 2 of the License, or             |
    |    (at your option) any later version.                                           |
    |                                                                                  |
    |    WPИ-XM Serverstack is distributed in the hope that it will be useful,         |
    |    but WITHOUT ANY WARRANTY; without even the implied warranty of                |
    |    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
    |    GNU General Public License for more details.                                  |
    |                                                                                  |
    |    You should have received a copy of the GNU General Public License             |
    |    along with this program; if not, write to the Free Software                   |
    |    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA    |
    |                                                                                  |
    +----------------------------------------------------------------------------------+
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
        return $this->filter('a')->each(function ($node, $i) {
            if (preg_match("#mariadb-(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[1];

                // skip all versions below v5.1.49, because this is the first one with a windows release folder
                if (version_compare($version, '5.1.48') <= 0) {
                    $version = '0.0.0';
                };

                // skip all v10.0.0+ alpha versions
                if (version_compare($version, '10.0.0') >= 0) {
                    $version = '0.0.0';
                };

                $filename = 'mariadb-'.$version.'-win32.zip';

                //  *** WARNING ***
                // The links are not consistent, because of folder name changes, see:
                // - windows releases are available from v5.1.49
                // - http://archive.mariadb.org/mariadb-5.1.49/kvm-zip-winxp-x86/
                // - some versions are missing in their archive, anyway..
                // - http://archive.mariadb.org/mariadb-5.2.6/win2008r2-vs2010-i386/mariadb-5.2.6-win32.zip
                // - http://archive.mariadb.org/mariadb-5.5.27/windows/mariadb-5.5.27-win32.zip
                // - http://archive.mariadb.org/mariadb-5.5.28/win32-packages/mariadb-5.5.28-win32.zip

                // Download Mirror
                // http://mirrors.n-ix.net/mariadb/mariadb-5.5.32/win32-packages/mariadb-5.5.32-win32.zip

                if ($version <= '5.1.49') {
                    $folder = 'kvm-zip-winxp-x86';
                    $filename = 'mariadb-noinstall-'.$version.'-win32.zip';
                } elseif ($version <= '5.2.6') {
                    $folder = 'win2008r2-vs2010-i386';
                } elseif ($version <= '5.5.23') {
                    $folder = 'win2008r2-vs2010-i386-packages';
                } elseif ($version <= '5.5.27') {
                    $folder = 'windows';
                } elseif ($version >= '5.5.28') {
                    $folder = 'win32-packages';
                }

                if (version_compare($version, $this->registry['mariadb']['latest']['version'], '>=')) {
                    // "http://archive.mariadb.org/mariadb-"; "http://mirrors.n-ix.net/mariadb/mariadb-"
                    return array(
                        'version' => $version,
                        'url' => 'http://mirrors.n-ix.net/mariadb/mariadb-' . $version . '/' . $folder .'/' . $filename
                    );
                }
            }
        });
    }
}