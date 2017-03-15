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

use WPNXM\Updater\RegistryHealth;

class RegistryHealthTest extends \PHPUnit_Framework_TestCase
{
    public function testCheck()
    {
        $registry = [
           'component1' => [
                'name' => 'SoftwareName1',
                'website' => 'http://website1.org',
                'latest' => [
                    'version' => '1.0.0',
                    'url' => 'http://dl-1.0.0.zip'
                ]
            ],
            'component2'=> [
                'name' => 'SoftwareName2',
                'website' => 'http://website2.org',
                'latest' => [
                    'version' => '1.0.0',
                    'url' => 'http://dl-1.0.0.zip'
                ]
            ]
        ];

        $registryHealth = new RegistryHealth($registry);
        $registryHealth->check();

        $errors = $registryHealth->getErrors();

        $this->assertEmpty($errors);
    }

    public function testGetErrors()
    {
        $registry = [
           'component1' => [
                'website' => 'http://website1.org',
                'latest' => [
                    'version' => '1.0.0',
                    'url' => 'http://dl-1.0.0.zip'
                ]
            ],
            'component2'=> [
                'website' => 'http://website2.org',
                'latest' => [
                    'version' => '1.0.0',
                    'url' => 'http://dl-1.0.0.zip'
                ]
            ]
        ];

        $registryHealth = new RegistryHealth($registry);
        $registryHealth->check();

        $errors = $registryHealth->getErrors();

        $this->assertSame('The registry is missing the key "name" for Component "component1".', $errors[0]);
        $this->assertSame('The registry is missing the key "name" for Component "component2".', $errors[1]);
    }
}
