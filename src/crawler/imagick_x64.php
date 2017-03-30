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
 * ImageMagick x64 - Version Crawler
 */
class Imagick_x64 extends VersionCrawler
{
    public $name = 'imagick-x64';

    // http://www.imagemagick.org/download/windows/
    // http://www.imagemagick.org/script/binary-releases.php
    public $url = 'http://www.imagemagick.org/download/binaries/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                /**
                 * http://www.imagemagick.org/download/binaries/ImageMagick-7.0.0-0-portable-Q16-x64.zip
                 *
                 * Version is "6.8.8-1", where "-1" might indicate an pre-release version, but i think its not semver.
                 * They also adhere to a standard, where archived versions have a "-10" suffix, e.g. "6.8.8-10".
                 */

                if (preg_match("#(\d+\.\d+(\.\d+)*-\d+)-portable-Q16-x64.zip$#", $node->attr('href'), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->latestVersion, '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'http://www.imagemagick.org/download/binaries/ImageMagick-' . $version . '-portable-Q16-x64.zip',
                        );
                    }
                }
        });
    }
}
