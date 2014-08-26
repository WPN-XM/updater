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
 * APC (PHP Extension) - Version Crawler
 */
class phpext_apc extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/apc/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['phpext_apc']['latest']['version'], '>=')) {
                    return array(
                       'version' => $version,
                       'url' => 'http://windows.php.net/downloads/pecl/releases/apc/'.$version.'/php_apc-'.$version.'-5.4-nts-vc9-x86.zip'
                    );
                }
            }
        });
    }
}
