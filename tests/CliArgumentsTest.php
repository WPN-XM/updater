<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace tests;

use WPNXM\Updater\CliArguments;

/**
  * @property mixed $a
  */
class TestOfCommandLineArgumentParsing extends \PHPUnit\Framework\TestCase
{
    public function testNoArgumentsWithJustProgramNameGivesFalseToEveryName()
    {
        $arguments = new CliArguments(['me']);
        $this->assertEquals($arguments->a, false);
        $this->assertEquals($arguments->all(), []);
    }

    public function testSingleArgumentNameDefaultToTrue()
    {
        $arguments = new CliArguments(['me', '-a']);
        $this->assertEquals($arguments->a, true);
    }

    public function testSingleArgumentAcceptsValue()
    {
        $arguments = new CliArguments(['me', '-a=AAA']);
        $this->assertEquals($arguments->a, 'AAA');
    }

    public function testSingleArgumentAcceptsSpaceSeparatedValue()
    {
        $arguments = new CliArguments(['me', '-a', 'AAA']);
        $this->assertEquals($arguments->a, 'AAA');
    }

    public function testBuildsArrayFromRepeatedValue()
    {
        $arguments = new CliArguments(['me', '-a', 'A', '-a', 'AA']);
        $this->assertEquals($arguments->a, ['A', 'AA']);
    }

    public function testBuildsArrayFromMultiplyRepeatedValues()
    {
        $arguments = new CliArguments(['me', '-a', 'A', '-a', 'AA', '-a', 'AAA']);
        $this->assertEquals($arguments->a, ['A', 'AA', 'AAA']);
    }

    public function testCanParseLongFormArguments()
    {
        $arguments = new CliArguments(['me', '--aa=AA', '--bb', 'BB']);
        $this->assertEquals($arguments->aa, 'AA');
        $this->assertEquals($arguments->bb, 'BB');
    }

    public function testGetsFullSetOfResultsAsHash()
    {
        $arguments = new CliArguments(['me', '-a', '-b=1', '--aa=AA', '--bb=BB', '-c']);
        $this->assertEquals($arguments->all(), ['a' => true, 'b' => '1', 'aa' => 'AA', 'bb' => 'BB', 'c' => true]);
    }
}