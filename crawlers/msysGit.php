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
 * msysGit (PortableGit) - Version Crawler
 */
class msysGit extends VersionCrawler
{
    public $url = 'https://github.com/msysgit/msysgit/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

            # https://github.com/msysgit/msysgit/releases/tag/Git-1.9.4-preview20140815

            if (preg_match("#PortableGit-(\d+\.\d+.\d+-\w+).7z#i", $node->text(), $matches)) {
                $version = $matches[1]; // 1.9.4-preview20140815
                if (version_compare($version, $this->registry['msysgit']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        # https://github.com/msysgit/msysgit/releases/download/Git-1.9.4-preview20140815/PortableGit-1.9.4-preview20140815.7z
                        'url' => 'https://github.com/msysgit/msysgit/releases/download/Git-' . $version . '/PortableGit-' . $version . '.7z',
                    );
                }
            }
        });
    }
}
