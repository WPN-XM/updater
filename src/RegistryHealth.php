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
	public static function check(array $registry)
    {
        foreach ($registry as $software => $component) {

            // Check for Keys
            // the following array keys have to exist for each component

            if (!isset($component['name'])) {
                echo 'The registry is missing the key "name" for Component "' . $software . '".';
            }

            if (!isset($component['website'])) {
                echo 'The registry is missing the key "website" for Component "' . $software . '".';
            }

            if (!isset($component['latest'])) {
                echo 'The registry is missing the key "latest" for Component "' . $software . '".';
            }

            if (!isset($component['latest']['url'])) {
                echo 'The registry is missing the key "url" of the "latest" array for Component "' . $software . '".';
            }

            if (!isset($component['latest']['version'])) {
                echo 'The registry is missing the key "url" of the "latest" array for Component "' . $software . '".';
            }

            // Check for Values
            // the following arrays must not be empty

            if (empty($component['latest']['url']) === true) {
                echo 'The registry is missing the values for ["latest"]["url"] array for Component "' . $software . '".';
            }

            if (empty($component['latest']['version']) === true) {
                echo 'The registry is missing the values for ["latest"]["version"] array for Component "' . $software . '".';
            }
        }

        return true;
    }
}