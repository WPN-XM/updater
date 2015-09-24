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

class ListActions extends ActionBase
{

    function __invoke()
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
    function addHtmlPreTag($string)
    {
        echo '<pre>' . $string . '</pre>';
    }

}
