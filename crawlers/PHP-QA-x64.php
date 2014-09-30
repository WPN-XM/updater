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
 * PHP QA x64- Version Crawler
 */
class PHP-QA-x64 extends VersionCrawler
{
    public $name = 'php-qa-x64';

    public $url = 'http://windows.php.net/downloads/qa/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#php-(\d+\.\d+(\.\d+)*(RC\d+))-nts-Win32-VC11-x64.zip$#", $node->text(), $matches)) {

                $version = $matches[1];

                if(false !== strpos($version, '5.3')) {
                    return;
                }

                if (version_compare($version, $this->registry['php-x64']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://windows.php.net/downloads/qa/' . $node->text()
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
        foreach ($registry['php-x64'] as $version => $url) {
            // do not modify array key "latest"
            if( $version === 'latest') continue;
            // do not modify array key with latest version number - it must point to "/releases".
            if( $version === $registry['php-qa-x64']['latest']['version']) continue;
            // replace the path on any other version
            $new_url = str_replace('php.net/downloads/qa', 'php.net/downloads/qa/archives', $url);
            // insert at old array position, overwriting the old url
            $registry['php-x64'][$version] = $new_url;
        }

        return $registry;
    }
}
