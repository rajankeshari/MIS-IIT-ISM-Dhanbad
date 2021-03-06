/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    $("[data-toggle='tooltip']").tooltip();
    var ts = $('#report').DataTable({scrollX: true, paginate: true});
    $('#report tbody').on("click", "tr .verifybutton", function () {
        if (confirm("Do You Really Want to perform on checked ones courses !")) {
            var arr = [], arr2 = [], arr3 = [];
            var element = $(this);
            var id = element.attr("id");
            ts.rows(id).nodes().each(function (a, b) {
                $(a).children().find('input[type=checkbox]:checked').each(function () {
                    arr.push(this.value);
                    arr2.push($(this).closest('td').index() + 1);
                    //console.log($(this).closest('td').index()+1);               
                    //console.log($('tr#'+id).find('td:eq('+$(this).closest("td").index()+')').find('p.after_perform > span.show_after').text());
                    arr3.push($('tr#' + id).find('td:eq(' + $(this).closest("td").index() + ')').find('p.after_perform > span.show_after').text());
                });
                //alert(arr.toString());
            });
            if(arr.length === 0){
                alert('!No course checked to perform.Please select...');
                return false
            }
            else{
            
            $.ajax({
                url: site_url("marks_common/shared_paper_grading/update_highest_and_grade"),
                type: 'POST',
                dataType: 'json',
                data: {'marks_master_list': arr.toString()},
                success: function (data) {
                    // 	alert(data);                                  
                    $("#msg" + id).html("");
                    $("#msg" + id).show();
                    if (data.result == 'Failed') {
                        $("#msg" + id).removeClass('alert alert-success').addClass("alert alert-danger");
                        $("#msg" + id).html(' <a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-exclamation"></i><strong>Grading Failed.</strong>');
                    }
                    else if (data.result == 'Successfully') {
                        var k = 0;
                        while (k < arr2.length) {
                            $('tr#' + id).find('td:eq(' + (arr2[k] - 1) + ')').removeClass('danger').addClass('success');
                            $('tr#' + id).find('td:eq(' + (arr2[k] - 1) + ')').children('p.chk').css("display", "none");
                            //$('tr#'+id).find('td:eq('+ (arr2[k]-1)+')').find('a.open-AddBookDialog').css("display", "none");                                    
                            $('tr#' + id).find('td:eq(' + (arr2[k] - 1) + ')').find('p.after_perform').html('<span class="badge badge-success">' + arr3[k] + '</span>');
                            k++;
                        }
                        $('td:last', '#' + id).find('button').css("display", "none");
                        $('td:last', '#' + id).append("<p><span class='label label-success'>Performed</span></p>");
                        //$('#report').dataTable().fnUpdate('Performed', '#' + id, ''+col+'');
                        $('td:last', '#' + id).removeClass('danger').addClass('success');                        
                        $("#msg" + id).removeClass('alert alert-danger').addClass("alert alert-success");
                        $("#msg" + id).html(' <a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-check"></i></i><strong>Grading Done <br>Successfully.<br>' + (data.updated_record == null || data.updated_record == '' ? 0 : data.updated_record) + '  student(s)\' grade updated <br>respectively for the <br>selected course(s)</strong>');
                    }
                },
                error: function (error) {
                    alert(JSON.stringify(error));
                    $("#msg" + id).html("");
                    $("#msg" + id).show();
                    $("#msg" + id).removeClass('alert alert-success').addClass("alert alert-danger");
                    $("#msg" + id).html(' <a href="#" class="close" data-dismiss="alert">&times;</a><i class="fa fa-exclamation"></i><strong>Grading Failed.</strong>');
                }
            });
    }
        } else
            return false;
    });
});

function view_grade_change(title, marks_master_id, session_yr, session, maxheighest, sub_code, exam_type) {
    $.ajax({
        url: site_url("marks_common/shared_paper_grading/get_grade_change_details"),
        type: "POST",
        //dataType: "json",
        async: false,
        data: {marks_master_id: marks_master_id, session_yr: session_yr, session: session, propsed_highest: maxheighest, subject_id: sub_code, exam_type: exam_type},
        success: function (result) {
            $("#myModal").modal();                     
            $('#myModal').on('shown.bs.modal', function () {    $('#report2').DataTable({   paginate: true,destroy:true });print('report2'); }) ;                     
            $('#grade_change_det').html(result);
            $('#sub_header ,#colspan_text').html(title);
        }
    });
}

$(document).on("click", ".open-AddBookDialog", function () {
    var element = $(this);
    var id = element.attr("id");    
    var title = $('#' + id).data('todo').subheader;
    view_grade_change(title, $('#' + id).data('todo').marks_master_id, $('#' + id).data('todo').session_yr, $('#' + id).data('todo').session, $('#' + id).data('todo').maxheighest, $('#' + id).data('todo').sub_code, $('#' + id).data('todo').exam_type);
});