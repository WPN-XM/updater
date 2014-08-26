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
 * PHP Extension xhProf - Version Crawler
 */
class phpext_xhprof extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/xhprof/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                return array(
                    'version' => $version,
                    'url' => 'http://windows.php.net/downloads/pecl/releases/xhprof/'.$version.'/php_xhprof-'.$version.'-5.4-nts-vc9-x86.zip'
                );
            }
        });
    }
}
