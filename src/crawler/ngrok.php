<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * ngrok - Version Crawler
 */
class ngrok extends VersionCrawler
{
    public $url = 'https://ngrok.com/download';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // https://dl.ngrok.com/ngrok_2.0.19_windows_386.zip

            if (preg_match("#ngrok_(\d+\.\d+\.\d+)_windows_386.zip#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['ngrok']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://dl.ngrok.com/ngrok_' . $version . '_windows_386.zip',
                    );
                }
            }
        });
    }
}
