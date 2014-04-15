<?php
require_once __DIR__ . '/vendor/goutte.phar';

use Goutte\Client as GoutteClient;
use Guzzle\Common\Exception\MultiTransferException;

class RegistryUpdater
{
    public $guzzleClient;
    public $crawlers = array();
    public $urls = array();
    public $responses = array();

    public $registry = array();
    public $old_registry = array();

    public function __construct($registry)
    {
        $this->registry = $registry;
        $this->old_registry = $registry;
    }

    public function setupCrawler()
    {
        // init Goutte and set header for all requests
        $goutteClient = new GoutteClient();
        $goutteClient->setHeader('User-Agent', 'WPN-XM Server Stack - Software Registry Update Tool - http://wpn-xm.org/');

        // fetch Guzzle out of Goutte and deactivate SSL Verification
        $this->guzzleClient = $goutteClient->getClient();
        $this->guzzleClient->setSslVerification(false);
    }

    public function getUrlsToCrawl($single_component = '')
    {
        if(isset($single_component) === true) {
            $crawlers = glob(__DIR__ . '\crawlers\\' . $single_component . '*.php');
        } else {
            $crawlers = glob(__DIR__ . '\crawlers\*.php');
        }

        include __DIR__ . '/VersionCrawler.php';
        
        $i = count($crawlers);

        foreach ($crawlers as $i => $file) {

            // instantiate version crawler
            include $file;
            $component = strtolower(pathinfo($file, PATHINFO_FILENAME));
            $classname = 'WPNXM\Updater\Crawler\\' . ucfirst($component);
            $crawler = new $classname;

            /* set registry and crawling client to version crawler */
            $crawler->setRegistry($this->registry, $component);
            $crawler->setGuzzle($this->guzzleClient);

            // store crawler object in crawlers array
            $this->crawlers[$i] = $crawler;

            // fetch URL from Version Crawler Object and prepare array with all URLs to crawl
            $this->urls[] = $this->guzzleClient->get( $crawler->getURL() );
        }

        return $i;
    }

    /**
     * Crawl launches several URL requests in parallel.
     * The response time will be the time of the longest request.
     */
    public function crawl()
    {
        try {
            $this->responses = $this->guzzleClient->send($this->urls);
        } catch (MultiTransferException $e) {

            echo "The following exceptions were encountered:\n";
            foreach ($e as $exception) {
                echo $exception->getMessage() . "\n";
            }

            echo "The following requests failed:\n";
            foreach ($e->getFailedRequests() as $request) {
                echo $request . "\n\n";
            }

            echo "The following requests succeeded:\n";
            foreach ($e->getSuccessfulRequests() as $request) {
                echo $request . "\n\n";
            }
        }
    }

    public function evaluateResponses()
    {
        $html = '';

        // iterate through responses and insert them in the crawler objects
        foreach ($this->responses as $i => $response) {

            // set the response to the version crawler object
            $this->crawlers[$i]->addContent( $response->getBody(), $response->getContentType() );

            $component = $this->crawlers[$i]->getName();
            $latestVersion = $this->crawlers[$i]->crawlVersion();

            $this->registry = Registry::addLatestVersionToRegistry($component, $latestVersion, $this->old_registry);

            /**
             * After Insert Event - to apply further changes to the registry.
             *
             * For instance, rewriting old URLs to take file movements into account,
             * like PHP moving old versions into "/archives" folder.
             */
            $this->registry = $this->crawlers[$i]->modifiyRegistry($this->registry);

            // write temporary component registry, for later registry insertion
            $old_version = $this->old_registry[$component]['latest']['version'];
            $new_version = $this->registry[$component]['latest']['version'];

            // strcmp is used for openssl version numbers, e.g. "1.0.1e" :)
            if (version_compare($old_version, $new_version, '<') === 1 || strcmp($old_version, $new_version) < 0) {
                Registry::writeRegistrySubset($component, $this->registry[$component]);
            }

            // render a table row for version comparison
            $html .= Viewhelper::renderTableRow($component, $old_version, $new_version);
        }

        return $html;
    }

    public function setRegistry($registry) {
        $this->registry = $registry;
    }
}

class Viewhelper
{
    /**
     * The function prints an update symbol if old_version is lower than new_version.
     *
     * @param string Old version.
     * @param string New version.
     */
    public static function printUpdatedSign($old_version, $new_version)
    {
        if (version_compare($old_version, $new_version, '<') === true || (strcmp($old_version, $new_version) < 0)) {
            $html = '<span class="badge alert-success">';
            $html .= $new_version;
            $html .= '</span><span style="color:green; font-size: 16px">&nbsp;&#x25B2;&nbsp;</span>';

            $html .= '<a class="btn btn-default btn-xs" href="/">Commit & Push</a>';

            return $html;
        }
    }

