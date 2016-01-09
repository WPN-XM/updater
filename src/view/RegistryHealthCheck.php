<h4>Registry Health Check</h3>

<?php
if(count($health) === 0) {
	echo '<span class="label label-success">OK</span>';
} else {
	$error = '<span class="label label-danger">ERROR</span> ';
    echo $error;
}
?>

<?php echo implode($health, '<br/>' . $error); ?>