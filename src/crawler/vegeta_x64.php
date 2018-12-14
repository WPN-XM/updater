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
 * vegeta - Version Crawler
 *
 * vegetra http load testing tool.
 */
class vegeta_x64 extends VersionCrawler
{
    public $name = 'vegeta-x64';

    // we are scraping the github releases page
    public $url = 'https://github.com/tsenart/vegeta/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) 
        {
            /**
             * https://github.com/tsenart/vegeta/releases/download/cli/v12.1.0/vegeta-12.1.0-windows-amd64.zip
             */
            if (preg_match("#/vegeta-(\d+\.\d+.\d+)-windows-amd64.zip#", $node->attr('href'), $matches)) 
            {
                $version = $matches[1];

                $dl_url = 'https://github.com/tsenart/vegeta/releases/download';
                $dl_url .= '/cli/v' . $version . '/vegeta-' . $version . '-windows-amd64.zip'; 

                if (version_compare($version, $this->latestVersion, '>=') === true) 
                {
                    return array(
                        'version' => $version,
                        'url'     => $dl_url,
                    );
                }
            }
        });
    }
}
