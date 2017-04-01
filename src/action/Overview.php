<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
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
        $crawlers = VersionCrawlers::findCrawlersWithoutRegistryEntry($registry);
       
        /* View */

        $view                    = new View();
        $view->data['registry']  = $registry;
        $view->data['crawlers']  = $crawlers;
        $view->render();
    } 
}
