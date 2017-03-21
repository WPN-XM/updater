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
 * ChromeDriver - Version Crawler
 * 
 * ChromeDriver is a standalone server which implements the
 * WebDriver's wire protocol for Chromium (Google Chrome Webbrowser).
 * 
 * WebDriver is an open source tool for automated testing of webapps
 * across many browsers. It provides capabilities for navigating to 
 * web pages, user input, JavaScript execution, and more.
 * 
 * Website:    https://sites.google.com/a/chromium.org/chromedriver/
 * Downloads:  http://chromedriver.storage.googleapis.com/index.html
 */
class ChromeDriver extends VersionCrawler
{
    public $name = 'chromedriver';
    
    /**
     * Note: 
     * The following URLs are not scrapeable.
     * They return no content or fetch and display the data via dynamic JS (ajax).
     *
     * - https://chromedriver.storage.googleapis.com/LATEST_RELEASE
     * - http://chromedriver.storage.googleapis.com/index.html
     *
     * @var string
     */    
    public $url = 'https://sites.google.com/a/chromium.org/chromedriver/downloads';

    public function crawlVersion()
    {
        return $this->filter('body table a')->each(function ($node) {
            if (preg_match("#TOC-Latest-Release:-ChromeDriver-(\d+\.\d+)#i", $node->attr('name'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['chromedriver']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://chromedriver.storage.googleapis.com/' . $version . '/chromedriver_win32.zip',
                    );
                }
            }
        });
    }

}
