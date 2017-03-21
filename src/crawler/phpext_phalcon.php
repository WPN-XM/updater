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
 * Phalcon - Version Crawler
 *
 * Website:   http://phalconphp.com/
 * Downloads: http://phalconphp.com/en/download/windows
 */
class phpext_phalcon extends VersionCrawler
{
    public $url = 'https://phalconphp.com/en/download/windows';

    private $url_template = 'https://static.phalconphp.com/www/files/phalcon_%bitsize%_%compiler%_php%phpversion%_%version%_nts.zip';

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // we take "rc" versions into account
            // https://static.phalconphp.com/www/files/phalcon_x64_vc11_php5.6.0_2.1.0.RC1_nts.zip
            if (preg_match("#_php(\d+\.\d+\.\d+)_(\d+\.\d+\.\d+(.RC\d+)?)_nts#i", $node->attr('href'), $matches)) {

                $version = $matches[2];

                if (version_compare($version, $this->registry['phpext_phalcon']['latest']['version'], '>=') === true) {

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
        $phpversions = array('5.4.0', '5.5.0', '5.6.0', '7.0.0', '7.1.0');
        $urls        = array();

        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {
                $compiler = self::getCompilerByPHPVersion($phpversion);

                $replacedUrl = str_replace(
                    array('%compiler%', '%phpversion%', '%bitsize%'),
                    array($compiler, $phpversion, $bitsize),
                    $url
                );

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
