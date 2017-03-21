<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2017 Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * PHP Extensions "PDO_SQLSRV" - Version Crawler
 *
 * Website:   https://github.com/Azure/msphpsql
 * Downloads: https://github.com/Azure/msphpsql/releases
 * PECL:      http://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/
 */
class phpext_pdo_sqlsrv extends VersionCrawler
{
    public $url = 'http://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/';

    private $url_template = 'http://windows.php.net/downloads/pecl/releases/pdo_sqlsrv/%version%/php_pdo_sqlsrv-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                if (version_compare($version, $this->registry['phpext_pdo_sqlsrv']['latest']['version'], '>=') === true)  {

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
