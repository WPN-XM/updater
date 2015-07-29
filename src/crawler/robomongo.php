<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;


/**
 * Version Crawler for
 * RoboMongo - A Shell-centric cross-platform MongoDB management tool
 * http://robomongo.org/
 */
class robomongo extends VersionCrawler
{
    public $url = 'http://robomongo.org/download.html';

    public function crawlVersion()
    {
        return $this->filter('table a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+.\d+)#", $node->attr('href'), $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->registry['robomongo']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        // http://robomongo.org/files/windows/Robomongo-0.8.4-i386.zip
                        'url' => 'http://robomongo.org/files/windows/Robomongo-' . $version . '-i386.zip',
                    );
                }
            }
        });
    }
}
