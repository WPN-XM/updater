<?php

namespace tests;

class VersionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        include_once dirname(__DIR__) . '/tools.php';
    }

    public function test_version_compare_imagick()
    {
        $oldVersion = '6.8.9-0';
        $newVersion = '6.9.0-0';
        $this->assertTrue(\Version::cmpImagick($oldVersion, $newVersion));

        $oldVersion = '1.2.3-1';
        $newVersion = '1.2.3-4';
        $this->assertTrue(\Version::cmpImagick($oldVersion, $newVersion));
    }
}
