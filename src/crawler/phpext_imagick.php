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
 * PHP Extension imagick - Version Crawler
 */
class phpext_imagick extends VersionCrawler
{
	public $name = 'phpext_imagick';
    public $url = 'https://windows.php.net/downloads/pecl/releases/imagick/';

    private $url_template = 'https://windows.php.net/downloads/pecl/releases/imagick/%version%/php_imagick-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            // https://windows.php.net/downloads/pecl/releases/imagick/3.2.0b2/php_imagick-3.2.0b2-5.3-nts-vc9-x86.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(b|rc)?(\d+))#", $node->text(), $matches)) {

                $version = $matches[0];

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
