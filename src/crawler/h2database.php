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
 * H2Database - Version Crawler
 * 
 * Website:         http://www.h2database.com/
 * Downloads:       http://www.h2database.com/html/download.html
 */
class H2Database extends VersionCrawler
{
    public $name = 'h2database';
    public $url = 'http://www.h2database.com/html/download.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // http://www.h2database.com/h2-2017-03-10.zip
            if (preg_match("#h2-(\d+\-\d+\-\d+).zip#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['h2database']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://www.h2database.com/h2-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
