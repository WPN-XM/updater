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
 * Curl x64 - Version Crawler
 *
 * Website: https://curl.haxx.se/
 * Github:  https://github.com/curl/curl
 *
 * Builds by Viktor Szakats
 * Github:               https://github.com/vszakats/harbour-deps
 * Downloads on Bintray: https://bintray.com/vszakats/generic/curl/#files
 */
class curl_x86 extends VersionCrawler
{
    public $name = 'curl-x86';

    /**
     * we could scrape https://bintray.com/vszakats/generic/curl/#files
     * or https://dl.bintray.com/vszakats/generic/
     * but bintray is cool and provides an API including "latest_version", which is superb!
     */
    public $url = 'https://api.bintray.com/packages/vszakats/generic/curl';

    public function crawlVersion()
    {
        $version = json_decode(file_get_contents($this->url), true)['latest_version'];

        /**
         * Downloads are on Bintray
         *
         * API for Downloading Content:
         * https://bintray.com/docs/api/#_download_content
         *
         * DL URL:
         * https://dl.bintray.com/vszakats/generic/curl-7.47.1-win32-mingw.7z
         */
        $download_file = 'https://dl.bintray.com/vszakats/generic/curl-' . $version . '-win32-mingw.7z';

        // the file exists check is needed, because we don't know
        // if a libressl version was build for latest version number
        if($this->fileExistsOnServer($download_file) === true)
        {
            if (version_compare($version, $this->latestVersion, '>=') === true) {
                return array(
                    'version' => $version,
                    'url'     => $download_file,
                );
            }
        }
    }
}
