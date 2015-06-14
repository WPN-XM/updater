<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

/**
 * PHP x64 - Version Crawler
 */
class PHP_x64 extends VersionCrawler
{
    public $name = 'php-x64';

    public $url = 'http://windows.php.net/downloads/releases/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * Notes for the Regular Expression:
             * "VC11" is needed for PHP 5.5 & 5.6.
             * "VC14" is needed for PHP 7.
             */
            if (preg_match("#php-(\d+\.\d+(\.\d+)*)-nts-Win32-VC(11|14)-x64.zip$#", $node->text(), $matches)) {
                $version = $matches[1];

                /**
                 * return version array, if
                 * 1) version is a "new" latest version
                 * 2) version doesn't exist, yet. mostly bugfix releases of "major.minor" version.
                 */
                if ((version_compare($version, $this->registry['php-x64']['latest']['version'], '>=') === true)
                    or isset($this->registry['php-x64'][$version]) === false) {
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
        foreach ($registry['php-x64'] as $version => $url) {

            // skip "name", "website", "latest"
            if(in_array($version, ['name', 'website', 'latest'])) {
                continue;
            }

            // do not modify array key with latest version number - it must point to "/releases".
            if ($version === $registry['php-x64']['latest']['version']) {
                continue;
            }

            // do not modify the highest version of each "major.minor" release
            if ($version === $this->isHighestMajorMinorVersion($version, $registry['php-x64']))
            {
                continue;
            }

            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/releases/php', 'php.net/downloads/releases/archives/php', $url);

            // insert at old array position, overwriting the old url
            $registry['php-x64'][$version] = $new_url;
        }

        return $registry;
    }
}
