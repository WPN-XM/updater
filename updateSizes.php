<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

/**
 * This scripts fetches the packed/unpacked archive sizes
 * and calculates the ExtraDiskSpaceRequired for each component
 * in each installer specific download folder.
 * It creates a "sizes-report.txt", which provides all the details.
 * Finally, the installer files (*.iss) are updated with the new ExtraDiskSpaceRequired value.
 */

// calc sizes
$array = recursiveGlob('D:\Github\WPN-XM\WPN-XM\downloads', 'zip');
$array = convertDirNamesToFileNames($array);
$array = calculateSizesForFolders($array);
$array = calculateSizesOfCombinedComponents($array);

file_put_contents('sizes-report.txt', var_export($array, true));

// insert
insertExtraDiskSizeIntoInstallers($array);

function recursiveGlob($dir, $ext)
{
    $dirs  = glob("$dir\*", GLOB_ONLYDIR);
    $results = array();

    foreach ($dirs as $dir) {

        $files = glob("$dir\*.$ext");

        if(count($files) > 0) {
            foreach ($files as $file) {
                $component = str_replace(".$ext", '', basename($file));

                $results[basename($dir)][$component]['file'] = $file;
            }
        }

        recursiveGlob($dir, $ext);
    }

    return $results;
}

// get rid of the "version number" and the "PHP version dot" in the directory names
// so that directory names match the installer filenames
function convertDirNamesToFileNames($array)
{
    foreach($array as $dir => $values) {
        $dirWithoutVersion = str_replace(array('-0.8.0', '.'), array('', ''), $dir);

        $array[$dirWithoutVersion] = $array[$dir];
        unset($array[$dir]);
    }
    return $array;
}

function calculateSizesForZipArchive($file)
{
    $zip = new ZipArchive();

    $results = array();

    if ($zip->open($file) === true) {

        $totalSize = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileStats = $zip->statIndex($i);
            $totalSize += $fileStats['size'];
        }

        $results['uncompressed'] = $totalSize;
        $results['compressed'] = filesize($file);
        $results['extraDiskSpaceRequired'] = round( ($totalSize - filesize($file)), -4); // round to nearest thousand

        $zip->close();
    }

    return $results;
}

function calculateSizesForFolders($folders)
{
    foreach($folders as $folder => $components) {
        foreach($components as $component => $values) {
            $file = $values['file'];
            $results = calculateSizesForZipArchive($file);
            if(empty($results) === true) {
                echo 'Error calculating Zip Archive size: ' . $file . PHP_EOL;
            } else {
                $folders[$folder][$component] = array_merge($folders[$folder][$component], $results);
            }
        }
    }

    return $folders;
}

function calculateSizesOfCombinedComponents($folders)
{
    $results = [];

    foreach($folders as $folder => $components) {
            /**
             * Component: "serverstack"
             * Calculate the size for the base of the stack: php + nginx + mariadb.
             */
            $base = $components['php']['extraDiskSpaceRequired'] + $components['nginx']['extraDiskSpaceRequired'] + $components['mariadb']['extraDiskSpaceRequired'];
            $results['serverstack']['extraDiskSpaceRequired'] = round($base, -4);

            /**
             * Component: "phpextesions"
             * Calculate the size for all PHP extensions.
             */
            $phpext = 0;
            foreach($components as $component => $values) {
                if(strpos('phpext_', $component) !== false) {
                    $phpext =+ $values['extraDiskSpaceRequired'];
                }
            }
            $results['phpextensions']['extraDiskSpaceRequired'] = round($phpext, -4);

            $folders[$folder] = array_merge($folders[$folder], $results);
    }

    return $folders;
}

function insertExtraDiskSizeIntoInstallers($folders)
{
    $installers = glob("D:\Github\WPN-XM\WPN-XM\installers\*.iss");

    foreach($installers as $installer)
    {
        #echo 'Writing to ' . $installer . PHP_EOL;

        $lines = file($installer);

        foreach($folders as $folder => $components)
        {
            foreach($components as $component => $values)
            {
                // skip PHP extensions, they are Component: phpextensions;
                if(false !== strpos($component, 'phpext_')) {
                    continue;
                }

                echo 'Updating value for ' . $component . PHP_EOL;

                $nameLookup = 'Name: ' . $component;
                $extraDiskSpaceRequired = $values['extraDiskSpaceRequired'];

                foreach ($lines as $lineNum => $line)
                {
                    if(false !== strpos($line, $nameLookup)) {
                        $lines[$lineNum] = preg_replace("/ExtraDiskSpaceRequired:\s(\d+);/", "ExtraDiskSpaceRequired: $extraDiskSpaceRequired;", $line, 1);
                        break;
                    }
                }
            }
        }

        file_put_contents($installer, $lines);
    }
}
