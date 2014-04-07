<?php
   /**
    * WPИ-XM Server Stack
    * Jens-André Koch © 2010 - onwards
    * http://wpn-xm.org/
    *
    *        _\|/_
    *        (o o)
    +-----oOO-{_}-OOo------------------------------------------------------------------+
    |                                                                                  |
    |    LICENSE                                                                       |
    |                                                                                  |
    |    WPИ-XM Serverstack is free software; you can redistribute it and/or modify    |
    |    it under the terms of the GNU General Public License as published by          |
    |    the Free Software Foundation; either version 2 of the License, or             |
    |    (at your option) any later version.                                           |
    |                                                                                  |
    |    WPИ-XM Serverstack is distributed in the hope that it will be useful,         |
    |    but WITHOUT ANY WARRANTY; without even the implied warranty of                |
    |    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                 |
    |    GNU General Public License for more details.                                  |
    |                                                                                  |
    |    You should have received a copy of the GNU General Public License             |
    |    along with this program; if not, write to the Free Software                   |
    |    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA    |
    |                                                                                  |
    +----------------------------------------------------------------------------------+
    */

$start = microtime(true);
set_time_limit(180); // 60*3
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', true);

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

require __DIR__ . '/tools.php';

$registry  = Registry::load();

// handle $_GET['action'], e.g. registry-update.php?action=write-file
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// insert version scans into registry
if (isset($action) && $action === 'update') {
    $registry = Registry::addLatestVersionScansIntoRegistry($registry);
    if(is_array($registry) === true) {
        Registry::writeRegistry($registry);
        echo 'The registry was updated.';
    } else {
        echo 'The registry is up to date.';
    }
} // end action "update"

// scan for new versions
if (isset($action) && $action === 'scan') {
    Registry::clearOldScans();
    $updater = new RegistryUpdater($registry);
    $updater->setupCrawler();
    $numberOfComponents = $updater->getUrlsToCrawl();
    $updater->crawl();
    $tableHtml = $updater->evaluateResponses();

    /******************************************************************************/
    ?>
    <table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th>Software Components (<?=$numberOfComponents?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th>
        </tr>
    </thead>
    <?php echo $tableHtml; ?>
    </table>
    Used a total of <?=round((microtime(true) - $start), 2)?> seconds for crawling <?=$numberOfComponents?> URLs.
<?php
} // end action "write-file"

// add a new software into the registry
if (isset($action) && $action === 'add') {
    ?>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Add Software To Registry</h4>

            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="registry-update.php?action=insert" method="post">
                    <fieldset>
                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="software">Name of the Software</label>
                          <div class="col-md-5">
                          <input id="software" name="software" placeholder="Application" class="form-control input-md" type="text">
                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="website">Website URL</label>
                          <div class="col-md-5">
                          <input id="website" name="website" placeholder="http://company.com/" class="form-control input-md" type="text">
                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="url">Download URL</label>
                          <div class="col-md-8">
                          <input id="url" name="url" placeholder="http://downloads.company.com/app-x86-1.2.3.zip" class="form-control input-md" type="text">
                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="version">Latest Version</label>
                          <div class="col-md-2">
                          <input id="version" name="version" placeholder="1.2.3" class="form-control input-md" type="text">
                          </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Add</button>
            </div>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
   // bind submit action
   $('#myModal button[type="submit"]').bind('click', function(event) {
       var form = $("#myModal .modal-body form");

       $.ajax({
         type: form.attr('method'),
         url: form.attr('action'),
         data: form.serializeArray(),

         cache: false,
         success: function(response, status) {
           $('#myModal .modal-body').html(response);
         }
       });

       event.preventDefault();
  });
});
</script>

    <?php
} // end action "add"

if (isset($action) && $action === 'insert') {

    $component = filter_input(INPUT_POST, 'software', FILTER_SANITIZE_STRING);
    $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
    $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);

    // compose new array, write a new registry scan, insert scan into registry
    $array = Registry::getArrayForNewComponent($component, $url, $version, $website);
    Registry::writeRegistrySubset($component, $array);
    $newRegistry = Registry::addLatestVersionScansIntoRegistry($registry, $component);
    $result = Registry::writeRegistry($newRegistry);

    // check result and send response
    $js = '<script type="text/javascript" charset="utf-8">
            $(document).ready(function() {
                $(\'#myModal button[type="submit"]\').hide();
            });
           </script>';

    $response_ok = '<div class="alert alert-success">Successfully added to registry.</div>' . $js;
    $response_fail = '<div class="alert alert-danger">Component was not added to registry.</div>' . $js;
    $response = (isset($newRegistry[$component]) === true) ? $response_ok : $response_fail;
    echo $response;

} // end action "insert"


