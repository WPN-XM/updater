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
        if($headers[0] === 'HTTP/1.1 200 OK') { return true; }
        return false;
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
}
