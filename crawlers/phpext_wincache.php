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
 * PHP Extension wincache - Version Crawler
 */
class phpext_wincache extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/wincache/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/wincache/%version%/php_wincache-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['phpext_wincache']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => $this->createPhpVersionsArrayForExtension($version, $this->url_template)
                    );
                }
            }
        });
    }
}
