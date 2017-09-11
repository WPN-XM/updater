<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

use WPNXM\Updater\JsonUtil;

class InstallerRegistry
{

/**
     * Writes the registry as JSON to the installer registry file.
     *
     * @param string $file
     * @param array  $registry
     */
    public static function write($file, $registry)
    {
        array_multisort($registry, SORT_ASC);

        $json        = json_encode($registry);
        $json_pretty = JsonUtil::prettyPrintCompact($json);
        $json_table  = JsonUtil::prettyPrintTableFormat($json_pretty);

        file_put_contents($file, $json_table);

        echo 'Updated or Created Installer Registry "' . $file . '"<br />';
    } 
}