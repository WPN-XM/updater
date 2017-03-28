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
 * NetbeansIDE - Version Crawler
 *
 * Netbeans is a Java-based IDE.
 *
 * Website: https://netbeans.org/features/index.html
 */
class Netbeans_x64 extends VersionCrawler
{
    public $name = 'netbeans-x64';

    /**
     * Welcome to another episode of worst-case-scenarios for version scraping. 
     * This time featuring the Netbeans Download page:
     *
     * 1. Because the version information is dynamically loaded and inserted into the page at rendering time,
     *    we can't simply scrape the verson text.
     * 2. Alternative sources?
     *    - There are other sources, including an RSS feed, but the announcement format for a new version isn't really standardized.
     *    - There are no real archive or download listings available.
     * 3. Maybe a two step scraping approach is needed at a later stage:
     *    - firstly, we scrape the main page, to extract the path location of the build_info
     *    - secondly, we construct the path to build_info.js and scrape it, to extract the version from the path of the download  
     * 4. Thanks for wasting my lifetime with this shit. 
     *    It could have been so easy: http://download.netbeans.org/latest_version.json
     */
    public $url = 'https://netbeans.org/downloads/index.html';

    public function crawlVersion()
    {
        return $this->filter('head > script')->each(function ($node) {      
            // scrape /images_www/v6/download/8.2/final/      
            if (preg_match("#PAGE_ARTIFACTS_LOCATION = \"(.*)\";#", $node->text(), $matches)) {
                $page_artifact_location = $matches[1];
                $version = explode('/', $page_artifact_location)[4];            

                if (version_compare($version, $this->registry['netbeans-x64']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,  
                        // http://download.netbeans.org/netbeans/8.2/final/bundles/netbeans-8.2-php-windows-x64.exe                         
                        'url'     => 'http://download.netbeans.org/netbeans/' . $version . '/final/bundles/netbeans-' . $version . '-php-windows-x64.exe',
                    );
                }
            }
        });
    }
}
