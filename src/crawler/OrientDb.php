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
 * OrientDb - Version Crawler
 *
 * OrientDb is a multi-model NoSQL / Distributed Graph Database.
 *
 * Website: https://www.orientdb.com/
 */
class OrientDb extends VersionCrawler
{
    public $name = 'orientdb';

    public $url = 'http://orientdb.com/download/';

    /**
     * Direct Download URL
     * http://orientdb.com/download.php?email=unknown@unknown.com&file=orientdb-community-2.1.5.zip&os=win
     */
    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#orientdb-community-(\d+\.\d+.\d+).zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://orientdb.com/download.php?email=unknown@unknown.com&file=orientdb-community-' . $version . '.zip&os=win',
                    );
                }
            }
        });
    }
}
