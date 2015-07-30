<?php

namespace tests;

class RegistryDataTest extends \PHPUnit_Framework_TestCase
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
