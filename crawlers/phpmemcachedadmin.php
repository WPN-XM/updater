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
 * phpMemcachedAdmin - Version Crawler
 */
class Phpmemcachedadmin extends VersionCrawler
{
    /**
     * WARNING 
     * The project name is phpmemcache[]admin
     * while  file name is phpmemcached[d]admin.
     * 
     * In WPN-XM the name is "phpmemcachedadmin" with D.
     * 
     * @var string
     */
    public $url = 'http://code.google.com/p/phpmemcacheadmin/downloads/list';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node, $i) {
            // phpMemcachedAdmin-1.2.2-r262.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(r)?(\d+))?#", $node->attr('href'), $matches)) {
                $version_long = $matches[0]; // 1.2.3-r123
                $version = $matches[1]; // 1.2.3
                if (version_compare($version, $this->registry['phpmemcachedadmin']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://phpmemcacheadmin.googlecode.com/files/phpMemcachedAdmin-'.$version_long.'.zip'
                    );
                }
            }
        });
    }
}
?>