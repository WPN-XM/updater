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
 * Node x64 - Version Crawler
 *
 * This scans for highest version (edge), not latest (release version).
 *
 * Latest Versions:
 * 32 bit http://nodejs.org/dist/latest/node.exe
 * 64 bit http://nodejs.org/dist/latest/x64/node.exe
 */
class node_x64 extends VersionCrawler
{
    public $name = 'node-x64';

    public $url = 'http://nodejs.org/dist/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // http://nodejs.org/dist/v0.11.9/
            if (preg_match("#v(\d+\.\d+(\.\d+)*)/$#i", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['node-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $matches[1],
                        // http://nodejs.org/dist/v0.11.9/x64/node.exe
                        'url' => 'http://nodejs.org/dist/v' . $matches[1] . '/x64/node.exe',
                    );
                }
            }
        });
    }
}
