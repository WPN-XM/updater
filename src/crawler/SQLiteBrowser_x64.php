<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * SQLiteBrowser - Version Crawler
 *
 * Website:       http://sqlitebrowser.org/
 * Github:        https://github.com/sqlitebrowser/sqlitebrowser/releases
 */
class SQLiteBrowser_x64 extends VersionCrawler
{
    public $name = 'sqlitebrowser-x64';

    public $url = 'https://github.com/sqlitebrowser/sqlitebrowser/releases/latest';

    public function crawlVersion()
    {
        // Download URL: 
        // https://github.com/sqlitebrowser/sqlitebrowser/releases/download/v3.10.1/DB.Browser.for.SQLite-3.10.1-win64.exe

        // filter all a href's with "end of string" match ($)
        return $this->filter('a[href$=".exe"]')->each(function ($node) {

            if (preg_match("#SQLite-(\d+\.\d+(\.\d+)*)-win64#i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://github.com/sqlitebrowser/sqlitebrowser/releases/download/v' . $version . '/DB.Browser.for.SQLite-' . $version . '-win64.exe',
                    );
                }
            }
        });
    }
}
