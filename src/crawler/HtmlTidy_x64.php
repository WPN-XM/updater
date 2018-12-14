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
 * HtmlTidy x64 mt
 *
 * Website:       https://github.com/htacg/tidy-html5
 * Downloads:     https://github.com/htacg/tidy-html5/releases
 */
class htmltidy_x64 extends VersionCrawler
{
    public $name = 'htmltidy-x64';

    public $url = 'https://github.com/htacg/tidy-html5/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node)
        {
            // https://github.com/htacg/tidy-html5/releases/download/5.6.0/tidy-5.6.0-vc14-mt-64b.zip

            if (preg_match("#/releases/download/(\d+\.\d+.\d+)/#", $node->attr('href'), $matches))
            {
                $version = $matches[1];

                $download_file = 'https://github.com/htacg/tidy-html5/releases/download/'.$version.'/tidy-'.$version.'-vc14-mt-64b.zip';

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