    public static function renderTableRow($component, $old_version, $new_version)
    {
        $html = '<tr>';
        $html .= '<td>' . $component . '</td>';
        $html .= '<td>' .  $old_version . '</td>';
        $html .= '<td>' .  self::printUpdatedSign($old_version, $new_version) . '</td>';
        $html .= '</tr>';

        return $html;
    }
}

class Registry
{
    /**
     * Writes the registry array to a php file for (re-)inclusion.
     * e.g.
     *  $registry = include 'registry.php';
     *
     * @param $registry The registry array.
     */
    public static function writeRegistry(array $registry)
    {
        // backup current registry
        rename(__DIR__ . '/registry/wpnxm-software-registry.php', __DIR__ . '/registry/wpnxm-software-registry-backup-' . date("dmy-His") . '.php');

        // registry file header
        $content = "<?php\n";
        $content .= "   /**\n";
        $content .= "    * WPN-XM Software Registry\n";
        $content .= "    * ------------------------\n";
        $content .= "    * Last Update " . date(DATE_RFC2822) . ".\n";
        $content .= "    * Do not edit manually!\n";
        $content .= "    */\n";
        $content .= "\n return ";

        // formatting
        $registry = Registry::sort($registry);
        $content  .= Registry::prettyPrint($registry);

        // write new registry
        return (bool) file_put_contents(__DIR__ . '/registry/wpnxm-software-registry.php', $content);
    }

    public static function getArrayForNewComponent($component, $url, $version, $website)
    {
        return array(
            'name' => $component,
            'website' => $website,
            $version => $url,
            'latest' => array(
                'version' => $version,
                'url' => $url
            )
        );
    }

    /**
     * Add latest version scan of component to the main software component array.
     *
     * @param $name Name of Software Component
     * @param $latestVersion Registry subset of the software component, which should be added to the main array.
     */
    public static function addLatestVersionToRegistry($name, array $latestVersion, array $registry)
    {
        $latestVersion = ArrayTool::clean($latestVersion);

        if (isset($latestVersion['url']) === true and isset($latestVersion['version']) === true) {
            // the array contains only one element

            // create [latest] sub-array
            $registry[$name]['latest']['url'] = $latestVersion['url'];
            $registry[$name]['latest']['version'] = $latestVersion['version'];

            // create [version] => [url] relationship
            $registry[$name][ $latestVersion['version'] ] =$latestVersion['url'];

            unset($latestVersion);
        } else {
            // sort by version number, from low to high
            asort($latestVersion);

            // add the last array item of multiple elements (the one with the highest version number)

            // insert the last array item as [latest][version] => [url]
            $registry[$name]['latest'] = array_pop($latestVersion);

            // insert the last array item also as a pure [version] => [url] relationship
            $registry[$name][ $registry[$name]['latest']['version'] ] = $registry[$name]['latest']['url'];
        }

        // added remaining array items (if any) as pure [version] => [url] relationships
        if (false === empty($latestVersion)) {
            foreach ($latestVersion as $new_version_entry) {
                $registry[$name][ $new_version_entry['version'] ] = $new_version_entry['url'];
            }
        }

        array_multisort(array_keys($registry[$name]), SORT_NATURAL, $registry[$name]);

        return $registry;
    }

    public static function clearOldScans()
    {
        $scans = glob(__DIR__ . '\scans\*.php');
        foreach($scans as $file) {
            unlink($file);
        }
    }

    public static function writeRegistrySubset($component, $registry)
    {
        // write a return array for "array to var" inclusion
        $content = "<?php\nreturn " . self::prettyPrint($registry);

        file_put_contents(__DIR__ . '/scans/latest-version-' . $component . '.php', $content);
    }

    public static function addLatestVersionScansIntoRegistry(array $registry, $forComponent = '')
    {
        $scans = glob(__DIR__ . '\scans\*.php');

        // nothing to do, return early
        if(count($scans) === 0) {
            return false;
        }

        foreach($scans as $i => $file) {
            $subset = include $file;
            preg_match('/latest-version-(.*).php/', $file, $matches);
            $component = $matches[1];

            // add the registry subset only for a specific component
            if(isset($forComponent) && ($forComponent === $component)) {
                 $registry[$component] = $subset;
                 break;
            }

            $registry[$component] = $subset;
        }

        return $registry;
    }

    public static function load()
    {
        // load software components registry
        $registry = include __DIR__ . '\registry\wpnxm-software-registry.php';

        // ensure registry array is available
        if (!is_array($registry)) {
            header("HTTP/1.0 404 Not Found");
        }

        return $registry;
    }


