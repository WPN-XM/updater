<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

use WPNXM\Updater\VersionCrawler;

/**
 * FireBirdSQL - Version Crawler
 *
 * Firebird is a relational database offering many ANSI SQL standard features.
 * Firebird offers excellent concurrency, high performance, and powerful language support
 * for stored procedures and triggers.
 *
 * Website: http://www.firebirdsql.org/
 */
class Firebird extends VersionCrawler
{
    public $name = 'firebird';

    /**
     * Download:    http://www.firebirdsql.org/en/firebird-2-5/
     * SourceForge: http://sourceforge.net/projects/firebird/files/firebird-win32/
     * RSS:         http://sourceforge.net/projects/firebird/rss?path=/firebird-win32
     */
    public $url = 'http://sourceforge.net/projects/firebird/rss?path=/firebird-win32';

    public function crawlVersion()
    {
        return $this->filterXPath('//channel//item//link')->each(function ($node) {
            $url = $node->text();
            if (preg_match("#firebird-win32/(\d+\.\d+(.\d|-RC1)+)/Firebird-#i", $url, $matches)) {
                $version = $matches[1];
                if (version_compare($version, $this->registry['firebird']['latest']['version'], '>=') === true) {
                    return array(
                        'version' => $version,
                        'url'     => $url,
                    );
                }
            }
        });
    }
}
