<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

/**
 * Xcache - Version Crawler
 */
class phpext_xcache extends VersionCrawler
{
    public $url = 'http://xcache.lighttpd.net/pub/Releases/';

    private $url_template = 'http://xcache.lighttpd.net/pub/Releases/%version%/XCache-%version%-php-%phpversion%-Win32-%compiler%-%bitsize%.zip'

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

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
                            /*->reduce( function ($node) {
                        if(!\strstr($node->text, 'php-5.4')) {
                            return false;
                        }
                    });*/
                    //var_dump($nodes);

                    // http://xcache.lighttpd.net/pub/Releases/3.1.0-rc1/XCache-3.1.0-rc1-php-5.4.20-Win32-VC9-x86.zip
                    return array(
                        'version' => $version,
                        'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template)
                    );
                }
            }
        });
    }
}
