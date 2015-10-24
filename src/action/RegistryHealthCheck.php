<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */
namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;

/**
 * Check the health of the registry (data structure).
 */
class RegistryHealthCheck extends ActionBase
{

    public function __construct()
    {
    	$registry = Registry::load();

    	Registry::healthCheck($registry);
    }

    public function __invoke()
    {
        $view = new View();
        $view->render();
    }

}
