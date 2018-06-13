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
use WPNXM\Updater\InstallerRegistries;
use WPNXM\Updater\InstallerRegistry;

/**
 * Pretty prints all installation wizard registries.
 */
class PrettyPrintRegistries extends ActionBase
{
    public function __invoke()
    {
        $nextRegistries = InstallerRegistries::getRegistriesOfNextRelease();

        echo 'Pretty printing all installation wizard registries.<br>';

        foreach ($nextRegistries as $file) 
        {            
            echo '<br>Processing Installer: "' . basename($file) . '":<br>';

            $content = file_get_contents($file);

            $registry = json_decode($content, true);             
                    
            if(json_last_error() !== JSON_ERROR_NONE) {            
                throw new \Exception(
                    sprintf("JSON PARSING ERROR: '%s' in file '%s'.", $file, json_last_error_msg())
                );
            }

            InstallerRegistry::write($file, $registry);
        }

        echo '<pre>You might "git commit/push":<br>pretty printed registries</pre>';
    }
}
