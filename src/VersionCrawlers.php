<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

class VersionCrawlers
{
    /**
     * Return array with one or more crawler file names.
     *
     * @param string $components
     * @return array
     */
    public static function getCrawlers($components = null)
    {
        // return multiple crawlers        
        if (isset($components)) {
            $components = (array) $components;
            $crawlers = [];
            foreach($components as $component) {
                $files = self::getCrawlerFile($component);
                if($files != false) {
                    $crawlers[] = $files[0];
                } else {
                    echo 'Crawler not found for Component: ' .  $component;         
                }
            }
            return $crawlers;
        }       

        // return all crawlers
        return glob(__DIR__ . '\crawler\*.php');
    }

    public static function getCrawlerFile($component)
    {
        $file = str_replace('-', '_', $component);

        return glob(__DIR__ . '\crawler\\' . $file . '.php');
    }

    /**
     * The function returns a list with all "name" properties of all crawlers.
     *
     * @return array Array of software names (crawler property "name").
     */
    public static function getSoftwareNames()
    {
        $softwareNames = [];

        $crawlers = self::getCrawlers();

        foreach ($crawlers as $i => $file) {

            // load and instantiate version crawler
            include $file;
            $classname = str_replace(array('-', '.'), array('_', '_'), strtolower(pathinfo($file, PATHINFO_FILENAME)));        
            $fqcn = 'WPNXM\Updater\Crawler\\' . ucfirst($classname);
            $crawler = new $fqcn();

            // check, if we have a latest version for this software, insert latest version to crawler object
            // this saves the array access inside the object
            $softwareName = $crawler->name;

            $softwareNames[] = $softwareName;
        }

        return $softwareNames;
    }

    /**
     * The function returns the "software name" of newly added crawlers.
     *
     * @return array Array of software names (crawler property "name").
     */
    public static function findCrawlersWithoutRegistryEntry($registry)
    {
        $registrySoftwareNames = array_keys($registry);
        $crawlerSoftwareNames  = self::getSoftwareNames();        
        $diff = array_diff($crawlerSoftwareNames, $registrySoftwareNames);

        return $diff;
    }
}