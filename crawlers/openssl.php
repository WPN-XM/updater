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
class openssl extends VersionCrawler
{
    public $url = 'http://slproweb.com/products/Win32OpenSSL.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node) {
            // http://slproweb.com/download/Win32OpenSSL_Light-1_0_1d.exe
            if (preg_match("#Win32OpenSSL_Light-(\d+\_\d+\_\d+[a-z]).exe$#", $node->attr('href'), $matches)) {
                // the version match contains underscores: so turn "1_0_1d" into "1.0.1d", that's still not SemVer but anyway
                $version = str_replace('_', '.', $matches[1]);
                if (version_compare($version, $this->registry['openssl']['latest']['version'], '>')
                || (strcmp($this->registry['openssl']['latest']['version'], $version) < 0) ) {
                    return array(
                        'version' => $version,
                        'url' => 'http://slproweb.com/download/Win32OpenSSL_Light-'.$matches[1].'.exe'
                    );
                }
            }
        });
    }
}
