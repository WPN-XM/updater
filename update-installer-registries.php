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
 * Generate individual installation wizard registries (.json)
 *
 * This scripts generates individual download definitions per installation wizard "wpnxm-software-registry-{installer}.json".
 * The registry file for the "BigPack" is used by the build task "download-components", see "build.xml".
 * A seperate downloads.txt file is created for using aria2c for downloading.
 * Therefore the registries files must be available in the main WPN-XM folder.
 * This is done by fetching this repo as a git submodule.
 * A download of the software components is required when building the "not-web" Installers.
 *
 * The data is also used on the websites download list.
 * Installers and registries are versionized.
 * This allows to identify the version number for all packages of each installation wizards.
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
 * Additional components compared to "All In One":
 * perl, postgresql, imagick + phpext_imagick, varnish + phpext_varnish
 */
$lists['bigpack-w32'] = array(
  //software, download url, target file name
  0  => array('adminer', 'http://wpn-xm.org/get.php?s=adminer', 'adminer.php'), // ! php file
  1  => array('closure-compiler', 'http://wpn-xm.org/get.php?s=closure-compiler', 'closure-compiler.zip'),
  2  => array('composer', 'http://wpn-xm.org/get.php?s=composer', 'composer.phar'), // ! phar file
  3  => array('imagick', 'http://wpn-xm.org/get.php?s=imagick', 'imagick.zip'),
  4  => array('junction', 'http://wpn-xm.org/get.php?s=junction', 'junction.zip'),
  5  => array('mariadb', 'http://wpn-xm.org/get.php?s=mariadb', 'mariadb.zip'),
  6  => array('memadmin', 'http://wpn-xm.org/get.php?s=memadmin', 'memadmin.zip'),
  7  => array('memcached', 'http://wpn-xm.org/get.php?s=memcached', 'memcached.zip'),
  8  => array('mongodb', 'http://wpn-xm.org/get.php?s=mongodb', 'mongodb.zip'),
  9  => array('nginx', 'http://wpn-xm.org/get.php?s=nginx', 'nginx.zip'),
  10 => array('node', 'http://wpn-xm.org/get.php?s=node', 'node.exe'), // ! exe file
  11 => array('nodenpm', 'http://wpn-xm.org/get.php?s=nodenpm', 'nodenpm.zip'),
  12 => array('openssl', 'http://wpn-xm.org/get.php?s=openssl', 'openssl.exe'), // ! exe file
  13 => array('pear', 'http://wpn-xm.org/get.php?s=pear', 'go-pear.phar'), // ! phar file
  14 => array('perl', 'http://wpn-xm.org/get.php?s=perl', 'perl.zip'),
  15 => array('php', 'http://wpn-xm.org/get.php?s=php', 'php.zip'),
  16 => array('phpext_amqp', 'http://wpn-xm.org/get.php?s=phpext_amqp', 'phpext_amqp.zip'),
  17 => array('phpext_apc', 'http://wpn-xm.org/get.php?s=phpext_apc', 'phpext_apc.zip'),
  18 => array('phpext_imagick', 'http://wpn-xm.org/get.php?s=phpext_imagick', 'phpext_imagick.zip'),
  19 => array('phpext_mailparse', 'http://wpn-xm.org/get.php?s=phpext_mailparse', 'phpext_mailparse.zip'),
  20 => array('phpext_memcache', 'http://wpn-xm.org/get.php?s=phpext_memcache', 'phpext_memcache.zip'), // without D
  21 => array('phpext_mongo', 'http://wpn-xm.org/get.php?s=phpext_mongo', 'phpext_mongo.zip'),
  22 => array('phpext_msgpack', 'http://wpn-xm.org/get.php?s=phpext_msgpack', 'phpext_msgpack.zip'),
  23 => array('phpext_phalcon', 'http://wpn-xm.org/get.php?s=phpext_phalcon', 'phpext_phalcon.zip'),
  24 => array('phpext_rar', 'http://wpn-xm.org/get.php?s=phpext_rar', 'phpext_rar.zip'),
  25 => array('phpext_trader', 'http://wpn-xm.org/get.php?s=phpext_trader', 'phpext_trader.zip'),
  26 => array('phpext_varnish', 'http://wpn-xm.org/get.php?s=phpext_varnish', 'phpext_varnish.zip'), // ! exe file
  27 => array('phpext_wincache', 'http://wpn-xm.org/get.php?s=phpext_wincache', 'phpext_wincache.exe'),
  28 => array('phpext_xcache', 'http://wpn-xm.org/get.php?s=phpext_xcache', 'phpext_xcache.zip'),
  29 => array('phpext_xdebug', 'http://wpn-xm.org/get.php?s=phpext_xdebug', 'phpext_xdebug.dll'), // ! dll file
  30 => array('phpext_xhprof', 'http://wpn-xm.org/get.php?s=phpext_xhprof', 'phpext_xhprof.zip'),
  31 => array('phpext_zmq', 'http://wpn-xm.org/get.php?s=phpext_zmq', 'phpext_zmq.zip'),
  32 => array('phpmemcachedadmin', 'http://wpn-xm.org/get.php?s=phpmemcachedadmin', 'phpmemcachedadmin.zip'),
  33 => array('phpmyadmin', 'http://wpn-xm.org/get.php?s=phpmyadmin', 'phpmyadmin.zip'),
  34 => array('postgresql', 'http://wpn-xm.org/get.php?s=postgresql', 'postgresql.zip'),
  35 => array('redis', 'http://wpn-xm.org/get.php?s=redis', 'redis.zip'),
  36 => array('rockmongo', 'http://wpn-xm.org/get.php?s=rockmongo', 'rockmongo.zip'),
  37 => array('sendmail', 'http://wpn-xm.org/get.php?s=sendmail', 'sendmail.zip'),
  38 => array('varnish', 'http://wpn-xm.org/get.php?s=varnish', 'varnish.zip'),
  // vcredist_x86.exe (do not delete this comment, its for easier comparison with the .iss file)
  39 => array('webgrind', 'http://wpn-xm.org/get.php?s=webgrind', 'webgrind.zip'),
  40 => array('wpnxmscp', 'http://wpn-xm.org/get.php?s=wpnxmscp', 'wpnxmscp.zip'),
  41 => array('xhprof', 'http://wpn-xm.org/get.php?s=xhprof', 'xhprof.zip'),
);

