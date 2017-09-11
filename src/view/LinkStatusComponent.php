<h5>WPN-XM Software Registry - Status <span class="pull-right"><?=date(DATE_RFC822)?></span></h5>
<h5>For Component "<?=$software?>"</h5>
<table class="table table-sm table-hover" style="font-size: 12px;">
<tr><th>Download URLs</th></tr>
<?php
foreach($urlsHttpStatus as $url => $status) {
    $color = ($status === true) ? 'green' : 'red';
    echo '<tr>';
    echo '  <td><a style="color:' . $color . ';" href="' . $url . '">' . $url . '</a></td>';
    if($status === false) {
        echo '  <td>';
        echo '    <form action="index.php?action=link-remove-component&amp;software=' . $software . '" method="POST" name="removeLink">';
        echo '      <input type="hidden" name="software" value="' . $software . '">';
        echo '      <input type="hidden" name="url" value="' . urlencode($url) . '">';
        echo '      <a class="btn btn-alert btn-xs" href="#" onclick="document.removeLink.submit();">Remove Link</a>';
        echo '    </form>';
        echo '  </td>';
    }
    echo '</tr>';
}
?>
</table>
<br>
Used a total of <?=$crawlingTime?> seconds for crawling <?=$numberOfUrls?> URLs. Total Page Build Time <?=round((microtime(true) - TIME_STARTED), 2)?> secs.