<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * Oracle VirtualBox - Version Crawler
 *
 * Returns VirtualBox version and download URL.
 *
 * Version Request URL: 
 * - https://download.virtualbox.org/virtualbox/LATEST.TXT
 * Download URLs:
 * - https://download.virtualbox.org/virtualbox/
 * - https://download.virtualbox.org/virtualbox/5.2.22/VirtualBox-5.2.22-126460-Win.exe
 */
class virtualbox_x64 extends VersionCrawler
{
    public $name = 'virtualbox-x64';
    
    public $url = 'https://download.virtualbox.org/virtualbox/LATEST.TXT';

    private $dl_folder_template = 'https://download.virtualbox.org/virtualbox/%version%';
    private $dl_url_template = 'https://download.virtualbox.org/virtualbox/%version%/VirtualBox-%version%-%buildversion%-Win.exe';    

    public function crawlVersion()
    {
        // Step 1) Scrape Version
        $version_raw = file_get_contents($this->url);

        // url returns TEXT with added space, let's remove that
        $version = trim($version_raw);
       
        if (version_compare($version, $this->latestVersion, '>=') === true) {

            // Step 2) Scrape Folder Content
            $dl_folder_url = str_replace('%version%', $version, $this->dl_folder_template);
            
            $this->newHtmlScrapeRequest($dl_folder_url);

            return $this->filter('a')->each(function ($node)
            {
                // https://download.virtualbox.org/virtualbox/5.2.2/VirtualBox-5.2.2-119230-Win.exe
                if (preg_match("#VirtualBox-((\d+\.\d+.\d+)-(\d+))-Win.exe#", $node->attr('href'), $matches))
                {
                    $version = $matches[2];
                    $buildversion = $matches[3];
                    $dl_url = str_replace(['%version%', '%buildversion%'], [$version, $buildversion], $this->dl_url_template);

                    return array(
                        'version' => $version,
                        'url' => $dl_url,
                    ); 
                }                          
            });            
        }
    }
}
