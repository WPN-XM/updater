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
use WPNXM\Updater\Registry;

class InsertComponent extends ActionBase
{

    public function __construct()
    {

    }

    public function __invoke()
    {
        $component  = filter_input(INPUT_POST, 'software', FILTER_SANITIZE_STRING);
        $shorthand  = filter_input(INPUT_POST, 'shorthand', FILTER_SANITIZE_STRING);
        $url        = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
        $version    = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
        $website    = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);
        $phpversion = ($phpversion = filter_input(INPUT_POST, 'phpversion', FILTER_SANITIZE_STRING)) ? $phpversion : '5.5';

        // create a registry entry for the component (array)
        $array = Registry::getArrayForNewComponent($component, $shorthand, $url, $version, $website, $phpversion);

        // write array as new "registry scan"
        Registry::writeRegistrySubset($shorthand, $array);

        $registry = Registry::load();

        // insert into registry
        $newRegistry = Registry::addLatestVersionScansIntoRegistry($registry, $component);

        // write registry
        if ($newRegistry !== false) {
            Registry::writeRegistry($newRegistry);
        }

        // check result and send response
        $js = '<script type="text/javascript">
            $(document).ready(function () {
                $(\'#myModal button[type="submit"]\').hide();
            });
           </script>';

        $response_ok   = '<div class="alert alert-success">Successfully added to registry.</div>';
        $response_fail = '<div class="alert alert-danger">Component was not added to registry.</div>';
        $response = (isset($newRegistry[$component]) === true) ? $response_ok : $response_fail;

        echo $response . $js;
    }

}
