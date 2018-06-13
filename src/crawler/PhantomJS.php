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
 * PhantomJS - Version Crawler
 */
class PhantomJS extends VersionCrawler
{
    public $name = 'phantomjs';
    public $url = 'https://bitbucket.org/ariya/phantomjs/downloads/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#phantomjs-(\d+\.\d+(\.\d+)*)-windows.zip#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' . $version . '-windows.zip',
                    );
                }
            }
        });
    }
}
