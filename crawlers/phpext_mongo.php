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
 * phpext_mongo (PHP Extension for MongoDB) - Version Crawler
 */
class phpext_mongo extends VersionCrawler
{
    /**
     * WARNING
     * The windows builds got no version listing, because Github stopped their downloads service.
     * Old Listing URL: https://github.com/mongodb/mongo-php-driver/downloads
     *
     * Downloads are now on AS3.
     * We scrape the PECL site for version numbers (mongo-1.3.4.tgz)
     * and expect a matching windows build on AS3  (mongo-1.3.4.zip).
     */

    public $url = 'http://pecl.php.net/package/mongo';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node, $i) {
            // mongo-1.3.4.tgz
            if (preg_match("#mongo-(\d+\.\d+(\.\d+)*)(?:[._-]?(rc)?(\d+))?#i", $node->attr('href'), $matches)) {
                $version = $matches[1]; // 1.2.3
                if (version_compare($version, $this->registry['phpext_mongo']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://s3.amazonaws.com/drivers.mongodb.org/php/php_mongo-'.$version.'.zip'
                    );
                }
            }
        });
    }
}
