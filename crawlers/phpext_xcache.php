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
 * Xcache - Version Crawler
 */
class phpext_xcache extends VersionCrawler
{
    public $url = 'http://xcache.lighttpd.net/pub/Releases/';

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node, $i) {

            // there are "rc" versions, but we don't take them into account
            if (preg_match("#(\d+\.\d+(\.\d+)*)#i", $node->text(), $matches))
            {
                $version = $matches[0];

                // skip versions lower than 3
                if(version_compare($version, '3', '<=')) {
                    return;
                }

                if (version_compare($version, $this->registry['phpext_xcache']['latest']['version'], '>='))
                {
                    // build URL for second version specific request - taking the php version into account
                    // e.g. http://xcache.lighttpd.net/pub/Releases/3.0.0/
                    //$uri = $this->url . $version . '/';

                    // do secondary request to the version folder
                    //$response = $this->guzzle->get($uri)->send();

                    // add response content as new scraping content to the crawler
                    //echo $response->getBody();
                    //$this->addHTMLContent($response->getBody());

                    //$nodes = $this->filter('a');
                            /*->reduce( function ($node, $i) {
                        if(!\strstr($node->text, 'php-5.4')) {
                            return false;
                        }
                    });*/
                    //var_dump($nodes);

                    // http://xcache.lighttpd.net/pub/Releases/3.1.0-rc1/XCache-3.1.0-rc1-php-5.4.20-Win32-VC9-x86.zip
                    return array(
                        'version' => $version,
                        'url' => 'http://xcache.lighttpd.net/pub/Releases/'.$version.'/XCache-'.$version.'-php-5.4.20-Win32-VC9-x86.zip'
                    );
                }
            }
        });
    }
}
