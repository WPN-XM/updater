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
 * GeckoDriver - Version Crawler
 * 
 * GeckoDriver is a standalone server which implements the
 * WebDriver's wire protocol for Firefox.
 * 
 * WebDriver is an open source tool for automated testing of webapps
 * across many browsers. It provides capabilities for navigating to 
 * web pages, user input, JavaScript execution, and more.
 * 
 * Website:    https://github.com/mozilla/geckodriver
 * Downloads:  https://github.com/mozilla/geckodriver/releases
 */
class GeckoDriver_x64 extends VersionCrawler
{
    public $name = 'geckodriver-x64';
      
    public $url = 'https://github.com/mozilla/geckodriver/releases';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) { 
            // https://github.com/mozilla/geckodriver/releases/download/v0.10.0/geckodriver-v0.10.0-win64.zip           
            if (preg_match("#/mozilla/geckodriver/releases/download/v(\d+\.\d+.\d+)/geckodriver-v(\d+\.\d+.\d+)-win64.zip#", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,                        
                        'url'     => 'https://github.com/mozilla/geckodriver/releases/download/v' . $version . '/geckodriver-v' . $version . '-win64.zip',
                    );
                }
            }
        });
    }

}
