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
 * perl (strawberry perl)
 */
class perl extends VersionCrawler
{
    public $name = 'perl';
    public $url = 'http://strawberryperl.com/releases.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // perl-5.4.1.1-32bit.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)-32bit?#", $node->attr('href'), $matches)) {
                $version = $matches[1]; // 5.4.1.1
                if (version_compare($version, $this->registry['perl']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://strawberryperl.com/download/' . $version . '/strawberry-perl-' . $version . '-32bit.zip',
                    );
                }
            }
        });
    }
}
