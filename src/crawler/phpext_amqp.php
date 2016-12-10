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
 * AMQP (PHP Extension) - Version Crawler
 */
class phpext_amqp extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/amqp/';

    // http://windows.php.net/downloads/pecl/releases/amqp/1.4.0/php_amqp-1.4.0-5.6-ts-vc11-x86.zip
    // http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-%thread%-%compiler%-%bitsize%.zip
    private $url_template = 'http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(alpha|beta)(\d+))$#", $node->text(), $matches)) {
                    $version = $matches[0]; // take alpha/beta into account

                    if (version_compare($version, $this->registry['phpext_amqp']['latest']['version'], '>=') === true) {

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
