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
 * Generate wpnxm-software-registry.csv - Downloads
 *
 * This scripts generates individual download definitions per installation wizard "wpnxm-software-registry-{installer}.csv".
 * The registry file for the "BigPack" is used by the build task "download-components", see "build.xml".
 * The csv content is split up and the download urls are used on wget for fetching the downloads.
 * The file must be copied to the main WPN-XM folder - this is done by fetching this repository as a git submodule.
 * Downloading these software components is required when building the "not-web" Installers.
 *
 * The data from the csv files is also used on the websites download list.
 * Installers are itself versionzied. For each packaged component we can identify the version number.
 */

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

// load software components registry
$registry = include __DIR__ . '/registry/wpnxm-software-registry.php';

echo '<h2>Generating Software Registry Files...</h2>';

// Array containing the individual download definitions and version numbers for each installer
$lists = array();

/**
 * Array containg the downloads and version numbers for the "BigPack - w32" Installation Wizard.
 * Additional Components compared to "All In One": perl, postgresql
 */
$lists['bigpack-w32'] = array (
  // 0 => software, 1 => download url, 2 => target file name
  0  => array ( 0 => 'adminer', 1 => 'http://wpn-xm.org/get.php?s=adminer', 2 => 'adminer.php', ), // ! php file
  1  => array ( 0 => 'composer', 1 => 'http://wpn-xm.org/get.php?s=composer', 2 => 'composer.phar', ), // ! phar file
  2  => array ( 0 => 'junction', 1 => 'http://wpn-xm.org/get.php?s=junction', 2 => 'junction.zip', ),
  3  => array ( 0 => 'mariadb', 1 => 'http://wpn-xm.org/get.php?s=mariadb', 2 => 'mariadb.zip', ),
  4  => array ( 0 => 'memadmin', 1 => 'http://wpn-xm.org/get.php?s=memadmin', 2 => 'memadmin.zip', ),
  5  => array ( 0 => 'memcached', 1 => 'http://wpn-xm.org/get.php?s=memcached', 2 => 'memcached.zip', ),
  6  => array ( 0 => 'mongodb', 1 => 'http://wpn-xm.org/get.php?s=mongodb&v=2.0.8', 2 => 'mongodb.zip', ),
  7  => array ( 0 => 'nginx', 1 => 'http://wpn-xm.org/get.php?s=nginx', 2 => 'nginx.zip', ),
  8  => array ( 0 => 'openssl', 1 => 'http://wpn-xm.org/get.php?s=openssl', 2 => 'openssl.exe', ),
  9  => array ( 0 => 'pear', 1 => 'http://wpn-xm.org/get.php?s=pear', 2 => 'go-pear.phar', ), // ! phar file
  10 => array ( 0 => 'perl', 1 => 'http://wpn-xm.org/get.php?s=perl', 2 => 'perl.zip', ),
  11 => array ( 0 => 'php', 1 => 'http://wpn-xm.org/get.php?s=php', 2 => 'php.zip', ),
  12 => array ( 0 => 'phpext_amqp', 1 => 'http://wpn-xm.org/get.php?s=phpext_amqp', 2 => 'phpext_amqp.zip', ),
  13 => array ( 0 => 'phpext_apc', 1 => 'http://wpn-xm.org/get.php?s=phpext_apc', 2 => 'phpext_apc.zip', ),
  14 => array ( 0 => 'phpext_imagick', 1 => 'http://wpn-xm.org/get.php?s=phpext_imagick', 2 => 'phpext_imagick.zip', ),
  15 => array ( 0 => 'phpext_mailparse', 1 => 'http://wpn-xm.org/get.php?s=phpext_mailparse', 2 => 'phpext_mailparse.zip', ),
  16 => array ( 0 => 'phpext_memcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_memcache', 2 => 'phpext_memcache.zip', ), // without D
  17 => array ( 0 => 'phpext_mongo', 1 => 'http://wpn-xm.org/get.php?s=phpext_mongo', 2 => 'phpext_mongo.zip', ),
  18 => array ( 0 => 'phpext_msgpack', 1 => 'http://wpn-xm.org/get.php?s=phpext_msgpack', 2 => 'phpext_msgpack.zip', ),
  19 => array ( 0 => 'phpext_rar', 1 => 'http://wpn-xm.org/get.php?s=phpext_rar', 2 => 'phpext_rar.zip', ),
  20 => array ( 0 => 'phpext_trader', 1 => 'http://wpn-xm.org/get.php?s=phpext_trader', 2 => 'phpext_trader.zip', ),
  21 => array ( 0 => 'phpext_varnish', 1 => 'http://wpn-xm.org/get.php?s=phpext_varnish', 2 => 'phpext_varnish.zip', ),
  22 => array ( 0 => 'phpext_wincache', 1 => 'http://wpn-xm.org/get.php?s=phpext_wincache', 2 => 'phpext_wincache.zip', ),
  23 => array ( 0 => 'phpext_xcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_xcache', 2 => 'phpext_xcache.zip', ),
  24 => array ( 0 => 'phpext_xdebug', 1 => 'http://wpn-xm.org/get.php?s=phpext_xdebug', 2 => 'phpext_xdebug.dll', ), // ! dll file
  25 => array ( 0 => 'phpext_xhprof', 1 => 'http://wpn-xm.org/get.php?s=phpext_xhprof', 2 => 'phpext_xhprof.zip', ),
  26 => array ( 0 => 'phpext_zmq', 1 => 'http://wpn-xm.org/get.php?s=phpext_zmq', 2 => 'phpext_zmq.zip', ),
  27 => array ( 0 => 'phpmemcachedadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmemcachedadmin', 2 => 'phpmemcachedadmin.zip', ),
  28 => array ( 0 => 'phpmyadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmyadmin', 2 => 'phpmyadmin.zip', ),
  29 => array ( 0 => 'postgresql', 1 => 'http://wpn-xm.org/get.php?s=postgresql', 2 => 'postgresql.zip', ),
  30 => array ( 0 => 'redis', 1 => 'http://wpn-xm.org/get.php?s=redis', 2 => 'redis.zip', ),
  31 => array ( 0 => 'rockmongo', 1 => 'http://wpn-xm.org/get.php?s=rockmongo', 2 => 'rockmongo.zip', ),
  32 => array ( 0 => 'sendmail', 1 => 'http://wpn-xm.org/get.php?s=sendmail', 2 => 'sendmail.zip', ),
  33 => array ( 0 => 'webgrind', 1 => 'http://wpn-xm.org/get.php?s=webgrind', 2 => 'webgrind.zip', ),
  34 => array ( 0 => 'wpnxmscp', 1 => 'http://wpn-xm.org/get.php?s=wpnxmscp', 2 => 'wpnxmscp.zip', ),
  35 => array ( 0 => 'xhprof', 1 => 'http://wpn-xm.org/get.php?s=xhprof', 2 => 'xhprof.zip', ),
);

