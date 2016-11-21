<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2016 Jens A. Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater;

use WPNXM\Updater\ApplicationVersion;
use WPNXM\Updater\CliArguments;

class CliApplication
{
    public function run()
    {
    	global $argv;
    	$cliArguments = new CliArguments($argv);    	
    	$this->handleCliArguments($cliArguments->all());
    }

    public function handleCliArguments($args)
    {
        if(empty($args)) {
            return $this->printHelp();
        }

    	foreach($args as $arg => $value) {

    		// "--help"
    		if($arg == 'help' || $arg == '-h') {
    			return $this->printHelp();
    		}
    		// "--crawl-version=adminer"
    		// "-c adminer"
    		if($arg == 'crawl-version' || ($arg == 'c' && is_string($value)) ) {
    			return $this->crawlVersions($value);
    		}

			// "--crawl-versions"
			// "--crawl-version a,b"
			// "-c"
			// "-c a,b"
    		if($arg == 'crawl-versions' || is_bool($value) || is_array($value))  {
				return $this->crawlVersions($value);
    		}

    		// "--version"
    		if($arg == 'version') {
    			return $this->printVersion();
    		}
    	}
    }

    public function printVersion()
    {    	
    	echo 'WPN-XM Server Stack - Updater ' . ApplicationVersion::get() . PHP_EOL;
    	echo 'Copyright (c) '.date('Y').' Jens A. Koch.'. PHP_EOL;
        echo PHP_EOL;
    }

    public function printHelp()
    {
    	echo $this->printVersion();
    	echo PHP_EOL;
        echo '@todo List commands and explain usage...';
        echo PHP_EOL;
    }

    public function crawlVersions($arg)
    {
    	echo $this->printVersion();

    	echo '[Started] Version Crawling for: ' . $arg . PHP_EOL;
        echo PHP_EOL;

        $components = explode(',', $arg);

		$crawl = new Action\ScanComponent();
        $crawl->crawl($components);
    	$crawl->getResults();
    }
}

