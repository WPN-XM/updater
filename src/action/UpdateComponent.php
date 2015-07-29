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
use WPNXM\Updater\Registry;

// inserts a single component version scan into the main registry
// - automatically git commit's with a standardized commit message
// - shows a git push reminder
class UpdateComponent extends ActionBase
{
    function __construct()
    {
       //Registry::clearOldScans(); 
    }
    
    function __invoke()
    {
        $registry = Registry::load();
        
        $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

        // fix alternative registry shorthand
        if (false !== strpos($component, 'php-x86')) {
            $component = 'php';
        }

        $registry = Registry::addLatestVersionScansIntoRegistry($registry, $component);
        
        if (is_array($registry) === true) {
            Registry::writeRegistry($registry);
            echo 'The registry was updated. Component "' . $component . '" inserted.';

            $name = isset($registry[$component]['name']) ?  $registry[$component]['name'] : $component;

            $commitMessage = 'updated software registry - ' . $name . ' v' . $registry[$component]['latest']['version'];
            Registry::gitCommitAndPush($commitMessage);
        } else {
            echo 'No version scans found: The registry is up to date.';
        }
    }
}




