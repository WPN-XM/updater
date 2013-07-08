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
$registry = include __DIR__ . '/wpnxm-software-registry.php';

// clone old registry for comparing latest versions (see html section below)
$old_registry = $registry;

// ensure registry array is available
if (!is_array($registry)) {
    header("HTTP/1.0 404 Not Found");
}

$goutte_client = new Client();
$goutte_client->setHeader('User-Agent', 'WPN-XM Server Stack - Registry Update Tool - http://wpn-xm.org/');
$guzzle = $goutte_client->getClient();
$guzzle->setSslVerification(false);

// a how to scrape one liner :)
// printf("%s (%s)\n</br>", $node->nodeValue, $node->getAttribute('href'));

/**
 * NGINX
 */
function get_latest_version_of_nginx()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://www.nginx.org/download/');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->nodeValue, $matches)) {
            if (version_compare($matches[1], $registry['nginx']['latest']['version'], '>=')) {
                return array('version' => $matches[1], 'url' => 'http://www.nginx.org/download/' . $node->nodeValue);
            }
        }
    });
}

/**
 * PHP
 */
function get_latest_version_of_php()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://windows.php.net/downloads/releases/');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#php-+(\d+\.\d+(\.\d+)*)-nts-Win32-VC9-x86.zip$#", $node->nodeValue, $matches)) {
            if (version_compare($matches[1], $registry['php']['latest']['version'], '>=')) {
                return array('version' => $matches[1], 'url' => 'http://windows.php.net/downloads/releases/' . $node->nodeValue);
            }
        }
    });
}

/**
 * MariaDB
 */
function get_latest_version_of_mariadb()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://archive.mariadb.org/');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#mariadb-(\d+\.\d+(\.\d+)*)#", $node->nodeValue, $matches)) {
            $version = $matches[1];

            // skip all versions below v5.1.49, because this is the first one with a windows release folder
            if(version_compare($version, '5.1.48') <= 0) {
                $version = '0.0.0';
            };

            // skip all v10.0.0+ alpha versions
            if(version_compare($version, '10.0.0') >= 0) {
                $version = '0.0.0';
            };

            $filename = 'mariadb-'.$version.'-win32.zip';

            //  *** WARNING ***
            // The links are not consistent, because of folder name changes, see:
            // - windows releases are available from v5.1.49
            // - http://archive.mariadb.org/mariadb-5.1.49/kvm-zip-winxp-x86/
            // - some versions are missing in their archive, anyway..
            // - http://archive.mariadb.org/mariadb-5.2.6/win2008r2-vs2010-i386/mariadb-5.2.6-win32.zip
            // - http://archive.mariadb.org/mariadb-5.5.27/windows/mariadb-5.5.27-win32.zip
            // - http://archive.mariadb.org/mariadb-5.5.28/win32-packages/mariadb-5.5.28-win32.zip

            if($version <= '5.1.49') { $folder = 'kvm-zip-winxp-x86'; $filename = 'mariadb-noinstall-'.$version.'-win32.zip'; }
            elseif($version <= '5.2.6')  { $folder = 'win2008r2-vs2010-i386'; }
            elseif($version <= '5.5.23') { $folder = 'win2008r2-vs2010-i386-packages'; }
            elseif($version <= '5.5.27') { $folder = 'windows'; }
            elseif($version >= '5.5.28') { $folder = 'win32-packages'; }

            if (version_compare($version, $registry['mariadb']['latest']['version'], '>=')) {
                return array('version' => $version, 'url' => 'http://archive.mariadb.org/mariadb-' . $version . '/' . $folder .'/' . $filename);
            }
        }
    });
}

/**
 * XDebug - PHP Extension
 */
function get_latest_version_of_xdebug()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://xdebug.org/files/');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#((\d+\.)?(\d+\.)?(\d+\.)?(\*|\d+))([^\s]+nts(\.(?i)(dll))$)#i", $node->nodeValue, $matches)) {
            $version = $matches[1];
            if (version_compare($version, $registry['phpext_xdebug']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://xdebug.org/files/' . $node->nodeValue);
            }
        }
    });
}

/**
 * APC - PHP Extension
 */
