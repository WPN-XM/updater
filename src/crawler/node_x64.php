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
 * Node x64 - Version Crawler
 */
class node_x64 extends VersionCrawler
{
    public $name = 'node-x64';

    public $url = 'http://nodejs.org/dist/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#v(\d+\.\d+(\.\d+)*)/$#i", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'http://nodejs.org/dist/v' . $version . '/win-x64/node.exe',
                    );
                }
            }
        });
    }
}
