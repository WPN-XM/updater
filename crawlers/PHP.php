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
 * Php - Version Crawler
 */
class PHP extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/releases/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#php-+(\d+\.\d+(\.\d+)*)-nts-Win32-VC9-x86.zip$#", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['php']['latest']['version'], '>=')) {
                    return array(
                        'version' => $matches[1],
                        'url' => 'http://windows.php.net/downloads/releases/' . $node->text()
                    );
                }
            }
        });
    }

    /**
     * PHP release files are moved from "/releases" to "/releases/archives", with every new version.
     * That means, latest version must point to "/releases".
     * Every other version points to "/releases/archives".
     */
    public function modifyRegistry($registry)
    {
        foreach ($registry['php'] as $version => $url) {
            // do not modify array key "latest"
            if( $version === 'latest') continue;
            // do not modify array key with latest version number - it must point to "/releases".
            if( $version === $registry['php']['latest']['version']) continue;
            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/releases/php', 'php.net/downloads/releases/archives/php', $url);
            // insert at old array position, overwriting the old url
            $registry['php'][$version] = $new_url;
        }

        return $registry;
    }
}
