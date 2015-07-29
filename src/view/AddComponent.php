<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title">Add Component To Software Registry</h4>
</div>
<div class="modal-body">
  <form class="form-horizontal" action="index.php?action=insert" method="post">
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