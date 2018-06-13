<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace tests;

use WPNXM\Updater\FileCache;

class FileCacheTest extends \PHPUnit\Framework\TestCase
{
    private $cacheFile = '';

    protected function setUp()
    {
        $this->cacheFile = __DIR__ . '/phpunit.xml.dist.cache';
    }

    protected function tearDown()
    {
        unlink($this->cacheFile);
    }

    public function testGet()
    {
        // callback to modify the fetched content, before caching it
        $modificationCallback = function ($content) {
            return str_replace('phpunit', 'modified-string', $content);
        };

        $result = FileCache::get(
            __DIR__ . '/phpunit.xml.dist',
            $this->cacheFile,
            $modificationCallback
        );

        $this->assertContains('modified-string', $result);
        $this->assertContains('convertErrorsToExceptions', $result);
    }
}
