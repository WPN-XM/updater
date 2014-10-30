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
 * PostGreSql - Version Crawler
 */
class postgresql extends VersionCrawler
{
    // do not use  http://www.enterprisedb.com/products-services-training/pgdownload
    // because beta versions are only listed on the following page:
    public $url = 'http://www.enterprisedb.com/products-services-training/pgbindownload';

    public function crawlVersion()
    {
        /**
         * We scrape the "text above the link images".
         * "Binaries from installer version 9.4 beta Release 1" => 9.4.0-beta1-1
         *
         * Scraping the links is not possible, because matching the version number wouldn't work:
         * "http://www.enterprisedb.com/postgresql-8421-binaries-win32?ls=Crossover&type=Crossover"
         * Because of the added "1", it's unclear if 8421 means 8.4.2 or 8.4.21.
         */
        return $this->filterXPath('//p/i')->each(function ($node) {

            $value = strtolower($node->text());

            if (preg_match("/(\d+\.\d+(\.\d+)*)(-beta(\d+))?/", $value, $matches) && false === strpos($value, 'beta')) {

                $download_version = '9.3.0-beta2-1';

                if (isset($matches[3]) === true) { // if we have "release candidate" or "beta"
                    $version = $matches[1];
                    $pre_release_version = $matches[4];
                    if ($matches[3] === 'release candidate') {
                        $download_version = $version . '-rc' . $pre_release_version;
                    }
                } else {
                    $version = $matches[0]; // just 1.2.3

                    if(3 === substr_count($version, '.')) { // 9.3.5.1
                        $version = substr($version, 0, -2); // 9.3.5    WTF? WHY?
                    }

                    $download_version = $version . '-1'; // wtf? "-1" means "not beta" or "stable release", or what?
                }

                if (version_compare($version, $this->registry['postgresql']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        // x86-64: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-x64-binaries.zip
                        // x86-32: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-binaries.zip
                        'url' => 'http://get.enterprisedb.com/postgresql/postgresql-' . $download_version . '-windows-binaries.zip'
                    );
                }
            }
        });
    }
}
