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
    public $url = 'https://www.arangodb.com/repositories/VERSIONS';

    public function crawlVersion()
    {       
        // lets explode the text (containing the version on each new line)
        $versions = explode(chr(10), $this->text());
        
        // drop empty array values
        $versions = array_filter($versions, 'strlen');
        
        // reverse array to get latest version on top
        $versions  = array_reverse($versions);
        
        // get latest version
        $version = $versions[0];
        
        if (version_compare($version, $this->registry['arangodb-x64']['latest']['version'], '>=') === true) {       
            return array(
                'version' => $version,
                'url'     => 'https://www.arangodb.com/repositories/Windows7/x86_64/ArangoDB-' . $version . '-win64.zip',
            );
        }
    }
}
