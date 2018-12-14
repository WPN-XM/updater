<h3>Scan Component(s)</h3>
<table class="table table-sm table-hover table-striped table-bordered">
<thead class="thead-light">
    <tr>
        <th scope="col">Software Components (<?=$numberOfComponents?>)</th>
        <th scope="col">(Old) Latest Version</th>
        <th scope="col">(New) Latest Version</th>
        <th scope="col">Action</th>
    </tr>
</thead>
<?php echo $tableHtml; ?>
</table>
Used a total of <?=round((microtime(true) - TIME_STARTED), 2)?> seconds for crawling <?=$numberOfComponents?> URLs.
