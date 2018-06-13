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
 * OpenSSL - Version Crawler
 */
class openssl_x64 extends VersionCrawler
{
    public $name = 'openssl-x64';

    public $url = 'http://indy.fulgan.com/SSL/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /*
             * The regexp must take the following cases into account:
             *
             * http://indy.fulgan.com/SSL/openssl-0.9.8ze-x64_86-win64.zip - two chars lowercase
             * http://indy.fulgan.com/SSL/openssl-1.0.0l-x64_86-win64.zip  - one char lowercase
             * http://indy.fulgan.com/SSL/openssl-1.0.2-x64_86-win64.zip   - version only
             */
            if (preg_match("/openssl-(\d+\.\d+\.\d+[A-Za-z]*)-x64_86-win64.zip$/i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (strcmp($this->latestVersion, $version) < 0) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://indy.fulgan.com/SSL/openssl-' . $version . '-x64_86-win64.zip',
                    );
                }
            }
        });
    }
}