function get_latest_version_of_apc()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://windows.php.net/downloads/pecl/releases/apc/');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)$#", $node->nodeValue, $matches)) {
            $version = $matches[1];
            if (version_compare($version, $registry['phpext_apc']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://windows.php.net/downloads/pecl/releases/apc/'.$version.'/php_apc-'.$version.'-5.4-nts-vc9-x86.zip'
                );
            }
        }
    });
}

/**
 * phpMyAdmin
 */
function get_latest_version_of_phpmyadmin()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://www.phpmyadmin.net/home_page/downloads.php');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(beta|b|rc|alpha|a|patch|pl|p)?(\d+)(?:[.-]?(\d+))?)?([.-]?dev)?#i", $node->nodeValue, $matches)) {
            $version = $matches[0];
            if (version_compare($version, $registry['phpmyadmin']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://switch.dl.sourceforge.net/project/phpmyadmin/phpMyAdmin/'.$version.'/phpMyAdmin-'.$version.'-english.zip'
                );
            }
        }
    });
}

/**
 * Adminer
 */
function get_latest_version_of_adminer()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://www.adminer.org/#download');

    return $crawler->filter('a')->each(function ($node, $i) use ($registry) {
        if (preg_match("#(\d+\.\d+(\.\d+)*)#", $node->nodeValue, $matches)) {
            $version = $matches[0];
            return array(
                'version' => $version,
                'url' => 'http://garr.dl.sourceforge.net/project/adminer/Adminer/Adminer%20'.$version.'/adminer-'.$version.'.php'
            );
        }
    });
}

/**
 * RockMongo - MongoDB Administration Webinterface
 */
function get_latest_version_of_rockmongo()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://rockmongo.com/downloads');

    // span tag contains "RockMongo v1.1.5"
    $text = $crawler->filterXPath('//ul/li/a/span')->text();

    if (preg_match("#(\d+\.\d+(\.\d+)*)#", $text, $matches)) {
        $version = $matches[0];
        if (version_compare($version, $registry['rockmongo']['latest']['version'], '>=')) {
            return array(
                'version' => $version,
                'url' => 'http://rockmongo.com/release/rockmongo-'.$version.'.zip'
            );
        }
    }
}

/**
 * MongoDb
 */
function get_latest_version_of_mongodb()
{
    global $goutte_client, $registry;

    // formerly http://www.mongodb.org/downloads
    $crawler = $goutte_client->request('GET', 'http://dl.mongodb.org/dl/win32/');

    return $crawler->filter('a')->each( function ($node, $i) use ($registry) {
        if (preg_match("#win32-i386-(\d+\.\d+(\.\d+)*).zip$#", $node->getAttribute('href'), $matches)) {
            $version = $matches[1];
            if (version_compare($version, $registry['mongodb']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://downloads.mongodb.org/win32/mongodb-win32-i386-'.$version.'.zip'
                );
            }
        }
     });
}

/**
 * phpMemcachedAdmin
 */
function get_latest_version_of_phpmemcachedadmin()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://code.google.com/p/phpmemcacheadmin/downloads/list');

    return $crawler->filter('a')->each( function ($node, $i) use ($registry) {
        // phpMemcachedAdmin-1.2.2-r262.zip
        if (preg_match("#(\d+\.\d+(\.\d+)*)(?:[._-]?(r)?(\d+))?#", $node->getAttribute('href'), $matches)) {
            $version_long = $matches[0]; // 1.2.3-r123
            $version = $matches[1]; // 1.2.3
            if (version_compare($version, $registry['phpmemcachedadmin']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://phpmemcacheadmin.googlecode.com/files/phpMemcachedAdmin-'.$version_long.'.zip'
                );
            }
        }
     });
}


/**
 * phpext_mongodb - PHP Extension for MongoDB
 */