/**
 * Array containg the downloads and version numbers for the "All In One - w32" Installation Wizard.
 */
$lists['allinone-w32'] = array(
  //software, download url, target file name
  0  => array('adminer', 'http://wpn-xm.org/get.php?s=adminer', 'adminer.php'), // ! php file
  1  => array('closure-compiler', 'http://wpn-xm.org/get.php?s=closure-compiler', 'closure-compiler.zip'),
  2  => array('composer', 'http://wpn-xm.org/get.php?s=composer', 'composer.phar'), // ! phar file
  3  => array('junction', 'http://wpn-xm.org/get.php?s=junction', 'junction.zip'),
  4  => array('mariadb', 'http://wpn-xm.org/get.php?s=mariadb', 'mariadb.zip'),
  5  => array('memadmin', 'http://wpn-xm.org/get.php?s=memadmin', 'memadmin.zip'),
  6  => array('memcached', 'http://wpn-xm.org/get.php?s=memcached', 'memcached.zip'),
  7  => array('mongodb', 'http://wpn-xm.org/get.php?s=mongodb', 'mongodb.zip'),
  8  => array('nginx', 'http://wpn-xm.org/get.php?s=nginx', 'nginx.zip'),
  9  => array('openssl', 'http://wpn-xm.org/get.php?s=openssl', 'openssl.exe'), // ! exe file
  10 => array('pear', 'http://wpn-xm.org/get.php?s=pear', 'go-pear.phar'), // ! phar file
  11 => array('php', 'http://wpn-xm.org/get.php?s=php', 'php.zip'),
  12 => array('phpext_amqp', 'http://wpn-xm.org/get.php?s=phpext_amqp', 'phpext_amqp.zip'),
  13 => array('phpext_apc', 'http://wpn-xm.org/get.php?s=phpext_apc', 'phpext_apc.zip'),
  14 => array('phpext_mailparse', 'http://wpn-xm.org/get.php?s=phpext_mailparse', 'phpext_mailparse.zip'),
  15 => array('phpext_memcache', 'http://wpn-xm.org/get.php?s=phpext_memcache', 'phpext_memcache.zip'), // without D
  16 => array('phpext_mongo', 'http://wpn-xm.org/get.php?s=phpext_mongo', 'phpext_mongo.zip'),
  17 => array('phpext_msgpack', 'http://wpn-xm.org/get.php?s=phpext_msgpack', 'phpext_msgpack.zip'),
  18 => array('phpext_phalcon', 'http://wpn-xm.org/get.php?s=phpext_phalcon', 'phpext_phalcon.zip'),
  19 => array('phpext_rar', 'http://wpn-xm.org/get.php?s=phpext_rar', 'phpext_rar.zip'),
  20 => array('phpext_trader', 'http://wpn-xm.org/get.php?s=phpext_trader', 'phpext_trader.zip'),
  21 => array('phpext_wincache', 'http://wpn-xm.org/get.php?s=phpext_wincache', 'phpext_wincache.exe'), // ! exe file
  22 => array('phpext_xcache', 'http://wpn-xm.org/get.php?s=phpext_xcache', 'phpext_xcache.zip'),
  23 => array('phpext_xdebug', 'http://wpn-xm.org/get.php?s=phpext_xdebug', 'phpext_xdebug.dll'), // ! dll file
  24 => array('phpext_xhprof', 'http://wpn-xm.org/get.php?s=phpext_xhprof', 'phpext_xhprof.zip'),
  25 => array('phpext_zmq', 'http://wpn-xm.org/get.php?s=phpext_zmq', 'phpext_zmq.zip'),
  26 => array('phpmemcachedadmin', 'http://wpn-xm.org/get.php?s=phpmemcachedadmin', 'phpmemcachedadmin.zip'),
  27 => array('phpmyadmin', 'http://wpn-xm.org/get.php?s=phpmyadmin', 'phpmyadmin.zip'),
  28 => array('redis', 'http://wpn-xm.org/get.php?s=redis', 'redis.zip'),
  29 => array('rockmongo', 'http://wpn-xm.org/get.php?s=rockmongo', 'rockmongo.zip'),
  30 => array('sendmail', 'http://wpn-xm.org/get.php?s=sendmail', 'sendmail.zip'),
  // vcredist_x86.exe (do not delete this comment, its for easier comparison with the .iss file)
  31 => array('webgrind', 'http://wpn-xm.org/get.php?s=webgrind', 'webgrind.zip'),
  32 => array('wpnxmscp', 'http://wpn-xm.org/get.php?s=wpnxmscp', 'wpnxmscp.zip'),
  33 => array('xhprof', 'http://wpn-xm.org/get.php?s=xhprof', 'xhprof.zip'),
);

