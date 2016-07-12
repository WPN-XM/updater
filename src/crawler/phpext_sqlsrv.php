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
 * PHP Extensions "SQLSRV" - Version Crawler
 *
 * Website:   https://github.com/Azure/msphpsql
 * Downloads: https://github.com/Azure/msphpsql/releases
 */
class phpext_sqlsrv extends VersionCrawler
{
    public $url = 'https://github.com/Azure/msphpsql/releases';

    private $url_template = 'https://github.com/Azure/msphpsql/releases/download/v%version%/php_sqlrv_%version%_%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // https://github.com/Azure/msphpsql/releases/download/v4.0.4/php_sqlrv_4.0.4_x86.zip

            if (preg_match("#Azure\/msphpsql\/releases\/download\/v(\d+\.\d+\.\d+)#i", $node->attr('href'), $matches)) {
                $version = $matches[1];

                if (version_compare($version, $this->registry['phpext_sqlsrv']['latest']['version'], '>=') === true)  {

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

    /**
     * Creates the version array for PHP Extension "sqlsrv".
     * 
     * I have to support a custom naming scheme for this extension,
     * referencing: https://github.com/Azure/msphpsql/issues/88
     *
     * php_sqlrv_4.0.4_x64.zip 
     * php_sqlrv_4.0.4_x86.zip
     * = php_sqlrv_%version%_%bitsize%.zip
     *
     * @param  string $version      Version.
     * @param  string $url_template PHP Extension URL with placeholders.
     * @param  bool   $skipURLcheck
     * @return array
     */
    public function createPhpVersionsArrayForExtension($version, $url, $skipURLcheck = false)
    {
        $url = str_replace("%version%", $version, $url);

        $bitsizes    = array('x86', 'x64');

        // WARNING!
        // The PHP version is only available inside the zip.
        // https://github.com/Azure/msphpsql/releases/download/v4.0.4/php_sqlrv_4.0.4_x86.zip
        // Update the array manually, until the naming scheme is changed...
        $phpversions = array('7.0.0');

        $urls        = array();
        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {

                $replacedUrl = str_replace('%bitsize%', $bitsize, $url);

                $urls[$bitsize][$phpversion] = $replacedUrl;
            }
        }

        //var_dump($urls); exit;

        return $urls;
    }
}
