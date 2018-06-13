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

class RegistryDataTest extends \PHPUnit\Framework\TestCase
{
    public $registry = array();

    /**
     * This test ensures that the git submodule "registry" and the registry itself is available.
     */
    protected function setUp()
    {
        $this->registry = include dirname(__DIR__) . '/data/registry/wpnxm-software-registry.php';
    }

    public function testRegistryIsValidArray()
    {
        $this->assertTrue(is_array($this->registry));
    }
}
