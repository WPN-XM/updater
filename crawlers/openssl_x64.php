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

    public $url = 'http://slproweb.com/products/Win32OpenSSL.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node) {

            /**
             * The regexp must take the following cases into account:
             *
             * http://slproweb.com/download/Win64OpenSSL_Light-1_0_1d.exe  - one char lowercase?
             * http://slproweb.com/download/Win64OpenSSL_Light-1_0_1L.exe  - one char uppercase ?
             * http://slproweb.com/download/Win64OpenSSL_Light-1_0_1ze.exe - two chars lowercase`?
             */

            if (preg_match("/Win64OpenSSL_Light-(\d+\_\d+\_\d+[A-Za-z]*).exe$/i", $node->attr('href'), $matches)) {

                // the version match contains underscores. so we turn "1_0_1d" into "1.0.1d".
                // the version match might contain uppercase char. so we turn to lowercase for the comparision.
                // that's still not SemVer, but anyway.
                $version = str_replace('_', '.', $matches[1]);

                $compare_version = strtolower($version);

                if (strcmp($this->registry['openssl']['latest']['version'], $compare_version) < 0) {
                    return array(
                        'version' => $version,
                        'url' => 'http://slproweb.com/download/Win64OpenSSL_Light-'.$matches[1].'.exe'
                    );
                }
            }
        });
    }
}