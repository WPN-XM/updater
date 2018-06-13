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
 * Sphinx Search Engine - Version Crawler
 *
 * Website: http://sphinxsearch.com/
 */
class sphinx extends VersionCrawler
{
    public $name = 'sphinx';

    public $url = 'http://sphinxsearch.com/downloads/release/';

    /**
     * There are multiple builds available, but we crawl the following one:
     * "Win64 binaries w/MySQL+PgSQL+libstemmer+id64 support"
     *
     * Direct Download URL: http://sphinxsearch.com/files/sphinx-2.2.10-release-win64-full.zip
     *
     * We grab the version number from the URL leading to the "Thank You" exit page.
     * http://sphinxsearch.com/downloads/sphinx-2.2.10-release-win32-full.zip/thankyou.html
     */
    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#sphinx-(\d+\.\d+\.\d+[A-Za-z]*)-release-win32-full.zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($matches[1], $this->registry['sphinx']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://sphinxsearch.com/files/sphinx-' . $version . '-release-win32-full.zip',
                    );
                }
            }
        });
    }
}
