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
              <li><a href="registry-update.php">Scan</a></li>
              <li><a href="registry-update.php?action=write-file">Update</a></li>
              <li><a href="update-installer-registries.php">Update Installer Registries</a></li>
            </ul>
          </div><!--/.nav-collapse -->
      </div>
    </div>

    <div id="ajax-container" class="container">
      <!-- This is where the precious Ajax Content goes... -->
    </div> <!-- /ajax-container -->

    <!-- The modal windows with Ajax Loading Indicator -->
    <div id="myModal" class="modal fade">
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
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- javascript -->

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {

        // init modal window and hide it
        $('#myModal').modal({show:false});

        // with a click on a link in the top navi, do the following
        $("#menu li a").click(function(event) {

          event.preventDefault(); // stop the click from causing navigation

          href = $(this).attr('href'); // get click target href

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

          return false; // stop the click from causing navigation
        });

        function doGetRequest(href) {
          // empty the main content area
          $("#ajax-container").empty();
          // show the modal window with the ajax loading indicator
          $('#myModal').modal('show');

          // remove the active class from the old clicked nav link
          $("#menu li").removeClass('active');
          // add the active class to the clicked nav link
          $(this).parent('li').addClass('active');

          // ajax call to the PHP scripts
          $.ajax({
              url: href,
              cache: false,
              timeout: 99999
          }).done(function(html) {
              // debug output to console
              //console.log(html);
              // hide the modal
              $('#myModal').modal('hide');
              // finally display the content in the main content area
              $("#ajax-container").empty().append(html);
          });
        }
      });
    </script>
  </body>
</html>