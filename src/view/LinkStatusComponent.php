<h5>WPN-XM Software Registry - Status <span class="pull-right"><?=date(DATE_RFC822)?></span></h5>
<h5>For Component "<?=$component?>"</h5>
<table class="table table-sm table-hover" style="font-size: 12px;">
<tr><th>Download URLs</th></tr>
<?php
foreach($urlsHttpStatus as $url => $status) {
	$color = ($status === true) ? 'green' : 'red';
	echo '<tr><td><a style="color:' . $color . ';" href="' . $url . '">' . $url . '</a></td></tr>';
}
?>
</table>
<br>
Used a total of <?=$crawlingTime?> seconds for crawling <?=$numberOfUrls?> URLs. Total Page Build Time <?=round((microtime(true) - TIME_STARTED), 2)?> secs.