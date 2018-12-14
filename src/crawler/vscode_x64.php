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
 * Microsoft Visual Studio Code - Version Crawler
 *
 * Returns Microsoft Visual Studio Code version and download URL.
 *
 * Download URLs:
 * - https://aka.ms/win32-x64-user-stable
 * - https://update.code.visualstudio.com/latest/win32-x64-user/stable
 */
class vscode_x64 extends VersionCrawler
{
    public $name = 'vscode-x64';

    /**
     * API for Version Request:
     * $url = 'https://update.code.visualstudio.com/api/update/$platform/$channel/VERSION';
     * $api_base_url = 'https://update.code.visualstudio.com/api/update';
     * $platform = ['darwin', 'win32', 'win32-user', 'win32-x64-user', 'win32-x64',
                'win32-x64-user', 'win32-archive', 'win32-x64-archive', 'linux-deb-ia32',
                'linux-deb-x64', 'linux-rpm-ia32', 'linux-ia32', 'linux-x64'];
     * $channel = ['insider', 'stable'];
     */
    public $url = 'https://update.code.visualstudio.com/api/update/win32-x64/stable/Version';

    public function crawlVersion()
    {
        /**
         * Keys;
         * url, name, version, productVersion, hash, timestamp, sha256hash, supportsFastUpdate
         */
        $releaseInfo = json_decode(file_get_contents($this->url), true);

        $version = $releaseInfo['productVersion'];
        $dl_url = $releaseInfo['url'];
       
        if (version_compare($version, $this->latestVersion, '>=') === true) {
            return array(
                'version' => $version,
                'url' => $dl_url,
            );
        }
    }
}
