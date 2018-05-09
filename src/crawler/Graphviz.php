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
 * GraphViz - Version Crawler
 *
 * GraphViz - Graph Visualization Software.
 *
 * Website:       http://www.graphviz.org/
 * Downloads:     https://graphviz.gitlab.io/_pages/Download/Download_windows.html
 */
class GraphViz extends VersionCrawler
{
    public $name = 'graphviz';

    public $url = 'https://graphviz.gitlab.io/_pages/Download/Download_windows.html';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->text();
            if (preg_match("#(\d+\.\d+(.\d+)?).zip#i", $url, $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // formerly:  http://www.graphviz.org/pub/graphviz/stable/windows/graphviz-2.38.zip
                        //            https://graphviz.gitlab.io/_pages/Download/windows/graphviz-2.38.zip
                        'url'     => 'https://graphviz.gitlab.io/_pages/Download/windows/graphviz-' . $version . '.zip'
                    );
                }
            }
        });
    }
}
