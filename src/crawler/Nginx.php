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

use WPNXM\Updater\VersionCrawler;

/**
 * NGINX - Version Crawler
 */
class Nginx extends VersionCrawler
{
    public $url = 'http://nginx.org/download/';

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
