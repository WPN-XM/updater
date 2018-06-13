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

/**
 * add a new software into the registry
 */
class AddComponent extends ActionBase
{

    public function __construct()
    {

    }

    public function __invoke()
    {
        $shorthand  = filter_input(INPUT_GET, 'shorthand', FILTER_SANITIZE_STRING);

        $view = new View();
        $view->data['shorthand'] = $shorthand;
        $view->render();
    }

}
