<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright © 2010 - 2015 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;

/**
 * Pretty prints all installation wizard registries.
 */
class PrettyPrintRegistries extends ActionBase
{
    public $registry;

    public function __construct()
    {
        $this->registry = Registry::load();

        Registry::clearOldScans();
    }

    public function __invoke()
    {
        $nextRegistries = $this->getInstallerRegistriesOfNextVersion();

        echo 'Pretty printing all installation wizard registries.<br>';

        foreach ($nextRegistries as $file) {
            $filename        = basename($file);
            echo '<br>Processing Installer: "' . $filename . '":<br>';
            $components      = json_decode(file_get_contents($file), true);
            Registry::write($file, $components);
        }

        echo '<pre>You might "git commit/push":<br>pretty printed registries</pre>';
    }

    public function getInstallerRegistriesOfNextVersion()
    {
        $nextRegistries = glob(REGISTRY_DIR . '*-next-*.json');

        if (empty($nextRegistries) === true) {
            throw new \Exception('No "next" JSON registries found. Create installers for the next version.');
        }

        return $nextRegistries;
    }
}
