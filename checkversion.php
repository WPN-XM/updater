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

/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <daniel.winterfeldt@gmail.com>
 * wrote this file. As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a beer in return Daniel Winterfeldt
 * ----------------------------------------------------------------------------
 */

set_time_limit(60*3);

date_default_timezone_set('UTC');

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

require_once __DIR__ . '/vendor/goutte.phar';

use Goutte\Client;

// load software components registry
$registry = include __DIR__ . DIRECTORY_SEPARATOR . '\wpnxm-software-registry.php';

// ensure registry array is available
if (!is_array($registry)) {
    header("HTTP/1.0 404 Not Found");
}

// current versions (hardcoded, next step would be to detected them on the client's localhost)
$version = array();
$version['nginx']['current']       = '1.2.1';
$version['php']['current']         = '5.4.3';
$version['mariadb']['current']     = '5.5.24';
$version['xdebug']['current']      = '2.2.0';
$version['apc']['current']         = '3.1.10';
$version['phpmyadmin']['current']  = '3.4.9';
$version['adminer']['current']     = '3.3.4';
//echo "version: "; var_dump($version);

$goutte_client = new Client();

/**
 * NGINX
 */
function get_latest_version_of_nginx()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://www.nginx.org/download/');

    return $nginx_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->nodeValue, $matches)) {
            if ($version['nginx']['current'] <= $matches[1]) {
                return array('version' => $matches[1], 'url' => 'http://www.nginx.org/download/' . $node->nodeValue);
            }
        }
    });
}

add('nginx', get_latest_version_of_nginx() );

/**
 * PHP
 */
function get_latest_version_of_php()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://windows.php.net/downloads/releases/');

    return $php_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#php-+(\d+\.\d+(\.\d+)*)-nts-Win32-VC9-x86.zip$#", $node->nodeValue, $matches)) {
            if ($version['php']['current'] <= $matches[1]) {
                return array('version' => $matches[1], 'url' => 'http://windows.php.net/downloads/releases/' . $node->nodeValue);
            }
        }
    });
}

add('php', get_latest_version_of_php() );

/**
 * MariaDB
 */
function get_latest_version_of_mariadb()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://downloads.mariadb.org/MariaDB/+files/');

    return $mariadb_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)(.*)(win32.zip)$#", $node->nodeValue, $matches)) {
            if ($version['mariadb']['current'] <= $matches[0] and '7' > $matches[0]) {
                return array('version' => $matches[1], 'url' => 'http://mirror2.hs-esslingen.de/mariadb/mariadb-5.5.25/windows/' . trim($node->nodeValue));
            }
        }
    });
}

add('mariadb', get_latest_version_of_mariadb() );

/**
 * XDebug - PHP Extension
 */
function get_latest_version_of_xdebug()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://xdebug.org/files/');

    return $xdebug_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#((\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+))([^\s]+nts(\.(?i)(dll))$)#i", $node->nodeValue, $matches)) {
                if ($version['xdebug']['current'] <= $matches[1]) {
                    return array('version' => $matches[1], 'url' => 'http://xdebug.org/files/' . $node->nodeValue);
                }
        }
    });
}

add('xdebug', get_latest_version_of_xdebug() );

/**
 * APC - PHP Extension
 */
function get_latest_version_of_apc()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://downloads.php.net/pierre/');

    return  $apc_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#php_apc-(\d+\.\d+(\.\d+)*)(.*)-vc9-x86.zip$#", $node->nodeValue, $matches)) {
            if ($version['apc']['current'] <= $matches[1]) {
                return array('version' => $matches[1], 'url' => 'http://downloads.php.net/pierre/' . $node->nodeValue);
            }
        }
    });
}

add('apc', get_latest_version_of_apc() );

//$version['apc_latest'] = substr($version['apc_latest'], 8); // remove "php_ext-" (8)

/**
 * phpMyAdmin
 */
function get_latest_version_of_phpmyadmin()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://www.phpmyadmin.net/home_page/downloads.php');

    return $phpmyadmin_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->nodeValue, $matches)) {
            if ($version['phpmyadmin']['current'] <= $matches[0]) {
                // mirror redirect fails somehow
                //$url = 'http://sourceforge.net/projects/phpmyadmin/files/phpMyAdmin/'.$matches[0].'/phpMyAdmin-'.$matches[0].'-english.zip/download?use_mirror=autoselect';
                // using direkt link
                $url = 'http://kent.dl.sourceforge.net/project/phpmyadmin/phpMyAdmin/'.$matches[0].'/phpMyAdmin-'.$matches[0].'-english.zip';
                return array('version' => $matches[0], 'url' => $url);
            }
        }
    });
}

