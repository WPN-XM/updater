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
 * NGINX - Version Crawler
 */
class Nginx extends VersionCrawler
{
    // Download folder listing http://nginx.org/download/
    public $url = 'http://nginx.org/en/download.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['nginx']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $matches[1],
                        'url'     => 'http://nginx.org/download/' . $node->text(),
                    );
                }
            }
        });
    }
}
