<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * AMQP (PHP Extension) - Version Crawler
 */
class phpext_amqp extends VersionCrawler
{
    public $name = 'phpext_amqp';
    public $url = 'https://windows.php.net/downloads/pecl/releases/amqp/';

    // https://windows.php.net/downloads/pecl/releases/amqp/1.4.0/php_amqp-1.4.0-5.6-ts-vc11-x86.zip
    // https://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-%thread%-%compiler%-%bitsize%.zip
    private $url_template = 'https://windows.php.net/downloads/pecl/releases/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
                if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(alpha|beta)(\d+))$#", $node->text(), $matches)) {
                    $version = $matches[0]; // take alpha/beta into account

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
