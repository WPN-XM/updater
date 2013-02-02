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
 * Generates the download-files.csv.
 *
 * The file is used by Nant's build.xml to download the software components,
 * when building the AllInOne Installer.
 */

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

echo '<h2>Generating "download_files.csv".</h2>';

$list = array (
  // 0 => sofware, 1 => download url, 2 => target file name
  0  => array ( 0 => 'adminer', 1 => 'http://wpn-xm.org/get.php?s=adminer', 2 => 'adminer.php', ), // ! php file
  1  => array ( 0 => 'composer', 1 => 'http://wpn-xm.org/get.php?s=composer', 2 => 'composer.phar', ), // ! phar file
  2  => array ( 0 => 'junction', 1 => 'http://wpn-xm.org/get.php?s=junction', 2 => 'junction.zip', ),
  3  => array ( 0 => 'mariadb', 1 => 'http://wpn-xm.org/get.php?s=mariadb', 2 => 'mariadb.zip', ),
  4  => array ( 0 => 'memadmin', 1 => 'http://wpn-xm.org/get.php?s=memadmin', 2 => 'memadmin.zip', ),
  5  => array ( 0 => 'memcached', 1 => 'http://wpn-xm.org/get.php?s=memcached', 2 => 'memcached.zip', ),
  6  => array ( 0 => 'nginx', 1 => 'http://wpn-xm.org/get.php?s=nginx', 2 => 'nginx.zip', ),
  7  => array ( 0 => 'pear', 1 => 'http://wpn-xm.org/get.php?s=pear', 2 => 'go-pear.phar', ), // ! phar file
  8  => array ( 0 => 'php', 1 => 'http://wpn-xm.org/get.php?s=php', 2 => 'php.zip', ),
  9  => array ( 0 => 'phpext_apc', 1 => 'http://wpn-xm.org/get.php?s=phpext_apc', 2 => 'phpext_apc.zip', ),
  10 => array ( 0 => 'phpext_memcache', 1 => 'http://wpn-xm.org/get.php?s=phpext_memcache', 2 => 'phpext_memcache.zip', ), // without D
  11 => array ( 0 => 'phpext_xdebug', 1 => 'http://wpn-xm.org/get.php?s=phpext_xdebug', 2 => 'phpext_xdebug.dll', ), // ! dll file
  12 => array ( 0 => 'phpext_xhprof', 1 => 'http://wpn-xm.org/get.php?s=phpext_xhprof', 2 => 'phpext_xhprof.zip', ),
  13 => array ( 0 => 'phpmyadmin', 1 => 'http://wpn-xm.org/get.php?s=phpmyadmin', 2 => 'phpmyadmin.zip', ),
  14 => array ( 0 => 'sendmail', 1 => 'http://wpn-xm.org/get.php?s=sendmail', 2 => 'sendmail.zip', ),
  15 => array ( 0 => 'webgrind', 1 => 'http://wpn-xm.org/get.php?s=webgrind', 2 => 'webgrind.zip', ),
  16 => array ( 0 => 'wpnxmscp', 1 => 'http://wpn-xm.org/get.php?s=wpnxmscp', 2 => 'wpnxmscp.zip', ),
  17 => array ( 0 => 'xhprof', 1 => 'http://wpn-xm.org/get.php?s=xhprof', 2 => 'xhprof.zip', ),
  18 => array ( 0 => 'openssl', 1 => 'http://wpn-xm.org/get.php?s=openssl', 2 => 'openssl.exe', ),
  19 => array ( 0 => 'rockmongo', 1 => 'http://wpn-xm.org/get.php?s=rockmongo', 2 => 'rockmongo.zip', ),
);

$fp = fopen('download-filelist.csv', 'w');

foreach($list as $fields) {
  fputcsv($fp, $fields);
}

fclose($fp);

echo "Done. <br> <br> Now copy the file to the WPN-XM main folder. <br> Then trigger new build.";
