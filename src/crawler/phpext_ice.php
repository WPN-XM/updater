<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * PHP Extensions "Ice" - Version Crawler
 *
 * Website:   http://www.iceframework.org/
 * Downloads: http://www.iceframework.org/dll/
 */
class phpext_ice extends VersionCrawler
{
    public $url = 'http://www.iceframework.org/dll/';

    private $url_template = 'http://www.iceframework.org/dll/ice-%version%-php-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // ice-1.1.2-php-5.6-nts-vc11-x86.zip
            if (preg_match("/ice-(\d+\.\d+\.\d+)-php-(\d+\.\d+\.\d+)-nts-vc11-x86.zip$/i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->registry['phpext_ice']['latest']['version'], '>=') === true)  {

                    $urls = $this->createPhpVersionsArrayForExtension($version, $this->url_template);
                    if(empty($urls)) {
                        return;
                    }

                    return array(
                        'version' => $version,
                        'url'     => $urls,
                    );
                }
            }
        });
    }
}
