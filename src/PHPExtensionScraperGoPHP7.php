<?php

/*
 * WPИ-XM Server Stack
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater;

use WPNXM\Updater\FileCache;

/**
 * The purpose of this class is to scrape all available PHP extensions
 * from the manually maintained PHP extensions list at 
 * https://github.com/gophp7/gophp7-ext/wiki/extensions-catalog
 *
 * This is done by fetching the markdown document from the wiki
 * and scraping the table data into a JSON list of all available PHP extensions.
 */
class PHPExtensionScraperGoPHP7
{
    public function updateExtensionList()
    {
        $content = $this->getMarkdown();

        $array = $this->scrape($content);
        $array = $this->fixData($array);

        return $this->writeJson($array);
    }

    private function getMarkdown()
    {
        // callback to modify the fetched content, before caching it
        $modificationCallback = function ($content) {
            return self::strip($content);
        };

        $markdown = FileCache::get(
            'https://raw.githubusercontent.com/wiki/gophp7/gophp7-ext/extensions-catalog.md',
            DATA_DIR.'gophp7-ext-catalog.md',
            $modificationCallback
        );

        return $markdown;
    }    

    public static function strip($text)
    {
        // reduce to text segment: "# Pecl Extensions from other places"
        $reduced_text = strstr($text, '| aerospike');

        return $reduced_text;
    }

    /**
     * Scrape the markdown document.
     *
     * @param string $text markdown
     */
    private function scrape($text)
    {
        $lines = explode("\n", $text);

        // build array by named pattern matching
        // "| voltdb |", "| [[wso2|ext-wso2]] |" => name = voltdb, wso2
        $regexp = '#\|(\s+)\[\[(?<name>.*)\|(?:\w+\-\w+)\]\]\s+\||\|(?<name>.*)' 
                . '\|(?<website>.*)\|(?<maintainers>.*)\|(?<tests>.*)\|(?<docs>.*)'
                . '\|(?<worksonphp5>.*)\|(?<worksonphp7>.*)\|(?<goodonphp7>.*)\|(?<details>.*)\|#JU';

        $extensions = [];
        $matches = [];

        foreach($lines as $line)
        {
            preg_match($regexp, $line, $matches);

            // remove integer keys and superfluous spaces
            $matches = array_filter($matches, "is_string", ARRAY_FILTER_USE_KEY);
            $matches = array_map('trim', $matches);

            // use PHP Extension name as array key
            $name = $matches['name'];
            unset($matches['name']);

            $matches = $this->fixData($matches);

            $extensions[$name] = $matches;
        }

        return $extensions;

    }

    private function fixData($array)
    {
        /**
         * skip crap markdown entries
         */
        if(!array_key_exists('website', $array)) {
            return $array;
        }
        if(!is_string($array['website'])) { // probably invalid data entry, e.g. (bool) false
            $array['website'] = '';
            return $array;
        }

        // the key website contains a markdown formated link, e.g. [x](link)
        // let's extract the link 
        $array['website'] = strstr($array['website'], '(');    // remove everything before (
        $array['website'] = substr($array['website'], 1, -1);  // remove first char ( and last char )

        return $array;
    }

    private function writeJson($array)
    {
        $json = json_encode($array, JSON_PRETTY_PRINT);
        $file = DATA_DIR . 'registry/php-extensions-outside-pecl.json';
        
        return (bool) file_put_contents($file, $json);
    }
}
