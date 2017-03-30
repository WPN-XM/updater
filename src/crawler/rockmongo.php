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
 * RockMongo (MongoDB Administration Webinterface) - Version Crawler
 */
class rockmongo extends VersionCrawler
{
    public $name = 'rockmongo';
    
    // fomerly: http://rockmongo.com/downloads
    public $url = 'https://github.com/iwind/rockmongo/releases/latest';

    public function crawlVersion()
    {
        // span tag contains "RockMongo v1.1.5"
        // $text = $this->filterXPath('//ul/li/a/span')->text();
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // formerly http://rockmongo.com/release/rockmongo-1.1.3.zip
                        // now      https://github.com/iwind/rockmongo/archive/1.1.7.zip
                        'url' => 'https://github.com/iwind/rockmongo/archive/' . $version . '.zip',
                    );
                }
            }
        });
    }
}
