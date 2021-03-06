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
 * RabbitMQ - Version Crawler
 */
class RabbitMq extends VersionCrawler
{
    public $name = 'rabbitmq';
    public $url = 'https://www.rabbitmq.com/releases/rabbitmq-server/current/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // https://www.rabbitmq.com/releases/rabbitmq-server/current/rabbitmq-server-windows-3.4.3.zip
            if (preg_match("#rabbitmq-server-windows-(\d+\.\d+\.\d+).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://www.rabbitmq.com/releases/rabbitmq-server/current/rabbitmq-server-windows-' . $version . '.zip',
                    );
                }
            }
        });
    }

    /**
     * RabbitMq release files are moved.
     *
     * https://www.rabbitmq.com/releases/rabbitmq-server/current/rabbitmq-server-windows-3.4.4.zip
     * https://www.rabbitmq.com/releases/rabbitmq-server/v3.4.3/rabbitmq-server-windows-3.4.3.zip
     *
     * That means, latest version must point to "/releases/rabbitmq-server/current/".
     * Every other version points to "/releases/rabbitmq-server/v{$version}/".
     */
    public function onAfterVersionInsert($registry)
    {
        foreach ($registry['rabbitmq'] as $version => $url) {
            // do not modify array key "latest"
            if ($version === 'latest') {
                continue;
            }
            // do not modify array key with "latest version" number
            if ($version === $registry['rabbitmq']['latest']['version']) {
                continue;
            }
            // replace the path on any other version
            $new_url = str_replace('releases/rabbitmq-server/current/', 'releases/rabbitmq-server/v' . $version . '/', $url);
            // insert at old array position = overwrite old url
            $registry['rabbitmq'][$version] = $new_url;
        }

        return $registry;
    }
}
