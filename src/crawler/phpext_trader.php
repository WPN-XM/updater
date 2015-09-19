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
 * PHP Extension trader - Version Crawler
 */
class phpext_trader extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/trader/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/trader/%version%/php_trader-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                if (version_compare($version, $this->registry['phpext_trader']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template),
                    );
                }
            }
        });
    }
}