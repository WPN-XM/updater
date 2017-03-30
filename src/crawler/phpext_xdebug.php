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
 * XDebug (PHP Extension) - Version Crawler
 */
class phpext_xdebug extends VersionCrawler
{
	public $name = 'phpext_xdebug';
	
    public $url = 'http://windows.php.net/downloads/pecl/releases/xdebug/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/xdebug/%version%/php_xdebug-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(alpha|beta|RC)(\d+))$#i", $node->text(), $matches)) {
                $version = $matches[0];

                if (version_compare($version, $this->registry['phpext_xdebug']['latest']['version'], '>=') === true) {
             
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
