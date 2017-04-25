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
 * Downloads: http://www.graphviz.org/Download_windows.php
 */
class GraphViz extends VersionCrawler
{
    public $name = 'graphviz';

    public $url = 'http://www.graphviz.org/pub/graphviz/stable/windows/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node) {
            $url = $node->text();
            if (preg_match("#(\d+\.\d+(.\d+)?)#i", $url, $matches)) {
                $version = $matches[0];
                if (version_compare($version, $this->latestVersion, '>=') === true) {
                    return array(
                        'version' => $version,
                        // http://www.graphviz.org/pub/graphviz/stable/windows/graphviz-2.38.zip
                        'url'     => 'http://www.graphviz.org/pub/graphviz/stable/windows/graphviz-' . $version . '.zip'
                    );
                }
            }
        });
    }
}
