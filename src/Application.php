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
            throw new \Exception('No action given.');
        }

        // action to classname
        $className = implode('', array_map('ucfirst', explode('-', $actionName)));
        $class = 'WPNXM\Updater\Action\\' . $className;

        // load and instantiate action class
        $actionFile = __DIR__ . '/action/' . $className . '.php';
        if (!is_file($actionFile)) {
            throw new \Exception('Action not found: '. $className);
        }
        require __DIR__ . '/ActionBase.php';
        require $actionFile;
        $action = new $class;

        // then call class as function. you need __invoke() in your actions.
        $action();
    }

}

Application::run();