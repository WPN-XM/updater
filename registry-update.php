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

if (isset($action) && $action === 'write-file') {
    $registry = Registry::addLatestVersionScansIntoRegistry($registry);
    if(is_array($registry) === true) {
        Registry::writeRegistry($registry);
        echo 'The registry was updated.';
    } else {
        echo 'The registry is up to date.';
    }
} else {
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
        <th>Software Components (<?php echo $numberOfComponents ?>)</th><th>(Old) Latest Version</th><th>(New) Latest Version</th>
    </tr>
</thead>
<?php echo $tableHtml; ?>
</table>
Used a total of <?php echo round((microtime(true) - $start), 2); ?> seconds
<?php } ?>