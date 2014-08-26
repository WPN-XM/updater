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
 * phpext_mongo (PHP Extension for MongoDB) - Version Crawler
 */
class phpext_mongo extends VersionCrawler
{
    /**
     * WARNING
     * The windows builds got no version listing, because Github stopped their downloads service.
     * Old Listing URL: https://github.com/mongodb/mongo-php-driver/downloads
     * S3 Listing:      https://s3.amazonaws.com/drivers.mongodb.org/php/index.html
     * PECL:            http://pecl.php.net/package/mongo
     *
     * Downloads are now on AS3.
     */

    public $url = 'https://s3.amazonaws.com/drivers.mongodb.org/php/index.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node) {
            // /package/mongo/1.4.5/windows
            if (preg_match("#mongo/(\d+\.\d+(\.\d+)*)(?:[._-]?(rc)?(\d+))/windows?#i", $node->attr('href'), $matches)) {
                $version = $matches[1]; // 1.2.3
                if (version_compare($version, $this->registry['phpext_mongo']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://s3.amazonaws.com/drivers.mongodb.org/php/php_mongo-'.$version.'.zip'
                    );
                }
            }
        });
    }
}
