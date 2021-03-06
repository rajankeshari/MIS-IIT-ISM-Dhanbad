<?php
  $ui = new UI();

  $tabBox1 = $ui->tabBox()
       ->icon($ui->icon("list"))
       ->title("Room Planning")
       ->tab("old_building", "Old Building", true)
       ->tab("extension_building", "Extension Building")
       ->open();

  $tab1 = $ui->tabPane()->id("old_building")->active()->open();
    $row = $ui->row()->open();
      $col = $ui->col()->id('old_col')->width(12)->open();
      $col->close();
    $row->close();
  $tab1->close();

  $tab2 = $ui->tabPane()->id("extension_building")->open();
    $row = $ui->row()->id('extension_row')->open();
      $col = $ui->col()->id('extension_col')->width(12)->open();
      $col->close();
    $row->close();
  $tab2->close();

?>

<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<script>
 // $(document).tooltip();
  $(document).ready(function(){
     $.ajax({url : site_url("edc_booking/management/room_planning/Old"),
        success : function (result) {
          $('#old_col').html(result);
        }
      });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var target = $(e.target).attr("href") // activated tab
      if(target == '#old_building'){
        var building = 'Old';
        var col = '#old_col'
        $('#extension_col').empty();
      }
      else {
        var building = 'Extension';
        var col = '#extension_col';
        $('#old_col').empty();
      }

      $.ajax({url : site_url("edc_booking/management/room_planning/"+building),
        success : function (result) {
          $(col).html(result);
        }
      });
    });

  });

</script>
