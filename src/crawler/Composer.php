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
 * Version Crawler for
 * Composer - Dependency Manager for PHP
 * 
 * Website: https://getcomposer.org/
 */
class Composer extends VersionCrawler
{
	public $url = 'https://getcomposer.org/versions';

	public function crawlVersion()
    {
    	$protocol = extension_loaded('openssl') ? 'https' : 'http';
    	$content  = file_get_contents($protocol.'://getcomposer.org/versions');
    	$versions = json_decode($content, true);

    	// The following channels are available: 
    	// stable, preview, snapshot (version is latest git commit hash of dev-master branch)
    	$channel = 'stable';

    	// The channels seems to support multiple entries. We are using the first one (0).
    	$entry = 0;

        $url 	 = 'https://getcomposer.org'.$versions[$channel][$entry]['path'];
        $version = $versions[$channel][$entry]['version'];

        if (version_compare($version, $this->registry['composer']['latest']['version'], '>=') === true) {
            return array(
                'version' => $version,
                'url' => $url,
            );
        }
    }

    /**
     * A version crawl will update the latest version entry.
     * We can set the latest entry to "latest" and "latest version download",
     * because Composer provides the latest version download from a fixed URL.
     */
    public function onAfterVersionInsert($registry)
    {
    	$registry['composer']['latest'] = [
      	    'version' => 'latest',
      	    'url' => 'https://getcomposer.org/composer.phar',
    	];

    	return $registry;
    }
}