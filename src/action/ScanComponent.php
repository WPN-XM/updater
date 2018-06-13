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
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;
use WPNXM\Updater\RegistryUpdater;
use WPNXM\Updater\CliColors;

class ScanComponent extends ActionBase
{
    public $updater;
    public $registry = [];
    public $numberOfComponentsToCrawl = 0;

    public function __construct()
    {
        Registry::clearOldScans();

        $this->registry = Registry::load();

        $this->updater = new RegistryUpdater($this->registry);
    }

    public function __invoke()
    {
        /**
         * Scan a single component
         *
         * handles $_GET['component'],
         * e.g. "index.php?action=scan&component=openssl"
         */
        $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

        $this->crawl($component);

        /* View */

        $view                             = new View();
        $view->data['numberOfComponents'] = $this->numberOfComponentsToCrawl;
        $view->data['tableHtml']          = $this->updater->getHtmlTable();
        $view->render();
    }

    public function crawl($component)
    {       
        if(isset($component)) {
            if(is_array($component)) {
                $this->prepareCrawlingMultipleComponents($component);
            } else {
                $this->prepareCrawlingSingleComponent($component);
            }
        } else {
            $this->prepareCrawlingAllComponents();
        }

        echo $this->updater->crawl();
    }

    public function prepareCrawlingSingleComponent($component)
    {        
        $this->numberOfComponentsToCrawl = $this->updater->getUrlsToCrawl($component);
    }

    public function prepareCrawlingMultipleComponents($components)
    {   
        $components = array_unique( (array) $components);

        $this->updater->getUrlsToCrawl($components);        
    }

    public function prepareCrawlingAllComponents()
    {
        $this->numberOfComponentsToCrawl = $this->updater->getUrlsToCrawl();
    }

    public function getResults()
    {
        echo 'Version Crawling Results'.NL;
        echo '------------------------'.NL;
        //echo 'Crawled Versions: '.$this->numberOfComponentsToCrawl.NL.NL;

        $results = $this->updater->getResults();

        echo str_pad('Component', 21) . str_pad('Current Version', 20) . str_pad('Latest Version', 20).PHP_EOL;       
        
        foreach($results as $result) {
            $string = str_pad($result[0], 25).str_pad($result[1], 20).str_pad($result[2], 20).NL;            
            $color = ($result[3] === true) ? 'brightgreen' : 'brightred'; 
            echo CliColors::write($string, $color);
        }
    }
}
