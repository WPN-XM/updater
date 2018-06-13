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
 * phpMyAdmin - Version Crawler
 */
class phpmyadmin extends VersionCrawler
{
    public $name = 'phpmyadmin';
    public $url = 'https://www.phpmyadmin.net/home_page/phpmyadmin.xml';

    public function crawlVersion()
    {
        $version = $this->filterXPath('//Program_Info/Program_Version')->text();

        if(version_compare($version, $this->latestVersion, '>=') === true) {
            return [
                'version' => $version,
                'url'     => 'https://files.phpmyadmin.net/phpMyAdmin/' . $version . '/phpMyAdmin-' . $version . '-english.zip',
            ];
        }
    }
}
