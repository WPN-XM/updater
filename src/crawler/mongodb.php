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
 * MongoDb - Version Crawler
 */
class mongodb extends VersionCrawler
{
    // formerly http://www.mongodb.org/downloads
    // warning: do not use http://dl.mongodb.org/dl/win32/ - use instead: https://www.mongodb.org/dl/win32/
    public $url = 'https://www.mongodb.org/dl/win32/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // no RC versions!
            if (preg_match("#win32-i386-(\d+\.\d+(\.\d+)*).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['mongodb']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://downloads.mongodb.org/win32/mongodb-win32-i386-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
