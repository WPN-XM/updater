<?php
namespace WPNXM\Updater\Crawler;

/**
 * Node NPM - Version Crawler
 */
class Nodenpm extends VersionCrawler
{
    public $url = 'http://nodejs.org/dist/npm/';

    public function crawlVersion()
    {
        return $this->filter('a')->each(function ($node, $i) {
        	// http://nodejs.org/dist/npm/npm-1.4.6.zip
            if (preg_match("#(\d+\.\d+(\.\d+)*)(.zip)$#i", $node->text(), $matches)) {
                if (version_compare($matches[1], $this->registry['nodenpm']['latest']['version'], '>=')) {
                    return array(
                        'version' => $matches[1],
                        'url' => 'http://nodejs.org/dist/npm/npm-' . $matches[1] . '.zip'
                    );
                }
            }
        });
    }
}