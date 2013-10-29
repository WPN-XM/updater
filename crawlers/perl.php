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
 * perl (strawberry perl)
 */
class perl extends VersionCrawler
{
    public $url = 'http://strawberryperl.com/releases.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node, $i) {
            // perl-5.4.1.1-32bit.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)-32bit?#", $node->attr('href'), $matches)) {
                $version = $matches[1]; // 5.4.1.1
                if (version_compare($version, $this->registry['perl']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://strawberryperl.com/download/'.$version.'/strawberry-perl-'.$version.'-32bit.zip'
                    );
                }
            }
        });
    }
}
?>