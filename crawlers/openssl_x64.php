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

                if (strcmp($this->registry['openssl-x64']['latest']['version'], $version) < 0) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://indy.fulgan.com/SSL/openssl-' . $version . '-x64_86-win64.zip',
                    );
                }
            }
        });
    }
}
