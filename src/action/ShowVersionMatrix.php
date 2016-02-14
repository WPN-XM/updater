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

use Seld\JsonLint\JsonParser;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\ArrayUtil;
use WPNXM\Updater\InstallerRegistries;
use WPNXM\Updater\Registry;
use WPNXM\Updater\View;

/**
 * Show Installation Wizard Registries
 * - fetch the registry files
 * - split filenames to get version constraints (e.g. version, lite, php5.4, w32, w64)
 * - restructure the arrays for better iteration
 */
class ShowVersionMatrix extends ActionBase
{
    public $registry = array();

    public function __construct()
    {
        $this->registry = Registry::load();
    }

    public function __invoke()
    {
        $wizardFiles = InstallerRegistries::getAll();

        $wizardRegistries = [];

        foreach ($wizardFiles as $file) {
            $name = basename($file, '.json');

            $parts = [];

            if (substr_count($name, '-') === 2) {
                preg_match('/(?<installer>.*)-(?<version>.*)-(?<bitsize>.*)/i', $name, $parts);
            }

            if (substr_count($name, '-') === 3) {
                preg_match('/(?<installer>.*)-(?<version>.*)-(?<phpversion>.*)-(?<bitsize>.*)/i', $name, $parts);
            }

            $wizardRegistries[$name]['constraints'] = $this->dropNumericKeys($parts);
            unset($parts);

            /**
             * Finding errors in JSON files manually is tedious.
             * Let's use JSON lint instead.
             *
             * Due to parsing and check the overall process is slower,
             * but we get exceptions thrown on syntax errors.
             * I've also enabled the duplicate key check.
             */
            $parser = new JsonParser();
            try {
                $json = $parser->parse(file_get_contents($file), JsonParser::DETECT_KEY_CONFLICTS);
            } catch (\Exception $e) {
                if($e instanceOf \Seld\JsonLint\DuplicateKeyException) {
                    // duplicate key exception
                    $details = $e->getDetails();
                    throw new \Exception('Key ' . $details['key'] . ' is a duplicate in ' . $localConfig . ' at line ' . $details['line']);
                } else {
                    // parsing exception
                    throw new \Exception('Error while parsing "' . $file . '".' . $e->getMessage());
                }
            }

            $registryContent = $this->issetOrDefault($json, array());
            $wizardRegistries[$name]['registry'] = $this->fixArraySoftwareAsKey($registryContent);

            unset($parser, $registryContent);
        }

        $wizardRegistries = $this->sortWizardRegistries($wizardRegistries);

        /* -- View -- */

        $view = new View();
        $view->data['totalRegistries'] = count($this->registry);
        $view->data['registries']      = $wizardRegistries;
        $view->data['tableHeader']     = $this->renderTableHeader($wizardRegistries);
        $view->data['tableBody']       = $this->renderTableBody($this->registry, $wizardRegistries);
        $view->render();
    }

    /**
     * Sort Wizard registries from low to high version number,
     * with -next- registries at the bottom.
     */
    public function sortWizardRegistries($wizardRegistries)
    {
        uasort($wizardRegistries, "self::versionCompare");

        $cnt = $this->countNextRegistries($wizardRegistries);

        // copy
        $nextRegistries = array_slice($wizardRegistries, 0, $cnt, true);

        // reduce
        for ($i = 1; $i <= $cnt; $i++) {
            array_shift($wizardRegistries);
        }

        // append (to bottom)
        $wizardRegistries = array_merge($wizardRegistries, $nextRegistries);

        return $wizardRegistries;
    }

    public function countNextRegistries($registries)
    {
        $cnt = 0;

        foreach ($registries as $registry) {
            if ($registry['constraints']['version'] === 'next') {
                $cnt++;
            }
        }

        return $cnt;
    }

    public function versionCompare($a, $b)
    {
        return version_compare($a['constraints']['version'], $b['constraints']['version'], '>=');
    }

    public function dropNumericKeys(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_int($key) === true) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function issetOrDefault($var, $defaultValue = null)
    {
        return (isset($var) === true) ? $var : $defaultValue;
    }

    public function issetArrayKeyOrDefault(array $array, $key, $defaultValue = null)
    {
        return (isset($array[$key]) === true) ? $array[$key] : $defaultValue;
    }

