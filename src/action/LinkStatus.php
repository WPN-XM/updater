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
use WPNXM\Updater\StatusRequest;

/**
 * Registry Link Status
 *
 * This scripts check the software registry for broken download links.
 *
 * For each software component we check:
 * a) the download link for the latest version
 *      This link comes directly from the local registry.
 * b) the forwarding downloading link
 *      This link is a get request to the server and uses the registry on the server.
 *      Forwarding links are used in the innosetup scripts of the web installation wizards.
 */
class LinkStatus extends ActionBase
{
    private $registry;

    function __construct()
    {
        if (!extension_loaded('curl')) {
            exit('Error: PHP Extension cURL required.');
        }

        $this->registry = Registry::load();

        Registry::healthCheck($this->registry);
    }

    function __invoke()
    {
        $before       = microtime(true);
        $urls         = StatusRequest::getUrlsToCrawl($this->registry);
        $responses    = StatusRequest::getHttpStatusCodesInParallel($urls);
        $crawlingTime = round((microtime(true) - $before), 2);

        // build a lookup array with the relation of "url" => "http status code" (true, false)
        $urlsHttpStatus = array_combine($urls, $responses);

        // define a closure (as viewhelper) for the lookup (inherit array by-reference)
        $isAvailable = function($url) use(&$urlsHttpStatus) {
            return $urlsHttpStatus[$url];
        };

        $view = new View();
        $view->data['before']         = $before;
        $view->data['crawlingTime']   = $crawlingTime;
        $view->data['isAvailable']    = $isAvailable;
        $view->data['registry']       = $this->registry;
        $view->data['numberOfUrls']   = count($urls);
        $view->render();
    }

}
