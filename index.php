<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

include_once __DIR__ . '/src/bootstrap.php';

/**
 * The request to the URL 'http://localhost/updater/' fires up 'index.html'.
 * All subsequent GET requests with variables come from 'index.html' and are routed to the Application.
 */
return (empty($_GET))
    ? require __DIR__ . '/public/index.html'
    : require __DIR__ . '/src/Application.php';
