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
      .container { font-size: 12px; }
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
    </style>
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="assets/js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">-->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">WPN-XM Software Registry - Update Tool</a>
        </div>
          <div class="nav-collapse">
            <ul id="menu" class="nav navbar-nav">
              <li class="active"><a href="registry-status.php">Status</a></li>
              <li><a href="registry-update.php?action=scan">Scan</a></li>
              <li><a href="registry-update.php?action=update">Update</a></li>
              <li><a data-toggle="modal" data-target="#myModal" data-remote="registry-update.php?action=add" href="registry-update.php?action=add">Add</a></li>
              <li><a class="navbar-brand" href="#">Installation Wizard Registries</a></li>
              <li><a href="registry-update.php?action=versionmatrix">Show</a></li>
              <li><a href="update-installer-registries.php">Update</a></li>
              <li><a href="registry-update.php?action=build">Build</a></li>
            </ul>
          </div><!--/.nav-collapse -->
      </div>
    </div>

    <div id="ajax-container" class="container">
      <!-- This is where the precious Ajax Content goes... -->
    </div> <!-- /ajax-container -->

    <!-- The modal windows with Ajax Loading Indicator -->
    <div id="myModal" class="modal fade bootstrap-dialog type-primary size-normal in"
         tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Scanning URLs... Please wait...</h3>
          </div>
          <div class="modal-body center">
            <p><img src='assets/img/ajax_spinner.gif' alt="Loading... Please wait." /></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div><!-- /.modal -->

    <!-- javascript -->

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {

        // init modal window and hide it
        $('#myModal').modal({show:false});

        // with a click on a link in the top navi, do the following
        $("#menu li a").click(function(event) {

          href = $(this).attr('href'); // get click target href

          // href contain add, show dialog
          if(href.toLowerCase().indexOf('add') >= 0) {
            $.get(href, function(response) {
              $("#myModal .modal-content").html(response);
            });
            return;
          }

          event.preventDefault(); // stop the click from causing navigation

          // test, if script available with a timeout request
          // if the timeout is not reached, do the "non-timeout" call
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

          return false; // stop clicking from causing navigation
        });

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
          }).done(function(html) {
              // hide modal, insert content on target
              $('#myModal').modal('hide');
              $("#ajax-container").empty().append(html);
          });
        }
      });
    </script>
  </body>
</html>