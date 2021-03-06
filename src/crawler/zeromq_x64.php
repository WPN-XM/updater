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
 * ZeroMQ - Version Crawler
 * 
 * Website:   http://zeromq.org/
 * Downloads: http://zeromq.org/distro:microsoft-windows
 */
class zeromq_x64 extends VersionCrawler
{
    public $name = 'zeromq-x64';

    public $url = 'http://zeromq.org/distro:microsoft-windows';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#ZeroMQ-(\d+\.\d+.\d+)~miru1.0-x64.exe#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://miru.hk/archive/ZeroMQ-' . $version . '~miru1.0-x64.exe',
                    );
                }
            }
        });
    }
}
