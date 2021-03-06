
$(document).ready(function () {

    $add_course_form = $("#add_course_form");
    //$form_table = $("#form_table");
    $box_form = $("#box_form");
    $dept_selection = $('#dept_selection');
    $table_content = $('#refresh_list_courses');

    $duration = 1;
    /**
     * this function runs ajax to bring all courses, branches, years and semester's range
     */
    add_course();
    function add_course() {
        var dept_selected;
        
        $box_form.showLoading();
        $.ajax({url: site_url("course_structure/add/json_all_from_dept_id/" + $dept_selection.find(':selected').val()),
            success: function (data) {
                
                var loop_var;
                var table_ajax_content = "";
                /**
                 * color control vars
                 * @type String
                 */
                var pre_col = 'some_unknow_value';
                var is_white = false;
                for (loop_var = 0; loop_var < data.length; loop_var++) {
                    /*
                     * sem content
                     * @type String
                     */
                    var semester_options = "<select class='form-control' id='select_alternative_" + loop_var + "'>";
                    if (data[loop_var]['course_id'] === "honour" || data[loop_var]['course_id'] === "minor")
                    {
                        semester_options += "<option value = '*'>All</option>";
                        for (counter = 5; counter <= 8; counter++) {
                            semester_options += "<option value=\"" + counter + "\">" + counter + "</option>";
                        }
                    }
                    else if(data[loop_var]['course_id'] === 'prep'){
                        semester_options += "<option value=\"*\">All</option>";
                        semester_options += "<option value=\"-1\">1</option>";                        
                        semester_options += "<option value=\"0\">2</option>";
                    }
                    else if(data[loop_var]['course_id'] === 'b.tech' || data[loop_var]['course_id'] === 'dualdegree'){
						semester_options += "<option value = '*'>All</option>";
                            for (counter = 3; counter <= 2 * data[loop_var]['duration']; counter++) {
                                semester_options += "<option value=\"" + counter + "\">" + counter + "</option>";
                            }
					}else
                    {
                        
                        if (data[loop_var]['duration'] < 4) {
                            semester_options += "<option value = '*'>All</option>";
                            for (counter = 1; counter <= 2 * data[loop_var]['duration']; counter++) {
                                semester_options += "<option value=\"" + counter + "\">" + counter + "</option>";
                            }
                        }
                        else {
                            semester_options += "<option value = '*'>All</option>";
                            for (counter = 1; counter <= 2 * data[loop_var]['duration']; counter++) {
                                semester_options += "<option value=\"" + counter + "\">" + counter + "</option>";
                            }

                        }
                    }
                    semester_options += "</select>";
                    /*
                     * other content
                     */
                    if (pre_col !== data[loop_var]['course_id']) {
                        pre_col = data[loop_var]['course_id'];
                        is_white = !is_white;
                    }
                    if (is_white === true) {
                        table_ajax_content += "<tr style='background-color: lightgrey;'>";
                    } else {
                        table_ajax_content += "<tr style='background-color: white;'>";
                    }
                    //table courses
                    table_ajax_content += "<td>" + data[loop_var]['course_name'] +"<br/>["+ data[loop_var]['start'] + "-" + data[loop_var]['end']+ "]</td>";
                    //table branches
                    table_ajax_content += "<td>" + data[loop_var]['branch_name'] + "</td>";
                    //table years
                    //table_ajax_content += "<td>" +  + "</td>";
                    //semester options
                    var d_id = data[loop_var]['id'];//dept_id
                    var c_id = data[loop_var]['course_id'];//course_id
                    var b_id = data[loop_var]['branch_id'];//branch_id
                    var s_yr = data[loop_var]['start'].toString() + "_" + data[loop_var]['end'].toString();
                    table_ajax_content += "<td><div class='input-group col-md-12'>" + semester_options + "<div class='input-group-btn'><input type='submit' value='"+controller_type+"' class=' btn btn-success' onclick=action_course_structure('" + d_id + "','" + c_id + "','" + b_id + "','" + s_yr + "','" + loop_var + "')></input></div></div></td>";

                    table_ajax_content += "</tr>";

                }

                $table_content.html(table_ajax_content);
                $box_form.hideLoading();
            },
            type: "POST",
            //data :JSON.stringify({course:$course_selection.find(':selected').val()}),
            dataType: "json",
            fail: function (error) {
                console.log(error);
                $box_form.hideLoading();
            }
        });
    }
    //
    //
    //

    $dept_selection.change(function () {
        add_course();
    });



});

function action_course_structure(dept_id, course_id, branch_id, session, loop_var) {
    var e = document.getElementById("select_alternative_" + loop_var);
    var strSEM = e.options[e.selectedIndex].value;
    //alert(strSEM + " " + dept_id + " " + course_id + " " + branch_id + " " + session);
    var url = '';
    if(controller_type === 'View')
        url = "../ViewCourseStructure";
    if(controller_type === 'Edit')
        url = "../EditCourseStructure";
    var form = $('<form action="' + url + '" method="post">' +
            '<input type="hidden" name="dept" value="' + dept_id + '" />' +
            '<input type="hidden" name="course" value="' + course_id + '" />' +
            '<input type="hidden" name="branch" value="' + branch_id + '" />' +
            '<input type="hidden" name="session" value="' + session + '" />' +
            '<input type="hidden" name="sem" value="' + strSEM + '" />' +
            '</form>');
    $('body').append(form);
    form.submit();

}