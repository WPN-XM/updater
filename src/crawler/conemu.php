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
 * Version Crawler for
 * ConEmu - Customizable Windows terminal with tabs, splits, quake-style and more.
 *
 * Website: http://conemu.github.io/
 * Github:  https://github.com/Maximus5/ConEmu
 */
class Conemu extends VersionCrawler
{
    public $name = 'conemu';
    public $url = 'https://github.com/Maximus5/ConEmu/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * The download URL for a file looks like this:
             * https://github.com/Maximus5/ConEmu/releases/download/v15.05.13/ConEmuPack.150513.7z
             *
             * Here the release date (YY.MM.DD) is turned into a version number. Yikes, that's not SemVer!
             * Anyway, version comparison works with release dates, let's just grab what we need.
             */
            if (preg_match("#download/v(\d+\.\d+.\d+)/ConEmuPack.(\d+).7z#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                $versionNoDots = $matches[2];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://github.com/Maximus5/ConEmu/releases/download/v' . $version . '/ConEmuPack.' . $versionNoDots . '.7z',
                    );
                }
            }
        });
    }
}
