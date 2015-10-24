<table id="version-matrix" class="table table-sm table-bordered table-version-matrix" style="width: auto !important; padding: 0px; vertical-align: middle;">
  <thead>
    <tr>
      <th>Software Components (<?=$totalRegistries?>)</th>
      <?=$tableHeader?>
    </tr>
  </thead>
  <?=$tableBody?>
</table>

<script>
    $('div#ajax-container.container').css('width', 'auto');
    $('head').append('<style>.form-control { height: auto; padding: 0; } </style>');

    $("#save-button").click(function (event) {

        // find cell, where we clicked "syncDropDownButton"
        var column = $(this).parent().parent().children().index(this.parentNode);

        // get table
        var table = $(this).closest('table').find('tr');

        // fetch installer name from column header
        var installer = table.find('input[name="new-registry-name"]').val();

        // registry (component => version relationship)
        var registry = {};

        // for each table row
        table.each(function () {
            // get td element of current column
            var versionTd = $(this).find("td").eq(column);
            // get version number
            var version = versionTd.find("option:selected").val();

            // exclude "do-not-include" versions
            if (version == "do-not-include" || version == "") {
                return; // continue
            }

            // get component name from first td
            var component = $(this).find("td").eq(0).html();

            // add to registry
            registry[component] = version;
        });

        // debug
        console.log(registry);

        // prepare data
        var data = {};
        data["registry-json"] = JSON.stringify(registry);
        data["installer"] = installer;

        // ajax POST
        $.post("index.php?action=update-installer-registry", data);

        return false; // stop clicking from causing navigation
    });
</script>