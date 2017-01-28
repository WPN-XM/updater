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
 * PHP Extension imagick - Version Crawler
 */
class phpext_imagick extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/imagick/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/imagick/%version%/php_imagick-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // http://windows.php.net/downloads/pecl/releases/imagick/3.2.0b2/php_imagick-3.2.0b2-5.3-nts-vc9-x86.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(b|rc)?(\d+))#", $node->text(), $matches)) {

                $version = $matches[0];

                if (version_compare($version, $this->registry['phpext_imagick']['latest']['version'], '>=') === true) {
                		
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
