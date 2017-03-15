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

class RegistryDataTest extends PHPUnit\Framework\TestCase
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
