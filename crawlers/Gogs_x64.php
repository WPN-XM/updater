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
 * Gogs x64 - Version Crawler
 *
 * Gogs(Go Git Service) is a painless self-hosted Git Service written in Go.
 *
 * http://gogs.io - https://github.com/gogits/gogs
 */
class Gogs_x64 extends VersionCrawler
{
    public $name = 'gogs-x64';

    // we are scraping the github releases page
    // alternative: http://gogs.io/docs/installation/install_from_binary.html
    public $url = 'https://github.com/gogits/gogs/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#v(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];

                    // CDNs
                    // https://github.com/gogits/gogs/releases/download/v0.5.9/windows_amd64.zip
                    // http://gobuild3.qiniudn.com/github.com/gogits/gogs/tag-v-v0.5.5/gogs-windows_amd64.zip
                    // 'https://github.com/gogits/gogs/releases/download/v' . $version . '/windows_amd64.zip'

                    $download_file = 'https://github.com/gogits/gogs/releases/download/v' . $version . '/windows_amd64.zip';

                    if (version_compare($version, $this->registry['gogs-x64']['latest']['version'], '>=') === true
                        /*&& $this->fileExistsOnServer($download_file) === true*/) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
