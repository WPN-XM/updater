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

    public function readGoPHP7ExtensionCatalog()
    {
        $url = 'https://raw.githubusercontent.com/wiki/gophp7/gophp7-ext/extensions-catalog.md';
        $text = file_get_contents($url);

        // reduce to text segment: "# Pecl Extensions from other places"
        $reduced_text = strstr($text, '| aerospike');
        $lines = explode("\n", $reduced_text);

        // build array by named pattern matching
        $regexp = '/\|(?<name>.*)\|(?<website>.*)\|(?<maintainers>.*)\|(?<tests>.*)\|(?<docs>.*)'
                . '\|(?<worksonphp5>.*)\|(?<worksonphp7>.*)\|(?<goodonphp7>.*)\|(?<details>.*)\|/';

        $result = [];

        foreach($lines as $line)
        {
            preg_match($regexp, $line, $matches);
            
            // remove integer keys and superfluous spaces
            $matches = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
            $matches = array_map('trim', $matches);

            // use PHP Extension name as array key
            $name = $matches['name'];
            unset($matches['name']);

            $result[$name] = $matches;
        }

        $json = json_encode($result, JSON_PRETTY_PRINT);
        file_put_contents(DATA_DIR . 'registry/php-extensions-outside-pecl.json', $json);
    }
}
