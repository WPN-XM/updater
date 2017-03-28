<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

if(!extension_loaded('openssl')) {
    echo 'The PHP extension "OpenSSL" is required.';
    exit(1);
}

if(!extension_loaded('curl')) {
    echo 'The PHP extension "cURL" is required.';
    exit(1);
}

if(ini_get('curl.cainfo') == "") {
    echo 'The PHP extension "cURL" has an SSL certificate problem.<br>'
       . 'Please add a local issuer certificate<br>'
       . 'and set the php.ini directive: `curl.cainfo` accordingly.';
    exit(1);
}

define('TIME_STARTED', microtime(true));

// Settings for the PHP environment
set_time_limit(180); // 60*3
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Constants
define('DS', DIRECTORY_SEPARATOR);
define('NL', "\n");
define('DATA_DIR', dirname(__DIR__) . '\data' . DS);
define('VENDOR_DIR', dirname(__DIR__) . '\vendor' . DS);
define('REGISTRY_DIR', DATA_DIR . 'registry' . DS);

// Register Composer Autoloader
if (!is_file(VENDOR_DIR . 'autoload.php')) {
    throw new \RuntimeException(
        '[Error] Bootstrap: Could not find "vendor/autoload.php".' . PHP_EOL .
        'Did you forget to run "composer install --dev"?' . PHP_EOL
    );
}
require VENDOR_DIR . 'autoload.php';
