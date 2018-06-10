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
use WPNXM\Updater\ArrayUtil;
use WPNXM\Updater\Registry;
use WPNXM\Updater\StatusRequest;
use WPNXM\Updater\View;

/**
 * LinkRemove - removes a single (broken) link from the array of the software.
 */
class LinkRemoveComponent extends ActionBase
{
    private $registry = array();

    public function __construct()
    {
        $this->registry = Registry::load();
    }

    public function __invoke()
    {
        // handle incoming values
        $software = filter_var($_POST['software'], FILTER_SANITIZE_STRING);
        $url      = filter_var($_POST['url'], FILTER_SANITIZE_STRING);

        // get registry subset
        $subset = $this->registry[$software];
        $url    = urldecode($url);
        
        // delete url from array
        // iterate array and delete array key by value
        foreach ($subset as $key => $value){
            if ($value == $url) {
                unset($subset[$key]);
            }
        }

        // replace registry subset
        $this->registry[$software] = $subset;

        Registry::writeRegistry($this->registry);

        echo "Link removed.";
    }
}