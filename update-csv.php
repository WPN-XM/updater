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
 * This scripts generates the file "wpnxm-software-registry.csv".
 * The file is used by the build task "download-components", see "build.xml".
 * The csv content is split up and the download urls are used on wget for fetching the downloads.
 * The file must be copied to the main WPN-XM folder - this is done by fetching this repository as a git submodule.
 * Downloading these software components is required when building the All-In-One Installer.
 */

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

// load software components registry
$registry = include __DIR__ . '/wpnxm-software-registry.php';

echo '<h2>Generating "wpnxm-software-registry.csv".</h2>';

/**
 * Array containg the downloads for the All In One Installation Wizard.
 */
$list = array (
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
  11 => array ( 0 => 'phpext_apc', 1 => 'http://wpn-xm.org/get.php?s=phpext_apc', 2 => 'phpext_apc.zip', ),
  12 => array ( 0 => 'phpext_memcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_memcache', 2 => 'phpext_memcache.zip', ), // without D
  13 => array ( 0 => 'phpext_mongo', 1 => 'http://wpn-xm.org/get.php?s=phpext_mongo', 2 => 'phpext_mongo.zip', ),
  14 => array ( 0 => 'phpext_xdebug', 1 => 'http://wpn-xm.org/get.php?s=phpext_xdebug', 2 => 'phpext_xdebug.dll', ), // ! dll file
  15 => array ( 0 => 'phpext_xhprof', 1 => 'http://wpn-xm.org/get.php?s=phpext_xhprof', 2 => 'phpext_xhprof.zip', ),
  16 => array ( 0 => 'phpmemcachedadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmemcachedadmin', 2 => 'phpmemcachedadmin.zip', ),
  17 => array ( 0 => 'phpmyadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmyadmin', 2 => 'phpmyadmin.zip', ),
  18 => array ( 0 => 'rockmongo', 1 => 'http://wpn-xm.org/get.php?s=rockmongo', 2 => 'rockmongo.zip', ),
  19 => array ( 0 => 'sendmail', 1 => 'http://wpn-xm.org/get.php?s=sendmail', 2 => 'sendmail.zip', ),
  20 => array ( 0 => 'webgrind', 1 => 'http://wpn-xm.org/get.php?s=webgrind', 2 => 'webgrind.zip', ),
  21 => array ( 0 => 'wpnxmscp', 1 => 'http://wpn-xm.org/get.php?s=wpnxmscp', 2 => 'wpnxmscp.zip', ),
  22 => array ( 0 => 'xhprof', 1 => 'http://wpn-xm.org/get.php?s=xhprof', 2 => 'xhprof.zip', ),
  23 => array ( 0 => 'postgresql', 1 => 'http://wpn-xm.org/get.php?s=postgresql', 2 => 'postgresql.zip', ),
  24 => array ( 0 => 'perl', 1 => 'http://wpn-xm.org/get.php?s=perl', 2 => 'perl.zip', ),
);

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

/**
 * Identify the version number of each component in the list,
 * then append the version number to the original array.
 */
foreach ($list as $i => $component) {
   $list[$i][3] = getVersion($component[0], $component[1]);
}

//var_dump($list);

asort($list);

$fp = fopen('wpnxm-software-registry.csv', 'w');

foreach ($list as $fields) {
  fputcsv($fp, $fields);
}

fclose($fp);

echo 'Done. <br> <br> You might trigger a new build. <br> <br>
      The target folder of this file is "WPN-XM/updater/" (main repo). <br>
      The updater repo is automatically fetched to the main repo by pulling it as a git submodule.';
