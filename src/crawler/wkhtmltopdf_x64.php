<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens-André Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * wkhtmltopdf - Version Crawler
 *
 * wkhtmltopdf and wkhtmltoimage are open source command line tools 
 * to render HTML into PDF and various image formats using the Qt WebKit rendering engine. 
 * These run entirely "headless" and do not require a display or display service.

 * Website:   http://wkhtmltopdf.org/
 * Github:    https://github.com/wkhtmltopdf/wkhtmltopdf
 * Downloads: https://github.com/wkhtmltopdf/wkhtmltopdf/releases/
 */
class wkhtmltopdf_x64 extends VersionCrawler
{
    public $name = 'wkhtmltopdf-x64';
    
    public $url = 'https://github.com/wkhtmltopdf/wkhtmltopdf/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                if (preg_match("#/releases/download/(\d+\.\d+.\d+)/wkhtmltox-(.*)_msvc2015-win64.exe#", $node->attr('href'), $matches)) {
                    $version = $matches[1];

                    /**
                     * Hmm, version number isn't SemVer: "0.12.3.2". Looks like a version number in Microsoft-scheme.
                     * - http://download.gna.org/wkhtmltopdf/0.12/0.12.3.2/wkhtmltox-0.12.3.2_msvc2013-win32.exe
                     * - http://download.gna.org/wkhtmltopdf/0.12/0.12.3.2/wkhtmltox-0.12.3.2_msvc2013-win64.exe
                     *
                     * From v0.12.4 "msvc2015" is used. Now also SemVer. Yippie.
                     * - https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.4/wkhtmltox-0.12.4_msvc2015-win64.exe
                     */
                    $download_file = 'https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/';
                    $download_file .= $version . '/wkhtmltox-' . $version . '_msvc2015-win64.exe';

                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
