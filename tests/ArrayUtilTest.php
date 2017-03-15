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

use WPNXM\Updater\ArrayUtil;

class ArrayUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testClean()
    {
        $array = [
            '1' => 'string',
            '2' => NULL,
            '3' => array('abc', 'def'),
            '4' => array('abc', 'def'),
        ];

        $cleaned_array = ArrayUtil::clean($array);

        $this->assertArrayHasKey('1', $cleaned_array);
        $this->assertArrayNotHasKey('2', $cleaned_array); // removed by unsetNullValues
        $this->assertArrayHasKey('3', $cleaned_array);
        $this->assertArrayNotHasKey('4', $cleaned_array); // removed by removeDuplicates
    }

    public function testMove_key_to_top()
    {
        $array = [
            'top'    => '1',
            'middle' => '2',
            'bottom' => '3'
        ];

        ArrayUtil::move_key_to_top($array, 'bottom');

        $this->assertArrayHasKey('bottom', $array); // untestable ;)
    }

    public function testMove_key_to_bottom()
    {
        $array = [
            'top'    => '1',
            'middle' => '2',
            'bottom' => '3'
        ];

        ArrayUtil::move_key_to_bottom($array, 'top');

        $this->assertArrayHasKey('top', $array); // untestable ;)
    }

    public function testRemoveTrailingSpaces()
    {
        $content = "'key' => \n'key' => ";
        $result = ArrayUtil::removeTrailingSpaces($content);
        $this->assertSame($result, "'key' =>\n'key' =>");
    }

    public function testReduceArrayToContainOnlyVersions()
    {
        $array = [
            'website' => 'website',
            'name'    => 'name',
            '1.2.3'   => 'url', // reduce to only this key
            'latest'  => [
                'version' => '1.2.3',
                'url'     => 'url'
            ]
        ];

        $result = ArrayUtil::ReduceArrayToContainOnlyVersions($array);

        $this->assertSame($result, ['1.2.3' => 'url']);
    }
}
