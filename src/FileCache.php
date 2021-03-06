<?php

/*
 * WPИ-XM Server Stack
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater;

/**
 * FileCache helps to cache remote content.
 */
class FileCache
{
    public static function get($url, $cacheFile, $modificationCallback = null, $cachetime = null)
    {
        if($cachetime == null) {
            $cachetime = 3 * 24 * 60 * 60; // 3 days
        }

        // When the cache file is less then cachetime old (default is 3 days),
        // do not refresh, just use the file as-is.
        if (file_exists($cacheFile) && (filemtime($cacheFile) > (time() - $cachetime))) {
            return file_get_contents($cacheFile);
        }

        // When the cache file is out-of-date, load the data from server and save it to cache.
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Accept-language: en\r\n".
                "User-Agent: Mozilla/5.0 (Windows; U; MSIE 7.0; Windows NT 6.0; en-US)\r\n",
            ], 
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];

        $context  = stream_context_create($options);
        $content  = file_get_contents($url, false, $context);

        // apply the content modification
        if ($modificationCallback instanceof \Closure) {
            $content = $modificationCallback($content);
        }

        file_put_contents($cacheFile, $content, LOCK_EX);

        return $content;
    }
}
