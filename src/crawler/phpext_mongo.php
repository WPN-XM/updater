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
 * phpext_mongo (PHP Extension for MongoDB) - Version Crawler
 */
class phpext_mongo extends VersionCrawler
{
	public $name = 'phpext_mongo';
	
    /**
     * WARNING
     * The windows builds got no version listing, because Github stopped their downloads service.
     * Old Listing URL: https://github.com/mongodb/mongo-php-driver/downloads
     * S3 Listing:      https://s3.amazonaws.com/drivers.mongodb.org/php/index.html
     * PECL:            http://pecl.php.net/package/mongo
     *
     * Downloads are now on AS3.
     */
    public $url = 'https://windows.php.net/downloads/pecl/releases/mongo/';

    /**
     * S3 URL:   http://s3.amazonaws.com/drivers.mongodb.org/php/php_mongo-'.$version.'.zip
     * PECL URL: https://windows.php.net/downloads/pecl/releases/mongo/1.5.5/php_mongo-1.5.5-5.6-nts-vc11-x86.zip
     */
    private $url_template = 'https://windows.php.net/downloads/pecl/releases/mongo/%version%/php_mongo-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1]; // 1.2.3

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