/**
 * Array containg the downloads and version numbers for the "Lite - w32" Installation Wizard.
 */
$lists['lite-w32'] = array(
  //software, download url, target file name
  0 => array('adminer', 'http://wpn-xm.org/get.php?s=adminer', 'adminer.php'), // ! php file
  1 => array('composer', 'http://wpn-xm.org/get.php?s=composer', 'composer.phar'), // ! phar file
  2 => array('mariadb', 'http://wpn-xm.org/get.php?s=mariadb', 'mariadb.zip'),
  3 => array('nginx', 'http://wpn-xm.org/get.php?s=nginx', 'nginx.zip'),
  4 => array('php', 'http://wpn-xm.org/get.php?s=php', 'php.zip'),
  5 => array('phpext_xdebug', 'http://wpn-xm.org/get.php?s=phpext_xdebug', 'phpext_xdebug.dll'), // ! dll file
  6 => array('wpnxmscp', 'http://wpn-xm.org/get.php?s=wpnxmscp', 'wpnxmscp.zip'),
);

$ariaDownloadsTxt  = '# Aria2c Downloads' . PHP_EOL;
$ariaDownloadsTxt .= '# http://aria2.sourceforge.net/manual/en/html/aria2c.html#id2' . PHP_EOL;
$ariaDownloadsTxt .= '#' . PHP_EOL;
$php_version = '';

/**
 * Iterate all installer arrays and identify the version numbers for all components
 * then write the registry file for the installer.
 */
