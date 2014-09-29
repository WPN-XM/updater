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
 * yuicompressor - Version Crawler
 */
class yuicompressor extends VersionCrawler
{
    // we are scraping the github releases page
    public $url = 'https://github.com/yui/yuicompressor/releases/latest';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {

                if (preg_match("#releases/download/v(\d+\.\d+.\d+)#", $node->text(), $matches)) {
                    $version = $matches[1];
                    if (version_compare($version, $this->registry['yuicompressor']['latest']['version'], '>=')) {
                        return array(
                            'version' => $version,
                            // https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar
                            'url'     => 'https://github.com/yui/yuicompressor/releases/download/v' . $version . '/yuicompressor-' . $version . '.jar'
                        );
                    }
                }
            });
    }

}
