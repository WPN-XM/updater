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
 * PHP QA x64- Version Crawler
 */
class PHP_QA_x64 extends VersionCrawler
{
    public $name = 'php-qa-x64';

    public $url = 'http://windows.php.net/downloads/qa/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            /**
             * Notes for the Regular Expression:
             * "VC11" is needed for PHP 5.5 & 5.6.
             * "VC14" is needed for PHP 7.
             * "VC15" is needed for PHP 7.2.
             */
            if (preg_match("#php-(\d+\.\d+(\.\d+)*(alpha|beta|RC)(\d+))-nts-Win32-VC(11|14|15)-x64.zip$#", $node->text(), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'http://windows.php.net/downloads/qa/' . $node->text(),
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
        foreach ($registry['php-qa-x64'] as $version => $url) {

            // skip "name", "website", "latest"
            if(in_array($version, ['name', 'website', 'latest'])) {
                continue;
            }

            // do not modify array key with latest version number - it must point to "/releases".
            if ($version === $registry['php-qa-x64']['latest']['version']) {
                continue;
            }

            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/qa/php-', 'php.net/downloads/qa/archives/php-', $url);

            // insert at old array position, overwriting the old url
            $registry['php-qa-x64'][$version] = $new_url;
        }

        return $registry;
    }
}
