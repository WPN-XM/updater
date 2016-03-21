<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * WinCacheGrind - Version Crawler
 *
 * WinCacheGrind is a viewer for cachegrind.out files generated by xdebug 2.
 * Its is functionally similar to KCacheGrind, only it is much simpler and runs on Windows.
 *
 * Website: http://ceefour.github.io/wincachegrind
 * Github:  https://github.com/ceefour/wincachegrind
 */
class wincachegrind extends VersionCrawler
{
    public $name = 'wincachegrind';

    // we are scraping the github releases page
    public $url = 'https://github.com/ceefour/wincachegrind/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                /**
                 * Releases are not consistent. Switching between ".exe" and ".zip" releases.
                 *
                 * https://github.com/ceefour/wincachegrind/releases/download/1.1/wincachegrind-1.1.zip
                 */
                if (preg_match("#wincachegrind-(\d+.\d+(.\d+)?)\.zip#", $node->attr('href'), $matches)) {
                    $version = $matches[1];

                    $download_file = 'https://github.com/ceefour/wincachegrind/releases/download/-' . $version . '/wincachegrind-' . $version . '.zip';

                    if (version_compare($version, $this->registry['wincachegrind']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}