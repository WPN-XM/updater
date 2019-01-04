<h4>Registry Health Check</h3>

<?php
if(count($health) === 0) {
	echo '<span class="badge badge-success">OK</span>';
} else {
	$errorMsg = '<span class="badge badge-danger">ERROR</span> ';
    echo $errorMsg;
    echo implode($health, '<br/>' . $errorMsg);
}
?>