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
 * Apache ActiveMQ - Version Crawler
 *
 * Apache ActiveMQ is a open source messaging and Integration Patterns server.
 *
 * Website:       http://activemq.apache.org/
 * Download Repo: http://archive.apache.org/dist/activemq/
 */
class ActiveMQ extends VersionCrawler
{
    public $name = 'activemq';

    public $url = 'http://archive.apache.org/dist/activemq/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->text();
            // http://archive.apache.org/dist/activemq/5.14.4/
            if (preg_match("#(\d+\.\d+.\d+)#i", $url, $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // http://archive.apache.org/dist/activemq/5.14.3/apache-activemq-5.14.3-bin.zip
                        'url'     => 'http://archive.apache.org/dist/activemq/' . $version . '/apache-activemq-' . $version . '-bin.zip'
                    );
                }
            }
        });
    }
}
