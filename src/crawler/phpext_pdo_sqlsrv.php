<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2017 Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * PHP Extensions "PDO_SQLSRV" - Version Crawler
 *
 * Website:   https://github.com/Azure/msphpsql
 * Downloads: https://github.com/Azure/msphpsql/releases
 * PECL:      https://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/
 */
class phpext_pdo_sqlsrv extends VersionCrawler
{
	public $name = 'phpext_pdo_sqlsrv';
	
    public $url = 'https://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/';

    private $url_template = 'https://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/%version%/php_pdo_sqlsrv-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                if (version_compare($version, $this->latestVersion, '>=') === true)  {

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
}
