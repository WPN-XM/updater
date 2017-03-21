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
 * from the manually maintained PHP extensions list at 
 * http://github.com/gophp7/gophp7-ext/extensions-catalog.md
 *
 * This is done by fetching the markdown document from the wiki
 * and scraping the table data into a JSON list of all available PHP extensions.
 */
class PHPExtensionScraperGoPHP7
{
    public function readExtensionCatalog()
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

            $result[$name] = $matches;
        }

        $json = json_encode($result, JSON_PRETTY_PRINT);
        
        file_put_contents(DATA_DIR . 'registry/php-extensions-outside-pecl.json', $json);
    }
}
