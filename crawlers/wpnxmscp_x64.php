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
 * WPN-XM Server Control Panel x64 - Version Crawler
 *
 * The Server Control Panel is a tray app for daemon control written in Qt.
 *
 * https://github.com/WPN-XM/server-control-panel
 */
class wpnxmscp_x64 extends VersionCrawler
{
    public $name = 'wpnxmscp-x64';

    // we are scraping the github releases page
    public $url = 'https://github.com/WPN-XM/server-control-panel/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    if (version_compare($version, $this->registry['wpnxmscp-x64']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'https://github.com/WPN-XM/server-control-panel/releases/download/' . $version . '/wpnxm-scp-x86_64.zip',
                        );
                    }
                }
            });
    }
}
