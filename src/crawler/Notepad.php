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
 * Nodepad Plus Plus - Version Crawler
 *
 * Website:   https://notepad-plus-plus.org/
 * Downloads: https://notepad-plus-plus.org/download/v7.3.3.html
 */
class Notepad extends VersionCrawler
{
    public $name = 'notepad';
    public $url = 'http://nodejs.org/dist/npm/';

    public function crawlVersion()
    {
        $latestVersion = $this->getLatestVersion();
        
        return $this->filter('a')->each(function ($node) {
            // http://nodejs.org/dist/npm/npm-1.4.6.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->text(), $matches)) {
                if (version_compare($matches[1], $latestVersion, '>=') === true) {
                    return array(
                        'version' => $matches[1],
                        'url'     => 'http://nodejs.org/dist/npm/npm-' . $matches[1] . '.zip',
                    );
                }
            }
        });
    }    
}
