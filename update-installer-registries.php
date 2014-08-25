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

/**
 * This scripts generates
 *  - download definitions for each BigPack installation wizard (download-*.txt)
 *  - installation wizard registries for each installation wizard (installer-*.json).
 *
 * download.txt
 * ------------
 *
 * The file is versionized as "downloads-{phpXY}-{x86/64}.txt".
 *
 * A downloads.txt file contain all downloads for the "Full" Installation Wizard for that specific PHP version.
 * The build task "download-components" from "build.xml" uses aria2c and the "downloads.txt" to
 * download all files in parallel to the "/downloads" dir.
 * This implies, that the registry files must be available in the main WPN-XM folder.
 * This is done by fetching the registry repository as a git submodule inside the WPN-XM main repository.
 *
 * A download of the software components is only required, when building the "not-web" installation wizards.
 * The webinstallers acquire their download urls from the website and download their packages from the web.
 *
 * The other installation wizards (lite, standard) only use a subset of the components downloaded for the "full" wizard.
 * The subsets are created by copying from the /downloads folder, to a own versionized subfolder for this installation wizard.
 * E.g. all packages to be included in the lite installation wizard are copied from /downloads to /downloads/lite-{versionized}.
 *
 * installer.json
 * --------------
 *
 * The file is versionized as "{installer}.json".
 *
 * The installer.json files are used to identify the version number for all packages
 * of an installation wizard. The data is used on the websites download list.
 */

set_time_limit(60 * 3);
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

// load software components registry
$registry = include __DIR__ . '/registry/wpnxm-software-registry.php';

echo '<h2>Generating Software Registry Files...</h2>';

$php_version      = '';

/**
 * Iterate the installer array and identify the version numbers for all components
 * then write the registry file for the installer.
 */

// incomming $_POST array
// containing key "versions" containing an array with "component" => "version" relationship
// and key "installer-registry-name"

foreach($registryData as $component => $version)
{

}

    foreach ($components as $i => $component) {
        $components[$i][3] = getVersionFromMainRegistry($component[0], $component[1]);

        // create aria download file content, by concatenating download url with version and their target names
        if ($installer === 'full-w32') {
            if ($component[0] === 'php') { # or $component[0] === 'php-x64') {
                $php_version = substr($components[$i][3], 0, 3);
            }
            if (false !== strpos($component[0], 'phpext_')) {
                # $components[$i][1] . '&v=' . $components[$i][3] . '&p=' . $php_version . PHP_EOL;
            } else {
                # $components[$i][1] . '&v=' . $components[$i][3] . PHP_EOL;
            }
        }
    }
    // get PHP version in the format major.minor
    foreach($component as $component) {
        if ($component[0] === 'php' or $component[0] === 'php-x64') {
            $php_version = substr($components[3], 0, 3);
        }
    }

    $file = __DIR__ . '\registry\\' . $installer . '.json';

    writeRegistryFileJson($file, $components);

echo 'Done. <br> <br> You might commit the registries and then trigger a new build.';

#######################################################################################################################

/**
 * Returns the version number for a given component.
 * The URL string is parsed.
 * If the download URL contains "&v=x.y.z", then return this static version number.
 * if "&v=" is not set, then return the "latest version" from the registry
 *
 * @param string $component
 * @param string $link
 * @return string Version Number
 */
function getVersionFromMainRegistry($component, $link)
{
    global $registry;

    parse_str($link, $result);

    return (isset($result['v']) === true) ? $result['v'] : $registry[$component]['latest']['version'];
}

/**
 * @param string $file
 * @param array $registry
 */
function writeRegistryFileJson($file, $registry)
{
    asort($registry);

    $json        = json_encode($registry);
    $json_pretty = jsonPrettyPrintCompact($json);
    $json_table  = jsonPrettyPrintTableFormat($json_pretty);

    file_put_contents($file, $json_table);

    echo 'Created ' . $file . '<br />';
}

/**
 * Returns compacted, pretty printed JSON data.
 * Yes, there is JSON_PRETTY_PRINT, but it is odd at printing compact.
 *
 * @param string $json The unpretty JSON encoded string.
 * @return string Pretty printed JSON.
 */
function jsonPrettyPrintCompact($json)
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
        } else if ($char === '{' || $char === '[') {
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
function jsonPrettyPrintTableFormat($json)
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
    $numberOfSpacesToAdd = function($longest_line_length, $line_length) {
        return ($longest_line_length - $line_length) + 2; // were the magic happens
    };

    // append certain number of spaces to string
    $appendSpaces = function($num, $string) {
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

    // cleanups
    $lines = str_replace(',,', ',', $lines);
    $lines = str_replace('],', "],\n", $lines);

    // remove spaces before '['
    //$lines = preg_replace('/\s*[/', '', $lines);
    $lines = str_replace('     [', '[', $lines);
    $lines = str_replace('    [', '[', $lines);
    $lines = str_replace('   [', '[', $lines);
    $lines = str_replace('  [', '[', $lines);
    $lines = str_replace(' [', '[', $lines);

    $lines = "[\n" . trim($lines) . "\n]";

    return $lines;
}
