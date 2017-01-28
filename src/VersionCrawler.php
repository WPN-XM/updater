<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

/**
 * Abstract Base Class for all Version Crawlers. surprise, surprise :)
 * The base class extends Symfony's DomCrawler.
 */
abstract class VersionCrawler extends \Symfony\Component\DomCrawler\Crawler
{
    public $url;
    public $registry;
    public $guzzle;
    public $name;

    /**
     * The variable controls, if this version crawler object gets
     * a complete registry (for dependency analysis) or only the
     * "self-named" subset (for new version greater then old version).
     *
     * @var boolean
     */
    public $needsOnlyRegistrySubset = true;
    public $needsGuzzle             = true;

    /**
     * Set the request URL for the version crawler.
     *
     * @return string The request URL.
     */
    public function getURL()
    {
        return $this->url;
    }

    /**
     * Set Guzzle to the crawler object, for doing further requests.
     *
     * @param GuzzleClient $guzzle
     */
    public function setGuzzle($guzzle)
    {
        if ($this->needsGuzzle === true) {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * Set Software Registry (for version_compare).
     */
    public function setRegistry($registry, $component = null)
    {
        // set only the component relevant subset of the software registry
        if ($this->needsOnlyRegistrySubset === true && isset($registry[$component]) === true) {
            $this->registry = array($component => $registry[$component]);
        } else {
            $this->registry = $registry;
        }
    }

    /**
     * Get component name from namespaced (child-)classname.
     * This is the registry key = component shorthand.
     *
     * @return string Name of Component (lowercased).
     */
    public function getName()
    {
        if (isset($this->name)) {
            return $this->name;
        }

        $classname = get_called_class();

        return strtolower(substr($classname, strrpos($classname, '\\')+1));
    }

    /**
     * Checks, if URL exists via header evaluation.
     * Uses cURL and looks for HTTP Status Code 200.
     *
     * @param  string $url
     * @return bool   Returns true, if URL exists, otherwise false.
     */
    public function fileExistsOnServer($url)
    {
        if(!extension_loaded('curl')) {
            throw new \Exception('PHP Extension cURL not loaded.');
        }

        $curl = curl_init();

        $options = array(
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST  => 'HEAD' // do only HEAD requests
        );

        curl_setopt_array($curl, $options);
        curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return ($response_code === 200) ? true : false;
    }

    /**
     * Each Version Crawler has to implement this "scraping" method.
     *
     * The "how to scrape" one liner :)
     * printf("%s (%s)\n</br>", $node->text(), $node->attr('href'));
     *
     * See API of \Symfony\Component\DomCrawler\Crawler for more.
     *
     * @return array Array with keys 'version' and 'url'.
     */
    abstract public function crawlVersion();

    /**
     * Overload this method in a crawler object,
     * to perform registry changes, after a new version is detected.
     * This is just a pass-through.
     *
     * @return array The Registry.
     */
    public function modifiyRegistry($registry)
    {
        return $registry;
    }

    /**
     * Creates an array for a PHP Extension URL.
     * Replaces %compiler% and %phpversion% placeholder strings in that URL:
     * http://php.net/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-%bitsize%.zip
     *
     * array (
     *   'x86' => array(
     *     '5.4' => url,
     *     '5.5' => url,
     *     '5.6' => url
     *    ),
     *  'x64' => array(
     *     '5.4' => url,
     *     '5.5' => url,
     *     '5.6' => url
     *  ),
     * )
     *
     * @param  string $url     PHP Extension URL with placeholders.
     * @param  string $version
     * @return array
     */
    public function createPhpVersionsArrayForExtension($version, $url, $skipURLcheck = false)
    {
        $bitsizes    = array('x86', 'x64');
        $phpversions = array('5.4', '5.5', '5.6', '7.0', '7.1'); // EOL: 5.4, 5.5
        $urls        = array();

        foreach ($bitsizes as $bitsize) {
            foreach ($phpversions as $phpversion) {

                $extUrl = self::getPHPExtensionURL($url, $version, $phpversion, $bitsize);

                if ($skipURLcheck === true) {
                    $urls[$bitsize][$phpversion] = $extUrl;
                } elseif ($this->fileExistsOnServer($extUrl) === true) {
                    $urls[$bitsize][$phpversion] = $extUrl;
                }
            }
        }

        return $urls;
    }

    /**
     * get PHP Extension URL from placeholder string
     *
     * @param  $url A string with placeholders for the PHP extension.
     * @return string URL of PHP extension.
     */
    public static function getPHPExtensionURL($url, $version, $phpversion, $bitsize)
    {
        $compiler = self::getCompilerByPHPVersion($phpversion);

        return str_replace(
            array('%version%', '%compiler%', '%phpversion%', '%bitsize%'),
            array($version, $compiler, $phpversion, $bitsize),
            $url
        );
    }

    public static function getCompilerByPHPVersion($phpversion)
    {
        $map = [
            '5.4' => 'vc9',
            '5.5' => 'vc11',
            '5.6' => 'vc11',
            '7.0' => 'vc14',
            '7.1' => 'vc14'
        ];

        if(isset($map[$phpversion])) { return $map[$phpversion]; }

        throw new \Exception('Can\'t find Compiler version for this PHP version: ' . $phpversion);
    }

        /**
     * Returns the latest version of a component inside a min/max version range.
     *
     * Example: fetch the "latest patch version" of a given "major.minor" version (5.4.*).
     * getLatestVersion('php', '5.4.1', '5.4.99') = "5.4.30".
     *
     * @param array Only the versions array for this component from the registry.
     * @param string A version number, setting the minimum (>=).
     * @param string A version number, setting the maximum (<).
     *
     * @return string Returns the latest version of a component given a min max version constraint.
     */
    public function getLatestVersionOfRange($versions, $minConstraint = null, $maxConstraint = null)
    {
        // get rid of (version => url) and use (idx => version)
        $versions = array_keys($versions);

        // reverse array, in order to have the highest version number on top
        $versions = array_reverse($versions);

        // reduce array to values in constraint range
        foreach ($versions as $idx => $version) {

            // fix "5.y" to "5.y.1"
            if (strlen($version) === 3) {
                $version = $version . '.1';
            }

            if (version_compare($version, $minConstraint, '>=') === true && version_compare($version, $maxConstraint, '<') === true) {
                #echo 'Version v' . $version . ' is greater v' . $minConstraint . '(MinConstraint) and smaller v' . $maxConstraint . '(MaxConstraint).<br>';
            } else {
                unset($versions[$idx]);
            }
        }

        // pop off the first element
        $latestVersion = array_shift($versions);

        return $latestVersion;
    }

    public function isHighestMajorMinorVersion($version, $registry_subset)
    {
        if (2 === substr_count($version, '.')) { // 1.2.3
            $phpVersion = substr($version, 0, -2);
            return $this->getLatestVersionOfRange($registry_subset, $phpVersion . '.0', $phpVersion . '.99');
        }

        return $version;
    }
}
