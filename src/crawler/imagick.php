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
 * ImageMagick x86 - Version Crawler
 */
class imagick extends VersionCrawler
{
    public $name = 'imagick';

    // http://www.imagemagick.org/download/windows/
    // http://www.imagemagick.org/script/binary-releases.php
    public $url = 'http://www.imagemagick.org/download/binaries/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                /**
                 * http://www.imagemagick.org/download/binaries/ImageMagick-6.9.1-8-portable-Q16-x86.zip
                 *
                 * Version is "6.8.8-1", where "-1" might indicate an pre-release version, but i think its not semver.
                 * They also adhere to a standard, where archived versions have a "-10" suffix, e.g. "6.8.8-10".
                 */

                if (preg_match("#(\d+\.\d+(\.\d+)*-\d+)-portable-Q16-x86.zip$#", $node->attr('href'), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->registry['imagick']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => 'http://www.imagemagick.org/download/binaries/ImageMagick-' . $version . '-portable-Q16-x86.zip',
                        );
                    }
                }
        });
    }
}
