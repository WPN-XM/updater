<?php
   /**
    * WPИ-XM Server Stack
    * Jens-André Koch © 2010 - onwards
    * http://wpn-xm.org/
    *
    *        _\|/_
    *        (o o)
    +-----oOO-{_}-OOo------------------------------------------------------------------+
    |                                                                                  |
    |    LICENSE                                                                       |
    |                                                                                  |
    |    WPИ-XM Serverstack is free software; you can redistribute it and/or modify    |
    |    it under the terms of the GNU General Public License as published by          |
    |    the Free Software Foundation; either version 2 of the License, or             |
    |    (at your option) any later version.                                           |
    |                                                                                  |
    |    WPИ-XM Serverstack is distributed in the hope that it will be useful,         |
    |    but WITHOUT ANY WARRANTY; without even the implied warranty of                |
    |    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
    |    GNU General Public License for more details.                                  |
    |                                                                                  |
    |    You should have received a copy of the GNU General Public License             |
    |    along with this program; if not, write to the Free Software                   |
    |    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA    |
    |                                                                                  |
    +----------------------------------------------------------------------------------+
    */

$start = microtime(true);

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

require_once __DIR__ . '/vendor/goutte.phar';

use Goutte\Client as GoutteClient;
use Guzzle\Common\Exception\MultiTransferException;

// load software components registry
$registry = include __DIR__ . '\registry\wpnxm-software-registry.php';

// clone old registry for comparing latest versions (see html section below)
$old_registry = $registry;

// ensure registry array is available
if (!is_array($registry)) {
    header("HTTP/1.0 404 Not Found");
}

// init Goutte and set header for all requests
$goutteClient = new GoutteClient();
$goutteClient->setHeader('User-Agent', 'WPN-XM Server Stack - Software Registry Update Tool - http://wpn-xm.org/');

// fetch Guzzle out of Goutte and deactivate SSL Verification
$guzzleClient = $goutteClient->getClient();
$guzzleClient->setSslVerification(false);

/**
 * Removes all keys with value "null" from the array and returns the array.
 *
 * @param $array Array
 * @return $array
 */
function array_unset_null_values(array $array)
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
function array_remove_duplicates(array $array)
{
    return array_map("unserialize", array_unique(array_map("serialize", $array)));
}

/**
 * Adds array data to the main software component array.
 *
 * @param $name Name of Software Component
 * @param $array Subarray of a software component, which should be added to the main array.
 */
function add($name, array $array)
{
    global $registry;

    // cleanup array
    $array = array_unset_null_values($array);

    $array = array_remove_duplicates($array);

    if (isset($array['url']) and isset($array['version'])) {
        // the array contains only one element

        // create [latest] sub-array
        $registry[$name]['latest']['url'] = $array['url'];
        $registry[$name]['latest']['version'] = $array['version'];

        // create [version] => [url] relationship
        $registry[$name][ $array['version'] ] = $array['url'];

        unset($array);
    } else {
        // sort by version number, from low to high
        asort($array);

        // add the last array item of multiple elements (the one with the highest version number)

        // insert the last array item as [latest][version] => [url]
        $registry[$name]['latest'] = array_pop($array);

        // insert the last array item also as a pure [version] => [url] relationship
        $registry[$name][ $registry[$name]['latest']['version'] ] = $registry[$name]['latest']['url'];
    }

    // added remaining array items (if any) as pure [version] => [url] relationships
    if (false === empty($array)) {
        foreach ($array as $new_version_entry) {
            $registry[$name][ $new_version_entry['version'] ] = $new_version_entry['url'];
        }
    }

    asort($registry[$name]);
}

/**
 * PHP release files are moved from "/releases" to "/releases/archives".
 * That means, latest version must point to "/releases".
 * Every other version points to "/releases/archives".
 */
function adjust_php_download_path()
{
    global $registry;

    foreach ($registry['php'] as $version => $url) {
        // do not modify array key "latest"
        if( $version === 'latest') continue;
        // do not modify array key with latest version number - it must point to "/releases".
        if( $version === $registry['php']['latest']['version']) continue;
        // replace the path on any other version
        $new_url = str_replace('php.net/downloads/releases/php', 'php.net/downloads/releases/archives/php', $url);
        // insert at old array position, overwriting the old url
        $registry['php'][$version] = $new_url;
    }
}

/**
 * Writes the registry array to a php file for (re-)inclusion.
 * e.g.
 *  $registry = include 'registry.php';
 *
 * @param $registry The registry array.
 */
