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
 * Git for Windows (PortableGit) - Version Crawler
 */
class msysgit_x64 extends VersionCrawler
{
    public $name = 'msysgit-x64';

    public $url = 'https://github.com/git-for-windows/git/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            # https://github.com/git-for-windows/git/releases/tag/v2.5.0.windows.1
            # https://github.com/git-for-windows/git/releases/download/v2.5.0.windows.1/PortableGit-2.5.0-64-bit.7z.exe

            if (preg_match("#PortableGit-(\d+\.\d+.\d+)-64-bit.7z.exe#i", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://github.com/git-for-windows/git/releases/download/v'.$version.'.windows.1/PortableGit-'.$version.'-64-bit.7z.exe',
                    );
                }
            }
        });
    }
}
