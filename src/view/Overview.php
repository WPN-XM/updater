<!-- Table -->
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
              <span class="glyphicon glyphicon-list"></span>&nbsp; Version Crawlers              
            </div>
            <div class="panel-body">
              <table class="table table-sm table-hover table-striped table-bordered" style="font-size: 12px;">
              <thead><tr><th style="width: 220px">Software Component</th><th>Version</th><th>Action</th></tr></thead>
              <tbody>
                <?php 
                    if($crawlers) { 
                      foreach ($crawlers as $name) {
                          echo '<tr>';
                          echo '<td><span class="badge badge-danger">New Crawler</span> ' . $name . '</td>';   
                          echo '<td>Not in registry, yet.</td>';
                          echo '<td><a class="btn btn-info btn-xs" href="index.php?action=scan-component&amp;component=' . $name . '">Scan</a></td>';
                          echo '<td><a class="btn btn-info btn-xs" href="index.php?action=add-component&amp;component=' . $name . '">Add Registry Entry</a></td>';
                          echo '</tr>';
                      }
                    }
                ?>
                <?php 
                    foreach ($registry as $item => $component) {
                        echo '<tr>';
                        echo '<td>' . $component['name'] . '</td>';   
                        echo '<td>' . $component['latest']['version'] . '</td>';
                        echo '<td><a class="btn btn-info btn-xs" href="index.php?action=scan-component&amp;component=' . $item . '">Scan</a></td>';
                        echo '</tr>';
                    }                
                ?>
              </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

