<?php 
// WPNXM Software Registry
$registry  = include __DIR__ . '\registry\wpnxm-software-registry.php';

// Installation Wizard Registries
$wizardFiles = glob(__DIR__ . '\registry\*.json');
$wizardRegistries = array();
foreach($wizardFiles as $file) {
	$name = str_replace('wpnxm-software-registry-', '', basename($file, '.json'));
    //$version = explode("-", $name)[1];
	$wizardRegistries[$name] = fixArraySoftwareAsKey(json_decode(file_get_contents($file), true));
}


function fixArraySoftwareAsKey($array) {
	$out = array();
	foreach($array as $key => $values) {
		$software = $values[0];
		unset($values[0]);
		$out[$software] = $values[3];
	}
	return $out;
}

function renderTableHeader($wizardRegistries)
{
	$header = '';
    $i = 0;
    
    // 1th header row
	foreach($wizardRegistries as $wizardName => $wizardRegistry) {
		$header .= '<th>' . $wizardName. '</th>';
        $i++;
	}     
    $header .= '<th class="col-md-2">Latest</th><th>Compose New Registry</th></tr>';
    
    // 2nd header row
    $header .= '<tr><th>&nbsp</th>';
    for($j=1; $j <= $i; $j++) {
        $header .= '<th><span class="glyphicon glyphicon-share-alt pull-right"></span></th>';
    }
    $header .= '<th>&nbsp;</th>';
    $header .= '<th><input type="text" class="form-control" name="new-registry-name"><br/>';
    $header .= '<button type="submit" class="btn btn-xs btn-primary pull-right">Create</button></th>';
    
	return $header;
}

function renderTableCells($wizardRegistries, $software)
{
	$cells = '';
	foreach($wizardRegistries as $wizardName => $wizardRegistry) { 
        // special cases
        /*if($software === 'closure-compiler') { // always latest
            $cells .= '<td class="alert alert-success">Latest</td>';
            continue;
        }*/
        
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
    // special case: closure-compiler = is always latest, no dropdown required
    /*if($software === 'closure-compiler') { // 
        $html = '<td class="alert alert-success"><strong>Latest<strong></td>';
        $html .= '<td>&nbsp;</td>';
        return $html;
    }*/  
    
    // td: latest version
    $html = '<td class="alert alert-info center"><strong>'.key($versions).'</strong></td>';
   
    // td: version dropdown
    $html .= '<td><!-- Select --><div>
              <select id="version_' . $software . '" name="version_' . $software . '" class="form-control">
                  <option value="">Do Not Include</option>';

    $latest_version = key($versions);
    
    foreach ($versions as $version => $url) {        
        $selected = ($version === $latest_version) ? ' selected' : '';
        $html .= '<option value="' . $version . '"' . $selected .'>' . $version . '</option>' . PHP_EOL;
    }

    $html .= '</select></div></td>';
    
    return $html;
}
?>

<table class="table table-condensed table-bordered" style="width: auto !important; padding: 0px; vertical-align: middle;">
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