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
 * Php - Version Crawler
 */
class PHP_X86 extends VersionCrawler
{
    public $name = 'php';

    public $url = 'http://windows.php.net/downloads/releases/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#php-+(\d+\.\d+(\.\d+)*)-nts-Win32-(VC9|VC11)-x86.zip$#", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['php']['latest']['version'], '>=')) {
                    return array(
                        'version' => $matches[1],
                        'url' => 'http://windows.php.net/downloads/releases/' . $node->text()
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
    public function modifyRegistry($registry)
    {
        foreach ($registry['php'] as $version => $url) {
            // do not modify array key "latest"
            if( $version === 'latest') continue;
            // do not modify array key with latest version number - it must point to "/releases".
            if( $version === $registry['php']['latest']['version']) continue;
            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/releases/php', 'php.net/downloads/releases/archives/php', $url);
            // insert at old array position, overwriting the old url
            $registry['php'][$version] = $new_url;
        }

        return $registry;
    }
}
