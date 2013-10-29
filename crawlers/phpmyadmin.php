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
 * phpMyAdmin - Version Crawler
 */
class Phpmyadmin extends VersionCrawler
{
    public $url = 'http://www.phpmyadmin.net/home_page/downloads.php';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node, $i) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(beta|b|rc|alpha|a|patch|pl|p)?(\d+)(?:[.-]?(\d+))?)?([.-]?dev)?#i", $node->text(), $matches)) {
            $version = $matches[0];
            if (version_compare($version, $this->registry['phpmyadmin']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://switch.dl.sourceforge.net/project/phpmyadmin/phpMyAdmin/'.$version.'/phpMyAdmin-'.$version.'-english.zip'
                );
            }
        }
    });
    }
}
?>