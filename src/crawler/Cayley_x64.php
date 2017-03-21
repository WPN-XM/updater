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
 * Google Cayley - Version Crawler
 *
 * Cayley is an open-source graph inspired by the graph database behind Freebase and Google's Knowledge Graph.
 * Its goal is to be a part of the developer's toolbox where Linked Data 
 * and graph-shaped data (semantic webs, social networks, etc) in general are concerned.
 *
 * We use the DataStax Community Edition of Apache Cassandra.
 *
 * Website:       https://github.com/google/cayley/
 * Downloads:     https://github.com/google/cayley/releases
 */
class Cayley_x64 extends VersionCrawler
{
    public $name = 'cayley-x64';

    public $url = 'https://github.com/google/cayley/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            // https://github.com/google/cayley/releases/download/v0.4.1/cayley_v0.4.1_windows_amd64.zip

            if (preg_match("#/releases/download/v(\d+\.\d+.\d+)/#", $node->attr('href'), $matches))
            {
                $version = $matches[1];

                $download_file = 'https://github.com/google/cayley/releases/download/v'.$version.'/cayley_v'.$version.'_windows_amd64.zip';

                if (version_compare($version, $this->registry['cayley-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $download_file,
                    );
                }
            }
        });
    }
}
