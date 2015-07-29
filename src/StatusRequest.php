<?php

namespace WPNXM\Updater;

class StatusRequest
{
    /**
     * Builds an array with Download URLs to the WPN-XM Server
     *
     * http://wpn-xm.org/get.php?s=%software%
     *
     * http://wpn-xm.org/get.php?s=%software%&p=%phpversion%&bitsize=%bitsize%
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

    /**
     * Returns the HTTP Status Code for a URL
     *
     * @param  string $url URL
     * @return string 3-digit status code
     */
    public static function getHttpStatusCode($url)
    {
        if(false !== strpos($url, 'googlecode')) {
            $method = 'GET';
        } else {
            $method = 'HEAD';
        }

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

        #var_dump($statusCode);

        #if($statusCode === 'HTTP/1.0 404 Not Found') {
        #    var_dump($url);
        #}

        $code = 0;

        if($statusCode === '302 Found') {
            $code = substr($statusCode, 0, 6);
        }

        if($statusCode === 'HTTP/1.0 200 OK' or $statusCode === 'HTTP/1.1 200 OK') {
            $code = substr($statusCode, 9, 3);
        }

        return $code;
    }

    /*
     * Returns cURL responses (http status code) for multiple target URLs (CurlMultiResponses).
     *
     * @param array $targetUrls Array of target URLs for cURL
     * @return array cURL Responses
     */
    public static function getHttpStatusCodesInParallel(array $targetUrls, $timeout = 15)
    {
        // get number of urls
        $count = count($targetUrls);

        $options = array(
            CURLOPT_HEADER         => true,
            CURLOPT_RETURNTRANSFER => true, // do not output to browser
            CURLOPT_NOPROGRESS     => true,
            //CURLOPT_URL => $url,
            CURLOPT_NOBODY         => true, // do HEAD request only, exclude the body from output
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_FORBID_REUSE   => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSLVERSION     => 3,
            CURLOPT_ENCODING       => '', // !important
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_USERAGENT, 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/',
            CURLOPT_CUSTOMREQUEST  => 'HEAD' // do only HEAD requests
        );

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
            //echo $targetUrls[$i];
            //var_dump($responses[$i]);

            // Response: HTTP Status Code
            $code = curl_getinfo($ch[$i], CURLINFO_HTTP_CODE);
            $responses[$i] = ($code === 200 or $code === 302) ? true : false;
        }

        curl_multi_close($mh);

        return $responses;
    }
}
