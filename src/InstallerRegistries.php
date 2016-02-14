<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

/**
 * This class helps to fetch installer registries
 * and work with their filenames to get version constraints.
 */
class InstallerRegistries
{

    /**
     * Returns an array with all installation wizard registry files (json)
     * of all installer versions.
     *
     * @return array filenames
     */
    public static function getAll()
    {
        $files = self::recursiveFind(REGISTRY_DIR.'installer', '#^.+\.json#i');

        if (empty($files)) {
            throw new \Exception('No JSON registries found.');
        }

        return $files;
    }

    /**
     * Returns an array with all installation wizard registry files (json)
     * of the requested version.
     *
     * @param  $version version string (without v prefix)
     * @return array filenames
     */
    public static function getByVersion($version)
    {
        $files = self::glob(REGISTRY_DIR.'installer\\v'.$version, '#^.+\.json#i');

        if (empty($files)) {
            throw new \Exception('No JSON registries found.');
        }

        return $files;
    }

    /**
     * Return the installer registries of the next
     * (upcoming, but yet unreleased) installer version.
     *
     * @return array filenames
     */
    public static function getRegistriesOfNextRelease()
    {
        $files = glob(REGISTRY_DIR.'installers\next\*-next-*.json');

        if (empty($files)) {
            throw new \Exception('No JSON registries found.');
        }

        return $files;
    }

    public static function recursiveFind($folder, $regexp)
    {
        $dir = new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dir);
        $files = new \RegexIterator($iterator, $regexp, \RegexIterator::GET_MATCH);

        $fileList = array();
        foreach($files as $file) {
            $fileList[] = $file[0];
        }

        return $fileList;
    }

    /**
     * Return the PHP version of a registry file.
     *
     * @param string  A filename, e.g. registry filename, like "full-next-php5.6-w64.json".
     * @param string $file
     * @return string PHP Version.
     */
    public static function getPHPVersionFromFilename($file)
    {
        preg_match("/-php(.*)-/", $file, $matches);

        return $matches[1];
    }

    public static function getPartsOfInstallerFilename($file)
    {
        $file = basename($file, '.json');

        if (substr_count($file, '-') === 3) {
            preg_match('/(?<installer>.*)-(?<version>.*)-php(?<phpversion>.*)-(?<bitsize>.*)/i', $file, $parts);

            return $parts;
        }

        if (substr_count($file, '-') === 2) {
            preg_match('/(?<installer>.*)-(?<version>.*)-(?<bitsize>.*)/i', $file, $parts);

            return $parts;
        }
    }

    public static function fixBitsize($bitsize)
    {
        $map = ['w32' => 'x86', 'w64' => 'x64'];

        return $map[$bitsize];
    }

    public static function getConstraintsFromFilename($file)
    {
        $constraints = self::getPartsOfInstallerFilename($file);
        $constraints['bitsize'] = self::fixBitsize($constraints['bitsize']);

        return $constraints;
    }

}