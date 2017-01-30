<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
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
 * Gna:       http://gna.org/projects/wkhtmltopdf
 * Downloads: http://download.gna.org/wkhtmltopdf/
 */
class wkhtmltopdf_x86 extends VersionCrawler
{
    public $name = 'wkhtmltopdf-x86';

    // we are scraping the gna downloads folder
    public $url = 'http://download.gna.org/wkhtmltopdf/0.12/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                if (preg_match("#(\d+.\d+.\d+(.\d+)?)/#", $node->attr('href'), $matches)) {
                    $version = $matches[1];

                    /**
                     * Hmm, version number isn't SemVer: "0.12.3.2". Looks like a version number in Microsoft-scheme.
                     * - http://download.gna.org/wkhtmltopdf/0.12/0.12.3.2/wkhtmltox-0.12.3.2_msvc2013-win32.exe
                     * - http://download.gna.org/wkhtmltopdf/0.12/0.12.3.2/wkhtmltox-0.12.3.2_msvc2013-win64.exe
                     *
                     * From v0.12.4 "msvc2015" is used.
                     */
                    $download_file = 'http://download.gna.org/wkhtmltopdf/0.12/' . $version . '/wkhtmltox-' . $version . '_msvc2015-win32.exe';

                    if (version_compare($version, $this->registry['wkhtmltopdf-x86']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
