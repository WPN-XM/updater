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

use WPNXM\Updater\ApplicationVersion;

class ApplicationVersionTest extends PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $version = ApplicationVersion::get();

        // v1.2.3-dev.61b37ad (2017-03-15 06:03:05)
        $this->assertStringStartsWith('v', $version);
        $this->assertStringEndsWith(')', $version);
    }
}
