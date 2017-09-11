
<h4>Add Component To Software Registry</h4>

<form class="form-horizontal" action="index.php?action=insert-component" method="post">
  <fieldset>
    <!-- Text input-->
    <div class="form-group">
      <label class="col-md-4 control-label" for="shorthand">Registry Shorthand</label>
      <div class="col-md-5">
        <input id="shorthand" name="shorthand" value="<?=$shorthand;?>" class="form-control input-md" type="text">
      </div>
    </div>

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

  <!-- Text input-->
  <div class="form-group">
      <label class="col-md-4 control-label" for="version">Normalized Download Filename</label>
      <div class="col-md-2">
        <input id="downloadfilename" name="downloadfilename" placeholder="software.zip" class="form-control input-md" type="text">
      </div>
    </div>
  </fieldset>
  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
