<?php
/**
 * WPИ-XM Server Stack
 * Copyright © 2010 - 2014 Jens-André Koch <jakoch@web.de>
 * http://wpn-xm.org/
 *
 * This source file is subject to the terms of the MIT license.
 * For full copyright and license information, view the bundled LICENSE file.
 */
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WPN-XM Software Registry - Update Tool</title>
    <meta name="description" content="WPN-XM Software Registry Update Tool" />
    <meta name="author" content="Jens-André Koch" />

    <!-- Le styles -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { padding-top: 60px; } /* 60px to make the container go all the way to the bottom of the topbar */
      .navbar { border-top: 4px solid #D73D3D; }
      .navbar .brand { font-size: 18px; }
      .container { font-size: 14px; }
      .center { text-align: center; }
      h1, h2, h3, h4, h5 {
        color: #555555;
        font-family: 'Open Sans',sans-serif !important;
        font-weight: normal !important;
        margin-top: 5px;
        text-shadow: 0 0 1px #F6F6F6;
        text-rendering: optimizeLegibility;
      }
      h3 {
        font-size: 24.5px;
      }
      select {
          -webkit-appearance: none;
          background-color: transparent;
      }
      select.updated {
          background-color: yellow;
           transition: background-color 2s;
          -moz-transition: background-color 2s;
          -webkit-transition: background-color 2s;
          -o-transition: background-color 2s;
      }
      .table-version-matrix > thead > tr > th,
      .table-version-matrix > tbody > tr > th,
      .table-version-matrix > tfoot > tr > th,
      .table-version-matrix > thead > tr > td,
      .table-version-matrix > tbody > tr > td,
      .table-version-matrix > tfoot > tr > td {
          padding: 0.1em;
          text-align: center;
          vertical-align: middle;
      }
      .version-number {
          background-color: #dff0d8;
          color: #006400;
      }
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="assets/ico/favicon.ico">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">WPN-XM Software Registry - Update Tool</a>
        </div>
          <div class="nav-collapse">
            <ul id="menu" class="nav navbar-nav">
              <li><a href="registry-status.php">Link Status</a></li>
              <li class="active"><a href=".">Version Scanner</a></li>
              <li><a class="navbar-brand" href="#">Installation Wizard Registries</a></li>
              <li><a href="registry-update.php?action=show-version-matrix">Show Version Matrix</a></li>
              <li><a href="registry-update.php?action=update-components"
                     title="Raise the version of all Components, of all 'next' Installation Registries to their latest version.">
                     Update
                  </a>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
      </div>
    </div>

    <div id="ajax-container" class="container">
      <!-- This is where the precious Ajax Content goes... -->
    </div> <!-- /ajax-container -->

    <!-- The modal windows with Ajax Loading Indicator - hidden, but loaded with the main page / top-level position -->
    <div id="myModal" class="modal bootstrap-dialog type-primary size-normal fade in"
         tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 class="modal-title">Scanning URLs... Please wait...</h3>
          </div>
          <div class="modal-body center">
            <p><img src='assets/img/ajax_spinner.gif' alt="Loading... Please wait." /></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>

        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- javascript -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {

        // init modal window and hide it
        $('#myModal').modal({show: false});

        doGetRequestIfServerIsRunning('registry-update.php?action=single-component-scan');

        // bind "submit" button on the modal window
        $('#myModal button[type="submit"]').on("click", function (event) {
           var form = $("#myModal .modal-body form");

           $.ajax({
             type: form.attr('method'),
             url: form.attr('action'),
             data: form.serializeArray(),
             cache: false,
             success: function (response) {
               $('#myModal .modal-body').html(response);
             }
           });

           event.preventDefault();
        });

        // with a click on a link in the top navi, do the following
        $("#menu li a").on("click", function (event) {
          href = $(this).attr('href'); // get click target href
          if(href == '.') return; // skip version scanner link

          event.preventDefault(); // stop the click from causing navigation
          doGetRequestIfServerIsRunning(href);
          return false; // stop clicking from causing navigation
        });

        // remove the old modal content on hide event
        $('body').on('hidden.bs.modal', '.modal', function () {
            $(this).removeData('bs.modal');

        });

        // test, if script on the server is available with a timeout request
        // if the timeout is not reached, we assume the server is running and do the "non-timeout" call to href
        function doGetRequestIfServerIsRunning(href) {
            $.ajax({
              url: "index.php",
              type: "HEAD",
              timeout: 500, // set timeout to 0,5 sec
              cache: false,
              statusCode: {
                  200: function (response) {
                      doGetRequest(href);
                  },
                  400: function (response) {
                      alert("Request Timeout!\n\nEnsure Server & PHP are up!");
                  },
                  0: function (response) {
                      alert("Request Timeout!\n\nEnsure Server & PHP are up!");
                  }
              }
            });
        }

        function doGetRequest(href) {
          // reset target, show modal dlg
          $("#ajax-container").empty();
          $('#myModal').modal('show');

          // set new active element on top nav
          $("#menu li").removeClass('active');
          $(this).parent('li').addClass('active');

          // ajax call to the PHP scripts
          $.ajax({
              url: href,
              cache: false,
              timeout: 99999
          }).done(function (html) {
              // hide modal, insert content on target
              $('#myModal').modal('hide');
              $("#ajax-container").empty().append(html);
          });
        }

        // event binding for the version comparision matrix
        $("body").on("click", '[id^=syncDropDownsButton]', function() {

          // find cell, where we clicked "syncDropDownButton"
          var column = $(this).parent().parent().children().index(this.parentNode);

          // get table
          var table = $(this).closest('table').find('tr');

          // fetch installer name from column header
          var installer = table.find("th").eq(column).html();

          // set installer name to input field
          $('input[name="new-registry-name"]').val(installer);

          // for each table row
          table.each(function () {
              // get td element of current column
              var td = $(this).find("td").eq(column);
              // get version number
              var version = td.html();

              // select this version number at the version DropDown box in the same row
              if(typeof version != 'undefined') {
                  if(version == '&nbsp;') {
                    var selectOption = td.closest("tr").find("option[value='do-not-include']");
                  } else {
                    var selectOption = td.closest("tr").find("option[value='"+version+"']");
                  }

                  selectOption.attr("selected", "selected");

                  // add some color highlighting to indicate the change
                  selectOption.parent().addClass("updated");

                  setTimeout(function () { // toggle back after 1 second
                    selectOption.parent().removeClass("updated");
                  }, 1000);
              }
          });
        });

      // event binding for the version comparision matrix
      $("body").on("click", '[id^=syncInstallerNameButton]', function () {
          // find cell, where we clicked "syncInstallerNameButton"
          var column = $(this).parent().parent().children().index(this.parentNode);

          // get table
          var table = $(this).closest('table').find('tr');

          // fetch installer name from column header
          var installer = table.find("th").eq(column).html();

          // set installer name to input field
          $('input[name="new-registry-name"]').val(installer);
      });

      }); // document.read end
    </script>
  </body>
</html>
