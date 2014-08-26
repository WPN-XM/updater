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
 * XDebug (PHP Extension) - Version Crawler
 */
class phpext_xdebug extends VersionCrawler
{
    public $url = 'http://xdebug.org/files/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            # regexp for all version: "#((\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+))([^\s]+nts(\.(?i)(dll))$)#i"
            # we are fetching all xdebug versions for php 5.4
            if (preg_match("#php_xdebug-(\d+\.\d+(\.\d+)*)-5.4-vc9-nts.dll#", $node->text(), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['phpext_xdebug']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://xdebug.org/files/' . $node->text());
                }
            }
        });
    }
}
