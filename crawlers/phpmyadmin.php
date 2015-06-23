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
            /**
             * The old regexp includes alpha and beta releases, too
             * #(\d+\.\d+(\.\d+)*)(?:[._-]?(beta|b|rc|alpha|a|patch|pl|p)?(\d+)(?:[.-]?(\d+))?)?([.-]?dev)?#i
             * The problem is that these files are released and then deleted, when a new stable version is released.
             * We are switching to stable releases only, so that all the links to files remain valid in our registry.
             */

            if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->text(), $matches)) {
                $version = $matches[0];

                // Website: https://files.phpmyadmin.net/phpMyAdmin/4.4.10/phpMyAdmin-4.4.10-english.zip
                // SF Mirrors Main: http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/4.4.10/phpMyAdmin-4.4.10-english.7z/download
                // 'http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/' . $version . '/phpMyAdmin-' . $version . '-english.7z/download'

                if (version_compare($version, $this->registry['phpmyadmin']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => 'https://files.phpmyadmin.net/phpMyAdmin/' . $version . '/phpMyAdmin-' . $version . '-english.zip',
                    );
                }
            }
        });
    }
}
