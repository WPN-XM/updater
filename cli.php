<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

if (PHP_SAPI == 'cli') {
	include_once __DIR__ . '/src/bootstrap.php';
	$cli = new WPNXM\Updater\CliApplication;
    $cli->run();
} 