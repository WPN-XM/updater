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
 * QCacheGrind - Version Crawler
 *
 * Windows prebuilt binary of QCacheGrind (better known as KCacheGrind).
 *
 * Website: https://sourceforge.net/projects/qcachegrindwin/files/0.7.4/
 * Github:  https://github.com/ceefour/wincachegrind
 */
class qcachegrind_x86 extends VersionCrawler
{
    public $name = 'qcachegrind-x86';

    // we are scraping the sourceforge RSS feed
    public $url = 'https://sourceforge.net/projects/qcachegrindwin/rss';

    public function crawlVersion()
    {
        return $this->filterXPath('//channel//item//link')->each(function ($node) {
            $url = $node->text();

                /**
                 * Ok, another crazy non-semver undotted version append... "qcachegrind074"
                 *
                 * https://sourceforge.net/projects/qcachegrindwin/files/0.7.4/qcachegrind074-32bit-x86.zip
                 */
                if (preg_match("#qcachegrindwin/files/(\d+\.\d+\.\d+)/qcachegrind#", $url, $matches)) {
                    $version = $matches[1];
                    $undotted_version = str_replace('.', '', $version);

                    $download_file = 'https://sourceforge.net/projects/qcachegrindwin/files/'.$version.'/qcachegrind'.$undotted_version.'-32bit-x86.zip/download';

                    if (version_compare($version, $this->registry['qcachegrind-x86']['latest']['version'], '>=') === true) {
                        return array(
                            'version' => $version,
                            'url'     => $download_file,
                        );
                    }
                }
            });
    }
}
