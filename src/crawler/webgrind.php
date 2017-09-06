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
 * Webgrind - the Xdebug Profiling Web Frontend in PHP
 *
 * Website:     https://github.com/jokkedk/webgrind
 * Downloads:   https://github.com/jokkedk/webgrind/releases
 */
class Webgrind extends VersionCrawler
{
    public $name = 'webgrind';

    public $url = 'https://github.com/jokkedk/webgrind/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#v(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'https://github.com/jokkedk/webgrind/archive/v' . $version . '.zip',
                        );
                    }
                }
            });
    }
}
