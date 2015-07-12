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
 * Phalcon - Version Crawler
 */
class phpext_phalcon extends VersionCrawler
{
    /**
     * http://phalconphp.com/en/download/windows
     * http://static.phalconphp.com/files/ - now forbidden - thanks a lot.
     */
    public $url = 'https://phalconphp.com/en/download/windows';

    // http://static.phalconphp.com/files/phalcon_x86_VC9_php5.4.0_1.3.1_nts.zip
    private $url_template = 'https://static.phalconphp.com/www/files/phalcon_%bitsize%_%compiler%_php%phpversion%_%version%_nts.zip';

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // there are "rc" versions, but we don't take them into account
            if (preg_match("#_php(\d+\.\d+\.\d+)_(\d+\.\d+\.\d+)#i", $node->attr('href'), $matches)) {

                $phpversion = $matches[2];
                $version = $matches[2];

                if (version_compare($version, $this->registry['phpext_phalcon']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template),
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
        $phpversions = array('5.4.0', '5.5.0', '5.6.0');
        $urls        = array();

        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {
                $compiler = ($phpversion === '5.4.0') ? 'VC9' : 'vc11';

                $replacedUrl = str_replace(
                    array('%compiler%', '%phpversion%', '%bitsize%'),
                    array($compiler, $phpversion, $bitsize),
                    $url
                );

                #if ($skipURLcheck === true) {
                    $urls[$bitsize][$phpversion] = $replacedUrl;
                #} elseif($this->fileExistsOnServer($replacedUrl) === true) {
                #    $urls[$bitsize][$phpversion] = $replacedUrl;
                #}
            }
        }

        return $urls;
    }
}
