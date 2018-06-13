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

use WPNXM\Updater\ApplicationVersion;

class ApplicationVersionTest extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $version = ApplicationVersion::get();

        // v1.2.3-dev.61b37ad (2017-03-15 06:03:05)
        $this->assertStringStartsWith('v', $version);
        $this->assertStringEndsWith(')', $version);
    }
}
