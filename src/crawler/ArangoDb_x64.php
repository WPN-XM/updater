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
 * ArangoDb - Version Crawler
 * 
 * ArangoDb is a multi-model NoSQL database.
 * 
 * Website:       https://www.arangodb.com/
 * Download Repo: https://www.arangodb.com/repositories/Windows7/
 */
class ArangoDb_x64 extends VersionCrawler
{
    public $name = 'arangodb-x64';
    
    // https://www.arangodb.com/download/
    // https://www.arangodb.com/repositories/VERSIONS
    public $url = 'https://www.arangodb.com/repositories/download-current/download-windows.html';

    public function crawlVersion()
    {       
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#ArangoDB-(\d+\.\d+.\d+)-win64.zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['arangodb-x64']['latest']['version'], '>=') === true) {       
                    return array(
                        'version' => $version,
                        'url'     => 'https://www.arangodb.com/repositories/Windows7/x86_64/ArangoDB-' . $version . '-win64.zip',
                    );
                }
            }
        });
    }
}
