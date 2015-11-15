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
 * Neo4j - Version Crawler
 *
 * Neo4j is a Graph Database.
 *
 * Website: https://neo4j.com/
 */
class neo4j_x64 extends VersionCrawler
{
    public $name = 'neo4j-x64';

    public $url = 'http://neo4j.com/download/other-releases/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#release=(\d+\.\d+.\d+)&architecture=x64#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['neo4j-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://neo4j.com/artifact.php?name=neo4j-community-' . $version . '-windows.zip',
                    );
                }
            }
        });
    }
}
