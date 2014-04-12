<?php 
// WPNXM Software Registry
$registry  = include __DIR__ . '\registry\wpnxm-software-registry.php';

// Installation Wizard Registries
$wizardFiles = glob(__DIR__ . '\registry\*.json');
$wizardRegistries = array();
foreach($wizardFiles as $file) {
	$name = str_replace('wpnxm-software-registry-', '', basename($file, '.json'));
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
	foreach($wizardRegistries as $wizardName => $wizardRegistry) {
		$header .= '<td>' . $wizardName. '</td>';
	}
	return $header;
}

function renderTableCells($wizardRegistries, $software)
{
	$cells = '';
	foreach($wizardRegistries as $wizardName => $wizardRegistry) {
        
        if(isset($wizardRegistry[$software]) === true) {
        	$cells .= '<td class="alert alert-success">' . $wizardRegistry[$software] . '</td>';
        } else {
            $cells .= '<td>&nbsp;</td>';
        }
	}
        
	return $cells;
}
?>

<table class="table table-condensed table-bordered" style="width: auto !important; padding: 0px; font-size: 10px;">
<thead>
	<th>Software Components (<?php echo count($registry); ?>)</th> <?php echo renderTableHeader($wizardRegistries); ?>
</thead>
<?php
foreach($registry as $software => $data)
{
	echo '<tr><td>' . $software . '</td>' . renderTableCells($wizardRegistries, $software) . '</tr>';
}
?>
</table>