/**
 * Array containg the downloads and version numbers for the "All In One - w32" Installation Wizard.
 */
$lists['allinone-w32'] = array (
  // 0 => software, 1 => download url, 2 => target file name
  0  => array ( 0 => 'adminer', 1 => 'http://wpn-xm.org/get.php?s=adminer', 2 => 'adminer.php', ), // ! php file
  1  => array ( 0 => 'composer', 1 => 'http://wpn-xm.org/get.php?s=composer', 2 => 'composer.phar', ), // ! phar file
  2  => array ( 0 => 'junction', 1 => 'http://wpn-xm.org/get.php?s=junction', 2 => 'junction.zip', ),
  3  => array ( 0 => 'mariadb', 1 => 'http://wpn-xm.org/get.php?s=mariadb', 2 => 'mariadb.zip', ),
  4  => array ( 0 => 'memadmin', 1 => 'http://wpn-xm.org/get.php?s=memadmin', 2 => 'memadmin.zip', ),
  5  => array ( 0 => 'memcached', 1 => 'http://wpn-xm.org/get.php?s=memcached', 2 => 'memcached.zip', ),
  6  => array ( 0 => 'mongodb', 1 => 'http://wpn-xm.org/get.php?s=mongodb&v=2.0.8', 2 => 'mongodb.zip', ),
  7  => array ( 0 => 'nginx', 1 => 'http://wpn-xm.org/get.php?s=nginx', 2 => 'nginx.zip', ),
  8  => array ( 0 => 'openssl', 1 => 'http://wpn-xm.org/get.php?s=openssl', 2 => 'openssl.exe', ),
  9  => array ( 0 => 'pear', 1 => 'http://wpn-xm.org/get.php?s=pear', 2 => 'go-pear.phar', ), // ! phar file
  10 => array ( 0 => 'php', 1 => 'http://wpn-xm.org/get.php?s=php', 2 => 'php.zip', ),
  11 => array ( 0 => 'phpext_amqp', 1 => 'http://wpn-xm.org/get.php?s=phpext_amqp', 2 => 'phpext_amqp.zip', ),
  12 => array ( 0 => 'phpext_apc', 1 => 'http://wpn-xm.org/get.php?s=phpext_apc', 2 => 'phpext_apc.zip', ),
  13 => array ( 0 => 'phpext_imagick', 1 => 'http://wpn-xm.org/get.php?s=phpext_imagick', 2 => 'phpext_imagick.zip', ),
  14 => array ( 0 => 'phpext_mailparse', 1 => 'http://wpn-xm.org/get.php?s=phpext_mailparse', 2 => 'phpext_mailparse.zip', ),
  15 => array ( 0 => 'phpext_memcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_memcache', 2 => 'phpext_memcache.zip', ), // without D
  16 => array ( 0 => 'phpext_mongo', 1 => 'http://wpn-xm.org/get.php?s=phpext_mongo', 2 => 'phpext_mongo.zip', ),
  17 => array ( 0 => 'phpext_msgpack', 1 => 'http://wpn-xm.org/get.php?s=phpext_msgpack', 2 => 'phpext_msgpack.zip', ),
  18 => array ( 0 => 'phpext_rar', 1 => 'http://wpn-xm.org/get.php?s=phpext_rar', 2 => 'phpext_rar.zip', ),
  19 => array ( 0 => 'phpext_trader', 1 => 'http://wpn-xm.org/get.php?s=phpext_trader', 2 => 'phpext_trader.zip', ),
  20 => array ( 0 => 'phpext_varnish', 1 => 'http://wpn-xm.org/get.php?s=phpext_varnish', 2 => 'phpext_varnish.zip', ),
  21 => array ( 0 => 'phpext_wincache', 1 => 'http://wpn-xm.org/get.php?s=phpext_wincache', 2 => 'phpext_wincache.zip', ),
  22 => array ( 0 => 'phpext_xcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_xcache', 2 => 'phpext_xcache.zip', ),
  23 => array ( 0 => 'phpext_xdebug', 1 => 'http://wpn-xm.org/get.php?s=phpext_xdebug', 2 => 'phpext_xdebug.dll', ), // ! dll file
  24 => array ( 0 => 'phpext_xhprof', 1 => 'http://wpn-xm.org/get.php?s=phpext_xhprof', 2 => 'phpext_xhprof.zip', ),
  25 => array ( 0 => 'phpext_zmq', 1 => 'http://wpn-xm.org/get.php?s=phpext_zmq', 2 => 'phpext_zmq.zip', ),
  26 => array ( 0 => 'phpmemcachedadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmemcachedadmin', 2 => 'phpmemcachedadmin.zip', ),
  27 => array ( 0 => 'phpmyadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmyadmin', 2 => 'phpmyadmin.zip', ),
  28 => array ( 0 => 'redis', 1 => 'http://wpn-xm.org/get.php?s=redis', 2 => 'redis.zip', ),
  29 => array ( 0 => 'rockmongo', 1 => 'http://wpn-xm.org/get.php?s=rockmongo', 2 => 'rockmongo.zip', ),
  30 => array ( 0 => 'sendmail', 1 => 'http://wpn-xm.org/get.php?s=sendmail', 2 => 'sendmail.zip', ),
  // vcredist_x86.exe (do not delete this comment, its for easier comparison with the .iss file)
  31 => array ( 0 => 'webgrind', 1 => 'http://wpn-xm.org/get.php?s=webgrind', 2 => 'webgrind.zip', ),
  32 => array ( 0 => 'wpnxmscp', 1 => 'http://wpn-xm.org/get.php?s=wpnxmscp', 2 => 'wpnxmscp.zip', ),
  33 => array ( 0 => 'xhprof', 1 => 'http://wpn-xm.org/get.php?s=xhprof', 2 => 'xhprof.zip', ),
);

