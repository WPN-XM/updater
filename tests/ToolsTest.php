<?php

namespace Tests;

class ToolsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        include_once dirname(__DIR__) . '/tools.php';
    }

    protected function getJson1()
    {
        $array =  array(
          'composer' => array(
          'name' => 'Composer',
          'website' => 'http://getcomposer.org/',
          '1.0' => 'http://getcomposer.org/composer.phar',
          'latest' => array(
            'version' => '1.0',
            'url' => 'http://getcomposer.org/composer.phar',
          ),
        ));

        return json_encode($array);
    }

    protected function getJsonPrettyPrintCompact1()
    {
        return '{ "composer": ' . PHP_EOL .
               '{ "name": "Composer", "website": "http:\/\/getcomposer.org\/", "1.0": "http:\/\/getcomposer.org\/composer.phar", "latest": ' . PHP_EOL .
               '{ "version": "1.0", "url": "http:\/\/getcomposer.org\/composer.phar"  } }' . PHP_EOL .
               '}';
    }
    
    protected function getJson2()
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
        $json = $this->getJson1();
        $expected = $this->getJsonPrettyPrintCompact1();        
        $string = \JsonHelper::jsonPrettyPrintCompact($json);
        $this->assertSame($expected, $string);
    }
    
    public function testjsonPrettyPrintCompact2()
    {
        $json = $this->getJson2();
        $expected = $this->getJsonPrettyPrintCompact2();        
        $string = \JsonHelper::jsonPrettyPrintCompact($json);
        $this->assertSame($expected, $string);
    }

    public function testJsonPrettyPrintTableFormat()
    {
        $json = $this->getJson2();
        $jsonCompact = \JsonHelper::jsonPrettyPrintCompact($json);        
        $expected = $this->getJsonPrettyPrintTableFormatResult();        
        $string = \JsonHelper::jsonPrettyPrintTableFormat($jsonCompact);        
        $this->assertEquals($expected, $string);
    }
}