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
                    // show the new crawlers, which do not have a registry entry, yet.
                    if($crawlers) {
                      foreach ($crawlers as $name) {
                        echo '<tr>';
                        echo '<td><span class="badge badge-danger">New Crawler</span> ' . $name . '</td>';   
                        echo '<td>Not in registry, yet.</td>';
                        echo '<td><a class="badge badge-danger" href="index.php?action=scan-component&amp;component=' . $name . '">Scan</a></td>';
                        echo '<td><a class="badge badge-danger" href="index.php?action=add-component&amp;shorthand=' . $name . '">Add Registry Entry</a></td>';
                        echo '</tr>';                          
                      }
                    }
                                       
                    foreach ($registry as $item => $component) {
                      echo '<tr>';
                      echo '<td>' . $component['name'] . '</td>';
                      if(array_key_exists('alias', $component)) {
                        echo '<td><span class="badge badge-danger">Alias </span> ' . $component['alias'] . '</td>'; 
                      } else {

                        echo '<td>' . $component['latest']['version'] . '</td>';
                        echo '<td><a class="badge badge-secondary" href="index.php?action=scan-component&amp;component=' . $item . '">Scan</a></td>';
                        echo '<td><a class="badge badge-secondary" href="index.php?action=link-status-component&amp;software=' . $item . '">Check Link Health</a></td>';
                      }
                      echo '</tr>';
                    }                
                ?>
              </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

