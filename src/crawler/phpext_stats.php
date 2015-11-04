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
 * PHP Extension stats - Version Crawler
 * 
 * Website:   https://pecl.php.net/package/stats
 * Downloads: http://windows.php.net/downloads/pecl/releases/stats/
 */
class phpext_stats extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/stats/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/stats/%version%/php_stats-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->registry['phpext_stats']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template),
                    );
                }
            }
        });
    }
}
