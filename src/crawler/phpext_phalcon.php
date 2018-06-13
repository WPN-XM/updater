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
 * Phalcon - Version Crawler
 *
 * Website:         http://phalconphp.com/
 * Downloads:       http://phalconphp.com/en/download/windows
 * Github:          https://github.com/phalcon/cphalcon
 * Github Releases: https://github.com/phalcon/cphalcon/releases
 */
class phpext_phalcon extends VersionCrawler
{
    public $name = 'phpext_phalcon';

    public $url = 'https://github.com/phalcon/cphalcon/releases';

    // https://github.com/phalcon/cphalcon/releases/download/v3.2.2/phalcon_x64_vc11_php5.5.0_3.2.2.zip
    private $url_template = 'https://github.com/phalcon/cphalcon/releases/download/v%version%/phalcon_%bitsize%_%compiler%_php%phpversion%_%version%_nts.zip';

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            if (preg_match("#phalcon/cphalcon/releases/download/v(\d+\.\d+\.\d+)/#i", $node->attr('href'), $matches)) {

                $version = $matches[1];

                if (version_compare($version, $this->latestVersion, '>=') === true) {

                $urls = $this->createPhpVersionsArrayForExtension($version, $this->url_template, true);
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
     * Creates an array for a PHP Extension URL.
     * Replaces %compiler% and %phpversion% placeholder strings in that URL:
     * http://php.net/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip
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
        $phpversions = array('5.5.0', '5.6.0', '7.0.0', '7.1.0');
        $urls        = array();

        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {
                $compiler = self::getCompilerByPHPVersion($phpversion);

                $replacedUrl = str_replace(
                    array('%compiler%', '%phpversion%', '%bitsize%'),
                    array($compiler, $phpversion, $bitsize),
                    $url
                );

                $phpversion = self::removePatchLevelFromVersion($phpversion);

                if ($skipURLcheck === true) {
                    $urls[$bitsize][$phpversion] = $replacedUrl;
                } elseif($this->fileExistsOnServer($replacedUrl) === true) {
                    $urls[$bitsize][$phpversion] = $replacedUrl;
                }
            }
        }

        return $urls;
    }
}