function get_latest_version_of_phpext_mongo()
{
    global $goutte_client, $registry;

    /**
     * WARNING
     * The windows builds got no version listing, because Github stopped their downloads service.
     * Old Listing URL: https://github.com/mongodb/mongo-php-driver/downloads
     *
     * Downloads are now on AS3.
     * We scrape the PECL site for version numbers (mongo-1.3.4.tgz)
     * and expect a matching windows build on AS3  (mongo-1.3.4.zip).
     */

    $crawler = $goutte_client->request('GET', 'http://pecl.php.net/package/mongo');

    return $crawler->filter('a')->each( function ($node, $i) use ($registry) {
        // mongo-1.3.4.tgz
        if (preg_match("#mongo-(\d+\.\d+(\.\d+)*)(?:[._-]?(rc)?(\d+))?#i", $node->getAttribute('href'), $matches)) {
            $version = $matches[1]; // 1.2.3
            if (version_compare($version, $registry['phpext_mongo']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://s3.amazonaws.com/drivers.mongodb.org/php/php_mongo-'.$version.'.zip'
                );
            }
        }
     });
}

/**
 * OpenSSL
 */
function get_latest_version_of_openssl()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://slproweb.com/products/Win32OpenSSL.html');

    return $crawler->filter('a')->each( function ($node, $i) use ($registry) {
        // http://slproweb.com/download/Win32OpenSSL_Light-1_0_1d.exe
        if (preg_match("#Win32OpenSSL_Light-(\d+\_\d+\_\d+[a-z]).exe$#", $node->getAttribute('href'), $matches)) {
            // turn 1_0_1d to 1.0.1d - still not SemVer but anyway
            $version = str_replace('_', '.', $matches[1]);
            if (version_compare($version, $registry['openssl']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    'url' => 'http://slproweb.com/download/Win32OpenSSL_Light-'.$matches[1].'.exe'
                );
            }
        }
     });
}

/**
 * PostGreSql
 */
function get_latest_version_of_postgresql()
{
    global $goutte_client, $registry;

    $crawler = $goutte_client->request('GET', 'http://www.enterprisedb.com/products-services-training/pgbindownload');

    return $crawler->filterXPath('//p/i')->each( function ($node, $i) use ($registry) {
        //echo $node->nodeValue; // = Binaries from installer version 9.3.0 Beta2
        $value = strtolower($node->nodeValue);

        if (preg_match("#(\d+\.\d+(\.\d+)*)(?:(\s)(beta|b|rc|alpha|a|patch|pl|p)?(\d+))?#", $value, $matches)) {
            //var_dump($matches);

            if(isset($matches[4]) === true) { // if, we have " beta2" something after the version number
                $version = str_replace(' ', '-', $matches[0]); // turn "9.3.0 beta2" into "9.3.0-beta2"
                $download_version = $version;
            } else {
                $version = $matches[0]; // just 1.2.3
                $download_version = $version . '-1'; // wtf? "-1" means not beta or what?
            }

            if (version_compare($version, $registry['postgresql']['latest']['version'], '>=')) {
                return array(
                    'version' => $version,
                    // x86-64: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-x64-binaries.zip
                    // x86-32: http://get.enterprisedb.com/postgresql/postgresql-9.3.0-beta2-1-windows-binaries.zip
                    'url' => 'http://get.enterprisedb.com/postgresql/postgresql-'.$download_version.'-windows-binaries.zip'
                );
            }

        }
    });
}

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
 * Removes duplicates from the array.
 *
 * @param $array Array
 * @return $array
 */
function array_remove_duplicates(array $array)
{
    return array_map("unserialize", array_unique(array_map("serialize", $array)));
}

/**
 * Adds array data to the main software component array.
 *
 * @param $name Name of Software Component
 * @param $array Subarray of a software component, which should be added to the main array.
 */
function add($name, array $array)
{
    global $registry;

    // cleanup array
    $array = array_unset_null_values($array);

    $array = array_remove_duplicates($array);

    if(isset($array['url']) and isset($array['version'])) {
        // the array contains only one element

        // create [latest] sub-array
        $registry[$name]['latest']['url'] = $array['url'];
        $registry[$name]['latest']['version'] = $array['version'];

        // create [version] => [url] relationship
        $registry[$name][ $array['version'] ] = $array['url'];

        unset($array);
    } else {
        // sort by version number, from low to high
        asort($array);

        // add the last array item of multiple elements (the one with the highest version number)

        // insert the last array item as [latest][version] => [url]
        $registry[$name]['latest'] = array_pop($array);

        // insert the last array item also as a pure [version] => [url] relationship
        $registry[$name][ $registry[$name]['latest']['version'] ] = $registry[$name]['latest']['url'];
    }

    // added remaining array items (if any) as pure [version] => [url] relationships
    if(false === empty($array)) {
        foreach ($array as $new_version_entry) {
            $registry[$name][ $new_version_entry['version'] ] = $new_version_entry['url'];
        }
    }

    asort($registry[$name]);
}

