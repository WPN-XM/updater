<?php

/**
 * WPИ-XM Server Stack - Updater
 * Copyright (c) Jens A. Koch <jakoch@web.de>
 * https://wpn-xm.org/
 *
 * Licensed under the MIT License.
 * See the bundled LICENSE file for copyright and license information.
 */

namespace WPNXM\Updater\Action;

use WPNXM\Updater\ActionBase;
use WPNXM\Updater\View;
use WPNXM\Updater\Registry;

class SoftwareRepositoryReport extends ActionBase
{
    public function __invoke()
    {       
        $data = new SoftwareRepositoryReportData();
        $renderer = new SoftwareRepositoryReportRenderer($data);

        $view = new View();
        $view->data['date'] = $renderer->getDate();
        $view->data['summaryTable'] = $renderer->getSummaryTable();

        $view->data['progressBar_tasksImplementedPHPRegistryPercentage'] = 
        $renderer->renderProgressBar($data->tasksImplementedPHPRegistryPercentage);
        $view->data['progressBar_tasksImplementedStackRegistryPercentage'] = 
        $renderer->renderProgressBar($data->tasksImplementedStackRegistryPercentage);
        $view->data['progressBar_tasksImplementedTotalPercentage'] = 
        $renderer->renderProgressBar($data->tasksImplementedTotalPercentage);

        // files and folders
        $view->data['existingFoldersStackRegistry'] = $data->existingFoldersStackRegistry;
        $view->data['missingFolders'] = $data->missingFolders;
        $view->data['missingManifestJsonFiles'] = $data->missingManifestJsonFiles;
        $view->data['existingComposerJsonFiles'] = $data->existingComposerJsonFiles;
        $view->data['missingComposerJsonFiles'] = $data->missingComposerJsonFiles;
        $view->data['existingChangelogFiles'] = $data->existingChangelogFiles;
        $view->data['missingChangelogFiles'] = $data->missingChangelogFiles;

        // tasks
        $view->data['tasksImplementedStackRegistry'] = $data->tasksImplementedStackRegistry;
        $view->data['tasksToImplementForStackRegistry'] = $data->tasksToImplementForStackRegistry;
        $view->data['tasksImplementedTotal'] = $data->tasksImplementedTotal;
        $view->data['tasksToImplementTotal'] = $data->tasksToImplementTotal;
        $view->data['tasksToImplementForPHPRegistry'] = $data->tasksToImplementForPHPRegistry;

        // stats
        $view->data['stackRegistryItemsWithoutExtensionsTotal'] = $data->stackRegistryItemsWithoutExtensionsTotal;
        $view->data['phpExtensionsInSoftwareRegistry'] = $data->phpExtensionsInSoftwareRegistry;
        $view->data['stackRegistryItemsTotal'] = $data->stackRegistryItemsTotal;
        $view->data['softwareItemsTotal'] = $data->softwareItemsTotal;
        $view->data['phpRegistryItemsTotal'] = $data->phpRegistryItemsTotal;
        $view->data['tasksImplementedPHPRegistry'] = $data->tasksImplementedPHPRegistry;


        $view->render();
    }
}

class SoftwareRepositoryReportRenderer
{
    // list of tasks
    private $tasks = ['install', 'uninstall', 'update', 'backup', 'restore', 'version'];

    private $reportDataObj;
    private $reportData;

    public function __construct($data)
    {
        $this->reportDataObj = $data;
        $this->reportData = $this->reportDataObj->buildReportData();
    }

