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
 * PHP Extension runkit - Version Crawler
 */
class phpext_runkit extends VersionCrawler
{
	public $name = 'phpext_runkit';
	
    public $url = 'https://windows.php.net/downloads/pecl/releases/runkit/';

    private $url_template = 'https://windows.php.net/downloads/pecl/releases/runkit/%version%/php_runkit-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node){

            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                if (version_compare($version, $this->latestVersion, '>=') === true)  {				
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
