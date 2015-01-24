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
 * RabbitMQ - Version Crawler
 */
class RabbitMq extends VersionCrawler
{

    public $url = 'https://www.rabbitmq.com/nightlies/rabbitmq-server/current/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            // https://www.rabbitmq.com/nightlies/rabbitmq-server/current/rabbitmq-server-windows-3.4.2.51207.zip
            // major.minior.path.svn-commit
            if (preg_match("#rabbitmq-server-windows-(\d+\.\d+\.\d+\.\d+).zip$#", $node->attr('href'), $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['rabbitmq']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url' => 'https://www.rabbitmq.com/nightlies/rabbitmq-server/current/rabbitmq-server-windows-'.$version.'.zip'
                    );
                }
            }
        });
    }

}
