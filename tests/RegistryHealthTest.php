<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace tests;

use WPNXM\Updater\RegistryHealth;

class RegistryHealthTest extends \PHPUnit\Framework\TestCase
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

        $this->assertTrue($registryHealth->check());

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

        $this->assertFalse($registryHealth->check());

        $errors = $registryHealth->getErrors();

        $this->assertSame('The registry is missing the key "name" for Component "component1".', $errors[0]);
        $this->assertSame('The registry is missing the key "name" for Component "component2".', $errors[1]);
    }
}
