<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
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
	public $name = 'phpext_ice';
	
    public $url = 'http://www.iceframework.org/dll/';

    private $url_template = 'http://www.iceframework.org/dll/ice-%version%-php-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // ice-1.1.2-php-5.6-nts-vc11-x64.zip
            // ice-1.1.3-php-7.0-nts-vc14-x64.zip
            if (preg_match("/ice-(\d+\.\d+\.\d+)-php-(\d+\.\d+)-nts-vc\d+-x64\.zip/i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true)  {

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
