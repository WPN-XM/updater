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
use WPNXM\Updater\RegistryHealth;

/**
 * Check the health of the registry.
 */
class RegistryHealthCheck extends ActionBase
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
