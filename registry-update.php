<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */

$start = microtime(true);
set_time_limit(180); // 60*3
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (!extension_loaded('curl')) {
    exit('Error: PHP Extension cURL required.');
}

require __DIR__ . '/tools.php';

$registry  = Registry::load();

// handle $_GET['action'], e.g. registry-update.php?action=scan
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// insert version scans into main software registry
if (isset($action) && $action === 'update') {
    $registry = Registry::addLatestVersionScansIntoRegistry($registry);
    if(is_array($registry) === true) {
        Registry::writeRegistry($registry);
        echo 'The registry was updated.';
    } else {
        echo 'The registry is up to date.';
    }
} // end action "update"

// inserts a single component version scan into the main registry
// - automatically git commit's with a standardized commit message
// - shows a git push reminder
if (isset($action) && $action === 'update-component') {
    $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);

    if(false !== strpos($component, 'php-x86')) {
        $component = 'php';
    }

    $registry = Registry::addLatestVersionScansIntoRegistry($registry, $component);
    if(is_array($registry) === true) {
        Registry::writeRegistry($registry);
        echo 'The registry was updated. Component "' . $component .'" inserted.';

        $name = isset($registry[$component]['name']) ?  $registry[$component]['name'] : $component;

        $commitMessage = 'updated software registry - ' . $name . ' v' . $registry[$component]['latest']['version'];
        Registry::gitCommitAndPush($commitMessage);
    } else {
        echo 'No version scans found: The registry is up to date.';
    }
} // end action "update-component"

// scan for new versions
if (isset($action) && $action === 'scan') {
    Registry::clearOldScans();
    $updater = new RegistryUpdater($registry);
    $updater->setupCrawler();
    // handle $_GET['component'], for single component scans, e.g. registry-update.php?action=scan&component=openssl

    $component = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);
    $numberOfComponents = (isset($component) === true) ? $updater->getUrlsToCrawl($component) : $updater->getUrlsToCrawl() + 1;

    $updater->crawl();
    $tableHtml = $updater->evaluateResponses();

    /******************************************************************************/
    ?>
    <table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th>Software Components (<?=$numberOfComponents?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th><th>Action</th>
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
                          <label class="col-md-4 control-label" for="shorthand">Registry Shorthand</label>
                          <div class="col-md-5">
                          <input id="shorthand" name="shorthand" placeholder="Shorthand (phpext_xdebug)" class="form-control input-md" type="text">
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
$(document).ready(function () {
   // bind submit action
   $('#myModal button[type="submit"]').bind('click', function (event) {
       var form = $("#myModal .modal-body form");

       $.ajax({
         type: form.attr('method'),
         url: form.attr('action'),
         data: form.serializeArray(),

         cache: false,
         success: function (response, status) {
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

    $component  = filter_input(INPUT_POST, 'software', FILTER_SANITIZE_STRING);
    $shorthand  = filter_input(INPUT_POST, 'shorthand', FILTER_SANITIZE_STRING);
    $url        = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
    $version    = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
    $website    = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);
    $phpversion = ($phpversion = filter_input(INPUT_POST, 'phpversion', FILTER_SANITIZE_STRING)) ? $phpversion : '';

    // compose new array, write a new registry scan, insert scan into registry
    $array = Registry::getArrayForNewComponent($component, $url, $version, $website, $phpversion);
    Registry::writeRegistrySubset($shorthand, $array);
    $newRegistry = Registry::addLatestVersionScansIntoRegistry($registry, $component);
    if($newRegistry !== false) {
        $result = Registry::writeRegistry($newRegistry);
    }

    // check result and send response
    $js = '<script type="text/javascript" charset="utf-8">
            $(document).ready(function () {
                $(\'#myModal button[type="submit"]\').hide();
            });
           </script>';

    $response_ok = '<div class="alert alert-success">Successfully added to registry.</div>';
    $response_fail = '<div class="alert alert-danger">Component was not added to registry.</div>';
    $response = (isset($newRegistry[$component]) === true) ? $response_ok : $response_fail;

    echo $response . $js;

} // end action "insert"

if (isset($action) && $action === 'show') {
    include 'version-matrix.php';
} // end action "show"

if (isset($action) && $action === 'update-installer-registry') {

  $installer = filter_input(INPUT_POST, 'installer', FILTER_SANITIZE_STRING);
  $registryJson = filter_input(INPUT_POST, 'registry-json', FILTER_SANITIZE_STRING);

  $registryJson = html_entity_decode($registryJson, ENT_COMPAT, 'UTF-8'); // fix the JSON.stringify quotes &#34;
  $installerRegistry = json_decode($registryJson, true);

  $file = __DIR__ . '\registry\\' . $installer . '.json';

  $downloadFilenames = include __DIR__ . '\downloadFilenames.php';

  $data = array();

  foreach($installerRegistry as $component => $version) {
    $url = 'http://wpn-xm.org/get.php?s=' . $component . '&v=' . $version;

    // special handling for PHP - 'php', 'php-x64', 'php-qa-x64', 'php-qa'
    if (false !== strpos($component, 'php') && false === strpos($component, 'phpext_')) {
        $php_version = substr($installerRegistry[$component], 0, 3); // get only major.minor, e.g. "5.4", not "5.4.2"

        $bitsize = (false !== strpos($component, 'x64')) ? 'x64' : ''; // empty bitsize defaults to x86, see website "get.php"
    }

    // special handling for PHP Extensions (which depend on a specific PHP version and bitsize)
    if (false !== strpos($component, 'phpext_')) {
        $url .= '&p=' . $php_version;
        $url .= ($bitsize !== '') ? '&bitsize=' . $bitsize : '';
    }

    $downloadFilename = $downloadFilenames[$component];

    $data[] = array($component, $url, $downloadFilename, $version);
  }

  #var_dump($installer, $registryJson, $installerRegistry, $file, $data);

  $ir = new InstallerRegistry;
  $ir->writeRegistryFileJson($file, $data);

} // end action "update-installer-registry"
