<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class DownloadFilenames
{
    public static function load()
    {
        $file = REGISTRY_DIR.'wpnxm-download-filenames.php';

        if(!is_file($file)) {
            throw new RuntimeException('The download description file "'.$file.'" was not found.');
        }

        return include $file;
    }
}