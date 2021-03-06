<?php

class Student_ctl extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('student/student_model', 'main_model');
        $this->addJS("exam_control/standard/jquery.dataTables.buttons.min.js");
        $this->addJS("exam_control/standard/buttons.flash.min.js");
        $this->addJS("exam_control/standard/jszip.min.js");
        $this->addJS("exam_control/standard/pdfmake.min.js");
        $this->addJS("exam_control/standard/vfs_fonts.js");
        $this->addJS("exam_control/standard/buttons.html5.min.js");
        $this->addJS("exam_control/standard/buttons.print.min.js");
        $this->addJS("exam_control/standard/buttons.colVis.min.js");
        $this->addCSS("exam_control/standard/buttons.dataTables.min.css");
        $this->addJS("student/print_hostel_reports.js");
    }

    var $user_details = 'user_details';
    var $user_other_details = 'user_other_details';
    var $stu_details = 'stu_details';
    var $stu_hostel_info = 'stu_hostel_info';
    var $departments = 'departments';
    var $courses = "courses";
    var $branches = "branches";
    var $stu_academic = "stu_academic";
    var $emaildata = "emaildata";

    public function index() {

        $req_fields = array("id", "salutation", "first_name", "middle_name", "last_name", "dob", "email");
        $return_data["user_details"] = $this->main_model->get_records_from_id($this->user_details, $req_fields, "id", $this->session->userdata('id'));

        $req_data = array("id", "mobile_no", "father_name", "mother_name");
        $return_data["user_other_details"] = $this->main_model->get_records_from_id($this->user_other_details, $req_data, "id", $this->session->userdata('id'));

        $req_stu_fields = array("admn_no", "name_in_hindi");
        $return_data["stu_details"] = $this->main_model->get_records_from_id($this->stu_details, $req_stu_fields, "admn_no", $this->session->userdata('id'));

        $this->drawHeader("Edit Email-id / Mobile Number");
        $this->load->view("student/edit/update_email_mobile", $return_data);
        $this->drawFooter();
    }

    public function set_email_mobile() {

        if ($_POST) {
            $update_email["id"] = $this->session->userdata('id');
           // $update_email["email"] = $_POST["email"];
           // $this->main_model->add_update_record($this->user_details, $update_email, "id");


            $update_mobile["id"] = $this->session->userdata('id');
           // $update_mobile["mobile_no"] = $_POST["mobile"];
          //  $this->main_model->add_update_record($this->user_other_details, $update_mobile, "id");


            $update_name_in_hindi["admn_no"] = $this->session->userdata('id');
            $update_name_in_hindi["name_in_hindi"] = $_POST["name_in_hindi"];
            $this->main_model->add_update_record($this->stu_details, $update_name_in_hindi, "admn_no");


            $this->session->set_flashdata('update_info', 'Email-Id , Mobile Number and Hindi Name has been Updated');
        }
        redirect('student/student_ctl', 'refresh');
    }

    public function stu_hostel_info() {

        $req_fields = array("id", "salutation", "first_name", "middle_name", "last_name", "dob", "email");
        $return_data["user_details"] = $this->main_model->get_records_from_id($this->user_details, $req_fields, "id", $this->session->userdata('id'));

        $req_data = array("id", "mobile_no", "father_name", "mother_name");
        $return_data["user_other_details"] = $this->main_model->get_records_from_id($this->user_other_details, $req_data, "id", $this->session->userdata('id'));

        $filter[0]["id"] = "admn_no";
        $filter[0]["value"] = $this->session->userdata('id');
        $filter[1]["id"] = "status";
        $filter[1]["value"] = "SignIn";

        $req_data = array("id", "hostel_name", "block_no", "floor_no", "room_no");
        $hostel_data = $this->main_model->get_many_records($this->stu_hostel_info, $filter, $req_data, "id");
        if (!empty($hostel_data)) {
            $return_data["hostel_info"] = $hostel_data[0];
        } else {
            $return_data["hostel_info"]["hostel_name"] = "";
            $return_data["hostel_info"]["block_no"] = "";
            $return_data["hostel_info"]["floor_no"] = "";
            $return_data["hostel_info"]["room_no"] = "";
        }

        $this->drawHeader("Hostel Information");
        $this->load->view("student/edit/stu_hostel_info", $return_data);
        $this->drawFooter();
    }

    public function set_stu_hostel_info() {



        $filter[0]["id"] = "admn_no";
        $filter[0]["value"] = $this->session->userdata('id');
        $filter[1]["id"] = "exit_date";
        $filter[1]["value"] = NULL;

        $req_data = array("id", "status");
        $get_prev_data = $this->main_model->get_many_records($this->stu_hostel_info, $filter, $req_data, "id");

        if (!empty($get_prev_data)) {
            foreach ($get_prev_data as $get_prev_data_value) {
                $update_records = array();
                $update_records["id"] = $get_prev_data_value["id"];
                $update_records["status"] = "SignOut";
                $update_records["exit_date"] = date("Y-m-d");
                $update_records["modify_by"] = $this->session->userdata('id');
                $this->main_model->add_update_record($this->stu_hostel_info, $update_records, "id");
            }
        }


        $_POST["admn_no"] = $this->session->userdata('id');
        $_POST["entry_date"] = date("Y-m-d");
        $_POST["created_date"] = date("Y-m-d H:i:s");
        $_POST["created_by"] = $this->session->userdata('id');

        $this->main_model->add_update_record("stu_hostel_info", $_POST);

        redirect('home', 'refresh');
    }

    public function view_student_mail_info() {
        $this->drawHeader("Student Mail Report");
        $this->load->view("student/view/view_student_mail_info");
        $this->drawFooter();
    }

    public function get_course_from_dept() {
        $did = $this->input->post('dept_id');
        $data['course_list'] = $this->main_model->get_course_bydept($did);
        echo json_encode($data);
    }

    public function get_branch_from_course_dept() {

        $did = $this->input->post('dept_id');
        $cid = $this->input->post('course_id');
        $data['br_list'] = $this->main_model->get_branch_bycourse($cid, $did);
        echo json_encode($data);
    }

    public function get_student_mail_info() {
        if (!empty($_POST)) {

            $filterKey = 0;
            if (!empty($_POST["year"])) {
                $filter[$filterKey]["id"] = "b.enrollment_year"; //a => First Table, b => Second Table , c=> Third Table
                $filter[$filterKey++]["value"] = $_POST["year"];
            }

            if (!empty($_POST["dept"])) {
                $filter[$filterKey]["id"] = "a.dept_id";
                $filter[$filterKey++]["value"] = $_POST["dept"];
            }
            if (!empty($_POST["course"])) {
                $filter[$filterKey]["id"] = "b.course_id";
                $filter[$filterKey++]["value"] = $_POST["course"];
            }
            if (!empty($_POST["branch"])) {
                $filter[$filterKey]["id"] = "b.branch_id";
                $filter[$filterKey++]["value"] = $_POST["branch"];
            }
            if (!empty($_POST["semester"])) {
                $filter[$filterKey]["id"] = "b.semester";
                $filter[$filterKey++]["value"] = $_POST["semester"];
            }
            if (!empty($_POST["hostel_no"])) {
                $filter[$filterKey]["id"] = "c.hostel_name";
                $filter[$filterKey++]["value"] = $_POST["hostel_no"];
            }
            if (!empty($_POST["block_no"])) {
                $filter[$filterKey]["id"] = "c.block_no";
                $filter[$filterKey++]["value"] = $_POST["block_no"];
            }
            if (!empty($_POST["floor_no"])) {
                $filter[$filterKey]["id"] = "c.floor_no";
                $filter[$filterKey++]["value"] = $_POST["floor_no"];
            }
            $filter[$filterKey]["id"] = "c.status";
            $filter[$filterKey++]["value"] = "SignIn";
            $data = $this->main_model->get_records_from_three_join($this->user_details, "id", $this->stu_academic, "admn_no", $this->stu_hostel_info, "admn_no", $this->user_other_details, "id", $filter);
            $return_data["data"] = array();
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $email = $this->main_model->get_records_from_id($this->emaildata, array("domain_name"), "admission_no", $value["id"]);
                    if (!empty($email)) {
                        
						$value["email"] = $email["domain_name"];
                    }else{
						$value["email"] = "Not Registered";
					}
                    $return_data["data"][$key] = $value;
                }
            }
            $this->drawHeader("Student Mail Report");
            $this->load->view("student/view/view_student_mail_info");
            $this->load->view("student/view/details_student_mail_info", $return_data);
            $this->drawFooter();
        } else {
           // $this->session->set_flashdata('get_student_mail_info', 'Please select atleast one column');
            redirect('student/student_ctl/view_student_mail_info', 'refresh');
        }
    }

}

?>