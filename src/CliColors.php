<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens A. Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class CliColors
{
    protected static $ANSI_CODES = [
        'bold'       => 1,
        'italic'     => 3,
        'underline'  => 4,
        'blink'      => 5,
        'inverse'    => 7,
        'hidden'     => 8,
        'black'      => 30,
        'red'        => 31,
        'green'      => 32,
        'yellow'     => 33,
        'blue'       => 34,
        'magenta'    => 35,
        'cyan'       => 36,
        'white'      => 37,
        'gray'       => 90,
        'brightred'     => 91,
        'brightgreen'   => 92,
        'brightyellow'  => 93,
        'brightblue'    => 94,
        'brightmagenta' => 95,
        'brightcyan'    => 96,
        'brightwhite'   => 97,
        'black_bg'   => 40,
        'red_bg'     => 41,
        'green_bg'   => 42,
        'yellow_bg'  => 43,
        'blue_bg'    => 44,
        'magenta_bg' => 45,
        'cyan_bg'    => 46,
        'white_bg'   => 47
    ];

    public static function write($string, $color)
    {
        if(!self::hasCliColorSupport()) {
            return $string;
        }

        return chr(27) . "[".self::$ANSI_CODES[$color]."m" . $string . chr(27) . "[0m";
    }

    public static function log($message, $color)
    {
        error_log(self::set($message, $color));
    }

    public static function replace($full_text, $search_regexp, $color)
    {
        $new_text = preg_replace_callback(
            "/($search_regexp)/",
            function ($matches) use ($color) {
                return Color::set($matches[1], $color);
            },
            $full_text
        );
        return (null === $new_text) ? $full_text : $new_text;
    }

    /**
     * Returns true, if CLI colors are supported.
     *
     * @return bool True, if CLI colors are supported, otherwise false.
     */
    protected static function hasCliColorSupport()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return
                '10.0.10586' === PHP_WINDOWS_VERSION_MAJOR.'.'.PHP_WINDOWS_VERSION_MINOR.'.'.PHP_WINDOWS_VERSION_BUILD
                || false !== getenv('ANSICON')
                || 'ON' === getenv('ConEmuANSI')
                || 'xterm' === getenv('TERM');
        }
        return function_exists('posix_isatty') && @posix_isatty($this->stream);
    }
}