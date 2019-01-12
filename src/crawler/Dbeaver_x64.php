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
 * Dbeaver
 *
 * Website:       https://dbeaver.io
 * Downloads:     https://github.com/dbeaver/dbeaver/releases
 *                https://dbeaver.io/files/
 */
class Dbeaver_x64 extends VersionCrawler
{
    public $name = 'dbeaver-x64';

    public $url = 'https://github.com/dbeaver/dbeaver/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            // TODO remove duplicate win32 part, when they fix their release name
            // https://github.com/dbeaver/dbeaver/releases/download/5.3.2/dbeaver-ce-5.3.2-win32.win32.x86_64.zip

            if (preg_match("#/releases/download/(\d+\.\d+.\d+)/#", $node->attr('href'), $matches))
            {
                $version = $matches[1];

                $download_file = 'https://github.com/dbeaver/dbeaver/releases/download/'.$version.'/dbeaver-ce-'.$version.'-win32.win32.x86_64.zip';

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