/**
 * Array containg the downloads and version numbers for the "Lite - w32" Installation Wizard.
 */
$lists['lite-w32'] = array (
  // 0 => software, 1 => download url, 2 => target file name
  0 => array ( 0 => 'adminer', 1 => 'http://wpn-xm.org/get.php?s=adminer', 2 => 'adminer.php', ), // ! php file
  1 => array ( 0 => 'composer', 1 => 'http://wpn-xm.org/get.php?s=composer', 2 => 'composer.phar', ), // ! phar file
  2 => array ( 0 => 'mariadb', 1 => 'http://wpn-xm.org/get.php?s=mariadb', 2 => 'mariadb.zip', ),
  3 => array ( 0 => 'nginx', 1 => 'http://wpn-xm.org/get.php?s=nginx', 2 => 'nginx.zip', ),
  4 => array ( 0 => 'php', 1 => 'http://wpn-xm.org/get.php?s=php', 2 => 'php.zip', ),
  5 => array ( 0 => 'phpext_xdebug', 1 => 'http://wpn-xm.org/get.php?s=phpext_xdebug', 2 => 'phpext_xdebug.dll', ), // ! dll file
  6 => array ( 0 => 'wpnxmscp', 1 => 'http://wpn-xm.org/get.php?s=wpnxmscp', 2 => 'wpnxmscp.zip', ),
);

