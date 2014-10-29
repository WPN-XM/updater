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

    protected function getJsonStringA()
    {
        return '{"menu": {
                  "id": "file",
                  "value": "File",
                  "popup": {
                    "menuitem": [
                      {"value": "New", "onclick": "CreateNewDoc()"},
                      {"value": "Open", "onclick": "OpenDoc()"},
                      {"value": "Close", "onclick": "CloseDoc()"}
                    ]
                  }
                }}';
    }

    protected function getJsonStringAExpected()
    {
        return '[
"id": "file","value": "File","popup": {"menuitem":[{"value": "New""onclick": "CreateNewDoc()"},{"value": "Open""onclick": "OpenDoc()"},{"value": "Close""onclick": "CloseDoc()"}]}
]';
    }

    public function testjsonPrettyPrintCompact()
    {
    	$json = $this->getJsonStringA();
        $expected = $this->getJsonStringAExpected();

    	$string = \JsonHelper::jsonPrettyPrintCompact($json);

        $this->assertEquals($expected, $string);
    }



    public function testJsonPrettyPrintTableFormat()
    {
    	$json = $this->getJsonStringA();
        $expected = $this->getJsonStringAExpected();

    	$string = \JsonHelper::jsonPrettyPrintTableFormat($json);

        $this->assertEquals($expected, $string);
    }
}