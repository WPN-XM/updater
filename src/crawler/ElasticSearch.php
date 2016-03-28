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
 * ElasticSearch - Version Crawler
 *
 * Elasticsearch is a distributed, open source search and analytics engine,
 * designed for horizontal scalability, reliability, and easy management.
 *
 * Website:   https://www.elastic.co/
 * Downloads: https://www.elastic.co/downloads/elasticsearch
 */
class elasticsearch extends VersionCrawler
{
    public $name = 'elasticsearch';

    public $url = 'https://www.elastic.co/downloads/elasticsearch';

    /**
     * Well, the download URL could be
     * https://download.elasticsearch.org/2.2.1/elasticsearch-2.2.1.zip
     *
     * But wait, that would be too easy. To build a proper URL in the JAVA world:
     * repeat the product name 5 times and add at least one "org", "release" and "distribution" thingy.
     * Much improved URL. So amazed. Wow.
     *
     * https://download.elasticsearch.org/elasticsearch/release/org/elasticsearch/distribution/zip/elasticsearch/2.2.1/elasticsearch-2.2.1.zip
     */
    private $url_template = 'https://download.elasticsearch.org/elasticsearch/release/org/elasticsearch/distribution/zip/elasticsearch/%version%/elasticsearch-%version%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#elasticsearch-(\d+\.\d+.\d+).zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['elasticsearch']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => str_replace('%version%', $version, $this->url_template),
                    );
                }
            }
        });
    }
}
