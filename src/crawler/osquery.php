<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * Version Crawler for Redis
 *
 * Website: https://osquery.io/
 * Github:  https://github.com/facebook/osquery
 *
 * Notes:
 * 1. The Windows releases are only deployed to Chocolatey. 
 *    I wonder why they are not releasing to Github Releases.
 * 2. We can not scrape "Lastest Version" from "Github Releases",
 *    because they don't release every "Latest Version" for Windows on "Chocolatey".
 * 3. So, we could scrape Chocolatey's website for the osquery package or query their API. 
 *    The API lives at https://chocolatey.org/api/v2/ and returns an XML feed document.
 *    I tried adding "&$format=json", but it's not supported.
 */
class Osquery extends VersionCrawler
{
    public $name = 'osquery';
    public $url = 'https://chocolatey.org/api/v2/Packages()?$filter=((Id%20eq%20%27osquery%27)%20and%20(not%20IsPrerelease))%20and%20IsLatestVersion';

    public function crawlVersion()
    {
        $this->registerNamespace('m', 'http://schemas.microsoft.com/ado/2007/08/dataservices/metadata');
        $version = $this->filterXPath('//m:properties//d:Version')->text();

        if (version_compare($version, $this->latestVersion, '>=') === true) {
            return array(
                'version' => $version,
                'url'     => 'https://osquery-packages.s3.amazonaws.com/choco/osquery-'.$version.'.zip',
            );
        }
    }
}
