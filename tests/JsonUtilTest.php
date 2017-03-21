<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace tests;

use WPNXM\Updater\JsonUtil;

class JsonUtilTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        // JsonUtil contains only static methods.
    }

    protected function getJsonExampleData1()
    {
        $array =  array(
          'composer' => array(
          'name'    => 'Composer',
          'website' => 'http://getcomposer.org/',
          '1.0'     => 'http://getcomposer.org/composer.phar',
          'latest'  => array(
            'version' => '1.0',
            'url'     => 'http://getcomposer.org/composer.phar',
          ),
        ), );

        return json_encode($array);
    }

    protected function getJsonPrettyPrintCompact1()
    {
        return '{ "composer": ' . PHP_EOL .
               '{ "name": "Composer", "website": "http:\/\/getcomposer.org\/", "1.0": "http:\/\/getcomposer.org\/composer.phar", "latest": ' . PHP_EOL .
               '{ "version": "1.0", "url": "http:\/\/getcomposer.org\/composer.phar"  } }' . PHP_EOL .
               '}';
    }

    protected function getJsonExampleData2()
    {
        $array = array(
            0 => array('adminer', 'http://wpn-xm.org/get.php?s=adminer&v=4.1.0', 'adminer.php', '4.1.0'),
            1 => array('phpext_uploadprogress', 'http://wpn-xm.org/get.php?s=phpext_uploadprogress&v=1.0.3.1&p=5.4', 'phpext_uploadprogress.zip', '1.0.3.1'),
        );

        return json_encode($array);
    }

    protected function getJsonPrettyPrintCompact2()
    {
        return '[ ' . PHP_EOL .
               '[ "adminer", "http:\/\/wpn-xm.org\/get.php?s=adminer&v=4.1.0", "adminer.php", "4.1.0" ], ' . PHP_EOL .
               '[ "phpext_uploadprogress", "http:\/\/wpn-xm.org\/get.php?s=phpext_uploadprogress&v=1.0.3.1&p=5.4", "phpext_uploadprogress.zip", "1.0.3.1" ]' . PHP_EOL .
               ']';
    }

    protected function getJsonPrettyPrintTableFormatResult()
    {
        return '[' . PHP_EOL .
               '[ "adminer",                "http:\/\/wpn-xm.org\/get.php?s=adminer&v=4.1.0",                        "adminer.php",                "4.1.0" ],' . PHP_EOL .
               '[ "phpext_uploadprogress",  "http:\/\/wpn-xm.org\/get.php?s=phpext_uploadprogress&v=1.0.3.1&p=5.4",  "phpext_uploadprogress.zip",  "1.0.3.1" ]' . PHP_EOL .
               ']';
    }

    public function testjsonPrettyPrintCompact1()
    {
        $json     = $this->getJsonExampleData1();
        $expected = $this->getJsonPrettyPrintCompact1();
        $string   = JsonUtil::prettyPrintCompact($json);
        $this->assertSame($expected, $string);
    }

    public function testjsonPrettyPrintCompact2()
    {
        $json     = $this->getJsonExampleData2();
        $expected = $this->getJsonPrettyPrintCompact2();
        $string   = JsonUtil::prettyPrintCompact($json);
        $this->assertSame($expected, $string);
    }

    public function testJsonPrettyPrintTableFormat()
    {
        $json        = $this->getJsonExampleData2();
        $jsonCompact = JsonUtil::prettyPrintCompact($json);
        $expected    = $this->getJsonPrettyPrintTableFormatResult();
        $string      = JsonUtil::prettyPrintTableFormat($jsonCompact);
        $this->assertEquals($expected, $string);
    }
}
