<?php
require_once __DIR__ . '/vendor/goutte.phar';

use Goutte\Client as GoutteClient;
use Guzzle\Common\Exception\MultiTransferException;

class RegistryUpdater
{
    public $guzzleClient;
    public $crawlers = array();
    public $urls     = array();
    public $results  = array();
    public $registry     = array();
    public $old_registry = array();

    public function __construct($registry)
    {
        $this->registry     = $registry;
        $this->old_registry = $registry;
    }

    public function setupCrawler()
    {
        // init Goutte and set header for all requests
        $goutteClient = new GoutteClient();
        $goutteClient->setHeader('User-Agent', 'WPN-XM Server Stack - Software Registry Update Tool - http://wpn-xm.org/');

        // fetch Guzzle out of Goutte and deactivate SSL Verification
        $this->guzzleClient = $goutteClient->getClient();
        $this->guzzleClient->setDefaultOption('verify', false);

        $goutteClient->setClient($this->guzzleClient);
    }

    public function getUrlsToCrawl($single_component = null)
    {
        if (isset($single_component) === true) {
            $crawlers = glob(__DIR__ . '\crawlers\\' . $single_component . '.php');
        } else {
            $crawlers = glob(__DIR__ . '\crawlers\*.php');
        }

        include __DIR__ . '/VersionCrawler.php';

        foreach ($crawlers as $i => $file) {

            // instantiate version crawler
            include $file;
            $component = str_replace(array('-', '.'), array('_', '_'), strtolower(pathinfo($file, PATHINFO_FILENAME)));
            $classname = 'WPNXM\Updater\Crawler\\' . ucfirst($component);
            $crawler   = new $classname;

            /* set registry and crawling client to version crawler */
            $crawler->setRegistry($this->registry, $component);
            //$crawler->setGuzzle($this->guzzleClient);
            // store crawler object in crawlers array
            $this->crawlers[$i] = $crawler;

            // fetch URL from Version Crawler Object and prepare array with all URLs to crawl
            $this->urls[] = $crawler->getURL();
        }

        return $i;
    }

    /**
     * Crawl launches several URL requests in parallel.
     * The response time will be the time of the longest request.
     */
    public function crawl()
    {
        foreach($this->urls as $idx => $url) {
            // guzzle does not accept an array of URLs anymore
            // now Urls must be objects implementing the \GuzzleHttp\Message\RequestInterface
            $requests[] = $this->guzzleClient->createRequest('GET', $url);
        }

        $this->results = GuzzleHttp\batch($this->guzzleClient, $requests);
    }

    public function evaluateResponses()
    {
        $html = '';
        $i    = 0;

        // responses is an SplObjectStorage object where each request is a key
        // iterate through responses and insert them in the crawler objects
        foreach ($this->results as $request) {

            $new_version = $old_version = '';

            $response = $this->results[$request];

            // set the response to the version crawler object
            $this->crawlers[$i]->addContent($response->getBody(), $response->getHeader('Content-Type'));

            $component     = $this->crawlers[$i]->getName();
            $latestVersion = $this->crawlers[$i]->crawlVersion();
            $latestVersion = ArrayTool::clean($latestVersion);

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
            
            if (isset($new_version) === true) {
                //  Welcome in Version Compare Hell!
                switch ($component) {
                    case 'openssl':
                        if(strcmp($old_version, $new_version) < 0) {
                            Registry::writeRegistrySubset($component, $this->registry[$component]);
                        }
                        break;
                    case 'phpmyadmin':
                        if(version_compare($old_version, $new_version, '<') === 1 || (strcmp($old_version, $new_version) < 0)) {
                            Registry::writeRegistrySubset($component, $this->registry[$component]);
                        }
                        break;
                   case  'imagick':
                        if(Version::cmpImagick($old_version, $new_version) === 1) {
                            Registry::writeRegistrySubset($component, $this->registry[$component]);
                        }
                        break;
                    default: 
                        if(version_compare($old_version, $new_version, '<=')) {
                            Registry::writeRegistrySubset($component, $this->registry[$component]);
                        }
                        break;
                }
            }

            // render a table row for version comparison
            $html .= Viewhelper::renderTableRow($component, $old_version, $new_version);

            $i++;
        }

        return $html;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

}

class Version
{

