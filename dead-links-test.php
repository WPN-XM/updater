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

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

/**
 * Broken link check on the download links of the software comonents registry
 */

echo '<b>This is a check for dead and broken links in the WPN-XM software components registry.</b><br>';

// load software components registry
$registry = include __DIR__ . '/wpnxm-software-registry.php';

foreach($registry as $software => $versions) {

    echo '<b>'. $software .'</b><br>';

    foreach($versions as $version => $url) {

        // test every link
        #echo 'Testing Version "' . $version . '" ' . $url;
        #echo is_available($url, 30);

        // only test latest (for now)
        if($version === 'latest') {
            echo 'Latest Version (' . $url['version'] . ')';
            if(is_available($url['url']) === true)
            {       echo ' <a style="font-weight: light; color: green;" href="'.$url['url'].'">'.$url['url'].'</a><br>';
                } else {
                    echo ' <a style="font-weight: bold; color: red;" href="'.$url['url'].'">'.$url['url'].'</a><br>';
            }
        }
    }
}


function is_available($url, $timeout = 30)
{
    $ch = curl_init();

    // set cURL options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,         // do not output to browser
        CURLOPT_URL => $url,
        CURLOPT_NOBODY => true,                 // do HEAD request only
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_FOLLOWLOCATION => true
    );

    curl_setopt_array($ch, $options);
    curl_exec($ch);
    $retval = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200; // check if HTTP OK
    curl_close($ch);

    return $retval;
}

/**
 * Broken link check on the download links of the innosetup file
 */

echo '<br><b>This is a check for dead and broken links in the WPN-XM innosetup script file.</b><br>';

$innosetup_entries = array(
  'URL_nginx'            => 'http://wpn-xm.org/get.php?s=nginx',
  'URL_php'              => 'http://wpn-xm.org/get.php?s=php',
  'URL_mariadb'          => 'http://wpn-xm.org/get.php?s=mariadb',
  'URL_phpext_xdebug'    => 'http://wpn-xm.org/get.php?s=phpext_xdebug',
  'URL_phpext_apc'       => 'http://wpn-xm.org/get.php?s=phpext_apc',
  'URL_webgrind'         => 'http://wpn-xm.org/get.php?s=webgrind',
  'URL_xhprof'           => 'http://wpn-xm.org/get.php?s=xhprof',
  'URL_memcached'        => 'http://wpn-xm.org/get.php?s=memcached',
  'URL_memadmin'         => 'http://wpn-xm.org/get.php?s=memadmin',
  'URL_phpext_memcached' => 'http://wpn-xm.org/get.php?s=phpext_memcache',
  'URL_phpext_zeromq'    => 'http://wpn-xm.org/get.php?s=phpext_zeromq',
  'URL_phpmyadmin'       => 'http://wpn-xm.org/get.php?s=phpmyadmin',
  'URL_adminer'          => 'http://wpn-xm.org/get.php?s=adminer',
  'URL_junction'         => 'http://wpn-xm.org/get.php?s=junction',
  'URL_pear'             => 'http://wpn-xm.org/get.php?s=pear',
  'URL_composer'         => 'http://wpn-xm.org/get.php?s=composer',
  'URL_wpnxmscp'         => 'http://wpn-xm.org/get.php?s=wpnxmscp',
);

foreach($innosetup_entries as $name => $url) {
    if(is_available($url) === true)
    {       echo ' <a style="font-weight: light; color: green;" href="'.$url.'">'.$url.'</a><br>';
        } else {
            echo ' <a style="font-weight: bold; color: red;" href="'.$url.'">'.$url.'</a><br>';
    }
}
