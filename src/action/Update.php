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
 * insert version scans into main software registry
 */
class Update extends ActionBase
{
    public function __invoke()
    {
        require dirname(__DIR__) . '\Registry.php';
        
        $registry = Registry::load();

        $registry = Registry::addLatestVersionScansIntoRegistry($registry);

        if (is_array($registry) === true) {
            Registry::writeRegistry($registry);
            echo 'The registry was updated.';
        } else {
            echo 'The registry is up to date.';
        }
    }

}