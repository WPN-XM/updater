<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class ArrayUtil
{
    /**
     * Unsets null values and removes duplicates.
     *
     * @param  array $array
     * @return array
     */
    public static function clean(array $array)
    {
        $array = self::unsetNullValues($array);
        $array = self::removeDuplicates($array);

        return $array;
    }

    /**
     * Removes all keys with value "null" from the array and returns the array.
     *
     * @param $array Array
     * @return array
     */
    public static function unsetNullValues(array $array)
    {
        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Removes duplicates from the array.
     *
     * @param $array Array
     * @return array
     */
    public static function removeDuplicates(array $array)
    {
        return array_map("unserialize", array_unique(array_map("serialize", $array)));
    }

    /**
     * Strips EOL spaces from the content.
     * Note: PHP's var_export() adds EOL spaces after array keys, like "'key' => ".
     *       I consider this a PHP bug. Anyway. Let's get rid of that.
     * @param string $content
     */
    public static function removeTrailingSpaces($content)
    {
        $lines = explode("\n", $content);
        foreach ($lines as $idx => $line) {
            $lines[$idx] = rtrim($line, " ");
        }
        $content = implode("\n", $lines);

        return $content;
    }

    /**
     * This works on the array and moves the key to the top.
     *
     * @param array  $array
     * @param string $key
     */
    public static function move_key_to_top(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $temp  = array($key => $array[$key]);
            unset($array[$key]);
            $array = $temp + $array;
        }
    }

    /**
     * This works on the array and moves the key to the bottom.
     *
     * @param array  $array
     * @param string $key
     */
    public static function move_key_to_bottom(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $value       = $array[$key];
            unset($array[$key]);
            $array[$key] = $value;
        }
    }
    /**
     * Reduces an "Registry" array to contain only the versions
     * by dropping all other array keys.
     *
     * @param  array $array with multiple keys
     * @return array array with versions only
     */
    public static function reduceArrayToContainOnlyVersions(array $array)
    {
        unset($array['website'], $array['latest'], $array['name']);

        $array = array_reverse($array); // latest version first

        return $array;
    }
}
