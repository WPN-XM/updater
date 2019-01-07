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
 * Cassandra (PHP Extension) - Version Crawler
 */
class phpext_cassandra extends VersionCrawler
{
	public $name = 'phpext_cassandra';
	
    public $url = 'https://windows.php.net/downloads/pecl/releases/cassandra/';

    private $url_template = 'https://windows.php.net/downloads/pecl/releases/cassandra/%version%/php_cassandra-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {

                    $urls = $this->createPhpVersionsArrayForExtension($version, $this->url_template);
                    if(empty($urls)) {
                        return;
                    }

                    return array(
                       'version' => $version,
                       'url'     => $urls,
                    );
                }
            }
        });
    }
}
