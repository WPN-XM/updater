<?php
//declare(strict_types=1);

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
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class RegistryUpdater
{
    /**
     * @object \GuzzleHttp\Client
     */
    public $guzzleClient;
    public $crawlers     = [];
    public $urls         = [];
    public $registry     = [];
    public $old_registry = [];

    public $output       = 'html';
    public $results      = [];
    public $html         = '';

    const USER_AGENT = 'WPN-XM Server Stack - Software Registry Update Tool - http://wpn-xm.org/';

    public function __construct($registry)
    {
        $this->registry     = $registry;
        $this->old_registry = $registry;

        // Setup Guzzle Client and set header and curl options for all requests
        $this->guzzleClient = new \GuzzleHttp\Client([
            'headers' => [ 'User-Agent' => RegistryUpdater::USER_AGENT ]
            //,'curl' => [ CURLOPT_SSL_VERIFYPEER => false ]   
        ]);
    }

    /**
     * Return array with one or more crawler file names.
     *
     * @param string $components
     * @return array
     */
    public function getCrawlers($components = null)
    {
        // return multiple crawlers        
        if (isset($components)) {
            $components = (array) $components;
            $crawlers = [];
            foreach($components as $component) {
                $crawlers[] = self::getCrawlerFile($component)[0];
            }
            return $crawlers;
        }       

        // return all crawlers
        return glob(__DIR__ . '\crawler\*.php');
    }

    public static function getCrawlerFile($component)
    {
        $file = str_replace('-', '_', $component);
        return glob(__DIR__ . '\crawler\\' . $file . '.php');
    }

    /**
     * @return int The number of URLs to crawl.
     */
    public function getUrlsToCrawl($components = null)
    {
        $crawlers = $this->getCrawlers($components);

        foreach ($crawlers as $i => $file) {

            // load and instantiate version crawler
            include $file;
            $component = str_replace(array('-', '.'), array('_', '_'), strtolower(pathinfo($file, PATHINFO_FILENAME)));        
            $classname = 'WPNXM\Updater\Crawler\\' . ucfirst($component);
            $crawler = new $classname();

            // use the registry key from the crawler or the component as fallback
            // TODO find out why $component is needed here (which crawlers do not have a name?)
            $registryKey = isset($crawler->name) ? $crawler->name : $component;

            // set "software registry" and "component name" to crawler
            $crawler->setRegistry($this->registry, $registryKey);

            // store crawler object in crawlers array
            $this->crawlers[$i] = $crawler;

            // fetch URL from Version Crawler Object and prepare array with all URLs to crawl
            $this->urls[] = $crawler->getURL();
        }

        return count($this->urls); 
    }

    public function getTotalNumberOfRequests()
    {
        return count($this->urls);
    }

    /**
     * Crawl several URLs in parallel.
     */
    public function crawl()
    {  
        // Prepare Requests Closure.
        // The closure accepts the URLs array, counts the total number of URLs 
        // and creates new Requests for each URL.
        $requests = function (array $urls) {
            $total_num_requests = $this->getTotalNumberOfRequests();
            for ($i = 0; $i < $total_num_requests; $i++) {
                yield new Request('GET', $urls[$i]);
            }
        };

        // Setup Request Pool.
        // The Requests Closure is inserted as parameter of the Pool.
        $pool = new Pool($this->guzzleClient, $requests($this->urls), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                // this is delivered each successful response
                $this->fulfilledResponse($response, $index);
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
                $this->rejectedResponse($reason, $index);
            },
        ]);

        // Initiate the transfers and create a promise.
        $promise = $pool->promise();

        // Wait for Pool of Requests to complete.
        $promise->wait();
    }

    /**
     * Handler Function for a failed Requests (Reason).
     */
    public function rejectedResponse($reason, $index)
    {
        $message = sprintf("The request at index #%s failed.\nReason: %s.\n", $index, $reason);

        echo (PHP_SAPI !== 'cli') ? '<pre>'.$message.'</pre>' : $message;
    }

    /**
     * Handler Function for a successful Request (Response).
     */
    public function fulfilledResponse($response, $index)
    {
        /**
         * Set "response" to the "version crawler" object.
         * We use Symfony/DomCrawler->addContent() to set the content to scrape.
         */
        $body        = $response->getBody();
        $contentType = is_array($response->getHeader('Content-Type')) 
                        ? $response->getHeader('Content-Type')[0] // no clue, why this is an array...
                        : $response->getHeader('Content-Type');        
        $this->crawlers[$index]->addContent($body, $contentType);

        $component     = $this->crawlers[$index]->getName();
        $latestVersion = $this->crawlers[$index]->crawlVersion(); // scrape version from content

        if($latestVersion === null) {
            echo '[Crawling Error] Version Scan for Component "'.$component.'" returned no version.';
            return;
        }

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
        if(method_exists($this->crawlers[$index], 'onAfterVersionInsert') === true) {
            $this->registry = $this->crawlers[$index]->onAfterVersionInsert($this->registry);
        }

        /**
         * Get "old version" and "new version" for comparison.
         *
         * Use "old version" defaults to "0.0.0", in case the component is not in the registry, yet.
         * This happens only, when it is a newly added crawler.
         */
        $new_version = $this->registry[$component]['latest']['version'];

        $old_version = isset($this->old_registry[$component]['latest']['version'])
            ? $this->old_registry[$component]['latest']['version']
            : '0.0.0';        

        /**
         * Latest Version
         */
        if (Version::compare($component, $old_version, $new_version) === true) {
            // write a temporary component registry, for later registry insertion
            Registry::writeRegistrySubset($component, $this->registry[$component]);
            
            $this->results[] = [$component, $old_version, $new_version, true];
        }

        /**
         * Missing version
         *
         * TODO why are here 2 calls to Version::notInRegistry()?
         */
        elseif (Version::notInRegistry($latestVersion, $this->old_registry[$component]) === true)
        {
            // write a temporary component registry, for later registry insertion
            Registry::writeRegistrySubset($component, $this->registry[$component]);

            // check, if this is a missing version number
            $new_version = Version::notInRegistry($latestVersion, $this->old_registry[$component], true);

            $this->results[] = [$component, $old_version, $new_version, true];            
        } else {            
            $this->results[] = [$component, $old_version, $new_version, false];
        }        
    }

    public function getHtmlTable()
    {
        foreach($this->results as $index => $data ) {
            // render a table row (version comparison display)
            $this->html .= ViewHelper::renderTableRow($data[0],$data[1], $data[2], $data[3]);
        }

        return $this->html;
    }

    public function getResults()
    {
        return $this->results;
    }
}
