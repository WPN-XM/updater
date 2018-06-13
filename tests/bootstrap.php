<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

// Error Reporting Level
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

// add "subject under test" and "tests" to the include path
$sut   = realpath(dirname(__DIR__));
$tests = realpath(__DIR__ . '/../tests');

$paths = array(
    $sut,
    $tests,
    get_include_path(), // attach original include paths
);
set_include_path(implode(PATH_SEPARATOR, $paths));

// Composer Autoloader
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} else {
    echo '[Error] Updater > Tests > Bootstrap: Could not find "vendor/autoload.php".' . PHP_EOL;
    echo 'Did you forget to run "composer install --dev"?' . PHP_EOL;
    exit(1);
}
