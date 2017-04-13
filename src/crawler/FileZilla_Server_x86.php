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
 * FileZilla Server - Version Crawler
 *
 * Website:   https://filezilla-project.org/
 * Downloads: https://filezilla-project.org/download.php?show_all=1&type=server
 */
class FileZilla_Server_x86 extends VersionCrawler
{
    public $name = 'filezilla-server-x86';
    public $url = 'https://filezilla-project.org/download.php?show_all=1&type=server';

    public function crawlVersion()
    {       
        return $this->filter('a')->each(function ($node) {
            // http://sourceforge.net/projects/filezilla/files/FileZilla%20Server/0.9.60.2/FileZilla_Server-0_9_60_2.exe/download
             if (preg_match("#FileZilla_Server-(\d+\_\d+\_\d+_\d+).exe$#i", $node->attr('href'), $matches)) {
                $version_underscored = $matches[1]; 
                $version = str_replace('_', '.', $version_underscored);            
                if (version_compare($version, $this->latestVersion, '>=') === true) {                    
                    return array(
                        'version' => $version,                        
                        'url'     => 'http://sourceforge.net/projects/filezilla/files/FileZilla%20Server/'. $version .'/FileZilla_Server-' . $version_underscored . '.exe/download',
                    );
                }
            }
        });
    }    
}
