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
 * Registry Status
 *
 * This scripts performs a check for broken download links.
 *
 * We check for each software component of the registry
 * a) the download link for the latest version
 *    This link comes directly from the local registry.
 * b) the forwarding downloading link
 *    This link is a get request to the server and uses the registry on the server.
 *    Forwarding links are used in the innosetup scripts of the web installation wizards.
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

echo '<h2>WPN-XM Software Components Registry - Status - '. date(DATE_RFC822) .'</h2>';
echo '<h3>Components ('.count($registry).')</h3>';
echo '<table>';
echo '<tr><th>Software Component</th><th>Version</th><th>Download URL<br/>(local wpnxm-software-registry.php)</th><th>Forwarding URL<br/>(server wpnxm-software-registry.php)</th></tr>';

foreach($registry as $software => $versions) {

    echo '<tr><td><b>'. $software .'</b></td>';

    foreach($versions as $version => $url) {

        // test every link
        #echo 'Testing Version "' . $version . '" ' . $url;
        #echo is_available($url, 30);

        // Test link of latest version
        if($version === 'latest') {
            echo '<td>' . $url['version'] . '</td>';
            $color = is_available($url['url']) === true ? 'color: green' : 'color: red';
            echo '<td><a style="font-weight: bold; '.$color.';" href="'.$url['url'].'">'.$url['url'].'</a></td>';
        }
    }

    // Test forwarding links for all software components, e.g. http://wpn-xm.org/get.php?s=nginx
    $url = 'http://wpn-xm.org/get.php?s=' . $software;
    $color = is_available($url) === true ? 'color: green' : 'color: red';
    echo '<td><a style="font-weight: bold; '.$color.';" href="'.$url.'">'.$url.'</a></td>';

    echo '</tr>';
}

echo '</table>';

function is_available($url, $timeout = 30)
{
    $ch = curl_init();

    // set cURL options
    $options = array(
        CURLOPT_RETURNTRANSFER => true,         // do not output to browser
        CURLOPT_NOPROGRESS => true,
        CURLOPT_URL => $url,
        CURLOPT_NOBODY => true,                 // do HEAD request only
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FORBID_REUSE => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT, 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/',
    );

    curl_setopt_array($ch, $options);
    curl_exec($ch);
    $retval = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200; // check if HTTP OK
    curl_close($ch);

    return $retval;
}
