<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

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

                    if (version_compare($version, $this->registry['pickle']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'https://github.com/FriendsOfPHP/pickle/releases/download/v' . $version . '/pickle.phar',
                        );
                    }
                }
            });
    }
}