    // compare 1.2.3-1 vs. 1.2.3-4
    public static function cmpImagick($a, $b)
    {
        $a_array = explode('-', $a);
        $b_array = explode('-', $b);

        $vc1 = version_compare($a_array[0], $b_array[0]);
        $vc2 = Version::cmp($a_array[1], $b_array[1]);

        #var_dump($a_array, $b_array, $vc1, $vc2);

        if ($vc1 === 0 && $vc2 === 0) { // equal
            return 0;
        }

        if ($vc1 === -1 && $vc2 === -1) {   // (1.2.4-1, 1.4.0-9) = a greater b (-1, -1)
            return -1;
        }

        if (($vc1 === 0 && $vc2 === -1)     // (1.2.3-1, 1.2.3-2) = a lower b ( 0, -1)
            or ($vc1 === -1 && $vc2 === 0)) {    // (1.2.3-1, 1.2.4-1) = a lower b (-1, 0)
            return 1;
        }
        return -1;
    }

    /**
     * If a lower   b, -1
     * If a greater b,  1
     * If a equals  b,  0
     */
    public static function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
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
    public static function printUpdatedSign($old_version, $new_version, $component)
    {
        if (version_compare($old_version, $new_version, '<') === true || (strcmp($old_version, $new_version) < 0)) {
            $html = '<span class="badge alert-success">';
            $html .= $new_version;
            $html .= '</span><span style="color:green; font-size: 16px">&nbsp;&#x25B2;&nbsp;</span>';

            $html .= '<a class="btn btn-default btn-xs"';
            $html .= ' href="registry-update.php?action=update-component&component=' . $component;
            $html .= '">Commit & Push</a>';

            return $html;
        }
    }

    public static function renderTableRow($component, $old_version, $new_version)
    {
        $html = '<tr>';
        $html .= '<td>' . $component . '</td>';
        $html .= '<td>' . $old_version . '</td>';
        $html .= '<td>' . self::printUpdatedSign($old_version, $new_version, $component) . '</td>';
        $html .= '<td><a class="btn btn-default btn-xs"';
        $html .= ' href="registry-update.php?action=scan&component=' . $component . '">Scan</a></td>';
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
        rename(
            __DIR__ . '/registry/wpnxm-software-registry.php',
            __DIR__ . '/registry/wpnxm-software-registry-backup-' . date("dmy-His") . '.php'
        );

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
        $content .= Registry::prettyPrint($registry);
        $content .= ';';

        // write new registry
        return (bool) file_put_contents(__DIR__ . '/registry/wpnxm-software-registry.php', $content);
    }

