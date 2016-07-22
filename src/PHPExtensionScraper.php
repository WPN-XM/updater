<?php

/*
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2016, Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

use WPNXM\Updater\FileCache;

/**
 * The purpose of this class is to scrape all available PHP extensions
 * from the offical PECL server
 *
 * This is done by fetching the directory index of
*  http://windows.php.net/downloads/pecl/releases/
 * and converting the HTML into a JSON list of all available PHP extensions.
 *
 * An alternative to scraping folders is to consume the XML:
 * http://pecl.php.net/rest/p/packages.xml
 */
class PHPExtensionScraper
{
    private function getHtml()
    {
        // callback to modify the fetched content, before caching it
        $modificationCallback = function ($content) {
            return self::strip($content);
        };

        $releases = FileCache::get(
            'http://windows.php.net/downloads/pecl/releases/',
            DATA_DIR.'pecl-releases.cache.html',
            $modificationCallback
        );

        $snaps = FileCache::get(
            'http://windows.php.net/downloads/pecl/snaps/',
            DATA_DIR.'pecl-snaps.cache.html',
            $modificationCallback
        );

        $html = $releases.$snaps;

        return $html;
    }

    public static function strip($text)
    {
        // strip first two lines
        $a = explode("\n", $text);
        unset($a[0], $a[1]);
        $text = implode($a);

        // strip several strings
        $text = str_replace(
            ['<br>', '&lt;dir&gt;', '<pre><A HREF="/downloads/pecl/">[To Parent Directory]</A>'],
            ['', '', ''],
            $text
        );

        return $text;
    }

    /**
     * @param string $html
     */
    public function scrapeExtensionsHtml($html)
    {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";

        if (preg_match_all("/$regexp/siU", $html, $matches, PREG_SET_ORDER)) {
            $extensions = [];
            foreach ($matches as $match) {
                // $match[2] = link address
                // $match[3] = link text
                $extensions[] = $match[3];
            }
        }

        $extensions = array_unique($extensions);

        sort($extensions);

        return $extensions;
    }

    public function getJson()
    {
        return json_encode($this->scrapeExtensionsHtml($this->getHtml()));
    }
}
