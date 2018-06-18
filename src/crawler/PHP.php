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
 * PHP x86 - Version Crawler
 */
class PHP extends VersionCrawler
{
    public $name = 'php';

    /**
     * Alternative: JSON GET
     * - http://php.net/releases/index.php?json&version=5&max=10
     * - http://php.net/releases/active.php
     */
    public $url = 'http://windows.php.net/downloads/releases/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * Notes for the Regular Expression:
             * "VC11" is needed for PHP 5.5 & 5.6.
             * "VC14" is needed for PHP 7.
             * "VC15" is needed for PHP 7.2.
             */
            if (preg_match("#php-(\d+\.\d+(\.\d+)*)-nts-Win32-VC(11|14|15)-x86.zip$#", $node->text(), $matches)) {
                $version = $matches[1];

                if ((version_compare($version, $this->latestVersion, '>=') === true)
                    or isset($this->registry['php'][$version]) === false) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://windows.php.net/downloads/releases/' . $node->text(),
                    );
                }
            }
        });
    }

    /**
     * PHP release files are moved from "/releases" to "/releases/archives" with every new version.
     * The latest version must point to "/releases".
     * Every other version must point to "/releases/archives".
     */
    public function onAfterVersionInsert($registry)
    {
        foreach ($registry['php'] as $version => $url) {

            // skip "name", "website", "latest"
            if(in_array($version, ['name', 'website', 'latest'])) {
                continue;
            }

            // do not modify array key with latest version number - it must point to "/releases".
            if ($version === $registry['php']['latest']['version']) {
                continue;
            }

            // do not modify the highest version of each "major.minor" release
            if ($version === $this->isHighestMajorMinorVersion($version, $registry['php']))
            {
                continue;
            }

            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/releases/php-', 'php.net/downloads/releases/archives/php-', $url);

            // insert at old array position, overwriting the old url
            $registry['php'][$version] = $new_url;
        }

        return $registry;
    }
}
