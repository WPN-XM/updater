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
use WPNXM\Updater\VersionCrawlers;

class Overview extends ActionBase
{
    public function __invoke()
    {        
        $registry = Registry::load();        
        $newCrawlers = VersionCrawlers::findCrawlersWithoutRegistryEntry($registry);
       
        // View
        $view = new View();
        $view->data['registry']     = $registry;
        $view->data['newCrawlers']  = $newCrawlers;
        $view->render();
    } 
}
