<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

define('TIME_STARTED', microtime(true));

// Settings for the PHP environment
set_time_limit(180); // 60*3
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Constants
define('DATA_DIR', dirname(__DIR__) . '/data/');
define('REGISTRY_DIR', DATA_DIR . 'registry/');
define('VENDOR_DIR', dirname(__DIR__) . '/vendor/');

// Register Composer Autoloader
if (!is_file(VENDOR_DIR . 'autoload.php')) {
    throw new \RuntimeException(
        '[Error] Bootstrap: Could not find "vendor/autoload.php".' . PHP_EOL .
        'Did you forget to run "composer install --dev"?' . PHP_EOL
    );
}
require VENDOR_DIR . 'autoload.php';

// Start Application
require __DIR__ . '/Application.php';
WPNXM\Updater\Application::run();  