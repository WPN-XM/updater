<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * NGINX - Version Crawler
 */
class Nginx extends VersionCrawler
{
    // Download folder listing http://nginx.org/download/
    public $url = 'http://nginx.org/en/download.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#nginx-(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['nginx']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://nginx.org/download/nginx-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
