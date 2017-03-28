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
use WPNXM\Updater\Registry;

// inserts a single component version scan into the main registry
// - automatically git commit's with a standardized commit message
// - shows a git push reminder
class UpdateComponent extends ActionBase
{
    public function __invoke()
    {
        $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

        // fix alternative registry shorthand
        if (false !== strpos($component, 'php-x86')) {
            $component = 'php';
        }

        $registry = Registry::load();
        $registry = Registry::addLatestVersionScansIntoRegistry($registry, $component);

        if (is_array($registry)) {
            Registry::writeRegistry($registry);
            echo 'The registry was updated. Component "' . $component . '" inserted.';
            $commitMessage = getCommitMessage($registry, $component);
            Registry::gitCommitAndPush($commitMessage);
        } else {
            echo 'No version scans found: The registry is up to date.';
        }
    }

    private function getNumberOfVersionsForComponent($registry, $component)
    {
        // get registry subset for this component
        $r = $registry[$component]; 

        // reduce to versions
        unset($r['name'],$r['website'], $r['latest']);

        return count($r);
    }

    private function getName($registry, $component)
    {
        return isset($registry[$component]['name']) ?  $registry[$component]['name'] : $component;
    }

    private function getCommitMessage($registry, $component)
    {
        if(1 == getNumberOfVersionsForComponent($registry, $component)) {
            $commitMessage = 'added to';
        } else {
            $commitMessage = 'updated';
        }

        return $commitMessage . ' software registry - ' . $this->getName($registry, $component) . ' v' . $registry[$component]['latest']['version'];
    } 
}