    function getSummaryTable()
    {
        // default task status
        $task_status_install   = 'entry missing';
        $task_status_uninstall = 'entry missing';
        $task_status_update    = 'entry missing';
        $task_status_backup    = 'entry missing';
        $task_status_restore   = 'entry missing';
        $task_status_version   = 'entry missing';

        // build table rows
        $reportHtmlBody = '';
        foreach($this->reportData as $software => $data)
        {
            $metadata_key_exists = ($data['metadata_key_exists']) ? 
                $this->existsHtml() : 
                $this->missingHtml('registry-metadata entry missing <br>'. $software);
            
            $folderExists = ($data['folder_exists'])   ? 
                $this->existsHtml() : 
                $this->missingHtml('Folder missing <br> for <br>' . $software);    
            
            $manifestExists = ($data['manifest_exists']) ? 
                $this->existsHtml() : 
                $this->missingHtml('Manifest missing <br> for <br>' . $software);
            
            $composerJsonExists = ($data['composerjson_exists']) ? 
                $this->existsHtml() : 
                $this->missingHtml('composer.json missing <br> for <br>' . $software); 
            
            $changelogExists = ($data['changelog_exists']) ? 
                $this->existsHtml() : 
                $this->missingHtml('CHANGELOG.md missing <br> for <br>' . $software);     

            $softwareName   = ($data['registry_name_exists']) ? 
                $this->softwareNamePretty($data['registry_name']) : 
                $this->softwareNameRegKey($software);

            $softwareName  .= '&nbsp;';
            $softwareName  .= (strpos($software, 'x86') !== false) ? $this->bitsize86() : '';
            $softwareName  .= (strpos($software, 'x64') !== false) ? $this->bitsize64() : '';

            if($data['manifest_exists'] && !array_key_exists('logo', $data['manifest'])) {
                var_dump('missing logo in manifest', $software);
            }

            $logoExists = ($data['manifest_exists'] && $data['manifest']['logo'] !== '') 
                ? $this->logoExistsHtml($data['manifest']['logo'], $data['manifest']['name']) 
                : $this->missingHtml('Logo missing <br> for <br>' . $software); 

            if($data['manifest_exists'] == true) {
                foreach($this->tasks as $task) {
                    if($data['manifest']['tasks'][$task] == 'done') {
                        ${'task_status_'.$task} = $this->doneHtml();
                    } else {
                        ${'task_status_'.$task} = $this->todoHtml('todo: <br> write '. $task . ' script <br> for <br>' . $software);
                    }
                }
            }

            $reportHtmlBody .= sprintf(
                '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', 
                $softwareName,       
                $folderExists, 
                $logoExists,
                $manifestExists,
                $composerJsonExists,
                $changelogExists,
                $metadata_key_exists,
                $task_status_install,
                $task_status_uninstall,
                $task_status_update,
                $task_status_backup,
                $task_status_restore,
                $task_status_version
            );
        }
        return $reportHtmlBody;
    }

    /**
     * html fragments
     */
    function existsHtml() {
        return '<span class="badge badge-light">✔</span>';
    }
    function missingHtml($tooltipMessage) {
        return '<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$tooltipMessage.'">✘</span>';
    }
    function doneHtml() {
        return '<span class="badge badge-light">✔</span>';
    }
    function todoHtml($tooltipMessage) {
        return '<span class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$tooltipMessage.'">✘</span>';
    }
    function bitsize86() {
        return '<span class="badge badge-light">x86</span>';
    }
    function bitsize64() {
        return '<span class="badge badge-light">x64</span>';
    }
    function softwareNamePretty($name) { 
        return '<strong>'.$name.'</strong>&nbsp;'; 
    }
    function softwareNameRegKey($name) { 
        return '<span class="badge badge-danger">Registry key "name" missing:</span>&nbsp;' . $name; 
    }
    function logoExistsHtml($logo, $name) { 
        return '<img src="'.$logo.'" alt="Logo of '.$name.'" />'; 
    }
    function renderProgressBar($percentage) {
        return '<div class="text-xs-center" id="example-caption-1">'.$percentage.'</div>
    <progress class="progress" value="'.$percentage.'" max="100" aria-describedby="example-caption-1"></progress>';
    }
    function getDate()
    {
        $DateTime = new \DateTime();
        $DateTime->setTimezone(new \DateTimeZone('UTC'));
        return $DateTime->format('Y-m-d H:m:s');
    }
}

