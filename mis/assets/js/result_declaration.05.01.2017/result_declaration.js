/* tabulation process
 * Copyright (c) ISM dhanbad * 
 * @category   phpExcel
 * @package    exam_tabulation
 * @copyright  Copyright (c) 2014 - 2015 Ism dhanbad
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##0.1##, #26/11/15#
 * @Author     Ritu raj<rituraj00@rediffmail.com>
 */
$(document).ready(function () {
     if($('#exm_type').val()=='regular'){
                        if (!$('#dept option').filter(function () {return $(this).val() == 'comm';}).length)                            
                             $('<option/>').val('comm').html('Common').appendTo("#dept");                        
                  }else{
                          $("#dept option[value='comm']").remove();    
                     }
              
    $('#sec').hide(); $('#admn-no').hide();
    $.ajax({
        url: site_url("attendance/attendance_ajax/get_session_year_exam"),
        success: function (result) {
            $('.gS').html(result);
        }
    });
    $('#exm_type').on('change', function () {
        var exists = false;
        var exists1 = false;
        $('#dept option').each(function () {
            if (this.value == 'comm') {
                exists = true;
            }
            else if (this.value == 'all') {
                exists1 = true;
            }
        });
        if (this.value == "regular") {
            if (exists == true)
                $("#dept option[value='comm']").remove();
            else
                $("#dept").append('<option value="comm">Common</option>');
            if (exists1 == true)
                $("#dept option[value='all']").remove();
        }
        else if (this.value == "prep") {
            if (exists1 == true)
                $("#dept option[value='all']").remove();
            else
                $("#dept").append('<option value="all">All</option>');

            if (exists == true)
                $("#dept option[value='comm']").remove();
            $('#dept').val('all');
        }
        else {
            if (exists1 == true)
                $("#dept option[value='all']").remove();
            if (exists == true)
                $("#dept option[value='comm']").remove();
        }
    });
   $('#dept').on('change', function () {
        var session_year = $('#session_year_attendance').val();
        if (this.value == "comm") {
            $('#sec').show();
            $.ajax({url: site_url("result_declaration/result_declaration_drside/get_section_common2/" + session_year), type: "json",
                success: function (result) {
                    $('#section_id').html(result);
                    $('#section_id > option[value=""]').remove();
                    $('#section_id option').eq(0).before('<option value="all">ALL</option>');
                    $('#section_id').val('all');
                }});
        } else {
            $('#sec').hide();
        }
    });
	});