/**
 * Returns the version number for a given component
 * The URL string is parsed and if "v" was specified, the version is returned,
 * or using the latest version from the registry.
 *
 * @return string Version Number
 */
function getVersion($component, $link)
{
    global $registry;

    $version = '';

    parse_str($link, $result);

    // if the download URL contains "&v=x.y.z", then its a static version number
    if (isset($result['v']) === true) {
        $version = $result['v'];
    } else {
        // if "&v=" is not set, then the "latest version" is taken from the registry
        $version = $registry[$component]['latest']['version'];
    }

    return $version;
}

function writeRegistryFileCsv($file, $registry)
{
    asort($registry);

    $fp = fopen($file, 'w');

    foreach ($registry as $fields) {
      fputcsv($fp, $fields);
    }

    fclose($fp);

    echo 'Created ' . $file . '<br />';
}

function writeRegistryFileJson($file, $registry)
{
    asort($registry);
    $json = json_encode($registry);

    // pretty print the json
    $json_pretty = jsonPrettyPrintCompact($json);
    $json_table = jsonPrettyPrintTableFormat($json_pretty);

    file_put_contents($file, $json_table);

    echo 'Created ' . $file . '<br />';
}

/**
 * Iterate all installer arrays and identify the version numbers for all components
 * then write the registry file for the installer.
 */
foreach($lists as $installer => $components) {
    $file = __DIR__ . '\registry\wpnxm-software-registry-' . $installer;

    foreach ($components as $i => $component) {
        $components[$i][3] = getVersion($component[0], $component[1]);
    }

    // @deprecated
    writeRegistryFileCsv($file . '.csv', $components);

    writeRegistryFileJson($file . '.json', $components);
}

/**
 * Returns compacted, pretty printed JSON data.
 * Yes, there is JSON_PRETTY_PRINT, but its odd at printing compact.
 *
 * @param string $json The unpretty JSON encoded string.
 * @param string $indent The indentation string
 * @return string Pretty printed JSON.
 */