    public function fixArraySoftwareAsKey(array $array)
    {
        $out = array();
        foreach ($array as $key => $values) {
            $software       = $values[0];
            unset($values[0]);
            $out[$software] = $values[3];
        }

        return $out;
    }

    public function renderTableBody(array $registry, array $wizardRegistries)
    {
        $html = '';

        foreach ($registry as $software => $data) {
            $versions = ArrayUtil::reduceArrayToContainOnlyVersions($data);

            $html .= '<tr>';
            $html .= '<td>' . $software . '</td>';
            $html .= $this->renderTableCells($wizardRegistries, $software);
            $html .= $this->renderVersionDropdown($software, $versions);
            $html .= '</tr>';
        }

        return $html;
    }

    public function renderTableHeader(array $wizardRegistries)
    {
        $header = '';
        $i      = 0;

        // 1th header row - column identifiers
        foreach ($wizardRegistries as $wizardName => $wizardRegistry) {
            $header .= '<th>' . $wizardName . '</th>';
            $i++;
        }
        $header .= '<th style="width: 40px;">Latest Version</th><th>Compose New Registry <br> <input type="text" class="form-control" name="new-registry-name"></th></tr>';

        // 2nd header row - "use installer name buttons"
        $header .= '<tr><th>Use installer name</th>';
        for ($j = 1; $j <= $i; $j++) {
            $header .= '<th><button type="button" id="syncInstallerNameButton' . $j . '" class="btn btn-default btn-block" title="Use name of this installer.">';
            $header .= '<span class="glyphicon glyphicon-share-alt"></span>';
            $header .= '</button></th>';
        }
        $header .= '</tr>';

        // 3nd header row - "derive versions buttons"
        $header .= '<tr><th>Derive versions from this installer</th>';
        for ($j = 1; $j <= $i; $j++) {
            $header .= '<th><button type="button" id="syncDropDownsButton' . $j . '" class="btn btn-default btn-block" title="Derive versions from this installer.">';
            $header .= '<span class="glyphicon glyphicon-share-alt"></span>';
            $header .= '</button></th>';
        }
        $header .= '<th>&nbsp;</th>';
        $header .= '<th><button type="submit" class="btn btn-block btn-success pull-right" id="save-button">Save</button></th>';
        $header .= '</tr>';

        return $header;
    }

    public function renderTableCells(array $wizardRegistries, $software)
    {
        $cells = '';
        foreach ($wizardRegistries as $wizardName => $wizardRegistry) {
            // normal versions
            if (isset($wizardRegistry['registry'][$software]) === true) {
                $tooltip = 'data-rel="tooltip" data-placement="top" data-html="true" data-container="body" title="' . $wizardName . '<br>' . $software . '"';
                $cells .= '<td class="version-number" ' . $tooltip . '>' . $wizardRegistry['registry'][$software] . '</td>';
            } else {
                $cells .= '<td>&nbsp;</td>';
            }
        }

        return $cells;
    }

    public function renderVersionDropdown($software, $versions)
    {
        /*
         * handle "is always latest version" edge cases:
         * - "closure-compiler"
         * - "php-cs-fixer"
         * so the dropdown question must be : "do include" or "do not include".
         */
        if ($software === 'closure-compiler' || $software === 'php-cs-fixer') { //
            $html = '<td class="alert alert-success"><strong>Latest<strong></td>';
            // td: version dropdown
            $html .= '<td><!-- Select --><div>
                  <select id="version_' . $software . '" name="version_' . $software . '" class="form-control">
                  <option value="do-not-include">Do Not Include</option>
                  <option value="latest">Include</option>
                  </select></div></td>';

            return $html;
        }

        // td: latest version
        $html = '<td class="alert alert-info center"><strong>' . key($versions) . '</strong></td>';

        // td: version dropdown
        $html .= '<td><!-- Select --><div>
              <select id="version_' . $software . '" name="version_' . $software . '" class="form-control">
                  <option value="do-not-include">Do Not Include</option>';

        $latest_version = key($versions);

        foreach ($versions as $version => $url) {
            $selected = ($version === $latest_version) ? ' selected' : '';
            $html .= '<option value="' . $version . '"' . $selected . '>' . $version . '</option>' . PHP_EOL;
        }

        $html .= '</select></div></td>';

        return $html;
    }

}