    public static function sort(array $registry)
    {
        // sort registry (software components in alphabetical order)
        ksort($registry);

        // sort registry (version numbers in lower-to-higher order)
        // maintain "name" and "website" keys on top, then versions, then "latest" key on bottom.
        foreach ($registry as $component => $array) {
           // sort by version number
           // but version_compare does not seem to work on x.y.z{alpha} version numbers
           if($component === 'openssl') {
               uksort($array, 'strnatcmp');
           } else {
               uksort($array, 'version_compare');
           }

            // move 'latest' to the bottom of the arary
            $value = $array['latest'];
            unset($array['latest']);
            $array['latest'] = $value;

            // move 'name' to the top of the array
            if (array_key_exists('name', $array) === true) {
                $temp = array('name' => $array['name']);
                unset($array['name']);
                $array = $temp + $array;
            }

            $registry[$component] = $array;
         }

        return $registry;
    }

    public static function prettyPrint(array $registry)
    {
        $content = var_export( $registry, true ) . ';';

        return ArrayTool::removeTrailingSpaces($content);
    }
}

class ArrayTool
{
    public static function clean(array $array)
    {
        $array = self::unsetNullValues($array);
        $array = self::removeDuplicates($array);

        return $array;
    }
    /**
     * Removes all keys with value "null" from the array and returns the array.
     *
     * @param $array Array
     * @return $array
     */
    public static function unsetNullValues(array $array)
    {
        foreach ($array as $key => $value) {
            if ($value === null) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Removes duplicates from the array.
     *
     * @param $array Array
     * @return $array
     */
    public static function removeDuplicates(array $array)
    {
        return array_map("unserialize", array_unique(array_map("serialize", $array)));
    }

    /**
     * Strips EOL spaces from the content.
     * Note: PHP's var_export() adds EOL spaces after array keys, like "'key' => ".
     *       I consider this a PHP bug. Anyway. Let's get rid of that.
     */
    public static function removeTrailingSpaces($content)
    {
        $lines = explode("\n", $content);
        foreach ($lines as $idx => $line) {
            $lines[$idx] = rtrim($line);
        }
        $content = implode("\n", $lines);

        return $content;
    }
}

class StatusRequest
{
    public static function getUrlsToCrawl($registry)
    {
        // build array with URLs to crawl
        $urls = array();
        foreach ($registry as $software => $versions) {
            foreach ($versions as $version => $url) {
                if ($version === 'latest') {
                    $urls[] = $url['url'];
                }
            }
            $urls[] = 'http://wpn-xm.org/get.php?s=' . $software;
        }

        return $urls;
    }

    /**
     * Returns the HTTP Status Code
     */
    public static function getHttpStatusCode($url)
    {
        $headers = get_headers($url, 0);

        return substr($headers[0], 9, 3);
    }

    /*
     * Returns cURL responses (http status code) for multiple target URLs (CurlMultiResponses).
     *
     * @param array $targetUrls Array of target URLs for cURL
     * @return array cURL Responses
     */
    public static function getHttpStatusCodesInParallel(array $targetUrls, $timeout = 15)
    {
        // get number of urls
        $count = count($targetUrls);

        // set cURL options
        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,         // do not output to browser
            CURLOPT_NOPROGRESS => true,
            //CURLOPT_URL => $url,
            CURLOPT_NOBODY => true,                 // do HEAD request only, exclude the body from output
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FORBID_REUSE => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSLVERSION => 3,
            CURLOPT_ENCODING => '',                 // !important
            CURLOPT_AUTOREFERER => true,
            CURLOPT_USERAGENT, 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/'
        );

        // initialize multiple cURL handler
        $mh = curl_multi_init();
        
        $ch = array(); // cUrl handles storage

        for($i = 0; $i < $count; $i++) {
          // create multiple cURL handles
          $ch[$i] = curl_init($targetUrls[$i]);
          // set cURL options for each handle
          curl_setopt_array($ch[$i], $options);
          // Add the handles to the curl_multi handle
          curl_multi_add_handle($mh, $ch[$i]);
        }

        // Execute Multi curl
        $running = null;
        do {
          curl_multi_exec($mh, $running);
        } while ($running > 0);

        // Response Handling
        $responses = array();

        // Remove the handles and return the response
        for($i = 0; $i < $count; $i++) {
          curl_multi_remove_handle($mh, $ch[$i]);

          // Response: Content
          //$responses[$i] = curl_multi_getcontent($ch[$i]);

          //echo $targetUrls[$i];
          //var_dump($responses[$i]);

          // Response: HTTP Status Code
          $responses[$i]  = curl_getinfo($ch[$i], CURLINFO_HTTP_CODE) == 200; // check if HTTP OK
        }

        // Close multiple cURL handler
        curl_multi_close($mh);

        return $responses;
    }
}