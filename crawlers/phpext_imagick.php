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
 * PHP Extension imagick - Version Crawler
 */
class phpext_imagick extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/imagick/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // http://windows.php.net/downloads/pecl/releases/imagick/3.2.0b2/php_imagick-3.2.0b2-5.3-ts-vc9-x86.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(b|rc)?(\d+))#", $node->text(), $matches)) {
                $version = $matches[0];

                $url = 'http://windows.php.net/downloads/pecl/releases/imagick/'.$version.'/php_imagick-'.$version.'-5.4-nts-vc9-x86.zip';

                if (version_compare($version, $this->registry['phpext_imagick']['latest']['version'], '>=') and $this->fileExistsOnServer($url)) {
                    return array(
                        'version' => $version,
                        'url' => $url,
                    );
                }
            }
        });
    }
}