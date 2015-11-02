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
             * "VC9"  is needed for PHP 5.4. We can drop it, when 5.4 is EOL.
             * "VC11" is needed for PHP 5.5 & 5.6.
             * "VC14" is needed for PHP 7.
             */
            if (preg_match("#php-(\d+\.\d+(\.\d+)*)-nts-Win32-VC(9|11|14)-x86.zip$#", $node->text(), $matches)) {
                $version = $matches[1];

                if ((version_compare($version, $this->registry['php']['latest']['version'], '>=') === true)
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
     * PHP release files are moved from "/releases" to "/releases/archives", with every new version.
     * That means, latest version must point to "/releases".
     * Every other version points to "/releases/archives".
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
