<h4>Registry Health Check</h3>

<?php
if(count($health) === 0) {
	echo '<button class="btn btn-success">OK</button>';
}
?>

<?php echo implode($health, '<br/>'); ?>