class SoftwareRepositoryReportData
{
    private $STACK_SOFTWARE_FOLDER;
    private $PHP_SOFTWARE_FOLDER;

    private $stack;
    private $php;
    private $metadata;

    public $missingFolders = 0;
    public $missingManifestJsonFiles = 0;
    public $missingComposerJsonFiles = 0;
    public $missingChangelogFiles = 0;

    public $existingFoldersStackRegistry = 0;
    public $existingComposerJsonFiles = 0;
    public $existingChangelogFiles = 0;
    public $tasksImplementedStackRegistry= 0;
    public $tasksToImplementForStackRegistry= 0;
    public $tasksImplementedTotal= 0;
    public $tasksToImplementTotal= 0;
    public $tasksToImplementForPHPRegistry= 0;
    public $stackRegistryItemsWithoutExtensionsTotal = 0;
    public $phpExtensionsInSoftwareRegistry = 0;
    public $stackRegistryItemsTotal= 0;
    public $softwareItemsTotal= 0;
    public $phpRegistryItemsTotal= 0;
    public $tasksImplementedPHPRegistry= 0;

    // list of tasks
    private $tasks = ['install', 'uninstall', 'update', 'backup', 'restore', 'version'];

    function __construct()
    {
        $this->stack = include REGISTRY_DIR . '/wpnxm-software-registry.php';
        $this->php = include REGISTRY_DIR . '/wpnxm-php-software-registry.php';
        $this->metadata = include REGISTRY_DIR . '/wpnxm-registry-metadata.php';

        $this->STACK_SOFTWARE_FOLDER = DATA_DIR . 'stack-software';
        $this->PHP_SOFTWARE_FOLDER = DATA_DIR . '/php-software';

        $this->calculateStats();        
    }

    function getComposerJsonFile($software)
    {
        return $this->STACK_SOFTWARE_FOLDER . '/'.$software.'/composer.json';
    }

    function getChangelogFile($software)
    {
        return $this->STACK_SOFTWARE_FOLDER . '/'.$software.'/CHANGELOG.md';
    }

    function getManifestJsonFile($software)
    {
        return $this->STACK_SOFTWARE_FOLDER . '/'.$software.'/manifest.json';
    }

    function readManifestJson($manifestJson)
    {
        return json_decode(file_get_contents($manifestJson), true);
    }

    function softwareKeyToFolderName($softwareRegKey)
    {
        // if key contains bitsize, return folder without bitsize  
        if(strpos($softwareRegKey, '-x86') !== false || 
           strpos($softwareRegKey, '-x64') !== false)
        {
           return explode('-', $softwareRegKey)[0];
        }

        // else pass through
        return $softwareRegKey;
    }

    function getNumberOfPhpExtensionsInRegistry()
    {
        $phpExtensionsInSoftwareRegistry = 0;
        foreach($this->stack as $software => $data)
        {
            if(strpos($software, 'phpext_') !== false) { // Only PHP Extensions         
                $phpExtensionsInSoftwareRegistry++;
            }
        }
        return $phpExtensionsInSoftwareRegistry;
    }

    function removePhpExtensionsFromRegistryData()
    {
        foreach($this->stack as $software => $data)
        {
            if(strpos($software, 'phpext_') !== false) {       
                unset($this->stack[$software]);
            }
        }
    }

