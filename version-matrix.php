<?php
// WPN-XM Software Registry
$registry  = include __DIR__ . '\registry\wpnxm-software-registry.php';

/**
 * Installation Wizard Registries
 * - fetch the registry files
 * - split filenames to get version constraints (e.g. version, lite, php5.4, w32, w64)
 * - restructure the arrays for better iteration
 */
$wizardFiles = glob(__DIR__ . '\registry\*.json');
$wizardRegistries = array();
foreach($wizardFiles as $file) {
    $name = basename($file, '.json');
    if( preg_match('/(?<installer>.*)-(?<version>.*)-(?<phpversion>.*)-(?<bitsize>.*)/i', $name, $parts)
     || preg_match('/(?<installer>.*)-(?<bitsize>.*)/i', $name, $parts)) {
        $parts = dropNumericKeys($parts);
        $registries[$name]['constraints'] = $parts;
        unset($parts);
    }
    $registryContent = issetOrDefault(json_decode(file_get_contents($file), true), array());
    $wizardRegistries[$name] = fixArraySoftwareAsKey($registryContent);
}

asort($wizardRegistries);

function dropNumericKeys(array $array)
{
    foreach ($array as $key => $value) {
        if (is_int($key) === true) {
            unset($array[$key]);
        }
    }
    return $array;
}

function issetOrDefault($var, $defaultValue = null)
{
    return (isset($var) === true) ? $var : $defaultValue;
}

function issetArrayKeyOrDefault(array $array, $key, $defaultValue = null)
{
    return (isset($array[$key]) === true) ? $array[$key] : $defaultValue;
}

function fixArraySoftwareAsKey(array $array) {
    $out = array();
    foreach($array as $key => $values) {
        $software = $values[0];
        unset($values[0]);
        $out[$software] = $values[3];
    }
    return $out;
}

function renderTableHeader(array $wizardRegistries)
{
    $header = '';
    $i = 0;

    // 1th header row
    foreach($wizardRegistries as $wizardName => $wizardRegistry) {
        $header .= '<th>' . $wizardName. '</th>';
        $i++;
    }
    $header .= '<th style="width: 40px;">Latest</th><th>Compose New Registry <br> <input type="text" class="form-control" name="new-registry-name"></th></tr>';

    // 2nd header row
    $header .= '<tr><th>&nbsp</th>';
    for($j=1; $j <= $i; $j++) {
        $header .= '<th><button type="button" id="syncDropDownsButton' . $j . '" class="btn btn-default btn-block" title="Derive versions from this installer.">';
        $header .= '<span class="glyphicon glyphicon-share-alt"></span>';
        $header .= '</button></th>';
    }
    $header .= '<th>&nbsp;</th>';
    $header .= '<th><button type="submit" class="btn btn-small btn-primary pull-right">Create</button></th>';

    return $header;
}

function renderTableCells(array $wizardRegistries, $software)
{
    $cells = '';
    foreach($wizardRegistries as $wizardName => $wizardRegistry) {
        // normal versions
        if(isset($wizardRegistry[$software]) === true) {
            $cells .= '<td class="alert alert-success">' . $wizardRegistry[$software] . '</td>';
        } else {
            $cells .= '<td>&nbsp;</td>';
        }
    }

    return $cells;
}

function reduceArrayToContainOnlyVersions($array)
{
    unset($array['website'], $array['latest'], $array['name']);
    $array = array_reverse($array); // latest version first
    return $array;
}

function renderVersionDropdown($software, $versions)
{
    // edge case: "closure-compiler" is always latest version
    // so the dropdown question must be : "do include" or "do not include"
    if($software === 'closure-compiler') { //
        $html = '<td class="alert alert-success"><strong>Latest<strong></td>';
        // td: version dropdown
        $html .= '<td><!-- Select --><div>
                  <select id="version_' . $software . '" name="version_' . $software . '" class="form-control">
                  <option value="">Do Not Include</option>
                  <option value="latest">Include</option>
                  </select></div></td>';
        return $html;
    }

    // td: latest version
    $html = '<td class="alert alert-info center"><strong>'.key($versions).'</strong></td>';

    // td: version dropdown
    $html .= '<td><!-- Select --><div>
              <select id="version_' . $software . '" name="version_' . $software . '" class="form-control">
                  <option value="Do Not Include">Do Not Include</option>';

    $latest_version = key($versions);

    foreach ($versions as $version => $url) {
        $selected = ($version === $latest_version) ? ' selected' : '';
        $html .= '<option value="' . $version . '"' . $selected .'>' . $version . '</option>' . PHP_EOL;
    }

    $html .= '</select></div></td>';

    return $html;
}
?>

<table id="version-matrix" class="table table-condensed table-bordered" style="width: auto !important; padding: 0px; vertical-align: middle;">
<thead>
    <tr>
        <th>Software Components (<?php echo count($registry); ?>)</th>
        <?php echo renderTableHeader($wizardRegistries); ?>
    </tr>
</thead>
<?php
foreach($registry as $software => $data)
{
    echo '<tr><td>' . $software . '</td>'
        . renderTableCells($wizardRegistries, $software)
        . renderVersionDropdown($software, reduceArrayToContainOnlyVersions($data))
        . '</tr>';
}
?>
</table>
<script>
    $('div#ajax-container.container').css('width', 'auto');
    $('head').append('<style>.form-control { height: auto; padding: 0;} </style>');
</script>