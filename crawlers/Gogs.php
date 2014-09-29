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
 * Gogs - Version Crawler
 *
 * Gogs(Go Git Service) is a painless self-hosted Git Service written in Go.
 *
 * http://gogs.io - https://github.com/gogits/gogs
 */
class Gogs extends VersionCrawler
{
    // we are scraping the github releases page
    // alternative: http://gogs.io/docs/installation/install_from_binary.html
    public $url = 'https://github.com/gogits/gogs/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                if (preg_match("#releases/download/v(\d+\.\d+.\d+)/windows_amd64.zip#", $node->text(), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->registry['gogs']['latest']['version'], '>=')) {
                        return array(
                            'version' => $version,
                            // CDNs
                            // - http://gogs.dn.qbox.me/gogs_v0.5.2_windows_amd64.zip
                            // - https://github.com/gogits/gogs/releases/download/v0.5.2/windows_amd64.zip
                            'url'     => 'https://github.com/gogits/gogs/releases/download/v' . $version . '/windows_amd64.zip'
                        );
                    }
                }
            });
    }

}
