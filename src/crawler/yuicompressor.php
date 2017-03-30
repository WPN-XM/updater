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
 * yuicompressor - Version Crawler
 */
class yuicompressor extends VersionCrawler
{
    public $name = 'yuicompressor';
    
    // we are scraping the github releases page
    public $url = 'https://github.com/yui/yuicompressor/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#yuicompressor-(\d+\.\d+.\d+).jar#", $node->text(), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            // https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar
                            'url'     => 'https://github.com/yui/yuicompressor/releases/download/v' . $version . '/yuicompressor-' . $version . '.jar',
                        );
                    }
                }
            });
    }
}