    function calculateStats()
    {
        $this->phpExtensionsInSoftwareRegistry = $this->getNumberOfPhpExtensionsInRegistry();

        // calculations
        $this->stackRegistryItemsTotal = count($this->stack);
        $this->phpRegistryItemsTotal = count($this->php);
        $this->stackRegistryItemsWithoutExtensionsTotal = $this->stackRegistryItemsTotal - $this->phpExtensionsInSoftwareRegistry;
        $this->softwareItemsTotal = $this->stackRegistryItemsWithoutExtensionsTotal  + $this->phpRegistryItemsTotal;

        $this->tasksToImplementForStackRegistry = $this->stackRegistryItemsWithoutExtensionsTotal  * 6;
        $this->tasksToImplementForPHPRegistry = $this->phpRegistryItemsTotal * 6;
        $this->tasksToImplementTotal = $this->tasksToImplementForStackRegistry + $this->tasksToImplementForPHPRegistry;

        $this->tasksImplementedTotal = $this->tasksImplementedStackRegistry + $this->tasksImplementedPHPRegistry;

        $this->tasksImplementedStackRegistryPercentage = round( ((100/$this->tasksToImplementForStackRegistry) * $this->tasksImplementedStackRegistry), 2);
        $this->tasksImplementedPHPRegistryPercentage = round( ((100/$this->tasksToImplementForPHPRegistry) * $this->tasksImplementedPHPRegistry), 2);

        //$this->tasksImplementedTotalPercentage = ($this->tasksImplementedStackRegistryPercentage+$this->tasksImplementedPHPRegistryPercentage / 2);
        $this->tasksImplementedTotalPercentage = round( ((100/$this->tasksToImplementTotal)*$this->tasksImplementedTotal),2);

        $this->existingFoldersStackRegistry = $this->stackRegistryItemsWithoutExtensionsTotal - $this->missingFolders;
        //$this->existingFoldersPHPRegistry = $this->phpRegistryItemsTotal - $this->missingFolders;

        $this->existingComposerJsonFiles = $this->stackRegistryItemsWithoutExtensionsTotal - $this->missingComposerJsonFiles;
        $this->existingChangelogFiles  = $this->stackRegistryItemsWithoutExtensionsTotal - $this->missingChangelogFiles;
    }

    function buildReportData()
    {
        /**
         * Build report data
         * 1. check that a folder exists for every software in the registry
         * 2. read manifest, check that all task entries are present
         * 3. check the status of the entries (value), if "todo" = red, if "done" = green
         */
        $report = [];
        foreach($this->stack as $software => $data)
        {
            $folder = $this->softwareKeyToFolderName($software);

            if(array_key_exists('name', $data)) {
                $report[$software]['registry_name_exists'] = true;
                $report[$software]['registry_name'] = $data['name'];
            } else {
                $report[$software]['registry_name_exists'] = false;;
            }

            if(array_key_exists($software, $this->metadata)) {
                $report[$software]['metadata_key_exists'] = true;
            } else {
                $report[$software]['metadata_key_exists'] = false;
            }

            if(is_dir($this->STACK_SOFTWARE_FOLDER . '/'.$folder))
            {
                $report[$software]['folder_exists'] = true;

                $manifestJson = $this->getManifestJsonFile($folder);        
                if(file_exists($manifestJson)) {
                    $report[$software]['manifest_exists'] = true;  
                    $report[$software]['manifest'] = $this->readManifestJson($manifestJson);

                    foreach($this->tasks as $task) {
                        if($report[$software]['manifest']['tasks'][$task] == 'done') {
                            $this->tasksImplementedStackRegistry++;
                        }
                    }
                } else {
                    $report[$software]['manifest_exists'] = false; 
                    $this->missingManifestJsonFiles++; 
                }
               
                $composerJson = $this->getComposerJsonFile($folder);
                if(file_exists($composerJson)) {
                    $report[$software]['composerjson_exists'] = true;       
                } else {           
                    $report[$software]['composerjson_exists'] = false; 
                    $this->missingComposerJsonFiles++;      
                }

                $changelog = $this->getChangelogFile($folder);
                if(file_exists($changelog)) {
                    $report[$software]['changelog_exists'] = true;       
                } else {           
                    $report[$software]['changelog_exists'] = false; 
                    $this->missingChangelogFiles++;      
                }

            } else {
                $this->missingFolders++;
                $this->missingManifestJsonFiles++;
                $this->missingComposerJsonFiles++;
                $this->missingChangelogFiles++;

                $report[$software]['folder_exists']   = false;
                $report[$software]['manifest_exists'] = false;
                $report[$software]['composerjson_exists'] = false; 
                $report[$software]['changelog_exists'] = false; 
            }    
        }
        return $report;
    }
}