function jsonPrettyPrintCompact($json) {
    $out = ''; $nl = "\n"; $cnt = 0; $tab = 1; $len = strlen($json); $space = ' ';
    $k = strlen($space) ? strlen($space) : 1;

    for ($i=0; $i<=$len; $i++) {

        $char = substr($json, $i, 1);

        if($char == '}' || $char == ']') {
            $cnt--;
            if($i+1 === $len) { // newline before last ]
                $out .= $nl;
            } else {
                $out .= str_pad('', ($tab * $cnt * $k), $space);
            }
        } else if($char == '{' || $char == '[') {
            $cnt++;
            if($cnt > 1) { $out .= $nl; } // no newline on first line
        }

        $out .= $char;

        if($char == ',' || $char == '{' || $char == '[') {
            /*$out .= str_pad('', ($tab * $cnt * $k), $space);*/
            if($cnt >= 1) { $out .= $space; }
        }
        if($char == ':' && '\\' != substr($json, $i+1, 1)) {
            $out .= ' ';
        }
    }
    return $out;
}

/**
 * JSON Table Format, like an ASCII table
 * Aligns values correctly underneath each other.
 * jakoch: my tackling of this indention problem is ugly, but it works.
 */
function jsonPrettyPrintTableFormat($json)
{
    $lines = explode("\n", $json);

    // array for lines and lengths
    $array = array();

    // count lengths and set to array
    foreach($lines as $line) {
      $line = trim($line);

      $commas = explode(", ", $line);
      $keyLengths = array_map('strlen', array_values($commas));

      $array[] = array('lines' => $commas, 'lengths' => $keyLengths);
    }

    // calculate the number of missing spaces
    $numberOfSpacesToAdd = function($longest_line_length, $line_length) {
      return ($longest_line_length - $line_length) + 1; // were the magic happens
    };

    // append certain number of spaces to string
    $appendSpaces = function($num, $string) {
      for($i = 0; $i <= $num; $i++) {
        $string .= ' ';
      }
      return $string;
    };

    // chop of first and last element of the array: the brackets [,]
    $first_element = $array[0]; unset($array[0]);
    $last_nr = count($array);
    $last_element = $array[$last_nr]; unset($array[$last_nr]);

    // walk through multi-dim array and comapare key lengths
    // return array with longest key length back
    $elements = $last_nr-1;
    $num_keys = count($array[1]['lines']);
    $longest = array();
    for($i = 1; $i <= $elements; $i++) {
        for($j = 0; $j < $num_keys; $j++) {
          $key_length = $array[$i]['lengths'][$j];
          if(isset($longest[$j]) && $longest[$j] >= $key_length) {
               continue;
          }
          $longest[$j] = $key_length;
        }
    }

    // appends the missing number of spaces to the elements
    // to align them correctly underneath each other
    for($i = 1; $i <= $elements; $i++) {
        for($j = 0; $j < $num_keys; $j++) {
          // append spaces to the element
          $newElement = $appendSpaces(
              $numberOfSpacesToAdd($longest[$j], $array[$i]['lengths'][$j]),
              $array[$i]['lines'][$j]
          );

          // reinsert the element
          $array[$i]['lines'][$j] = $newElement;
          //$array[$i]['lengths'][$j] = $longest[$j];
        }
    }

    // re-combine the lines, to get the full string
    $lines = '';
    foreach($array as $idx => $values)
    {
       foreach($values['lines'] as $key => $value) {
          $lines .= $value;
       }
    }

    // reinsert commas
    $lines = str_replace('"  ', '", ', $lines);

    // cleanups
    $lines = str_replace(',,', ',', $lines);
    $lines = str_replace('],', "],\n", $lines);
    $lines = str_replace('     [', '[', $lines);
    $lines = str_replace('    [', '[', $lines);
    $lines = str_replace('   [', '[', $lines);
    $lines = str_replace('  [', '[', $lines);
    $lines = str_replace(' [', '[', $lines);
    $lines = "[\n" . trim($lines) . "\n]";

    return $lines;
}

echo 'Done. <br> <br> You might commit the registries and then trigger a new build.';