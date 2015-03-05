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

Registry::healthCheck($registry);

// handle $_GET['action'], e.g. registry-update.php?action=scan
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

// insert version scans into main software registry
if (isset($action) && $action === 'update') {
    $registry = Registry::addLatestVersionScansIntoRegistry($registry);
    if (is_array($registry) === true) {
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

    if (false !== strpos($component, 'php-x86')) {
        $component = 'php';
    }

    $registry = Registry::addLatestVersionScansIntoRegistry($registry, $component);
    if (is_array($registry) === true) {
        Registry::writeRegistry($registry);
        echo 'The registry was updated. Component "' . $component . '" inserted.';

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

    $component          = filter_input(INPUT_GET, 'component', FILTER_SANITIZE_STRING);
    $numberOfComponents = (isset($component) === true) ? $updater->getUrlsToCrawl($component) : $updater->getUrlsToCrawl() + 1;

    $updater->crawl();
    $tableHtml = $updater->evaluateResponses();

    /******************************************************************************/
    ?>
    <table class="table table-condensed table-hover table-striped table-bordered">
    <thead>
        <tr>
            <th>Software Components (<?=$numberOfComponents?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th><th>Action</th>
        </tr>
    </thead>
    <?php echo $tableHtml;
    ?>
    </table>
    Used a total of <?=round((microtime(true) - $start), 2)?> seconds for crawling <?=$numberOfComponents?> URLs.
<?php

} // end action "write-file"

// "single-component-scan" = main page
if (isset($action) && $action === 'single-component-scan') {
    ?>

<!-- Table -->
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
              <span class="glyphicon glyphicon-list"></span>&nbsp; Version Crawler
              <span class="pull-right">
                <button class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal" href="registry-update.php?action=add">
                  <span class="glyphicon glyphicon-plus"></span>
                  Add Component
                </button>
                <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" href="registry-update.php?action=update">
                  <span class="glyphicon glyphicon-search"></span>
                  Merge Scans into Registry
                </button>
                <button class="btn btn-info btn-xs" data-toggle="modal" data-target="#myModal" href="registry-update.php?action=scan">
                  <span class="glyphicon glyphicon-search"></span>
                  Scan All
                </button>
              </span>
            </div>
            <div class="panel-body">
              <table class="table table-condensed table-hover table-striped table-bordered" style="font-size: 12px;">
              <thead><tr><th style="width: 220px">Software Component</th><th>Version</th><th>Action</th></tr></thead>
              <tbody>
                <?php foreach ($registry as $item => $component) {
    echo '<tr>';
    echo '<td>' . $component['name'] . '</td>';
    echo '<td>' . $component['latest']['version'] . '</td>';
    echo '<td><a class="btn btn-info btn-xs" href="registry-update.php?action=scan&amp;component=' . $item . '">Scan</a></td>';
    echo '</tr>';
}
    ?>
              </tbody>
              </table>
            </div>
        </div>
    </div>
</div>

    <?php

} // end action "single-component-scan"

// add a new software into the registry
if (isset($action) && $action === 'add') {
    ?>

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title">Add Component To Software Registry</h4>
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
    if ($newRegistry !== false) {
        $result = Registry::writeRegistry($newRegistry);
    }

    // check result and send response
    $js = '<script type="text/javascript">
            $(document).ready(function () {
                $(\'#myModal button[type="submit"]\').hide();
            });
           </script>';

    $response_ok   = '<div class="alert alert-success">Successfully added to registry.</div>';
    $response_fail = '<div class="alert alert-danger">Component was not added to registry.</div>';
    $response      = (isset($newRegistry[$component]) === true) ? $response_ok : $response_fail;

    echo $response . $js;
} // end action "insert"

if (isset($action) && $action === 'show-version-matrix') {
    include 'version-matrix.php';
} // end action "show"

if (isset($action) && $action === 'update-installer-registry') {
    $installer    = filter_input(INPUT_POST, 'installer', FILTER_SANITIZE_STRING);
    $registryJson = filter_input(INPUT_POST, 'registry-json', FILTER_SANITIZE_STRING);

    $registryJson    = html_entity_decode($registryJson, ENT_COMPAT, 'UTF-8'); // fix the JSON.stringify quotes &#34;
  $installerRegistry = json_decode($registryJson, true);

    $file = __DIR__ . '\registry\\' . $installer . '.json';

    $downloadFilenames = include __DIR__ . '\downloadFilenames.php';

    $data = array();

    foreach ($installerRegistry as $component => $version) {
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

  InstallerRegistry::write($file, $data);
} // end action "update-installer-registry"

/*
 * This updates all components of all installation registry to their latest version.
 */
if (isset($action) && $action === 'update-components') {
    $nextRegistries = glob(__DIR__ . '\registry\*-next-*.json');

    if (empty($nextRegistries) === true) {
        exit('No "next" JSON registries found. Create installers for the next version.');
    }

    echo 'Update all components to their latest version.<br>';

    $downloadFilenames = include __DIR__ . '\downloadFilenames.php';

    foreach ($nextRegistries as $file) {
        $filename = basename($file);
        echo '<br>Processing Installer: "' . $filename . '":<br>';
        $components      = json_decode(file_get_contents($file), true);
        $version_updated = false;
        for ($i = 0; $i < count($components); ++$i) {
            $componentName = $components[$i][0];
            $url           = $components[$i][1];
            $version       = $components[$i][3];

            if(!isset($downloadFilenames[$componentName])) {
                 echo 'The download description file has no value for the Component "' . $componentName . '"<br>';
            } else {
              // update the download filename with the value of the download description file
              // in case the registry contains a different (old) value
              $downloadFilename = $downloadFilenames[$componentName];

              if($components[$i][2] !== $downloadFilename) {
                   $components[$i][2] = $downloadFilename;
              }
            }

            $latestVersion = getLatestVersionForComponent($componentName, $filename);

            if (version_compare($version, $latestVersion, '<') === true) {
                $components[$i][3] = $latestVersion;
                if (false !== strpos($url, $version)) { // if the url has a version appended, update it too
                    $components[$i][1] = str_replace($version, $latestVersion, $url);
                }
                echo 'Updated "' . $componentName . '" from v' . $version . ' to v' . $latestVersion . '.<br>';
                $version_updated = true;
            }
        }
        if ($version_updated === true) {
            InstallerRegistry::write($file, $components);
        } else {
            echo 'The installer registry is up-to-date.<br>';
        }
    }
} // end action "update-all-next-registries"

/**
 * Return the PHP version of a registry file.
 *
 * @param string  A filename, e.g. registry filename, like "full-next-php5.6-w64.json".
 * @return string PHP Version.
 */
function getPHPVersionFromFilename($file)
{
    preg_match("/-php(.*)-/", $file, $matches);

    return $matches[1];
}

/**
 * Return the latest version for a component.
 * Takes the PHP major.minor.latest version constraint into account.
 *
 * @param string $component
 * @param string $filename
 * @return string version
 */
function getLatestVersionForComponent($component, $filename)
{
    // latest version of PHP means "latest version for PHP5.4, PHP5.5, PHP5.6"
    // we have to raise the PHP version, respecting the major.minor version constraint
    if ($component === 'php' || $component === 'php-x64' || $component === "php-qa" || $component === "php-qa-x64") {
        $minVersionConstraint = getPHPVersionFromFilename($filename); // 5.4, 5.5
        $maxVersionConstraint = $minVersionConstraint . '.99'; // 5.4.99, 5.5.99
        return getLatestVersion($component, $minVersionConstraint, $maxVersionConstraint);
    }

    return getLatestVersion($component);
}

function getLatestVersion($component, $minConstraint = null, $maxConstraint = null)
{
    global $registry;

    if (isset($component) === false) {
        throw new RuntimeException('No component provided.');
    }

    if (isset($registry[$component]) === false) {
        throw new RuntimeException('The component "' . $component . '" was not found in the registry.');
    }

    if ($minConstraint === null && $maxConstraint === null) {
        return $registry[$component]['latest']['version'];
    }

  // determine latest version for a component given a min/max constraint

  $software = $registry[$component];

  // remove all non-version stuff
  unset($software['name'], $software['latest'], $software['website']);
  // the array is already sorted.
  // get rid of (version => url) and use (idx => version)
  $software = array_keys($software);
  // reverse array, in order to have the highest version number on top.
  $software = array_reverse($software);
  // reduce array to values in constraint range
  foreach ($software as $url => $version) {
      if (version_compare($version, $minConstraint, '>=') === true && version_compare($version, $maxConstraint, '<') === true) {
          #echo 'Version v' . $version . ' is greater v' . $minConstraint . '(MinConstraint) and smaller v' . $maxConstraint . '(MaxConstraint).<br>';
      } else {
          unset($software[$url]);
      }
  }
  // pop off the first element
  $latestVersion = array_shift($software);

    return $latestVersion;
}
