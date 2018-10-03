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
use WPNXM\Updater\Registry;

class InsertComponent extends ActionBase
{

    public function __construct()
    {

    }

    public function __invoke()
    {
        var_dump($_POST);

        $shorthand  = filter_input(INPUT_POST, 'shorthand', FILTER_SANITIZE_STRING);
        $software   = filter_input(INPUT_POST, 'software', FILTER_SANITIZE_STRING);       
        $url        = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
        $version    = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
        $website    = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);
        $phpversion = ($phpversion = filter_input(INPUT_POST, 'phpversion', FILTER_SANITIZE_STRING)) ? $phpversion : '5.5';

        /**
         * Registry Entry
         */ 

        // create a registry entry for the component (array)
        $array = Registry::getArrayForNewComponent($software, $shorthand, $url, $version, $website, $phpversion);

        var_dump($shorthand, $array);

        $registry = Registry::load();

        $registry[$shorthand] = $array;

        Registry::writeRegistry($registry);
        
        // check result and send response

        $response_ok   = '<div class="alert alert-success">Successfully added to registry.</div>';
        $response_fail = '<div class="alert alert-danger">Component was not added to registry.</div>';
        $response = (isset($registry[$shorthand]) === true) ? $response_ok : $response_fail;

        echo $response;

        /**
         * Download File Entry
         */ 

        //$downloadfilename = filter_input(INPUT_POST, 'downloadfilename', FILTER_SANITIZE_STRING);
    }

}
