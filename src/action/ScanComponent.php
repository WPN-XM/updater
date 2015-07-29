<?php

/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;
use WPNXM\Updater\RegistryUpdater;

class ScanComponent extends ActionBase
{

    function __construct()
    {
        Registry::clearOldScans();
    }

    function __invoke()
    {
        $registry = Registry::load();

        $updater = new RegistryUpdater($registry);
        $updater->setupCrawler();

        // handle $_GET['component'], for single component scans, e.g. index.php?action=scan&component=openssl
        $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

        $numberOfComponents = (isset($component) === true) ?
            $updater->getUrlsToCrawl($component) :
            $updater->getUrlsToCrawl();

        $updater->crawl();

        /* ----- */

        $view                             = new View();
        $view->data['numberOfComponents'] = $numberOfComponents;
        $view->data['tableHtml']          = $updater->evaluateResponses();
        $view->render();
    }

}
