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

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
