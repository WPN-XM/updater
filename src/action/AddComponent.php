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
