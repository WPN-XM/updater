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
     * http://static.phalconphp.com/files/
     */
    public $url = 'http://static.phalconphp.com/files/';

    // http://static.phalconphp.com/files/phalcon_x86_VC9_php5.4.0_1.3.1_nts.zip
    private $url_template = 'http://static.phalconphp.com/files/phalcon_%bitsize%_%compiler%_php%phpversion%_%version%_nts.zip';

    public $needsOnlyRegistrySubset = false;

    public $needsGuzzle = true;

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // there are "rc" versions, but we don't take them into account
            if (preg_match("#php5.4.0_(\d+\.\d+(\.\d+)*)#i", $node->text(), $matches))
            {
                $version = $matches[1];

                if (version_compare($version, $this->registry['phpext_xcache']['latest']['version'], '>='))
                {
                    return array(
                        'version' => $version,
                        'url'     => $this->createPhpVersionsArrayForExtension($version, $this->url_template)
                    );
                }
            }
        });
    }
}