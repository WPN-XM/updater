<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater;

class Application
{
    public static function run()
    {
        // determine action to call
        $actionName = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        if ($actionName === null) {
            $actionName = 'Index';
        }

        // action to classname
        $className = implode('', array_map('ucfirst', explode('-', $actionName)));
        $class = 'WPNXM\Updater\Action\\' . $className;

        // instantiate action class via composer autoloader
        // then call class as function. you need __invoke() in your actions.
        (new $class)();
    }

}

Application::run();