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
 *
 * Formerly we used http://slproweb.com/ builds.
 * Now using builds by Frederik A. Winkelsdorf (https://opendec.wordpress.com): http://indy.fulgan.com/SSL/
 */
class openssl extends VersionCrawler
{
    public $name = 'openssl'; // do not add "-x86". we need to maintain BC to old webinstallers looking for "openssl".

    public $url = 'http://indy.fulgan.com/SSL/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /*
             * The regexp must take the following cases into account:
             *
             * http://indy.fulgan.com/SSL/openssl-0.9.8ze-i386-win32.zip - two chars lowercase
             * http://indy.fulgan.com/SSL/openssl-1.0.0l-i386-win32.zip  - one char lowercase
             * http://indy.fulgan.com/SSL/openssl-1.0.2-i386-win32.zip   - version only
             */
            if (preg_match("/openssl-(\d+\.\d+\.\d+[A-Za-z]*)-i386-win32.zip$/i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (strcmp($this->latestVersion, $version) < 0) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://indy.fulgan.com/SSL/openssl-' . $version . '-i386-win32.zip',
                    );
                }
            }
        });
    }
}
