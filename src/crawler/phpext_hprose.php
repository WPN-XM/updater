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
 * PHP Extension Hprose - Version Crawler
 *
 * PHP Extension for Hprose - a High Performance Remote Object Service Engine.
 *
 * Website: http://hprose.com/
 * Github:  https://github.com/hprose
 * PECL:    https://windows.php.net/downloads/pecl/releases/hprose/
 */
class phpext_hprose extends VersionCrawler
{
	public $name = 'phpext_hprose';
	
    public $url = 'https://windows.php.net/downloads/pecl/releases/hprose/';

    private $url_template = 'https://windows.php.net/downloads/pecl/releases/hprose/%version%/php_hprose-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

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
