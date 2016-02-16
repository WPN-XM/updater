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
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;
use WPNXM\Updater\RegistryUpdater;

class ScanComponent extends ActionBase
{

    public function __construct()
    {
        Registry::clearOldScans();
    }

    public function __invoke()
    {
        $registry = Registry::load();

        $updater = new RegistryUpdater($registry);
        $updater->setupCrawler();

        /**
         * Scan a single component
         *
         * handles $_GET['component'],
         * e.g. "index.php?action=scan&component=openssl"
         */
        $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

        if(isset($component) === true) {
            if(!isset($registry[$component])) {
                throw new \Exception('Component "'.$component.'" doesn\'t exist in registry, yet. Correctly spelled? Otherwise, please create an entry.');
            }
            $numberOfComponents = $updater->getUrlsToCrawl($component);
        } else {
            // scan multiple components
            $numberOfComponents = $updater->getUrlsToCrawl();
        }

        $updater->crawl();

        /* ----- */

        $view                             = new View();
        $view->data['numberOfComponents'] = $numberOfComponents;
        $view->data['tableHtml']          = $updater->evaluateResponses();
        $view->render();
    }

}
