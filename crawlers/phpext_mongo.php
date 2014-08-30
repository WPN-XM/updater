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
    public $url = 'http://windows.php.net/downloads/pecl/releases/mongo/';

    /**
     * S3 URL:   http://s3.amazonaws.com/drivers.mongodb.org/php/php_mongo-'.$version.'.zip
     * PECL URL: http://windows.php.net/downloads/pecl/releases/mongo/1.5.5/php_mongo-1.5.5-5.6-nts-vc11-x86.zip
     */
    private $url_template = 'http://windows.php.net/downloads/pecl/releases/mongo/%version%/php_mongo-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each( function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->text(), $matches)) {
                $version = $matches[1]; // 1.2.3
                if (version_compare($version, $this->registry['phpext_mongo']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => $this->createPhpVersionsArrayForExtension($version, $this->url_template)
                    );
                }
            }
        });
    }
}
