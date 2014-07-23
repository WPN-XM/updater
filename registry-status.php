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
 * This scripts check the software registry for broken download links.
 *
 * For each software component we check:
 * a) the download link for the latest version
 *      This link comes directly from the local registry.
 * b) the forwarding downloading link
 *      This link is a get request to the server and uses the registry on the server.
 *      Forwarding links are used in the innosetup scripts of the web installation wizards.
 */

$start = microtime(true);
set_time_limit(180); // 60*3
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

require __DIR__ . '/tools.php';

$registry  = Registry::load();

$urls      = StatusRequest::getUrlsToCrawl($registry);
$responses = StatusRequest::getHttpStatusCodesInParallel($urls);
// build a lookup array: url => http status code 200
$urlsHttpStatus = array_combine($urls, $responses);

function renderTd($url)
{
    $color = isAvailable($url) === true ? 'green' : 'red';
    return '<td><a style="color:'.$color.';" href="'.$url.'">'.$url.'</a></td>';
}

function isAvailable($url)
{
    global $urlsHttpStatus;
    // special handling for googlecode, because they don't like /HEAD requests via curl
    if (false !== strpos($url, 'googlecode') or false !== strpos($url, 'phpmemcachedadmin') or false !== strpos($url, 'webgrind')) {
        return (bool) StatusRequest::getHttpStatusCode($url);
    }
    return $urlsHttpStatus[$url];
}

/******************************************************************************/
?>

<h5>WPN-XM Software Registry - Status<span class="pull-right"><?=date(DATE_RFC822)?></span></h5>
<h5>Components (<?=count($registry)?>)</h5>
<table class="table table-condensed table-hover" style="font-size: 12px;">
<tr><th>Software Component</th><th>Version</th><th>Download URL<br/>(local wpnxm-software-registry.php)</th>
<th>Forwarding URL<br/>(server wpnxm-software-registry.php)</th></tr>

<?php

// test latest version links (and not every version url)
// test forwarding links

foreach ($registry as $software => $keys) {
    echo '<tr><td style="padding: 1px 5px;"><b>'. $software .'</b></td>';
        
    // if software is a PHP Extension, we have a latest version with URLs for multiple PHP versions           
    if (strpos($software, 'phpext_') !== false) {
        $phpversions = $keys['latest']['url'];
        $skipFirstTd = true;
        foreach ($phpversions as $phpversion => $url) {
            if($skipFirstTd === false) { echo '<td>&nbsp;</td>'; } else { $skipFirstTd = false; }
            echo '<td>' . $keys['latest']['version'] . ' - ' . $phpversion . '</td>' . renderTd($url);             
            echo renderTd('http://wpn-xm.org/get.php?s=' . $software . '&p=' . $phpversion);
            echo '</tr>';
        }
    } else {
        echo '<td>' . $keys['latest']['version'] . '</td>' . renderTd($keys['latest']['url']);
        echo renderTd('http://wpn-xm.org/get.php?s=' . $software);
        echo '</tr>';
    }
}
?>
</table>
Used a total of <?=round((microtime(true) - $start), 2)?> seconds for crawling <?=count($urls)?> URLs.