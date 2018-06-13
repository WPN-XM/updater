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

use WPNXM\Updater\Version;

class VersionTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
       /* Version contains only static methods. */
    }

    public function test_version_compare_openssl()
    {
        $oldVersion = '1.0.0a';
        $newVersion = '1.0.0z';
        $this->assertTrue(Version::compare('openssl', $oldVersion, $newVersion));
        $this->assertTrue(Version::compare('openssl-x64', $oldVersion, $newVersion));
    }

    public function test_version_compare_imagick()
    {
        $oldVersion = '6.8.9-0';
        $newVersion = '6.9.0-0';
        $this->assertTrue(Version::cmpImagick($oldVersion, $newVersion));

        $oldVersion = '1.2.3-1';
        $newVersion = '1.2.3-4';
        $this->assertTrue(Version::cmpImagick($oldVersion, $newVersion));
    }
}