/**
 * PHP release files are moved from "/releases" to "/releases/archives".
 * That means, latest version must point to "/releases".
 * Every other version points to "/releases/archives".
 */
function adjust_php_download_path()
{
    global $registry;

    foreach($registry['php'] as $version => $url) {
        // do not modify array key "latest"
        if( $version === 'latest') continue;
        // do not modify array key with latest version number - it must point to "/releases".
        if( $version === $registry['php']['latest']['version']) continue;
        // replace the path on any other version
        $new_url = str_replace('php.net/downloads/releases/php', 'php.net/downloads/releases/archives/php', $url);
        // insert at old array position, overwriting the old url
        $registry['php'][$version] = $new_url;
    }

}

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
    rename( 'wpnxm-software-registry.php', 'wpnxm-software-registry' . date("d-m-y-H-i-s") . '.php' );

    // file header
    $content = "<?php\n";
    $content .= "   /**\n";
    $content .= "    * WPN-XM Software Registry\n";
    $content .= "    * ------------------------\n";
    $content .= "    * Last Update " . date(DATE_RFC2822) . ".\n";
    $content .= "    * Do not edit manually!\n";
    $content .= "    */\n";
    $content .= "\n return ";

    // sort registry (software components in alphabetical order)
    ksort($registry);

    // sort registry (version numbers in lower-to-higher order)
    // maintain "name" and "website" keys on top, then versions, then "latest" key on bottom.
    foreach($registry as $component => $array) {
        uksort($array, 'version_compare');

        // move 'latest' to the bottom of the arary
        $value = $array['latest'];
        unset($array['latest']);
        $array['latest'] = $value;

        // move 'name' to the top of the array
        if(array_key_exists('name', $array) === true) {
            $temp = array('name' => $array['name']);
            unset($array['name']);
            $array = $temp + $array;
        }

        $registry[$component] = $array;
     }

    // pretty print the array
    $content .= var_export( $registry, true ) . ';';

    // remove trailing spaces
    $content = removeEOLSpaces($content);

    // write new registry
    file_put_contents( 'wpnxm-software-registry.php', $content );
}

/**
 * Strips EOL spaces from the content.
 * Note: PHP's var_export() adds EOL spaces after array keys, like "'key' => ".
 *       I consider this a PHP bug. Anyway. Let's get rid of that.
 */
function removeEOLSpaces($content)
{
    $lines = explode("\n", $content);
    foreach($lines as $idx => $line) {
        $lines[$idx] = rtrim($line);
    }
    $content = implode("\n", $lines);
    return $content;
}

/**
 * Get Latest Versions and add them to the registry.
 */
add('nginx',              get_latest_version_of_nginx() );
add('php',                get_latest_version_of_php() );
add('mariadb',            get_latest_version_of_mariadb() );
add('phpext_xdebug',      get_latest_version_of_xdebug() );
add('phpext_apc',         get_latest_version_of_apc() );
add('phpmyadmin',         get_latest_version_of_phpmyadmin() );
add('adminer',            get_latest_version_of_adminer() );
add('rockmongo',          get_latest_version_of_rockmongo() );
add('mongodb',            get_latest_version_of_mongodb() );
add('phpmemcachedadmin',  get_latest_version_of_phpmemcachedadmin() );
add('phpext_mongo',       get_latest_version_of_phpext_mongo());
add('openssl',            get_latest_version_of_openssl());
add('postgresql',         get_latest_version_of_postgresql());

adjust_php_download_path();

/*var_dump($registry);*/

// handle $_GET['action']
// example call: registry-update.php?action=write-file
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
if(isset($action) && $action === 'write-file') {
    write_registry_file($registry);
}

/**
 * The function prints an update symbol if old_version is lower than new_version.
 *
 * @param string Old version.
 * @param string New version.
 */
