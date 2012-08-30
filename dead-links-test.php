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

echo 'This is a check for broken links in the WPN-XM software components registry.<br>';
echo 'Results:';

// load software components registry
$registry = include __DIR__ . '/wpnxm-software-registry.php';

foreach($registry as $software => $versions) {

    echo '<br>Testing Link(s) of '. $software .'<br>';

    foreach($versions as $version => $url) {

        // test every link
        #echo 'Testing Version "' . $version . '" ' . $url;
        #echo is_available($url, 30);

        // only test latest (for now)
        if($version === 'latest') {
            echo 'Latest Version (' . $url['version'] . ') => ' . $url['url'];
            if(is_available($url['url']) === true)
            {       echo ' <span style="font-weight: light; color: green;">good</span><br>';
                } else {
                    echo ' <span style="font-weight: bold; color: red;">*dead*</span><br>';
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
