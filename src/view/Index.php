<h3>Main Menu</h3>
<table class="table table-bordered table-sm">
    <thead>
        <th></th>
        <th>Data &amp; Metadata Files</th>
        <th>Actions</th>
    </thead>
    <tr>
      <td><strong>Version Crawler</strong></td>
      <td></td>
      <td> 
        <nav class="nav flex-column">
           <a href="http://localhost/updater/index.php?action=overview" class="btn btn-secondary btn-sm" data-toggle="tooltip" 
                     title="The overview shows the latest versions for components and allows to scan single components.">Overview</a>
            <a href="http://localhost/updater/index.php?action=scan-component" class="btn btn-secondary btn-sm" data-toggle="tooltip" 
                     title="Do a version crawl for all components.">Scan for new Versions</a>
        </nav> 
    </td>
    </tr>
    <tr>
    <tr>
      <td><strong>Installer Registries</strong></td>
      <td>       
          <a href="https://github.com/WPN-XM/registry/tree/master/installer/next" data-toggle="tooltip"
                     title="Go to the folder with the installer registries of the next, but unreleased version."
                     class="badge badge-secondary">Unreleased Installers</a>
          <a href="https://github.com/WPN-XM/registry/tree/master/installer" data-toggle="tooltip"
                     title="Go to the folder with released installer registries."
                     class="badge badge-secondary">Released Installers</a>
      </td>
      <td>
        <nav class="nav flex-column">
          <a href="http://localhost/updater/index.php?action=update-components" class="nav-link btn btn-secondary btn-sm" data-toggle="tooltip"
                   title="Automatically raise the version of each Component to its latest version in all installer registries of the next, but unreleased version.">
                   Update Versions</a>
          <a href="http://localhost/updater/index.php?action=show-version-matrix" class="nav-link btn btn-secondary btn-sm" data-toggle="tooltip"
             title="Full overview of all components and versions included in all installers.">
             Show Version Matrix</a> 
          <a href="http://localhost/updater/index.php?action=compare-installers" class="nav-link btn btn-danger btn-sm" data-toggle="tooltip"
             title="Compare two installer registries.">
             Compare Installers</a>
          <a href="http://localhost/updater/index.php?action=pretty-print-registries" class="nav-link btn btn-secondary btn-sm" data-toggle="tooltip"
             title="This enables you to prettify the registries after a manual modification.">
             Pretty Print Registries</a>
        </nav>
      </td>
    </tr>
    <tr>
      <td><strong>Stack Software Registry</strong></td>
      <td>
          <a href="https://github.com/WPN-XM/registry/blob/master/wpnxm-software-registry.php" 
             class="badge badge-secondary">wpnxm-software-registry.php</a>
      </td>
      <td>     
         <nav class="nav flex-column">
            <a href="http://localhost/updater/index.php?action=add-component" class="btn btn-danger btn-sm">Add Software</a>
            <a href="http://localhost/updater/index.php?action=edit-component" class="btn btn-danger btn-sm">Edit Software</a>
            <a href="http://localhost/updater/index.php?action=health-check-registry" class="btn btn-secondary btn-">Lint (schema)</a> 
          <br>
            <a href="http://localhost/updater/index.php?action=link-status" class="btn btn-secondary btn-sm">Check Links - All</a>
            <a href="http://localhost/updater/index.php?action=link-status-component" class="btn btn-danger btn-sm">Check Links - Single Component</a>  
          </nav>        
      </td>
    </tr>
    <tr>
      <td><strong>PHP Software Registry</strong></td>
      <td><a href="https://github.com/WPN-XM/registry/blob/master/wpnxm-php-software-registry.php" class="badge badge-secondary">wpnxm-php-software-registry.php</a></td>
      <td>
         <nav class="nav flex-column">
          <a href="http://localhost/updater/index.php?action=add-component" class="btn btn-danger btn-sm" title="Add PHP Software">Add</a>
              <a href="" class="btn btn-danger btn-sm" title="Edit PHP Software">Edit</a>
              <a href="" class="btn btn-danger btn-sm" title="Lint: Check schema and structure of the registry.">Lint (schema)</a>
          </nav>
        </td>
    </tr>
    <tr>
      <td><strong>Software Metadata Registry</strong></td>
      <td><a href="https://github.com/WPN-XM/registry/blob/master/wpnxm-registry-metadata.php" class="badge badge-secondary">wpnxm-registry-metadata.php</a></td>
      <td>
        <nav class="nav flex-column">
        <a href="" class="btn btn-danger btn-sm" data-toggle="tooltip"
                 title="Lint: Check schema and structure of the registry and synchronization state with the software registry.">Lint (schema) (sync)</a>
        </nav>
      </td>
    </tr>
    <tr>
      <td><strong>PHP Extensions</strong></td>
      <td>
      <a href="https://github.com/WPN-XM/registry/blob/master/php-extensions-on-pecl.json" class="badge badge-secondary">php-extensions-on-pecl.json</a>
      <a href="https://github.com/WPN-XM/registry/blob/master/php-extensions-outside-pecl.json" class="badge badge-secondary">php-extensions-outside-pecl.json</a>
      </td>
      <td> 
        <nav class="nav flex-column">
          <a href="http://localhost/updater/index.php?action=update-phpextension-list" class="btn btn-secondary btn-sm" data-toggle="tooltip"
               title="Scan PECL (https://pecl.php.net/) for new PHP Extensions.">Update PHP Extension List</a>         
        </nav>
      </td>
    </tr>
    <tr>      
        <td><strong>Download Filenames</strong></td>
        <td><a href="https://github.com/WPN-XM/registry/blob/master/wpnxm-download-filenames.php" class="badge badge-secondary">wpnxm-download-filenames.php</a></td>
        <td> <nav class="nav flex-column">
            <a href="" class="btn btn-danger btn-sm" title="Lint: Check schema and structure.">Lint</a>
            <a href="" class="btn btn-danger btn-sm" title="Sync: Ensure there is a normalized filename for every software component.">Sync</a><nav>
        </td>
    </tr>
    </table>