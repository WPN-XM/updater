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
use WPNXM\Updater\PHPExtensionScraperPECL;
use WPNXM\Updater\PHPExtensionScraperGoPHP7;

/**
 * Fetch all PHP extension names from PECL and save them as a JSON file.
 */
class UpdatePhpExtensionList extends ActionBase
{    
    public function __invoke()
    {
        $scraper1 = new PHPExtensionScraperPECL; 
        $scraper1->updateExtensionList();       
       
        $scraper2 = new PHPExtensionScraperGoPHP7;
        $scraper2->updateExtensionList();
    }
}