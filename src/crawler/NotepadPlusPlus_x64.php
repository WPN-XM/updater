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
 * Downloads: https://notepad-plus-plus.org/download/
 *
 * Note: 
 * There is a repository/folder listing: https://notepad-plus-plus.org/repository/
 * But it's not "good" scrapable: no latest_version.txt|json and strange folder namings.
 * Which would require a two step crawling to get the latest version.
 *
 * For now, we go with the "/download/" short-url, which redirect to the latest version.
 */
class NotepadPlusPlus_x64 extends VersionCrawler
{
    public $name = 'notepadplusplus-x64';
    public $url = 'https://notepad-plus-plus.org/download/';

    public function crawlVersion()
    {       
        return $this->filter('a')->each(function ($node) {
            // https://notepad-plus-plus.org/repository/7.x/7.3.3/npp.7.3.3.bin.x64.zip
            if (preg_match("#npp.(\d+\.\d+\.\d+).bin.x64.zip$#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    $folder  = $version[0].'.x'; 
                    return array(
                        'version' => $version,                        
                        'url'     => 'https://notepad-plus-plus.org/repository/' . $folder . '/' . $version . '/npp.' . $version . '.bin.x64.zip',
                    );
                }
            }
        });
    }    
}
