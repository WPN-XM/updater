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
 * Node NPM - Version Crawler
 */
class node extends VersionCrawler
{
    public $url = 'http://nodejs.org/dist/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#v(\d+\.\d+(\.\d+)*)/$#i", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['node']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $matches[1],
                        'url' => 'http://nodejs.org/dist/v' . $matches[1] . '/win-x86/node.exe',
                    );
                }
            }
        });
    }
}
