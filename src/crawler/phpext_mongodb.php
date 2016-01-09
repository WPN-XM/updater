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
 * PHP Extension for MongoDB - Version Crawler
 *
 * The purpose of this driver is to provide exceptionally thin glue between MongoDB
 * and PHP, implementing only fundemental and performance-critical components
 * necessary to build a fully-functional MongoDB driver.
 *
 * Website: https://pecl.php.net/package/mongodb
 * Github:  http://mongodb.github.io/mongo-php-driver
 */
class phpext_mongodb extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/mongodb/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/mongodb/%version%/php_mongodb-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->registry['phpext_mongodb']['latest']['version'], '>=') === true) {
					
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
