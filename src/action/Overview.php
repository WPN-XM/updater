<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\Registry;
use WPNXM\Updater\View;

class Overview extends ActionBase
{
    private $registry;
    
    function __construct()
    {
        if (!extension_loaded('curl')) {
            exit('Error: PHP Extension cURL required.');
        }

        $this->registry = Registry::load();
        
        Registry::healthCheck($this->registry);
    }

    function __invoke()
    {
        $view = new View();
        $view->data['registry'] = $this->registry;
        $view->render();
    }

}
