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
 * uploadprogress (PHP Extension) - Version Crawler
 */
class phpext_uploadprogress extends VersionCrawler
{
	public $name = 'phpext_uploadprogress';
	
    public $url = 'http://windows.php.net/downloads/pecl/releases/uploadprogress/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/uploadprogress/%version%/php_uploadprogress-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->registry['phpext_uploadprogress']['latest']['version'], '>=') === true) {
					
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
