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
 * MongoDb (x86_64) - Version Crawler
 */
class mongodb-x64 extends VersionCrawler
{
    // formerly http://www.mongodb.org/downloads
    public $url = 'http://dl.mongodb.org/dl/win32/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // no RC versions!
            if (preg_match("#win32-x86_64-(\d+\.\d+(\.\d+)*).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['mongodb-x64']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        # http://downloads.mongodb.org/win32/mongodb-win32-x86_64-2.2.0.zip
                        'url' => 'http://downloads.mongodb.org/win32/mongodb-win32-x86_64-' . $version . '.zip'
                    );
                }
            }
        });
    }
}
