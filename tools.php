<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

require_once __DIR__ . '/vendor/goutte.phar';

use Goutte\Client as GoutteClient;

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
            $crawler_file = str_replace('-', '_', $single_component);
            $crawlers = glob(__DIR__ . '\crawlers\\' . $crawler_file . '.php');
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

            #echo $component . ' - ' . $file;

            /* set registry and crawling client to version crawler */
            $crawler->setRegistry($this->registry, $component);

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
        $requests = array();

        foreach($this->urls as $idx => $url) {
            // guzzle does not accept an array of URLs anymore
            // now Urls must be objects implementing the \GuzzleHttp\Message\RequestInterface
            $requests[] = $this->guzzleClient->createRequest('GET', $url, ['allow_redirects' => true]);
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

            if($response instanceOf GuzzleHttp\Exception\RequestException) {
                echo $response->getMessage(); // Get the exception message
            }

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

            // get old and new version for comparison.
            // if crawler is new and component not in registry, use 0.0.0
            if(!isset($this->old_registry[$component]['latest']['version'])) {
                $old_version = '0.0.0';
            } else {
                $old_version = $this->old_registry[$component]['latest']['version'];
            }

            $new_version = $this->registry[$component]['latest']['version'];

            // write temporary component registry, for later registry insertion
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
        $content .= "    * WPИ-XM Server Stack\n";
        $content .= "    * Copyright © 2010 - " . date("Y") . " Jens-André Koch <jakoch@web.de>\n";
        $content .= "    * http://wpn-xm.org/\n";
        $content .= "    *\n";
        $content .= "    * This source file is subject to the terms of the MIT license.\n";
        $content .= "    * For full copyright and license information, view the bundled LICENSE file.\n";
        $content .= "    */\n";
        $content .= "\n";
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
        $content .= ";\n";

        // write new registry
        return (bool) file_put_contents(__DIR__ . '/registry/wpnxm-software-registry.php', $content);
    }

    public static function getArrayForNewComponent($component, $url, $version, $website, $phpversion)
    {
        $version = (string) $version;

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
        $sort = function ($versionA, $versionB) {
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
     * @return string
     */
    public static function prettyPrint(array $registry)
    {
        ksort($registry);

        $content = var_export($registry, true);

        $content = str_replace('array (', 'array(', $content);

        $content = preg_replace('/\n\s+array/', 'array', $content);

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

class InstallerRegistry
{
    /**
     * @param string $file
     * @param array $registry
     */
    public function writeRegistryFileJson($file, $registry)
    {
        asort($registry);

        $json        = json_encode($registry);
        $json_pretty = JsonHelper::jsonPrettyPrintCompact($json);
        $json_table  = JsonHelper::jsonPrettyPrintTableFormat($json_pretty);

        file_put_contents($file, $json_table);

        echo 'Updated or Created Installer Registry "' . $file . '"<br />';
    }
}

class JsonHelper
{
    /**
     * Returns compacted, pretty printed JSON data.
     * Yes, there is JSON_PRETTY_PRINT, but it is odd at printing compact.
     *
     * @param string $json The unpretty JSON encoded string.
     * @return string Pretty printed JSON.
     */
    public static function jsonPrettyPrintCompact($json)
    {
        $out   = '';
        $nl    = "\n";
        $cnt   = 0;
        $tab   = 1;
        $len   = strlen($json);
        $space = ' ';
        $k     = strlen($space) ? strlen($space) : 1;

        for ($i = 0; $i <= $len; $i++) {

            $char = substr($json, $i, 1);

            if ($char === '}' || $char === ']') {
                $cnt--;
                if ($i + 1 === $len) { // newline before last ]
                    $out .= $nl;
                } else {
                    $out .= str_pad('', ($tab * $cnt * $k), $space);
                }
            } elseif ($char === '{' || $char === '[') {
                $cnt++;
                if ($cnt > 1) {
                    $out .= $nl;
                } // no newline on first line
            }

            $out .= $char;

            if ($char === ',' || $char === '{' || $char === '[') {
                /* $out .= str_pad('', ($tab * $cnt * $k), $space); */
                if ($cnt >= 1) {
                    $out .= $space;
                }
            }
            if ($char === ':' && '\\' !== substr($json, $i + 1, 1)) {
                $out .= ' ';
            }
        }
        return $out;
    }

    /**
     * JSON Table Format
     * Like "tab separated value" (TSV) format, BUT with spaces :)
     * Aligns values correctly underneath each other.
     * jakoch: my tackling of this indention problem is ugly, but it works.
     * @param string $json
     */
    public static function jsonPrettyPrintTableFormat($json)
    {
        $lines = explode("\n", $json);

        $array = array();

        // count lengths and set to array
        foreach ($lines as $line) {
            $line       = trim($line);
            $commas     = explode(", ", $line);
            $keyLengths = array_map('strlen', array_values($commas));
            $array[]    = array('lines' => $commas, 'lengths' => $keyLengths);
        }

        // calculate the number of missing spaces
        $numberOfSpacesToAdd = function ($longest_line_length, $line_length) {
            return ($longest_line_length - $line_length) + 2; // were the magic happens
        };

        // append certain number of spaces to string
        $appendSpaces = function ($num, $string) {
            for ($i = 0; $i <= $num; $i++) {
                $string .= ' ';
            }
            return $string;
        };

        // chop of first and last element of the array: the brackets [,]
        unset($array[0]);
        $last_nr = count($array);
        unset($array[$last_nr]);

        // walk through multi-dim array and compare key lengths
        // build array with longest key lengths
        $elements = $last_nr - 1;
        $num_keys = count($array[1]['lines']) - 1;
        $longest  = array();

        for ($i = 1; $i <= $elements; $i++) {
            for ($j = 0; $j < $num_keys; $j++) {
                $key_length = $array[$i]['lengths'][$j];
                if (isset($longest[$j]) === true && $longest[$j] >= $key_length) {
                    continue;
                }
                $longest[$j] = $key_length;
            }
        }

        // appends the missing number of spaces to the elements
        // to align them correctly underneath each other
        for ($i = 1; $i <= $elements; $i++) {
            for ($j = 0; $j < $num_keys; $j++) {
                // append spaces to the element
                $newElement = $appendSpaces(
                    $numberOfSpacesToAdd($longest[$j], $array[$i]['lengths'][$j]), $array[$i]['lines'][$j]
                );

                // reinsert the element
                $array[$i]['lines'][$j] = $newElement;
                //$array[$i]['lengths'][$j] = $longest[$j];
            }
        }

        // build output string from array
        $lines = '';
        foreach ($array as $idx => $values) {
            foreach ($values['lines'] as $key => $value) {
                $lines .= $value;
            }
        }

        // reinsert commas
        $lines = str_replace('"  ', '", ', $lines);

        // remove spaces before '['
        $lines = preg_replace('#\s+\[#i', '[', $lines);

        // cleanups
        $lines = str_replace(',,', ',', $lines);
        $lines = str_replace('],', "],\n", $lines);

        $lines = "[\n" . trim($lines) . "\n]";

        return $lines;
    }

}

class StatusRequest
{
    /**
     * Builds an array with Download URLs to the WPN-XM Server
     *
     * http://wpn-xm.org/get.php?s=%software%
     *
     * http://wpn-xm.org/get.php?s=%software%&p=%phpversion%&bitsize=%bitsize%
     *
     * @param type $registry
     * @return array
     */
    public static function getUrlsToCrawl($registry)
    {
        // build array with URLs to crawl
        $urls = array();

        foreach ($registry as $software => $keys) {

            // if software is a PHP Extension, we have a latest version with URLs for multiple PHP versions
            if (strpos($software, 'phpext_') !== false) {
                $bitsizes = $keys['latest']['url'];
                foreach ($bitsizes as $bitsize => $phpversions) {
                    foreach ($phpversions as $phpversion => $url) {
                        $urls[] = $url;
                        $urls[] = 'http://wpn-xm.org/get.php?s=' . $software . '&p=' . $phpversion . '&bitsize=' . $bitsize;
                    }
                }
            } else {
                // standard software component (without php constraints)
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
