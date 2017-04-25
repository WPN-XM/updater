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
 * Apache Ant - Version Crawler
 *
 * Apache Ant is a Java library and command-line tool,
 * whose mission is to drive processes described in build files 
 * as targets and extension points dependent upon each other.
 *
 * Website:       http://ant.apache.org/
 * Download: http://ant.apache.org/bindownload.cgi
 */
class Ant extends VersionCrawler
{
    public $name = 'ant';

    public $url = 'http://ftp.halifax.rwth-aachen.de/apache/ant/binaries/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->text();
            if (preg_match("#(\d+\.\d+.\d+)#i", $url, $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // http://ftp.halifax.rwth-aachen.de/apache/ant/binaries/apache-ant-1.9.9-bin.zip
                        'url'     => 'http://ftp.halifax.rwth-aachen.de/apache/ant/binaries/apache-ant-' . $version . '-bin.zip'
                    );
                }
            }
        });
    }
}
