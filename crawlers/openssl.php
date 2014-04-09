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
 * OpenSSL - Version Crawler
 */
class openssl extends VersionCrawler
{
    public $url = 'http://slproweb.com/products/Win32OpenSSL.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node, $i) {
            // http://slproweb.com/download/Win32OpenSSL_Light-1_0_1d.exe
            if (preg_match("#Win32OpenSSL_Light-(\d+\_\d+\_\d+[a-z]).exe$#", $node->attr('href'), $matches)) {
                // the version match contains underscores: so turn "1_0_1d" into "1.0.1d", that's still not SemVer but anyway
                $version = str_replace('_', '.', $matches[1]);
                if (version_compare($version, $this->registry['openssl']['latest']['version'], '>')
                || (strcmp($this->registry['openssl']['latest']['version'], $version) < 0) ) {
                    return array(
                        'version' => $version,
                        'url' => 'http://slproweb.com/download/Win32OpenSSL_Light-'.$matches[1].'.exe'
                    );
                }
            }
        });
    }
}
