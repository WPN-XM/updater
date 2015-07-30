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

use WPNXM\Updater\Version;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
       /* Version contains only static methods. */
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
