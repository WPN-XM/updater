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
use WPNXM\Updater\PHPExtensionScraper;

/**
 * Fetches all PHP extension names from PECL and save it as a JSON file.
 */
class UpdatePhpExtensionList extends ActionBase
{
    private $scraper;
    
    public function __invoke()
    {
        $this->scraper = new PHPExtensionScraper;

        return (bool) file_put_contents(
            DATA_DIR . 'registry/php-extensions-on-pecl.json',
            $this->scraper->getJson()
        );
    }
}