    
    <!-- Implementation Status of Task Scripts and Display for Git Repo Quality Checks -->       
    <div class="container">
        <br>
        <div class="card">
            <div class="card-header">WPN-XM Server Stack - Implementation Status of Task Scripts</div>           
            <div class="card-block">
                
                <!-- Summary Table -->
                <table id="summary" class="table table-bordered" style="text-align: center;">
                    <thead>
                        <tr>
                            <th colspan="5">Summary Report</th>
                        </tr>
                        <tr>
                            <th>Registry</th>
                            <th>Software Items In Registry</th>
                            <th>Tasks Implemented</th>
                            <th>Tasks To Implement <small>(*)</small></th>
                            <th>Percent Work Complete</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>Stack Registry</td>
                        <td><div style="font-size: 20px;"><?php echo $stackRegistryItemsWithoutExtensionsTotal; ?> <small>(*2)</small></div></td>
                        <td><div style="font-size: 20px;"><?php echo $tasksImplementedStackRegistry; ?></div></td>
                        <td><div style="font-size: 20px; font-weight: bold; color: red;"><?php echo $tasksToImplementForStackRegistry; ?></div></td>        
                        <td><?php echo $progressBar_tasksImplementedStackRegistryPercentage; ?></td>
                    </tr>
                    <tr>
                        <td>PHP Registry</td>
                        <td><div style="font-size: 20px;"><?php echo $phpRegistryItemsTotal; ?></div></td>
                        <td><div style="font-size: 20px;"><?php echo $tasksImplementedPHPRegistry; ?></div></td>
                        <td><div style="font-size: 20px; font-weight: bold; color: red;"><?php echo $tasksToImplementForPHPRegistry; ?></div></td>        
                        <td><?php echo $progressBar_tasksImplementedPHPRegistryPercentage; ?></td>
                    </tr>
                    <tr>
                        <td class="text-xs-right">Total</td>
                        <td><div style="font-size: 20px;"><?php echo $softwareItemsTotal; ?></td>        
                        <td><div style="font-size: 20px;"><?php echo $tasksImplementedTotal; ?></div></td>
                        <td><div style="font-size: 20px; font-weight: bold; color: red;"><?php echo $tasksToImplementTotal; ?></div></td>
                        <td><?php echo $progressBar_tasksImplementedTotalPercentage; ?></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">Report generated: <?php echo $date; ?></div>
        </div>
        <br>
        <!-- Repo Quality Checks Table -->
        <table id="server-stack-software" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Server Stack Software</th>
                    <th colspan="12"</th>
                </tr>
                <tr>
                    <th>Summary:</th>                       
                    <th style="border-left: 1px solid white;">
                        <?php echo $stackRegistryItemsWithoutExtensionsTotal; ?> total<br>
                        <?php echo $existingFoldersStackRegistry; ?> ✔ <br>
                        <?php echo $missingFolders; ?> ✘
                    </th>
                    <th style="border-left: 1px solid white;">
                        <!-- @TODO calc missing logos -->
                    </th>
                    <th style="border-left: 1px solid white;">
                        <?php echo $stackRegistryItemsWithoutExtensionsTotal; ?> total<br>
                        <?php echo $existingFoldersStackRegistry; ?> ✔ <br>
                        <?php echo $missingManifestJsonFiles; ?> ✘
                    </th>
                    <th style="border-left: 1px solid white;">
                        <?php echo $stackRegistryItemsWithoutExtensionsTotal; ?> total<br>
                        <?php echo $existingComposerJsonFiles; ?> ✔ <br>
                        <?php echo $missingComposerJsonFiles; ?> ✘
                    </th>
                    <th style="border-left: 1px solid white;">
                        <?php echo $stackRegistryItemsWithoutExtensionsTotal; ?> total<br>
                        <?php echo $existingChangelogFiles; ?> ✔ <br>
                        <?php echo $missingChangelogFiles; ?> ✘
                    </th>
                     <th style="border-left: 1px solid white;">
                        <!-- metadata key exists -->
                    </th>
                    <th colspan="6" style="text-align: center; border-left: 1px solid white;">
                        <?php echo ($tasksImplementedStackRegistry / $tasksToImplementForStackRegistry); ?>
                    </th>
                </tr>
                <tr>
                    <th>Software Name</th>                        
                    <th>Folder exists?</th>
                    <th>Logo exists?</th>
                    <th>Manifest exists?</th>
                    <th>composer.json exists?</th>
                    <th>Changelog exists?</th>
                    <th>Metadata Key exists?</th>
                    <th>Install</th>
                    <th>Uninstall</th>
                    <th>Update</th>
                    <th>Backup</th>
                    <th>Restore</th>
                    <th>Version Detection</th>
                </tr>                    
            </thead>
            <tbody>
                <?php echo $summaryTable; ?>
            </tbody>
        </table>

        <!-- footer -->
        <div style="font-size: 12px">
            *) This value is the number of softwares * number of tasks (6).
        <br>
            *2) This is the value without the <?php echo $phpExtensionsInSoftwareRegistry; ?> PHP Extensions. 
            The total number of items in the server stack registry is: <?php echo $stackRegistryItemsTotal; ?>
            (<?php echo ($stackRegistryItemsWithoutExtensionsTotal + $phpExtensionsInSoftwareRegistry); ?>).
        </div>
        <!-- scripts -->
        <script>
            // enable tooltips everywhere
            $(function () {
              $('[data-toggle="tooltip"]').tooltip()
            });
        </script>
        <!-- styles -->
        <style>   
            table#summary tr:last-child:not(:first-child) td:not(:first-child) {
                border-top: 3px double #ccc;
            }
            table#summary tr:last-child:not(:first-child) td:nth-child(4) {
                border-top: 3px double red;
            }
            table#summary tr:last-child:not(:first-child) td:nth-child(3) {
                border-top: 3px double green;
            }
            /*table#summary tr td:nth-child(4) {                    
                border: 2px solid red;
            }
            table#summary tr td:nth-child(3) {
                border: 2px solid green;
                border-right: none;
            }*/
            table#server-stack-software {
                counter-reset: rowNumber + 3; // start at row 4
            }
            table#server-stack-software tr {
                counter-increment: rowNumber;
            }
            table#server-stack-software tr td:first-child::before {
                content: counter(rowNumber);
                min-width: 1em;
                margin-right: 0.5em;
            }
        </style>
    </div>