<h4>Registry Health Check</h3>

<?php
if(count($health) === 0) {
	echo '<span class="label label-success">OK</span>';
} else {
	echo '<span class="label label-danger">ERROR</span>';
}
?>

<?php echo implode($health, '<br/>'); ?>