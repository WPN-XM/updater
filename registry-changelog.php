<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

/**
 * Registry Changelog
 *
 * This scripts generates a changelog by comparing two installer registries.
 */

echo getChangelog('bigpack-0.7.0-w32', 'full-0.8.0-php5.4-w32');

/**
 * Installer Registry Difference
 *
 * @return $result array Array Difference in Changelog style.
 */
function diffRegistries($registryA, $registryB)
{
    $a = json_decode($registryA, true);
    $b = json_decode($registryB, true);

    $a = reindexArrayComponentNamed($a);
    $b = reindexArrayComponentNamed($b);

    $diff = array();

    foreach ($a as $component => $values) {
        // difference entry: version A does no longer exist in B (removed).
        if (array_key_exists($component, $b) === false) {
            $diff[$component] = 'DEL ' . $component . ' was removed';
            continue;
        }

        $versionA = $values[3];
        $versionB = $b[$component][3];

        // version comparison
        #echo 'Comparing ' . $component . ': Version A ' . $versionA . ' with Version B ' . $versionB . '<br>';

        $vcResult = version_compare($versionA, $versionB);

        // Version A lower then Version B
        if ($vcResult === -1) {
            $diff[$component] = 'UPD ' . $component . ' was updated from v' . $versionA . ' to v' . $versionB;
        }

        // Version A equals Version B
        elseif ($vcResult === 0) {
            // do nothing
            //$diff[$component] = '';
        }

        /**
         * Version A higher Version B
         *
         * this shouldn't happen at all.
         * this would mean, that we use a lower component version in a higher installer version .. o.O
         * the only use case might be, to downgrade to fix a version incompatability.
         */ 
        elseif ($vcResult === 1) {
            $diff[$component] = 'UPD ' . $component . ' was downgraded from v' . $versionA . ' to v' . $versionB;
        }

        unset($versionA, $versionB);
    }

    foreach ($b as $component => $values) {
        // difference entry: B contains a component which is not in A (added).
        if (array_key_exists($component, $a) === false) {
            $diff[$component] = 'ADD ' . $component . ' v' . $values[3] . ' was added';
        }
    }

    return $diff;
}

/**
 * Convert a numerically indexed array to a "component name" indexed array.
 */
function reindexArrayComponentNamed($array)
{
    $result = array();
    foreach ($array as $key => $values) {
        $result[$values[0]] = $values;
    }
    return $result;
}

function getChangelog($registryNameA, $registryNameB)
{
    $diff = diffRegistries(
        file_get_contents(__DIR__ . '/registry/' . $registryNameA . '.json'), 
        file_get_contents(__DIR__ . '/registry/' . $registryNameB . '.json')
    );

    return "<pre>" . implode("\n", $diff) . "</pre>";
}
