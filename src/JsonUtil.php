<?php

namespace WPNXM\Updater;

class Json
{
    /**
     * Returns compacted, pretty printed JSON data.
     * Yes, there is JSON_PRETTY_PRINT, but it is odd at printing compact.
     *
     * @param  string $json The unpretty JSON encoded string.
     * @return string Pretty printed JSON.
     */
    public static function prettyPrintCompact($json)
    {
        $out   = '';
        $cnt   = 0;
        $tab   = 1;
        $len   = strlen($json);
        $space = ' ';
        $k     = strlen($space) ? strlen($space) : 1;

        for ($i = 0; $i <= $len; $i++) {
            $char = substr($json, $i, 1);

            if ($char === '}' || $char === ']') {
                $cnt--;
                // newline before last ]
                $out .= ($i + 1 === $len) ? PHP_EOL : str_pad('', ($tab * $cnt * $k), $space);
            } elseif ($char === '{' || $char === '[') {
                $cnt++;
                $out .= ($cnt > 1) ? PHP_EOL : ''; // no newline on first line
            }

            $out .= $char;

            if ($char === ',' || $char === '{' || $char === '[') {
                $out .= ($cnt >= 1) ? $space : '';
            }
            if ($char === ':' && '\\' !== substr($json, $i + 1, 1)) {
                $out .= ' ';
            }
        }

        return $out;
    }

    /**
     * JSON Table Format
     * Like "tab separated value" (TSV) format, BUT with spaces :)
     * Aligns values correctly underneath each other.
     *
     * @param  string $json
     * @return string
     */
    public static function prettyPrintTableFormat($json)
    {
        $lines = explode(PHP_EOL, $json);

        $array = array();

        // count lengths and set to array
        foreach ($lines as $line) {
            $line       = trim($line);
            $commas     = explode(", ", $line);
            $keyLengths = array_map('strlen', array_values($commas));
            $array[]    = array('lines' => $commas, 'lengths' => $keyLengths);
        }

        // calculate the number of missing spaces
        $numberOfSpacesToAdd = function ($longest_line_length, $line_length) {
            return ($longest_line_length - $line_length) + 2; // were the magic happens
        };

        // append certain number of spaces to string
        $appendSpaces = function ($num, $string) {
            for ($i = 0; $i <= $num; $i++) {
                $string .= ' ';
            }

            return $string;
        };

        // chop of first and last element of the array: the brackets [,]
        unset($array[0]);
        $last_nr = count($array);
        unset($array[$last_nr]);

        // walk through multi-dim array and compare key lengths
        // build array with longest key lengths
        $elements = $last_nr - 1;
        $num_keys = count($array[1]['lines']) - 1;
        $longest  = array();

        for ($i = 1; $i <= $elements; $i++) {
            for ($j = 0; $j < $num_keys; $j++) {
                $key_length = $array[$i]['lengths'][$j];
                if (isset($longest[$j]) === true && $longest[$j] >= $key_length) {
                    continue;
                }
                $longest[$j] = $key_length;
            }
        }

        // appends the missing number of spaces to the elements
        // to align them correctly underneath each other
        for ($i = 1; $i <= $elements; $i++) {
            for ($j = 0; $j < $num_keys; $j++) {
                // append spaces to the element
                $newElement = $appendSpaces(
                    $numberOfSpacesToAdd($longest[$j], $array[$i]['lengths'][$j]), $array[$i]['lines'][$j]
                );

                // reinsert the element
                $array[$i]['lines'][$j] = $newElement;
                //$array[$i]['lengths'][$j] = $longest[$j];
            }
        }

        // build output string from array
        $lines = '';
        foreach ($array as $idx => $values) {
            foreach ($values['lines'] as $key => $value) {
                $lines .= $value;
            }
        }

        // reinsert commas
        $lines = str_replace('"  ', '", ', $lines);

        // remove spaces before '['
        $lines = preg_replace('#\s+\[#i', '[', $lines);

        // cleanups
        $lines = str_replace(array(',,', '],'), array(',', '],' . PHP_EOL), $lines);

        $lines = '[' . PHP_EOL . trim($lines) . PHP_EOL . ']';

        return $lines;
    }
}