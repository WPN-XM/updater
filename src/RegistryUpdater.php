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

use Goutte\Client as GoutteClient;
use GuzzleHttp\Pool;

class RegistryUpdater
{
    public $guzzleClient;
    public $crawlers     = array();
    public $urls         = array();
    public $results      = array();
    public $registry     = array();
    public $old_registry = array();
    
    const USER_AGENT = 'WPN-XM Server Stack - Software Registry Update Tool - http://wpn-xm.org/';

    public function __construct($registry)
    {
        $this->registry     = $registry;
        $this->old_registry = $registry;
    }

    public function setupCrawler()
    {
        // init Goutte and set header for all requests
        $goutteClient = new GoutteClient;
        $goutteClient->setHeader('User-Agent', RegistryUpdater::USER_AGENT);

        // fetch Guzzle out of Goutte and deactivate SSL Verification
        $this->guzzleClient = $goutteClient->getClient();
        $this->guzzleClient->setDefaultOption('verify', false);

        $goutteClient->setClient($this->guzzleClient);
    }

    /**
     * Returns array with one or more crawler file names.
     *
     * @param string $component
     * @return array
     */
    public function getCrawlers($component = null)
    {
        // return single crawler
        if (isset($component) === true) {
            $file = str_replace('-', '_', $component);
            return glob(__DIR__ . '\crawler\\' . $file . '.php');
        }

        // return all crawlers
        return glob(__DIR__ . '\crawler\*.php');
    }

    public function getUrlsToCrawl($single_component = null)
    {
        $crawlers = $this->getCrawlers($single_component);

        $i = 0;

        foreach ($crawlers as $i => $file) {
           
            // instantiate version crawler
            include $file;
            $component = str_replace(array('-', '.'), array('_', '_'), strtolower(pathinfo($file, PATHINFO_FILENAME)));
            $classname = 'WPNXM\Updater\Crawler\\' . ucfirst($component);
            $crawler   = new $classname();
            
            // use the registry shorthand from the crawler and the component as fallback
            $registryShorthand = isset($crawler->name) ? $crawler->name : $component;

            // set registry and component name to crawler
            $crawler->setRegistry($this->registry, $registryShorthand);

            // store crawler object in crawlers array
            $this->crawlers[$i] = $crawler;

            // fetch URL from Version Crawler Object and prepare array with all URLs to crawl
            $this->urls[] = $crawler->getURL();
        }

        return $i + 1;
    }

    /**
     * Crawl launches several URL requests in parallel.
     * The response time will be the time of the longest request.
     */
    public function crawl()
    {
        $requests = array();

        foreach ($this->urls as $idx => $url) {
            // guzzle does not accept an array of URLs anymore
            // now Urls must be objects implementing the \GuzzleHttp\Message\RequestInterface
            $requests[] = $this->guzzleClient->createRequest('GET', $url, ['allow_redirects' => true]);
        }

        // results is a GuzzleHttp\BatchResults object
        $this->results = \GuzzleHttp\Pool::batch($this->guzzleClient, $requests);
    }

    public function evaluateResponses()
    {
        $html = '';
        $i    = 0;

        // Retrieve all failures.
        foreach ($this->results->getFailures() as $requestException) {
            echo $requestException->getMessage() . "\n";
        }

        // Retrieve all successful responses
        // iterate through responses and insert them in the crawler objects
        foreach ($this->results->getSuccessful() as $response) {
            $new_version = $old_version = '';

            // set the response to the version crawler object
            $this->crawlers[$i]->addContent($response->getBody(), $response->getHeader('Content-Type'));

            $component     = $this->crawlers[$i]->getName();
            $latestVersion = $this->crawlers[$i]->crawlVersion();
            $latestVersion = ArrayUtil::clean($latestVersion);

            /**
             * Add (new) latest version (array) for this component to the registry.
             */
            $this->registry = Registry::addLatestVersionToRegistry($component, $latestVersion, $this->old_registry);

            /**
             * onAfterVersionInsert Event
             *
             * This event allows executing custom functionality after the version was inserted.
             * One might apply further changes, like rewriting the registry, for instance,
             * to rewrite and update old URLs, when file movements of the download files occured.
             * For example, like "PHP" moves old versions into the download "/archives" folder.
             */
            if(method_exists($this->crawlers[$i], 'onAfterVersionInsert') === true) {
                $this->registry = $this->crawlers[$i]->onAfterVersionInsert($this->registry);
            }

            // get old and new version for comparison.

            /**
             * get old version
             * use 0.0.0, in case the component is not in the registry, yet (newly added crawler)
             */
            $old_version = isset($this->old_registry[$component]['latest']['version'])
                ? $this->old_registry[$component]['latest']['version']
                : '0.0.0';


            $new_version = $this->registry[$component]['latest']['version'];

            /**
             * Latest Version
             */
            if (Version::compare($component, $old_version, $new_version) === true) {
                // write a temporary component registry, for later registry insertion
                Registry::writeRegistrySubset($component, $this->registry[$component]);

                // render a table row (version comparison display)
                $html .= ViewHelper::renderTableRow($component, $old_version, $new_version, 'latest-version');
            }
            /**
             * A missing version
             */
            elseif (Version::notInRegistry($latestVersion, $this->old_registry[$component]) === true)
            {
                // write a temporary component registry, for later registry insertion
                Registry::writeRegistrySubset($component, $this->registry[$component]);

                // missing version number
                $new_version = Version::notInRegistry($latestVersion, $this->old_registry[$component], true);

                // render a table row (version comparison display)
                $html .= ViewHelper::renderTableRow($component, $old_version, $new_version, 'missing-version');
            } else {
                // render a table row (version comparison display)
                $html .= ViewHelper::renderTableRow($component, $old_version, $new_version, false);
            }

            $i++;
        }

        return $html;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }
}