    public static function getArrayForNewComponent($component, $url, $version, $website, $phpversion)
    {
        // array structure for PHP Extensions must take PHP Versions into account
        if (strpos($component, 'phpext_') !== false) {
            return array(
                'name'    => $component,
                'website' => $website,
                $version  => array(
                    $phpversion => $url
                ),
                'latest'  => array(
                    'version' => $version,
                    'url'     => array(
                        $phpversion => $url
                    )
                )
            );
        }

        return array(
            'name'    => $component,
            'website' => $website,
            $version  => $url,
            'latest'  => array(
                'version' => $version,
                'url'     => $url
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
        if (isset($latestVersion['url']) === true and isset($latestVersion['version']) === true) {
            // the array contains only one element
            // create [latest] sub-array
            $registry[$name]['latest']['url']     = $latestVersion['url'];
            $registry[$name]['latest']['version'] = $latestVersion['version'];

            // create [version] => [url] relationship
            $registry[$name][$latestVersion['version']] = $latestVersion['url'];

            unset($latestVersion);
        } else {
            // sort by version number, from low to high
            $latestVersion = static::sortArrayByVersion($latestVersion);            

            // add the last array item of multiple elements (the one with the highest version number)
            // insert the last array item as [latest][version] => [url]
            $registry[$name]['latest'] = array_pop($latestVersion);

            // insert the last array item also as a pure [version] => [url] relationship
            $registry[$name][$registry[$name]['latest']['version']] = $registry[$name]['latest']['url'];
        }

        // added remaining array items (if any) as pure [version] => [url] relationships
        if (false === empty($latestVersion)) {
            foreach ($latestVersion as $new_version_entry) {
                $registry[$name][$new_version_entry['version']] = $new_version_entry['url'];
            }
        }
        
        return static::sort($registry);
    }

    public static function sortArrayByVersion($array)
    {
        $sort = function($versionA, $versionB) {
            return version_compare($versionA['version'], $versionB['version']);
        };
        usort($array, $sort);

        return $array;
    }

    public static function clearOldScans()
    {
        $scans = glob(__DIR__ . '\scans\*.php');
        if (count($scans) > 0) {
            foreach ($scans as $file) {
                unlink($file);
            }
        }
    }

    /**
     * @param $component Component Registry Shorthand (e.g. "phpext_xdebug", not "xdebug").
     * @param $registry The registry.
     */
    public static function writeRegistrySubset($component, $registry)
    {      
        return (bool) file_put_contents(
            __DIR__ . '/scans/latest-version-' . $component . '.php',
            sprintf("<?php\nreturn %s;", self::prettyPrint($registry))
        );
    }

    public static function addLatestVersionScansIntoRegistry(array $registry, $forComponent = '')
    {
        $scans = glob(__DIR__ . '\scans\*.php');

        // nothing to do, return early
        if (count($scans) === 0) {
            return false;
        }

        foreach ($scans as $i => $file) {
            $subset    = include $file;
            preg_match('/latest-version-(.*).php/', $file, $matches);
            $component = $matches[1];

            // add the registry subset only for a specific component
            if (isset($forComponent) && ($forComponent === $component)) {
                printf('Adding Scan/Subset for "%s"' . PHP_EOL, $component);
                $registry[$component] = $subset;
                return $registry;
            } elseif (isset($forComponent) && ($forComponent != $component)) {
                // skip to the next component, if forComponent is used, but not found yet
                continue;
            } else {
                // forComponent not set = add all
                $registry[$component] = $subset;
            }
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
            if ($component === 'openssl') {
                uksort($array, 'strnatcmp');
            } else {
                uksort($array, 'version_compare');
            }

            // move 'latest' to the bottom of the arary
            self::move_to_bottom($array, 'latest');

            // move 'name' and 'website' to the top of the array
            self::move_to_top($array, 'website');
            self::move_to_top($array, 'name');

            $registry[$component] = $array;
        }

        return $registry;
    }

    /**
     * This works on the array and moves the key to the top.
     *
     * @param array $array
     * @param string $key
     */
    private static function move_to_top(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $temp  = array($key => $array[$key]);
            unset($array[$key]);
            $array = $temp + $array;
        }
    }

    /**
     * This works on the array and moves the key to the bottom.
     *
     * @param array $array
     * @param string $key
     */
    private static function move_to_bottom(array &$array, $key)
    {
        if (isset($array[$key]) === true) {
            $value       = $array[$key];
            unset($array[$key]);
            $array[$key] = $value;
        }
    }

    /**
     * Pretty prints the registry.
     *
     * @param array $registry
     * @return array
     */
    public static function prettyPrint(array $registry)
    {
        ksort($registry);

        $content = var_export($registry, true);

        return ArrayTool::removeTrailingSpaces($content);
    }

    /**
     * Git commits and pushes the latest changes to the
     * wpnxm software registry with specified commit message.
     *
     * @param string $commitMessage Optional Commit Message
     */
    public static function gitCommitAndPush($commitMessage = '')
    {
        // switch to the git submodule "registry"
        chdir(__DIR__ . '/registry');

        echo 'Pull possible changes' . PHP_EOL;
        echo exec('git pull');

        //echo PHP_EOL . 'Staging current changes' . PHP_EOL;
        //exec('git add .; git add -u .');

        echo PHP_EOL . 'Commit current changes "' . $commitMessage . '"' . PHP_EOL;
        echo exec('git commit -m "'. $commitMessage .'" -- wpnxm-software-registry.php');

        echo PHP_EOL . 'You might push now.' . PHP_EOL;
        //echo PHP_EOL . 'Push commit to remote server' . PHP_EOL;
        //echo exec('git push');

        //echo '<a href="#" class="btn btn-lg btn-primary">'
        //   . '<span class="glyphicon glyphicon-save"></span> Git Push</a>';
    }
}

class ArrayTool
{
    /**
     * Unsets null values and removes duplicates.
     *
     * @param array $array
     * @return array
     */
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
     * @param string $content
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
/*
foreach ($registry as $software => $versions) {

            // if software is a php extension, we have might have URLs for multiple PHP versions

            if(strpos($software, 'phpext_') !== false) {
  foreach ($versions as $version => $phpversions) {
                if ($version === 'latest') {
                   foreach($phpversions as $phpversion => $url) {
                        $urls[] = $url;
                    }
                }
            }
 *
 *
                foreach ($versions as $version => $phpversions) {
                    foreach($phpversions as $phpversion => $url) {
                        $urls[] = $url;
                    }
                    $urls[] = 'http://wpn-xm.org/get.php?s=' . $software .'&p=' . $phpversion;
                }
            } else {
                foreach ($versions as $version => $url) {
                    if ($version === 'latest') {
                        $urls[] = $url['url'];
                    }
                }
                $urls[] = 'http://wpn-xm.org/get.php?s=' . $software;
            }
        }*/


class StatusRequest
{
    /**
     * Builds an array with Download URLs to the WPN-XM Server
     * http://wpn-xm.org/get.php?s=%software%
     *
     * @param type $registry
     * @return array
     */
    public static function getUrlsToCrawl($registry)
    {
        // build array with URLs to crawl
        $urls = array();

        foreach ($registry as $software => $keys) {

            // if software is a php extension,
            // we have might have a latest version with URLs for multiple PHP versions
            if (strpos($software, 'phpext_') !== false) {
                $phpversions = $keys['latest']['url'];
                foreach ($phpversions as $phpversion => $url) {
                    $urls[] = $url;
                    $urls[] = 'http://wpn-xm.org/get.php?s=' . $software . '&p=' . $phpversion;
                }
            } else {
                $urls[] = $keys['latest']['url'];
                $urls[] = 'http://wpn-xm.org/get.php?s=' . $software;
            }
        }

        #echo '<pre>' . var_export($urls, true) . '</pre>'; exit;

        return $urls;
    }

