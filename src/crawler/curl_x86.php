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
 * Curl x64 - Version Crawler
 *
 * Website: https://curl.haxx.se/
 * Github:  https://github.com/curl/curl
 * Windows Builds Github: https://github.com/curl/curl-for-win
 */
class curl_x86 extends VersionCrawler
{
    public $name = 'curl-x86';

    public $url = 'https://curl.haxx.se/windows/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            if (preg_match("#curl-((\d+\.\d+.\d+)(_\d+))-win32-mingw.zip#", $node->attr('href'), $matches)) 
            {
                $vb = $matches[1]; // version_plus_buildVersion
                $version = $matches[2];

                $download_file = 'https://curl.haxx.se/windows/dl-'.$vb.'/curl-'.$vb.'-win32-mingw.zip';

                if (version_compare($version, $this->latestVersion, '>=') === true) 
                {
                    return array(
                        'version' => $version,
                        'url'     => $download_file,
                    );
                }
            }            
        });
    }
}
