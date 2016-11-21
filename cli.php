<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

if (PHP_SAPI == 'cli') {
	include_once __DIR__ . '/src/bootstrap.php';
	$cli = new WPNXM\Updater\CliApplication;
    $cli->run();
} 