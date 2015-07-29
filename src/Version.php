<?php

namespace WPNXM\Updater;

class Version
{
    /**
     * Welcome in Version Compare Hell!
     * Some software components need their own version compare handling.
     */
    public static function compare($component, $oldVersion, $newVersion)
    {
        switch ($component) {
            case 'openssl':
            case 'openssl-x64':
                if (strcmp($oldVersion, $newVersion) < 0) {
                    return true;
                }
            case 'phpmyadmin':
                if (version_compare($oldVersion, $newVersion, '<') === true || (strcmp($oldVersion, $newVersion) < 0)) {
                    return true;
                }
            case 'imagick':
                if (Version::cmpImagick($oldVersion, $newVersion) === true) {
                    return true;
                }
            default:
                if (version_compare($oldVersion, $newVersion, '<') === true) {
                    return true;
                }
        }

        return false;
    }

    /**
     * Compare an Imagick version number.
     *
     * @param  string  $oldVersion
     * @param  string  $newVersion
     * @return boolean True, if newVersion is higher then oldVersion.
     */
    public static function cmpImagick($oldVersion, $newVersion)
    {
        $rOldVersion = str_replace('-', '.', $oldVersion);
        $rNewVersion = str_replace('-', '.', $newVersion);

        return version_compare($rNewVersion, $rOldVersion, '>');
    }

    public static function notInRegistry($version, $registry, $returnVersion = false)
    {
        if(is_array($version)) {
            // re-index the array
            $versions = array_values($version);

            // if one out of multiple version is missing.. return true.
            foreach($version as $v) {
                if(isset($registry[$v['version']]) === false) {
                    return ($returnVersion === true) ? $v['version'] : true;
                }
            }
        } elseif(isset($registry[$version]) === false) {
            return ($returnVersion === true) ? $version : true;
        }
    }
}