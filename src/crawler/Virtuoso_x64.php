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
 * OpenLink Virtuoso Open-Source Edition - Version Crawler
 * 
 * Virtuoso is a modern enterprise grade solution for data access, 
 * integration, and relational database management 
 * (SQL Tables and/or RDF based Property/Predicate Graphs).
 *
 * Website: http://virtuoso.openlinksw.com/
 * Github:  https://github.com/openlink/virtuoso-opensource
 */
class Virtuoso_x64 extends VersionCrawler
{
    public $name = 'virtuoso-x64';

    public $url = 'https://github.com/openlink/virtuoso-opensource/releases/latest';

    /**
     * This scrapes the Github Releases "Latest" Page and grabs the Windows zip.
     * We could probably use node->attr(href) as the returned URL (to avoid the date grab), 
     * but we match and compose the URL manually.
     * 
     * Direct Download URL:
     * https://github.com/openlink/virtuoso-opensource/releases/download/v7.2.1/virtuoso-opensource-win-x64-20150625.zip
     */
    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {          
            if (preg_match("#download/v(\d+\.\d+.\d+)/virtuoso-opensource-win-x64-(\d+).zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                $date = $matches[2];            
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://github.com/openlink/virtuoso-opensource/releases/download/v' . $version . '/virtuoso-opensource-win-x64-' . $date . '.zip',
                    );
                }
            }
        });
    }
}
