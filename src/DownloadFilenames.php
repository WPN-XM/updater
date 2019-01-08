<?php

/*
 * WPИ-XM Server Stack
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater;

class DownloadFilenames
{
    public static function load()
    {
        return include REGISTRY_DIR . 'wpnxm-download-filenames.php';
    }
}