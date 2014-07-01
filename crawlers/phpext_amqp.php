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
 * AMQP (PHP Extension) - Version Crawler
 */
class phpext_amqp extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/amqp/';

    // http://windows.php.net/downloads/pecl/releases/amqp/1.4.0/php_amqp-1.4.0-5.6-ts-vc11-x86.zip
    // http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-%thread%-%compiler%-%bitsize%.zip
    private $url_template = 'http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-5.4-nts-vc9-x86.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];

                $url = str_replace("%version%", $version, $this->url_template);

                if (version_compare($version, $this->registry['phpext_amqp']['latest']['version'], '>=') && $this->fileExistsOnServer($url)) {
                    return array(
                       'version' => $version,
                       'url' => $url,
                    );
                }
            }
        });
    }
}
