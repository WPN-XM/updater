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
 * PostgreSQL x86 - Version Crawler
 */
class postgresql extends VersionCrawler
{
    public $name = 'postgresql';

    // do not use  http://www.enterprisedb.com/products-services-training/pgdownload
    // because beta versions are only listed on the following page:
    public $url = 'http://www.enterprisedb.com/products-services-training/pgbindownload';

    public function crawlVersion()
    {
        /*
         * We scrape the "text above the link images".
         *
         * "Binaries from installer version 9.4 beta Release 1"   9.4.0-beta1-1
         * "Binaries from installer version 9.4.0 RC 1"           9.4.0-rc1-1
         * "Binaries from installer version 9.3.0 beta2 1"        9.3.0-beta2-1
         *
         * Scraping the links is not possible, because matching the version number wouldn't work:
         * "http://www.enterprisedb.com/postgresql-8421-binaries-win32?ls=Crossover&type=Crossover"
         * Because of the added "1", it's unclear if 8421 means 8.4.2 or 8.4.21.
         */
        return $this->filterXPath('//p/i')->each(function ($node) {

            $value = strtolower($node->text());

            if (preg_match("/(\d+\.\d+(\.\d+)*)(.(RC|beta)(\d+))?/i", $value, $matches) && false === strpos($value, 'beta')) {
                $download_version = '';

                // is it a "release candidate"?
                if (isset($matches[4]) === true && $matches[4] === 'rc')
                {
                    // build a semver version number for the registry: "9.5.0 rc1" => "9.5.0.rc.1"
                    $version  = $matches[1] . '.' . $matches[4] . '.' . $matches[5];

                    // build the version number for the DL link: "9.5.0 rc1" => "9.5.0-rc1"
                    $download_version = $matches[1] . '-' . $matches[4] . '' . $matches[5];

                } else {
                    $version = $matches[0]; // just 1.2.3

                    if (3 === substr_count($version, '.')) { // 9.3.5.1
                        $version = substr($version, 0, -2); // 9.3.5    WTF? Why shorten version number for the DL link?
                    }

                    $download_version = $version . '-1'; // wtf? "-1" means "not beta" or "stable release", or what?
                }

                if (version_compare($version, $this->registry['postgresql']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        // x86-64: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-x64-binaries.zip
                        // x86-32: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-binaries.zip
                        // http://get.enterprisedb.com/postgresql/postgresql-9.4.0-1-windows-binaries.zip
                        'url' => 'http://get.enterprisedb.com/postgresql/postgresql-' . $download_version . '-windows-binaries.zip',
                    );
                }
            }
        });
    }
}