foreach($lists as $installer => $components) {
    $file = __DIR__ . '\registry\wpnxm-software-registry-' . $installer;

    foreach ($components as $i => $component) {
        $components[$i][3] = getVersionFromMainRegistry($component[0], $component[1]);
        
        // create aria download file content, by concatenating download url with version and their target names
        if($installer === 'bigpack-w32') {
            if($component[0] === 'php') { # or $component[0] === 'php-x64') {
                $php_version = substr($components[$i][3], 0, 3);
            }
            if(false !== strpos($component[0], 'phpext_')) {
                $ariaDownloadsTxt .= $components[$i][1].'&v='.$components[$i][3] . '&p=' . $php_version .PHP_EOL;
            } else {
                $ariaDownloadsTxt .= $components[$i][1].'&v='.$components[$i][3] . PHP_EOL;                
            }            
            $ariaDownloadsTxt .= '    out='.$components[$i][2] . PHP_EOL;
        }
    }

    writeRegistryFileJson($file . '.json', $components);
    
    // write the aria2c downloads file
    if($installer === 'bigpack-w32') {
        $file = __DIR__ . '\registry\downloads.txt';
        file_put_contents($file, $ariaDownloadsTxt);
        echo 'Created ' . $file . '<br />';
    }
}

// create aria2c file for multiple parallel downloads



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

    $version = (isset($result['v']) === true) ? $result['v'] : $version = $registry[$component]['latest']['version'];

    return $version;
}

/**
 * @param string $file
 * @param array $registry
 */
function writeRegistryFileJson($file, $registry)
{
    asort($registry);

    $json = json_encode($registry);
    $json_pretty = jsonPrettyPrintCompact($json);
    $json_table = jsonPrettyPrintTableFormat($json_pretty);

    file_put_contents($file, $json_table);

    echo 'Created ' . $file . '<br />';
}

/**
 * Returns compacted, pretty printed JSON data.
 * Yes, there is JSON_PRETTY_PRINT, but its odd at printing compact.
 *
 * @param string $json The unpretty JSON encoded string.
 * @return string Pretty printed JSON.
 */
function jsonPrettyPrintCompact($json) {
    $out = ''; $nl = "\n"; $cnt = 0; $tab = 1; $len = strlen($json); $space = ' ';
    $k = strlen($space) ? strlen($space) : 1;

    for ($i=0; $i<=$len; $i++) {

        $char = substr($json, $i, 1);

        if($char === '}' || $char === ']') {
            $cnt--;
            if($i+1 === $len) { // newline before last ]
                $out .= $nl;
            } else {
                $out .= str_pad('', ($tab * $cnt * $k), $space);
            }
        } else if($char === '{' || $char === '[') {
            $cnt++;
            if($cnt > 1) { $out .= $nl; } // no newline on first line
        }

        $out .= $char;

        if($char === ',' || $char === '{' || $char === '[') {
            /*$out .= str_pad('', ($tab * $cnt * $k), $space);*/
            if($cnt >= 1) { $out .= $space; }
        }
        if($char === ':' && '\\' !== substr($json, $i+1, 1)) {
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
    foreach($lines as $line) {
      $line = trim($line);
      $commas = explode(", ", $line);
      $keyLengths = array_map('strlen', array_values($commas));
      $array[] = array('lines' => $commas, 'lengths' => $keyLengths);
    }

    // calculate the number of missing spaces
    $numberOfSpacesToAdd = function($longest_line_length, $line_length) {
      return ($longest_line_length - $line_length) + 2; // were the magic happens
    };

    // append certain number of spaces to string
    $appendSpaces = function($num, $string) {
      for($i = 0; $i <= $num; $i++) {
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
    $elements = $last_nr-1;
    $num_keys = count($array[1]['lines'])-1;
    $longest = array();

    for($i = 1; $i <= $elements; $i++) {
        for($j = 0; $j < $num_keys; $j++) {
          $key_length = $array[$i]['lengths'][$j];
          if(isset($longest[$j]) === true && $longest[$j] >= $key_length) {
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

    // build output string from array
    $lines = '';
    foreach($array as $idx => $values) {
       foreach($values['lines'] as $key => $value) {
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
