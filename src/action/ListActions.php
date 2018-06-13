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

class ListActions extends ActionBase
{

    public function __invoke()
    {
        echo __CLASS__ . ' invoked';

        echo '<pre>';
        foreach (glob(__DIR__ . '\*.php') as $filename) {
            $this->addHtmlPreTag($filename . "\n");
        }
    }

    /**
     * @param string $string
     */
    public function addHtmlPreTag($string)
    {
        echo '<pre>' . $string . '</pre>';
    }

}
