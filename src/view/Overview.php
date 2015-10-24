<!-- Table -->
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
              <span class="glyphicon glyphicon-list"></span>&nbsp; Version Crawler
              <span class="pull-right">
                <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal" href="index.php?action=add">
                  <span class="glyphicon glyphicon-plus"></span>
                  Add Component
                </button>
                <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" href="index.php?action=update">
                  <span class="glyphicon glyphicon-search"></span>
                  Merge Scans into Registry
                </button>
                <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" href="index.php?action=scanComponent">
                  <span class="glyphicon glyphicon-search"></span>
                  Scan All
                </button>
              </span>
            </div>
            <div class="panel-body">
              <table class="table table-sm table-hover table-striped table-bordered" style="font-size: 12px;">
              <thead><tr><th style="width: 220px">Software Component</th><th>Version</th><th>Action</th></tr></thead>
              <tbody>
                <?php foreach ($registry as $item => $component) {
    echo '<tr>';
    echo '<td>' . $component['name'] . '</td>';
    echo '<td>' . $component['latest']['version'] . '</td>';
    echo '<td><a class="btn btn-info btn-xs" href="index.php?action=scanComponent&amp;component=' . $item . '">Scan</a></td>';
    echo '</tr>';
}
    ?>
              </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