add('phpmyadmin', get_latest_version_of_phpmyadmin() );

/**
 * Adminer
 */
function get_latest_version_of_adminer()
{
    global $goutte_client, $version;

    $crawler = $goutte_client->request('GET', 'http://www.adminer.org/#download');

    return $adminer_latest = $crawler->filter('a')->each(function ($node, $i) use ($version) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->nodeValue, $matches)) {
            if ($version['adminer']['current'] <= $matches[0]) {
                // mirror redirect fails somehow
                //$url = 'http://sourceforge.net/projects/adminer/files/Adminer/Adminer%20'.$matches[0].'/adminer-'.$matches[0].'.php/download?use_mirror=autoselect';
                // using direkt link
                $url = 'http://garr.dl.sourceforge.net/project/adminer/Adminer/Adminer%20'.$matches[0].'/adminer-'.$matches[0].'.php';

                return array('version' => $matches[0], 'url' => $url);
            }
        }
    });
}

add('adminer', get_latest_version_of_adminer() );

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
 * Adds array data to the main software component array.
 *
 * @param $name Name of Software Component
 * @param $array Subarray of a software component, which should be added to the main array.
 */
function add($name, array $array)
{
    global $version;

    // cleanup by removing all null values
    $array = array_unset_null_values($array);

    // insert the last array item as [latest][version] => [url]
    $version[$name]['latest'] = array_pop($array);

    // insert the last array item also as a pure [version] => [url] relationship
    $version[$name][ $version[$name]['latest']['version'] ] = $version[$name]['latest']['url'];

    // added remaining array items as pure [version] => [url] relationships
    foreach ($array as $new_version_entry) {
        $version[$name][ $new_version_entry['version'] ] = $new_version_entry['url'];
    }

    asort($version[$name]);
}

#var_dump($version);

function array_unset_current_values(array $array)
{
    foreach ($array as $key => $value) {
        if ($value === 'current') {
            unset($array[$key][$value]);
        }
    }

    return $array;
}

/**
 * combine arrays
 */
$registry = $version + $registry;
$registry = array_unset_current_values($registry);

write_registry_file($registry);

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
    rename( 'wpnxm-software-registry.php', 'wpnxm-software-registry-old.php' );

    // file header
    $content = "<?php\n";
    $content .= "\t/**\n";
    $content .= "\t * WPN-XM Software Registry\n";
    $content .= "\t * ------------------------\n";
    $content .= "\t * Last Update " . date(DATE_RFC2822) . ". \n";
    $content .= "\t * Do not edit manually! \n";
    $content .= "\t */\n";
    $content .= "\n return ";
    // pretty print the array
    $content .= var_export( $registry, true ) . ';';

    // write new registry
    file_put_contents( 'wpnxm-software-registry.php', $content );
}
//var_dump($registry['nginx']);
?>

<table>
    <thead>application</thead><thead>current version</thead><thead>latest version</thead>
<tr>
    <td>nginx</td><td><?php echo $version['nginx']['current'] ?></td><td><?php echo $version['nginx']['latest']['version'] ?></td>
</tr>
<tr>
    <td>php</td><td><?php echo $version['php']['current'] ?></td><td><?php echo $version['php']['latest']['version'] ?></td>
</tr>
<tr>
    <td>mariadb</td><td><?php echo $version['mariadb']['current'] ?></td><td><?php echo $version['mariadb']['latest']['version'] ?></td>
</tr>
<tr>
    <td>xdebug</td><td><?php echo $version['xdebug']['current'] ?></td><td><?php echo $version['xdebug']['latest']['version'] ?></td>
</tr>
<tr>
    <td>apc</td><td><?php echo $version['apc']['current'] ?></td><td><?php echo $version['apc']['latest']['version'] ?></td>
</tr>
<tr>
    <td>phpmyadmin</td><td><?php echo $version['phpmyadmin']['current'] ?></td><td><?php echo $version['phpmyadmin']['latest']['version'] ?></td>
</tr>
<tr>
    <td>adminer</td><td><?php echo $version['adminer']['current'] ?></td><td><?php echo $version['adminer']['latest']['version'] ?></td>
</tr>
</table>
