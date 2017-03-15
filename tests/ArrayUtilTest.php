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
}
