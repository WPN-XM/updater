<!-- Table -->
<div class="row justify-content-md-center">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <h5 class="card-header">List of all Version Crawlers</h5>
            <div class="card-body">
              <table class="table table-sm table-light table-hover table-striped table-bordered">
              <thead><tr><th>Software Component</th><th>Version</th><th colspan="2">Action</th></tr></thead>
              <tbody>
                <?php 
                    // show the new crawlers, which do not have a registry entry, yet.
                    if($newCrawlers) {
                      foreach ($newCrawlers as $name) {
                        echo '<tr class="table-danger">';
                        echo '<td><span class="badge badge-success">New Crawler</span> ' . $name . '</td>';   
                        echo '<td><span class="badge badge-warning">Not in registry, yet.</span></td>';
                        echo '<td><a class="badge badge-secondary" href="index.php?action=scan-component&amp;component=' . $name . '">Scan</a></td>';
                        echo '<td><a class="badge badge-secondary" href="index.php?action=add-component&amp;shorthand=' . $name . '">Add Registry Entry</a></td>';
                        echo '</tr>';                          
                      }
                    }
                                       
                    foreach ($registry as $item => $component) {
                      $isBitsizeX86 = (substr($item, -3) == 'x86') ? true : false;
                      echo '<tr>';
                      if(array_key_exists('name', $component)) {
                        echo '<td>' . $component['name'] . '</td>';
                      } else {
                        echo '<td><span class="badge badge-danger">';
                        echo 'Name entry missing for item (key):</span>'.$item.'</td>';
                      }
                      if(array_key_exists('alias', $component)) {
                        echo '<td><span class="badge badge-danger">Alias </span> ' . $component['alias'] . '</td>'; 
                      } else {
                        echo '<td>' . $component['latest']['version'] . '</td>';
                        // render version "scan" buttons for all components, except x86
                        if($isBitsizeX86) {
                            echo '<td>&nbsp;</td>';
                        } else {
                            echo '<td><a class="badge badge-secondary"';
                            echo ' href="index.php?action=scan-component&amp;component=' . $item . '">Scan</a>';
                            echo '</td>';
                        }
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

