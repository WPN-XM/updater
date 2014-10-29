<?php

namespace Tests;

class RegistryTest extends \PHPUnit_Framework_TestCase
{
	public $registry = array();

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->registry = include dirname(__DIR__) . '/registry/wpnxm-software-registry.php';
    }

    public function testRegistryIsValidArray()
    {
    	$this->assertTrue(is_array($this->registry));
    }
}