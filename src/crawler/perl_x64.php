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
 * Perl (Strawberry Perl 64bit)
 */
class perl_x64 extends VersionCrawler
{
    public $name = 'perl-x64';

    public $url = 'http://strawberryperl.com/releases.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // perl-5.4.1.1-64bit.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)-64bit?#", $node->attr('href'), $matches)) {
                $version = $matches[1]; // 5.4.1.1
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://strawberryperl.com/download/' . $version . '/strawberry-perl-' . $version . '-64bit.zip',
                    );
                }
            }
        });
    }
}
