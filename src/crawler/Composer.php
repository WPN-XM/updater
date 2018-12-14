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
 * Version Crawler for
 * Composer - Dependency Manager for PHP
 * 
 * Website: https://getcomposer.org/
 */
class Composer extends VersionCrawler
{
    public $name = 'composer';
	public $url = 'https://getcomposer.org/versions';

	public function crawlVersion()
    {
    	$content  = file_get_contents($this->url);
    	$versions = json_decode($content, true);

    	// The following channels are available: 
    	// stable, preview, snapshot (version is latest git commit hash of dev-master branch)
    	$channel = 'stable';

    	// The channels seems to support multiple entries. We are using the first one (0).
    	$entry = 0;

        $url 	 = 'https://getcomposer.org'.$versions[$channel][$entry]['path'];
        $version = $versions[$channel][$entry]['version'];

        if (version_compare($version, $this->latestVersion, '>=') === true) {
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