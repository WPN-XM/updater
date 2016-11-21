<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
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