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
 * Gimp -  Version Crawler
 *
 * Gimp is the GNU Image Manipulation Program.
 *
 * Website:   https://download.gimp.org/
 * Downloads: https://download.gimp.org/pub/gimp/
 */
class Gimp extends VersionCrawler
{
    /**
     * WTF? 
     * - Static versionized file: 
     *    https://download.gimp.org/pub/gimp/stable/0.0_LATEST-IS-2.8.20
     * - the version part on the file name changes, right? 
     * - one has to get the file listing to get the file, then split the filename to get the version, right?
     * - why should i look for "0.0_LATEST-IS-", if can get a latest version link on top by sorting?
     *    https://download.gimp.org/pub/gimp/stable/windows/?C=M;O=D
     * - The trick is to provide a non-changing file location, which contains the version, e.g.:
     *    https://download.gimp.org/pub/gimp/stable/latest_version.txt => "2.8.20". 
     * Ok.
     */
    public $url = 'https://download.gimp.org/pub/gimp/stable/windows/?C=M;O=D';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // https://download.gimp.org/pub/gimp/stable/windows/gimp-2.8.20-setup.exe
            if (preg_match("#gimp-(\d+\.\d+(\.\d+)*)-setup.exe$#i", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['gimp']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://download.gimp.org/pub/gimp/stable/windows/gimp-' . $version . '-setup.exe',
                    );
                }
            }
        });
    }
}
