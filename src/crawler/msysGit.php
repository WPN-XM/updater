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
 * Git for Windows (PortableGit) - Version Crawler
 */
class msysgit extends VersionCrawler
{
    public $name = 'msysgit';

    public $url = 'https://github.com/git-for-windows/git/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            # https://github.com/git-for-windows/git/releases/tag/v2.5.0.windows.1
            # https://github.com/git-for-windows/git/releases/download/v2.5.0.windows.1/PortableGit-2.5.0-32-bit.7z.exe

            if (preg_match("#PortableGit-(\d+\.\d+.\d+)-32-bit.7z.exe#", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://github.com/git-for-windows/git/releases/download/v'.$version.'.windows.1/PortableGit-'.$version.'-32-bit.7z.exe',
                    );
                }
            }
        });
    }
}
