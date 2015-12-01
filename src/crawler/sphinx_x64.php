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
 * Sphinx Search Engine - Version Crawler
 *
 * Website: http://sphinxsearch.com/
 */
class sphinx_x64 extends VersionCrawler
{
    public $name = 'sphinx-x64';

    public $url = 'http://sphinxsearch.com/downloads/release/';

    /**
     * There are multiple builds available, but we crawl the following one:
     * "Win32 binaries w/MySQL+PgSQL+libstemmer+id64 support"
     *
     * Direct Download URL: http://sphinxsearch.com/files/sphinx-2.2.10-release-win32-full.zip
     *
     * We grab the version number from the URL leading to the "Thank You" exit page.
     * http://sphinxsearch.com/downloads/sphinx-2.2.10-release-win32-full.zip/thankyou.html
     */
    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#sphinx-(\d+\.\d+\.\d+[A-Za-z]*)-release-win64-full.zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($matches[1], $this->registry['sphinx-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://sphinxsearch.com/files/sphinx-' . $version . '-release-win64-full.zip',
                    );
                }
            }
        });
    }
}
