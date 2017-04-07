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
 */
class FileZilla_x64 extends VersionCrawler
{
    public $name = 'filezilla-x64';
    public $url = 'https://notepad-plus-plus.org/download/';

    public function crawlVersion()
    {       
        return $this->filter('a')->each(function ($node) {
            // https://notepad-plus-plus.org/repository/7.x/7.3.3/npp.7.3.3.bin.zip
            if (preg_match("#npp.(\d+\.\d+\.\d+).bin.zip$#i", $node->attr('href'), $matches)) {
                $version = $matches[1];                
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    $folder = $version[0].'.x';
                    return array(
                        'version' => $version,                        
                        'url'     => 'https://notepad-plus-plus.org/repository/' . $folder . '/' . $version . '/npp.' . $version . '.bin.zip',
                    );
                }
            }
        });
    }    
}
