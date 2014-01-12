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
 * ImageMagick - Version Crawler
 */
class Imagick extends VersionCrawler
{
    public $url = 'http://www.imagemagick.org/script/binary-releases.php';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node, $i) {
            if($node->text() === 'download') {
                // http://www.imagemagick.org/download/binaries/ImageMagick-6.8.8-1-Q16-x86-windows.zip
                // Version is "6.8.8-1", where "-1" might indicate an pre-release version, but i think its not semver.
                // They also adhere to a standard, where archived versions are postfixed with "-10", e.g. "6.8.8-10".
                if (preg_match("#(\d+\.\d+(\.\d+)*-\d+)-Q16-x86-windows.zip$#", $node->attr('href'), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->registry['imagick']['latest']['version'], '>=')) {
                        return array(
                            'version' => $version,
                            'url' => 'http://www.imagemagick.org/download/binaries/ImageMagick-'.$version.'-1-Q16-x86-windows.zip',
                        );
                    }
                }
            }
        });
    }
}