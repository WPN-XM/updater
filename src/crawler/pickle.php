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
 * Pickle - Version Crawler
 *
 * https://github.com/FriendsOfPHP/pickle
 */
class pickle extends VersionCrawler
{
    public $name = 'pickle';

    // we are scraping the github releases page
    public $url = 'https://github.com/FriendsOfPHP/pickle/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'https://github.com/FriendsOfPHP/pickle/releases/download/v' . $version . '/pickle.phar',
                        );
                    }
                }
            });
    }
}


