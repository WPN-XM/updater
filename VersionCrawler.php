<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Crawler;

/**
 * Abstract Base Class for all Version Crawlers. surprise, surprise :)
 * The base class extends Symfony's DomCrawler.
 */
abstract class VersionCrawler extends \Symfony\Component\DomCrawler\Crawler
{
    public $url;
    public $registry;
    public $guzzle;

    /**
     * The variable controls, if this version crawler object gets
     * a complete registry (for dependency analysis) or only the
     * "self-named" subset (for new version greater then old version).
     *
     * @var boolean
     */
    public $needsOnlyRegistrySubset = true;
    public $needsGuzzle = true;

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
        if($this->needsGuzzle === true) {
            $this->guzzle = $guzzle;
        }
    }

    /**
     * Set Software Registry (for version_compare).
     */
    public function setRegistry($registry, $component = null)
    {
        // set only the component relevant subset of the software registry
        if($this->needsOnlyRegistrySubset === true && isset($registry[$component]) === true) {
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
        if(isset($this->name)) {
            return $this->name;
        }

        $classname = get_called_class();

        return strtolower(substr($classname, strrpos($classname, '\\')+1));
    }

    /**
     * Checks, if URL exists via header evaluation.
     *
     * @return bool Returns true, if URL exists, otherwise false.
     */
    public function fileExistsOnServer($url)
    {
        $headers = get_headers($url);

        return ($headers[0] === 'HTTP/1.1 200 OK') ? true : false;
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
     * http://php.net/amqp/%version%/php_amqp-%version%-%phpversion%-nts-%compiler%-x86.zip
     *
     * array(
     *  '5.4' => url,
     *  '5.5' => url,
     *  '5.6' => url
     * )
     *
     * @param URL $url PHP Extension URL with placeholders.
     * @return array
     */
    public function createPhpVersionsArrayForExtension($version, $url)
    {
        $url = str_replace("%version%", $version, $this->url);

        $phpversions = array('5.4', '5.5', '5.6');
        $urls        = array();

        foreach ($phpversions as $phpversion) {
            // compiler
            if ($phpversion === '5.4') {
                $compiler = 'VC9';
            } else {
                $compiler = 'VC11';
            }
            $replacedUrl = str_replace(array('%compiler%', '%phpversion%'), array($compiler, $phpversion), $url);

            if ($this->fileExistsOnServer($replacedUrl) === true) {
                $urls[$phpversion] = $replacedUrl;
            }
        }

        return $urls;
    }
}
