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

class StatusRequest
{
    /**
     * Builds an array with Download URLs to the WPN-XM Server
     *
     * https://wpn-xm.org/get.php?s=%software%
     *
     * https://wpn-xm.org/get.php?s=%software%&p=%phpversion%&bitsize=%bitsize%
     *
     * @param  type  $registry
     * @return array
     */
    public static function getUrlsToCrawl($registry)
    {
        // build array with URLs to crawl
        $urls = array();

        foreach ($registry as $software => $keys) {

            // if software is a PHP Extension, we have a latest version with URLs for multiple PHP versions
            if (strpos($software, 'phpext_') !== false) {
                $bitsizes = $keys['latest']['url'];
                foreach ($bitsizes as $bitsize => $phpversions) {
                    foreach ($phpversions as $phpversion => $url) {
                        $urls[] = $url;
                        $urls[] = 'http://wpn-xm.org/get.php?s=' . $software . '&p=' . $phpversion . '&bitsize=' . $bitsize;
                    }
                }
            } else {
                // standard software component (without php constraints)
                $urls[] = $keys['latest']['url'];
                $urls[] = 'http://wpn-xm.org/get.php?s=' . $software;
            }
        }

        #echo '<pre>' . var_export($urls, true) . '</pre>'; exit;

        return $urls;
    }

    // filter out the URLs which do not work with HEAD requests
    public static function filterSpecialUrls($urls)
    {
        $specialUrls = [];

        foreach($urls as $idx => $url)
        {
            if (strpos($url, 'googlecode') !== false)
            {
                $specialUrls[$idx] = $url;
                unset($urls[$idx]);
            }
        }

        return $specialUrls;
    }

    public static function getHttpStatusCodeOfUrls($urls)
    {
        $responses = [];

        foreach($urls as $idx => $url)
        {
            $responses[$idx] = self::getHttpStatusCode($url);
        }

        return $responses;
    }

    /**
     * Returns the HTTP Status Code for a URL
     *
     * @param  string $url URL
     * @return string 3-digit status code
     */
    public static function getHttpStatusCode($url)
    {
        // switch request method for "googlecode" to GET
        $method = (false !== strpos($url, 'googlecode')) ? 'GET' : 'HEAD';

        stream_context_set_default(array(
            'http' => array(
                'method' => $method
            )
        ));

        $headers = get_headers($url, 1);

        if ($headers !== false && isset($headers['Status'])) {
            $statusCode = $headers['Status'];
        } else {
            $statusCode = $headers[0];
        }

        $code = 0;

        if($statusCode === '302 Found') {
            $code = 302;
        }

        if($statusCode === 'HTTP/1.0 200 OK' or $statusCode === 'HTTP/1.1 200 OK') {
            $code = 200;
        }

        return $code;
    }

    /*
     * Returns cURL responses (http status code) for multiple target URLs (CurlMultiResponses).
     *
     * @param array $targetUrls Array of target URLs for cURL
     * @return array cURL Responses
     */
    public static function getHttpStatusCodesInParallel(array $targetUrls, $timeout = 30)
    {
        // get number of urls
        $count = count($targetUrls);

        // add additional curl options here
        $options = [
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CUSTOMREQUEST  => 'HEAD', // do only HEAD requests
            CURLOPT_ENCODING       => '', // !important
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FORBID_REUSE   => false,
            CURLOPT_HEADER         => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_NOBODY         => true, // do HEAD request only, exclude the body from output
            CURLOPT_NOPROGRESS     => true,
            CURLOPT_RETURNTRANSFER => true, // do not output to browser
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSLVERSION     => 4,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_USERAGENT      => 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/',
        ];

        $mh = curl_multi_init();

        $ch = array();

        // create multiple cURL handles, set options and add them to curl_multi handler
        for ($i = 0; $i < $count; $i++) {
            $ch[$i] = curl_init($targetUrls[$i]);
            curl_setopt_array($ch[$i], $options);
            curl_multi_add_handle($mh, $ch[$i]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        $responses = array();

        // remove handles and return the responses
        for ($i = 0; $i < $count; $i++) {
            curl_multi_remove_handle($mh, $ch[$i]);

            // Response: Content
            //$responses[$i] = curl_multi_getcontent($ch[$i]);

            // Response: HTTP Status Code
            $code = curl_getinfo($ch[$i], CURLINFO_HTTP_CODE);

            #var_dump($targetUrls[$i], $code, curl_getinfo($ch[$i]));

            // Check for errors and display the error message
            if($error_message = curl_error($ch[$i])) {
                echo sprintf('<p class="bg-danger">[cURL Error] %s</p>', $error_message);
            }

            $responses[$i] = ($code === 200 or $code === 302 or $code === 403) ? true : false;
        }

        curl_multi_close($mh);

        return $responses;
    }
}
