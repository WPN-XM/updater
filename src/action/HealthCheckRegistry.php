<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */
namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;
use WPNXM\Updater\RegistryHealth;

/**
 * Check the health of the registry.
 */
class HealthCheckRegistry extends ActionBase
{
    private $registryHealth;

    public function __construct()
    {
        $registry = Registry::load();
        $this->registryHealth = new RegistryHealth($registry);
    }

    public function __invoke()
    {
        $this->registryHealth->check();

        $view = new View;
        $view->data['health'] = $this->registryHealth->getErrors();
        $view->render();
    }
}
