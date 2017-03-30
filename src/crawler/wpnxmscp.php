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
 * WPN-XM Server Control Panel x86 - Version Crawler
 *
 * The Server Control Panel is a tray app for daemon control written in Qt.
 *
 * https://github.com/WPN-XM/server-control-panel
 */
class wpnxmscp extends VersionCrawler
{
    public $name = 'wpnxm-scp';

    // we are scraping the github releases page
    public $url = 'https://github.com/WPN-XM/server-control-panel/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                // https://github.com/WPN-XM/server-control-panel/releases/download/0.8.1/wpnxm-scp-x86_boxed.zip
                // since 0.8.5 with version in filename:
                // https://github.com/WPN-XM/server-control-panel/releases/download/0.8.5/wpnxm-scp-0.8.5-x86_boxed.zip
                // since 0.8.6 with "v" on tag-name:
                // https://github.com/WPN-XM/server-control-panel/releases/download/v0.8.6/wpnxm-scp-0.8.6-x86_boxed.zip
                if (preg_match("#(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'https://github.com/WPN-XM/server-control-panel/releases/download/v' . $version . '/wpnxm-scp-' . $version . '-x86_boxed.zip',
                        );
                    }
                }
            });
    }
}
