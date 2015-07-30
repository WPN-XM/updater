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
 * MongoDb (x86_64) - Version Crawler
 */
class mongodb_x64 extends VersionCrawler
{
    public $name = 'mongodb-x64';

    // formerly http://www.mongodb.org/downloads
    // warning: do not use http://dl.mongodb.org/dl/win32/ - use instead: https://www.mongodb.org/dl/win32/
    public $url = 'https://www.mongodb.org/dl/win32/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            /*
             * 1. We don't take RC versions into account
             * 2. RegExp switched from "64-bit legacy" (v0.9.0) to "64-bit 2008 plus" (v0.9.1)
             *    The 64-bit legacy build lacks newer features of Windows that enhance performance.
             *    Use legacy build on Windows Server 2003, 2008, or Windows Vista.
             *    Legacy RegExp: "mongodb-win32-x86_64-"
             */
            if (preg_match("#mongodb-win32-x86_64-2008plus-(\d+\.\d+(\.\d+)*).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['mongodb-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        # http://downloads.mongodb.org/win32/mongodb-win32-x86_64-2008plus-2.2.0.zip
                        'url' => 'http://downloads.mongodb.org/win32/mongodb-win32-x86_64-2008plus-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