function write_registry_file(array $registry)
{
    // backup current registry
    rename( __DIR__ . '/registry/wpnxm-software-registry.php', __DIR__ . '/registry/wpnxm-software-registry-backup-' . date("dmy-His") . '.php' );

    // file header
    $content = "<?php\n";
    $content .= "   /**\n";
    $content .= "    * WPN-XM Software Registry\n";
    $content .= "    * ------------------------\n";
    $content .= "    * Last Update " . date(DATE_RFC2822) . ".\n";
    $content .= "    * Do not edit manually!\n";
    $content .= "    */\n";
    $content .= "\n return ";

    // sort registry (software components in alphabetical order)
    ksort($registry);

    // sort registry (version numbers in lower-to-higher order)
    // maintain "name" and "website" keys on top, then versions, then "latest" key on bottom.
    foreach ($registry as $component => $array) {
        uksort($array, 'version_compare');

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

    // pretty print the array
    $content .= var_export( $registry, true ) . ';';

    // remove trailing spaces
    $content = removeEOLSpaces($content);

    // write new registry
    file_put_contents(__DIR__ . '/registry/wpnxm-software-registry.php', $content );
}

/**
 * Strips EOL spaces from the content.
 * Note: PHP's var_export() adds EOL spaces after array keys, like "'key' => ".
 *       I consider this a PHP bug. Anyway. Let's get rid of that.
 */
function removeEOLSpaces($content)
{
    $lines = explode("\n", $content);
    foreach ($lines as $idx => $line) {
        $lines[$idx] = rtrim($line);
    }
    $content = implode("\n", $lines);

    return $content;
}

/**
 * The function prints an update symbol if old_version is lower than new_version.
 *
 * @param string Old version.
 * @param string New version.
 */
function printUpdatedSign($old_version, $new_version)
{
    if (version_compare($old_version, $new_version, '<') === true) {
        $html = '<span class="label label-success">';
        $html .= $new_version;
        $html .= '</span><span style="color:green; font-size: 16px">&nbsp;&#x25B2;</span>';

        return $html;
    }

    return $new_version;
}

function renderTableRow($component)
{
    global $old_registry, $registry;

    $html = '<tr>';
    $html .= '<td>' . $component . '</td>';
    $html .= '<td>' .  $old_registry[$component]['latest']['version'] . '</td>';
    $html .= '<td>' .  printUpdatedSign($old_registry[$component]['latest']['version'], $registry[$component]['latest']['version']) . '</td>';
    $html .= '</tr>';

    return $html;
}

/******************************************************************************/

/**
 * Get Latest Versions and add them to the registry.
 */
$crawlers = glob(__DIR__ . '\crawlers\*.php');
include __DIR__ . '/VersionCrawler.php';

foreach ($crawlers as $i => $crawlerFile) {

    // load and instantiate Version Crawlers
    include $crawlerFile;
    $file = strtolower(pathinfo($crawlerFile, PATHINFO_FILENAME));
    $namespace = 'WPNXM\Updater\Crawler\\';
    $classname = $namespace . ucfirst($file);
    $crawler = new $classname;

    /* modifiy crawler object */

    // ask crawler, if full registry or "component/self" subset is needed
    // use-case: full registry for phpext_xcache, depends on php version number
    if($crawler->needsOnlyRegistrySubset === true) {
        $crawler->setRegistry($registry, $file);
    } else {
        $crawler->setRegistry($registry);
    }

    if($crawler->needsGuzzle === true) {
        $crawler->setGuzzle($guzzleClient);
    }

    // store crawler object under its filename in the crawlers array
    $crawlers[$i] = $crawler;

    // fetch URL from Version Crawler Object and prepare array with all URLs to crawl
    $URLs[] = $guzzleClient->get( $crawler->getURL() );
}

#var_dump($crawlers);

// launch several URL requests in parallel.
// response time will be the time of the longest request.
try {
    $responses = $guzzleClient->send($URLs);
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
//var_dump($responses);

$tableHtml = '';

// iterate through responses and insert them in the crawler objects
foreach ($responses as $i => $response) {

    // set the response to the version crawler object
    $crawlers[$i]->addContent( $response->getBody(), $response->getContentType() );

    $component = $crawlers[$i]->getName();
    $latestVersion = $crawlers[$i]->crawlVersion();

    // add new version number to the registry
    add($component, $latestVersion);

    // render a table row for comparing old and new version numbers
    $tableHtml .= renderTableRow($component);
}
#var_dump($crawlers);

adjust_php_download_path();

/*var_dump($registry);*/

// handle $_GET['action']
// example call: registry-update.php?action=write-file
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if (isset($action) && $action === 'write-file') {
    write_registry_file($registry);
}
?>

<table class="table table-condensed table-hover">
<thead>
    <tr>
        <th>Software Components (<?php echo $i; ?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th>
    </tr>
</thead>
<?php echo $tableHtml; ?>
</table>
<?php echo 'Used a total of ' . round((microtime(true) - $start), 2) . ' seconds' . PHP_EOL;
