<table class="table table-sm table-hover table-striped table-bordered">
<thead>
    <tr>
        <th>Software Components (<?=$numberOfComponents?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th><th>Action</th>
    </tr>
</thead>
<?php echo $tableHtml; ?>
</table>
Used a total of <?=round((microtime(true) - TIME_STARTED), 2)?> seconds for crawling <?=$numberOfComponents?> URLs.
