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
use WPNXM\Updater\View;

/**
 * add a new software into the registry
 */
class AddComponent extends ActionBase
{
    function __construct()
    {
        
    }
    
    function __invoke()
    {  
        $view = new View();
        $view->render();
    }
}