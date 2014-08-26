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
 * AMQP (PHP Extension) - Version Crawler
 */
class phpext_amqp extends VersionCrawler
{

    public $url = 'http://windows.php.net/downloads/pecl/releases/amqp/';
    
    // http://windows.php.net/downloads/pecl/releases/amqp/1.4.0/php_amqp-1.4.0-5.6-ts-vc11-x86.zip
    // http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-%thread%-%compiler%-%bitsize%.zip
    private $url_template = 'http://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-x86.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->registry['phpext_amqp']['latest']['version'], '>=')) {
                        return array(
                            'version' => $version,
                            'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template)
                        );
                    }
                }
            });
    }

}
