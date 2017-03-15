<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
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
