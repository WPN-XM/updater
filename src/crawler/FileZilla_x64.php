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
 * Nodepad Plus Plus - Version Crawler
 *
 * Website:   https://notepad-plus-plus.org/
 * Downloads: https://notepad-plus-plus.org/download/
 *            https://download.filezilla-project.org/client/
 *            https://sourceforge.net/projects/filezilla/files/FileZilla_Client/
 */
class FileZilla_x64 extends VersionCrawler
{
    public $name = 'filezilla-x64';

    // https://filezilla-project.org/download.php?platform=win64
    public $url = 'https://download.filezilla-project.org/client/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // https://download.filezilla-project.org/client/FileZilla_3.27.1_win64.zip
            if (preg_match("#FileZilla_(\d+\.\d+\.\d+)_win64.zip#i", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {                   
                    return array(
                        'version' => $version,
                        'url'     => 'https://download.filezilla-project.org/client/FileZilla_' . $version . '_win64.zip',
                    );
                }
            }
        });
    }
}
