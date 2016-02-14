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
 * aria2 - Version Crawler
 *
 * aria2 is a lightweight multi-protocol & multi-source,
 * cross platform download utility operated in command-line.
 * It supports HTTP/HTTPS, FTP, SFTP, BitTorrent and Metalink.
 *
 * https://aria2.github.io/
 * https://github.com/tatsuhiro-t/aria2
 */
class aria2_x64 extends VersionCrawler
{
    public $name = 'aria2-x64';

    // we are scraping the github releases page
    public $url = 'https://github.com/tatsuhiro-t/aria2/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                /**
                 * I'm living in a world with f**ked up release names. Why is there "-build1" on this URL?
                 *
                 * https://github.com/tatsuhiro-t/aria2/releases/download/release-1.19.2/aria2-1.19.2-win-64bit-build1.zip
                 *
                 * Releases are tagged as "release-1.19.3", instead of "v1.19.3". Nevermind..
                 */
                if (preg_match("#/tatsuhiro-t/aria2/releases/download/release-(\d+\.\d+.\d+)/aria2-#", $node->attr('href'), $matches)) {
                    $version = $matches[1];

                    $download_file = 'https://github.com/tatsuhiro-t/aria2/releases/download/release-' . $version . '/aria2-' . $version . '-win-64bit-build1.zip';

                    if (version_compare($version, $this->registry['aria2-x64']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
