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
 * ZealDocs - Version Crawler
 * 
 * Website:     http://zealdocs.org/
 * Downloads:   https://zealdocs.org/download.html
 * Bintray:     https://bintray.com/zealdocs/windows/zeal
 *              https://dl.bintray.com/zealdocs/windows/
 */
class zealdocs extends VersionCrawler
{
    public $name = 'zealdocs';

    // scrape the bintray downloads page
    public $url = 'https://dl.bintray.com/zealdocs/windows/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#zeal-portable-(\d+\.\d+.\d+)-windows-x86.7z#", $node->text(), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            // https://dl.bintray.com/zealdocs/windows/zeal-portable-0.3.1-windows-x86.7z
                            'url'     => 'https://dl.bintray.com/zealdocs/windows/zeal-portable-' . $version . '-windows-x86.7z',
                        );
                    }
                }
            });
    }
}
