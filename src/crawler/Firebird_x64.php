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
 * FireBirdSQL - Version Crawler
 *
 * Firebird is a relational database offering many ANSI SQL standard features.
 * Firebird offers excellent concurrency, high performance, and powerful language support
 * for stored procedures and triggers.
 *
 * Website: http://www.firebirdsql.org/
 */
class Firebird_x64 extends VersionCrawler
{
    public $name = 'firebird-x64';

    public $url = 'https://github.com/FirebirdSQL/firebird/releases/latest';

    public $dl_template = 'https://github.com/FirebirdSQL/firebird/releases/download/%release_version%/Firebird-%version%_0_x64.exe';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#download/(\w+)/firebird-(\d+.\d+.\d+.\d+)-0_x64.zip#i", $node->attr('href'), $matches)) {
                $release_version = $matches[1];
                $version = $matches[2];
                $url = str_replace(['%release_version%','%version%'], [$release_version, $version], $this->dl_template);
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // https://github.com/FirebirdSQL/firebird/releases/download/R3_0_4/Firebird-3.0.4.33054_0_x64.exe
                        'url'     => $url,
                    );
                }
            }
        });
    }
}
