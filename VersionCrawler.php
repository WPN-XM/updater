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
     * Set Software Registry (for version_compare).
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get component name from namespaced (child-)classname.
     *
     * @return string Name of Component (lowercased).
     */
    public function getName()
    {
        //
        $classname = get_called_class();
        return strtolower(substr($classname, strrpos($classname, '\\')+1));
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
    abstract function crawlVersion();
}
?>