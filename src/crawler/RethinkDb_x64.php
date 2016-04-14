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
 * Version Crawler for RethinkDB.
 * 
 * RethinkDB is an open-source, scalable JSON database built from the ground up for the realtime web. 
 *
 * Website:    https://www.rethinkdb.com/
 * Docs:       https://www.rethinkdb.com/docs/install/windows/
 * Downloads:  https://download.rethinkdb.com/windows/
 * Github:     https://github.com/rethinkdb/rethinkdb
 */
class RethinkDB_x64 extends VersionCrawler
{
    public $name = 'rethinkdb-x64';

    public $url = 'https://download.rethinkdb.com/windows/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * https://download.rethinkdb.com/windows/rethinkdb-2.3.0.zip
             */
            if (preg_match("#rethinkdb-(\d+\.\d+.\d+).zip#", $node->attr('href'), $matches)) {               
                $version = $matches[1];
                if (version_compare($version, $this->registry['rethinkdb-x64']['latest']['version'], '>=') === true)
                {
                    return array(
                        'version' => $version,
                        'url' => 'https://download.rethinkdb.com/windows/rethinkdb-' . $version . '.zip',
                    );
                }
            }
        });
    }
}
