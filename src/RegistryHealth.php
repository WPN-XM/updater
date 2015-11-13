<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class RegistryHealth
{
    private $registry = [];

    private $errors = [];

    public function __construct(array $registry)
    {
        $this->registry = $registry;
    }

	public function check()
    {
        foreach ($this->registry as $software => $component) {

            // Check for Keys
            // the following array keys have to exist for each component

            if (!isset($component['name'])) {
                $this->errors[] = 'The registry is missing the key "name" for Component "' . $software . '".';
            }

            if (!isset($component['website'])) {
                $this->errors[] = 'The registry is missing the key "website" for Component "' . $software . '".';
            }

            if (!isset($component['latest'])) {
                $this->errors[] = 'The registry is missing the key "latest" for Component "' . $software . '".';
            }

            if (!isset($component['latest']['url'])) {
                $this->errors[] = 'The registry is missing the key "url" of the "latest" array for Component "' . $software . '".';
            }

            if (!isset($component['latest']['version'])) {
                $this->errors[] = 'The registry is missing the key "url" of the "latest" array for Component "' . $software . '".';
            }

            // Check for Values
            // the following arrays must not be empty

            if (empty($component['latest']['url']) === true) {
                $this->errors[] = 'The registry is missing the values for ["latest"]["url"] array for Component "' . $software . '".';
            }

            if (empty($component['latest']['version']) === true) {
                $this->errors[] = 'The registry is missing the values for ["latest"]["version"] array for Component "' . $software . '".';
            }

            // the following version entry should not exist
            if(isset($component['0.0.0']) === true) {
                $this->errors[] = 'The registry has an invalid version entry (0.0.0) for Component "' . $software . '".';
            }
        }

        return (bool) count($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}