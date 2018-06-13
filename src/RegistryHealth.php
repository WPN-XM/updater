<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
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
        foreach ($this->registry as $software => $component) 
        {
            // Check for Keys
            // the following array keys are required on each component

            foreach (array('name', 'website', 'latest') as $key) {
                if (!isset($component[$key])) {
                    $this->errors[] = sprintf('The registry is missing the key "%s" for Component "%s".', $key, $software);
                }
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

            foreach($component as $version => $urls) {
                if(is_array($urls) && empty($urls)) {
                    $this->errors[] = 'The registry has an invalid version entry (empty) '.$version.' for Component "' . $software . '".';
                }
            }
        }

        return ! (bool) count($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}