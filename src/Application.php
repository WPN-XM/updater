<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
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

include __DIR__ . '/bootstrap.php';
Application::run();