function printUpdatedSign($old_version, $new_version) {
    if(version_compare($old_version, $new_version, '<') === true) {
        echo '<span style="color:green; font-size: 16px">&nbsp;&#x25B2;</span>';
    }
}
?>

<table border="1">
<tr>
    <th>Application</th><th>(Old) Latest Version</th><th>(New) Latest Version</th>
</tr>
<tr>
    <td>nginx</td><td><?php echo $old_registry['nginx']['latest']['version'] ?></td><td><?php echo $registry['nginx']['latest']['version'];
    printUpdatedSign($old_registry['nginx']['latest']['version'], $registry['nginx']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>php</td><td><?php echo $old_registry['php']['latest']['version'] ?></td><td><?php echo $registry['php']['latest']['version'];
    printUpdatedSign($old_registry['php']['latest']['version'], $registry['php']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>mariadb</td><td><?php echo $old_registry['mariadb']['latest']['version'] ?></td><td><?php echo $registry['mariadb']['latest']['version'];
    printUpdatedSign($old_registry['mariadb']['latest']['version'], $registry['mariadb']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>xdebug</td><td><?php echo $old_registry['phpext_xdebug']['latest']['version'] ?></td><td><?php echo $registry['phpext_xdebug']['latest']['version'];
    printUpdatedSign($old_registry['phpext_xdebug']['latest']['version'], $registry['phpext_xdebug']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>apc</td><td><?php echo $old_registry['phpext_apc']['latest']['version'] ?></td><td><?php echo $registry['phpext_apc']['latest']['version'];
    printUpdatedSign($old_registry['phpext_apc']['latest']['version'], $registry['phpext_apc']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>phpmyadmin</td><td><?php echo $old_registry['phpmyadmin']['latest']['version'] ?></td><td><?php echo $registry['phpmyadmin']['latest']['version'];
    printUpdatedSign($old_registry['phpmyadmin']['latest']['version'], $registry['phpmyadmin']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>adminer</td><td><?php echo $old_registry['adminer']['latest']['version'] ?></td><td><?php echo $registry['adminer']['latest']['version'];
    printUpdatedSign($old_registry['adminer']['latest']['version'],  $registry['adminer']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>rockmongo</td><td><?php echo $old_registry['rockmongo']['latest']['version'] ?></td><td><?php echo $registry['rockmongo']['latest']['version'];
    printUpdatedSign($old_registry['rockmongo']['latest']['version'],  $registry['rockmongo']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>mongodb</td><td><?php echo $old_registry['mongodb']['latest']['version'] ?></td><td><?php echo $registry['mongodb']['latest']['version'];
    printUpdatedSign($old_registry['mongodb']['latest']['version'],  $registry['mongodb']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>phpext_mongo</td><td><?php echo $old_registry['phpext_mongo']['latest']['version'] ?></td><td><?php echo $registry['phpext_mongo']['latest']['version'];
    printUpdatedSign($old_registry['phpext_mongo']['latest']['version'],  $registry['phpext_mongo']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>phpmemcachedadmin</td><td><?php echo $old_registry['phpmemcachedadmin']['latest']['version'] ?></td><td><?php echo $registry['phpmemcachedadmin']['latest']['version'];
    printUpdatedSign($old_registry['phpmemcachedadmin']['latest']['version'],  $registry['phpmemcachedadmin']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>openssl</td><td><?php echo $old_registry['openssl']['latest']['version'] ?></td><td><?php echo $registry['openssl']['latest']['version'];
    printUpdatedSign($old_registry['openssl']['latest']['version'],  $registry['openssl']['latest']['version']); ?>
    </td>
</tr>
<tr>
    <td>postgresql</td><td><?php echo $old_registry['postgresql']['latest']['version'] ?></td><td><?php echo $registry['postgresql']['latest']['version'];
    printUpdatedSign($old_registry['postgresql']['latest']['version'],  $registry['postgresql']['latest']['version']); ?>
    </td>
</tr>
</table>

<a href="registry-update.php">Run Software Components Registry Update (dry-run)</a>
<br>
<a href="registry-update.php?action=write-file">Run Software Components Registry Update</a>