    /**
     * Returns the HTTP Status Code for a URL
     *
     * @param string $url URL
     * @return string
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

        $options = array(
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true, // do not output to browser
            CURLOPT_NOPROGRESS     => true,
            //CURLOPT_URL => $url,
            CURLOPT_NOBODY         => true, // do HEAD request only, exclude the body from output
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FORBID_REUSE   => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_ENCODING       => '', // !important
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_USERAGENT, 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/'
        );

        $mh = curl_multi_init();

        $ch = array();

        // create multiple cURL handles, set options and add them to curl_multi handler
        for ($i = 0; $i < $count; $i++) {
            $ch[$i] = curl_init($targetUrls[$i]);
            curl_setopt_array($ch[$i], $options);
            curl_multi_add_handle($mh, $ch[$i]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        $responses = array();

        // remove handles and return the responses
        for ($i = 0; $i < $count; $i++) {
            curl_multi_remove_handle($mh, $ch[$i]);

            // Response: Content
            //$responses[$i] = curl_multi_getcontent($ch[$i]);
            //echo $targetUrls[$i];
            //var_dump($responses[$i]);
            // Response: HTTP Status Code
            $responses[$i] = curl_getinfo($ch[$i], CURLINFO_HTTP_CODE) == 200; // check if HTTP OK
        }

        curl_multi_close($mh);

        return $responses;
    }

}