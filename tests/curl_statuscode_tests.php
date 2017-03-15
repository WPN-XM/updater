<?php

$urls = [
    //'http://garr.dl.sourceforge.net/project/adminer/Adminer/Adminer%204.2.2/adminer-4.2.2.php', // 0
    //'http://wpn-xm.org/get.php?s=adminer', // 302 -> 0
    //'https://github.com/WPN-XM/benchmark-tools/releases/download/v1.0/ab.zip', // 0 | should give 200
    //'http://wpn-xm.org/get.php?s=apache-benchmark', // 302 | should be 302 -> 200
    //'http://wpn-xm.org/get.php?s=composer',
    //'http://getcomposer.org/composer.phar',
    'http://wpn-xm.org/get.php?s=nginx',
    'http://nginx.org/download/nginx-1.9.4.zip'
];


/**
 * Test A - getHttpStatusCodesInParallel
 */

include __DIR__ . '\src\StatusRequest.php';
WPNXM\Updater\StatusRequest::getHttpStatusCodesInParallel($urls);

/**
 * Test B - Curl
 */

foreach($urls as $url)
{
    $ch = curl_init();

    $options = array(
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_CUSTOMREQUEST  => 'HEAD', // do only HEAD requests
        CURLOPT_ENCODING       => '',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_NOBODY         => true, // do HEAD request only, exclude the body from output
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_URL            => $url,
        CURLOPT_USERAGENT      => 'WPN-XM Server Stack - Registry Status Tool - http://wpn-xm.org/',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLVERSION     => 4,
        #CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        CURLOPT_NOBODY         => true,
        CURLOPT_NOPROGRESS     => true,
        CURLOPT_VERBOSE        => 1,
    );
    curl_setopt_array( $ch, $options );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ( $httpCode != 200 ){
        echo "Return code is {$httpCode} \n" . curl_error($ch);
    } else {
        echo "<pre>".htmlspecialchars($response)."</pre>";
    }

    curl_close($ch);
}
