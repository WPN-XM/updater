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
 * phpMyAdmin - Version Crawler
 */
class phpmyadmin extends VersionCrawler
{
    public $url = 'http://www.phpmyadmin.net/home_page/downloads.php';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(beta|b|rc|alpha|a|patch|pl|p)?(\d+)(?:[.-]?(\d+))?)?([.-]?dev)?#i", $node->text(), $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->registry['phpmyadmin']['latest']['version'], '>=')) {
                    return array(
                        'version' => $version,
                        'url' => 'http://switch.dl.sourceforge.net/project/phpmyadmin/phpMyAdmin/'.$version.'/phpMyAdmin-'.$version.'-english.zip'
                    );
                }
            }
        });
    }
}
