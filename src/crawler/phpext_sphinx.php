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
 * PHP Extension "sphinx" - Version Crawler
 *
 * Client extension for Sphinx - opensource SQL full-text search engine
 * This extension provides bindings for libsphinxclient, client library for Sphinx.
 *
 * Website:   https://pecl.php.net/package/sphinx
 * Downloads: http://windows.php.net/downloads/pecl/releases/sphinx/
 */
class phpext_sphinx extends VersionCrawler
{
	public $name = 'phpext_sphinx';
    public $url = 'http://windows.php.net/downloads/pecl/releases/sphinx/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/sphinx/%version%/php_sphinx-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

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
