<?php

/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

/**
 * The request to the URL 'http://localhost/updater/' fires up 'index.html'.
 * All subsequent GET requests with variables come from 'index.html' and are routed to the Application.
 */
return (empty($_GET) || isset($_GET['XDEBUG_SESSION_START']) || isset($_GET['_'])) 
    ? require __DIR__ . '/public/index.html' 
    : require __DIR__ . '/src/bootstrap.php';
