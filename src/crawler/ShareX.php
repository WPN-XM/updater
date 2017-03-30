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
 * ShareX - Version Crawler
 *
 * ShareX is a free and open source program that
 * lets you capture or record any area of your screen
 * and share it with a single press of a key.
 *
 * It also allows uploading images, text or other types of files
 * to over 50 supported destinations you can choose from.
 *
 * Website: https://getsharex.com
 * Github:  https://github.com/ShareX/ShareX
 */
class ShareX extends VersionCrawler
{
    public $name = 'sharex';

    public $url = 'https://github.com/ShareX/ShareX/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            // https://github.com/ShareX/ShareX/releases/download/v10.5.0/ShareX-portable.zip

            if (preg_match("#/releases/download/v(\d+\.\d+.\d+)/#", $node->attr('href'), $matches))
            {
                $version = $matches[1];

                $download_file = 'https://github.com/ShareX/ShareX/releases/download/v' . $version . '/ShareX-portable.zip';

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
