<?php

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
            $lines[$idx] = rtrim($line);
        }
        $content = implode("\n", $lines);

        return $content;
    }
}
