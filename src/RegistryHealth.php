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

             if (!isset($component['name'])) {
                $this->errors[] = sprintf('The registry is missing the key "name" for Component "%s".', $software);
            }

            if (!isset($component['website'])) {
                $this->errors[] = sprintf('The registry is missing the key "website" for Component "%s".', $software);
            }

            // 1. the following array keys are required on each aliased component

            if (isset($component['alias'])) {
                if (!isset($component['info'])) {
                    $this->errors[] = sprintf('The registry is missing the key "info" for the aliased Component "%s".', $software);
                }
            }
            
            // 2. the following array keys are required on each (non-aliased) component

            if (!isset($component['latest'])) {
                $this->errors[] = sprintf('The registry is missing the key "latest" for Component "%s".', $software);
            }

            if (!isset($component['latest']['url'])) {
                $this->errors[] = sprintf('The registry is missing the key "url" of the "latest" array for Component "%s".', $software);
            }

            if (!isset($component['latest']['version'])) {
                $this->errors[] = sprintf('The registry is missing the key "url" of the "latest" array for Component "%s".', $software);
            }

            // Check for Values
            // the following arrays must not be empty

            if (empty($component['latest']['url']) === true) {
                $this->errors[] = sprintf('The registry is missing the values for ["latest"]["url"] array for Component "%s".', $software);
            }

            if (empty($component['latest']['version']) === true) {
                $this->errors[] = sprintf('The registry is missing the values for ["latest"]["version"] array for Component "%s".', $software);
            }

            // the following version entry should not exist
            if(isset($component['0.0.0']) === true) {
                $this->errors[] = sprintf('The registry has an invalid version entry (0.0.0) for Component "%s".', $software);
            }

            foreach($component as $version => $urls) {
                if(is_array($urls) && empty($urls)) {
                    $this->errors[] = sprintf('The registry has an invalid version entry (empty) '.$version.' for Component "%s".', $software);
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