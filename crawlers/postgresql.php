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
 * PostGreSql - Version Crawler
 */
class postgresql extends VersionCrawler
{
    public $url = 'http://www.enterprisedb.com/products-services-training/pgbindownload';

    public function crawlVersion()
    {
        return $this->filterXPath('//p/i')->each(function ($node) {

            $value = strtolower($node->text());

            if (preg_match("#(\d+\.\d+(\.\d+)*)#i", $value, $matches)) {

                $download_version = '9.3.0-beta2-1';

                if (isset($matches[3]) === true) { // if we have "release candidate" or "beta"
                    $version = $matches[1];
                    $pre_release_version = $matches[4];
                    if ($matches[3] === 'release candidate') {
                        $download_version = $version . '-rc' . $pre_release_version;
                    }
                } else {
                    $version = $matches[0]; // just 1.2.3
                    $download_version = $version . '-1'; // wtf? "-1" means "not beta", or what?
                }

                if (version_compare($version, $this->registry['postgresql']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        // x86-64: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-x64-binaries.zip
                        // x86-32: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-binaries.zip
                        'url' => 'http://get.enterprisedb.com/postgresql/postgresql-' . $download_version . '-windows-binaries.zip'
                    );
                }
            }
        });
    }
}
