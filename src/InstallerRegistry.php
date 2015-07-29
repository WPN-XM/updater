<?php

namespace WPNXM\Updater;

class InstallerRegistry
{
    /**
     * Writes the registry as JSON to the installer registry file.
     *
     * @param string $file
     * @param array  $registry
     */
    public static function write($file, $registry)
    {
        asort($registry);

        $json        = json_encode($registry);
        $json_pretty = Json::prettyPrintCompact($json);
        $json_table  = Json::prettyPrintTableFormat($json_pretty);

        file_put_contents($file, $json_table);

        echo 'Updated or Created Installer Registry "' . $file . '"<br />';
    }
}
