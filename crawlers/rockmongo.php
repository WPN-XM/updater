<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

/**
 * RockMongo (MongoDB Administration Webinterface) - Version Crawler
 */
class rockmongo extends VersionCrawler
{
    public $url = 'http://rockmongo.com/downloads';

    public function crawlVersion()
    {
        // span tag contains "RockMongo v1.1.5"
        $text = $this->filterXPath('//ul/li/a/span')->text();

        if (preg_match("#(\d+\.\d+(\.\d+)*)#", $text, $matches)) {
            $version = $matches[0];
            if (version_compare($version, $this->registry['rockmongo']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    // formerly http://rockmongo.com/release/rockmongo-1.1.3.zip
                    // now      https://github.com/iwind/rockmongo/archive/1.1.7.zip
                    'url' => 'https://github.com/iwind/rockmongo/archive/'.$version.'.zip'
                );
            }
        }
    }
}
