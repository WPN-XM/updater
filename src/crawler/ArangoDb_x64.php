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
    
    //
    // https://www.arangodb.com/download/
    // https://www.arangodb.com/repositories/VERSIONS
    // https://www.arangodb.com/download-major/
    // https://www.arangodb.com/repositories/archive/arangodb31/Windows7/x86_64/
    // https://www.arangodb.com/repositories/Windows7/x86_64/
    // -> https://download.arangodb.com/Windows7/x86_64/
    // 
    public $url = 'https://download.arangodb.com/Windows7/x86_64/';

    public function crawlVersion()
    {       
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#ArangoDB-(\d+\.\d+.\d+)-win64.zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {       
                    return array(
                        'version' => $version,
                        /**
                         * Stupidity increases... proof:
                         * https://www.arangodb.com/repositories/Windows7/x86_64/ArangoDB-3.0.9-win64.zip
                         * https://www.arangodb.com/repositories/Windows7/x86_64/ArangoDB3-3.1.9-1_win64.zip 
                         * 
                         * Oh wow, they changed it :D Thanks.. Now change your fucked up folder structure, too..
                         * Why "Windows7"? Yeah, 7up. Why not simply Windows?
                         */
                        'url'     => 'https://download.arangodb.com/Windows7/x86_64/ArangoDB-' . $version . '-win64.zip',
                    );
                }
            }
        });
    }
}
