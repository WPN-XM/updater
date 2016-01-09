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
 * PHP Extension Ioncube - Version Crawler
 */
class phpext_ioncube extends VersionCrawler
{
    public $url = 'https://www.ioncube.com/loaders.php';

    public $url_template = 'http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_win_nonts_%compiler%_%bitsize%.zip';

    public $needsGuzzle = false;

    public function crawlVersion()
    {
        /**
         * XPath for Chrome Console
         * $x("/html/body/div/table[contains(@class, 'loader_download')][1]/tbody/tr[contains(.,'Windows')]");
         *
         * This expression grabs the version number from the first table row containing "Windows".
         */
        $xPathExpression = "//html/body/div/table[contains(@class, 'loader_download')][1]/tbody/tr[contains(.,'Windows')][1]/td[5]";

        $version = $this->filterXPath($xPathExpression)->text();
        $version = trim($version);


        if (version_compare($version, $this->registry['phpext_ioncube']['latest']['version'], '>=') === true) {
			
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

    /**
     * Creates the version array for PHP Extension "ionCube".
     *
     * Replaces %compiler% and %bitsize% placeholder strings in the $url_template:
     * http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_win_nonts_%compiler%_%bitsize%.zip
     *
     * array (
     *   'x86' => array(
     *     '5.4.0' => url,
     *     '5.5.0' => url,
     *     '5.6.0' => url
     *    ),
     *  'x64' => array(
     *     '5.4.0' => url,
     *     '5.5.0' => url,
     *     '5.6.0' => url
     *  ),
     * )
     *
     * @param  string $url     PHP Extension URL with placeholders.
     * @param  string $version
     * @return array
     */
    public function createPhpVersionsArrayForExtension($version, $url, $skipURLcheck = false)
    {
        $url = str_replace("%version%", $version, $url);

        $bitsizes    = array('x86', 'x64');
        $phpversions = array('5.4.0', '5.5.0', '5.6.0');
        $urls        = array();

        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {
                $compiler = ($phpversion === '5.4.0') ? 'vc9' : 'vc11';

                // the bitsize on the file is not "x64", but "x86-64"
                $file_bitsize = ($bitsize === 'x64') ? 'x86-64' : 'x86';

                $replacedUrl = str_replace(
                    array('%compiler%', '%bitsize%'),
                    array($compiler, $file_bitsize),
                    $url
                );

                //if ($skipURLcheck === true) {
                    $urls[$bitsize][$phpversion] = $replacedUrl;
                //} elseif($this->fileExistsOnServer($replacedUrl) === true) {
                //    $urls[$bitsize][$phpversion] = $replacedUrl;
                //}
            }
        }

        return $urls;
    }
}
