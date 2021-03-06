<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Specific_student_model extends CI_Model {

    public function __construct() {
        parent::__construct(array('adhm', 'admin', 'dsw', 'adsw'));

        // $this->load->model('hs_reg/main_model');
        // $this->load->model('hs_reg/insert_model');
        //  $this->addCSS("exam_control/standard/buttons.dataTables.min.css");
        // $this->addJS("exam_control/standard/jquery.dataTables.buttons.min.js");
        // $this->addJS("exam_control/standard/buttons.flash.min.js");
        // $this->addJS("exam_control/standard/jszip.min.js");
        // $this->addJS("exam_control/standard/pdfmake.min.js");
        // $this->addJS("exam_control/standard/vfs_fonts.js");
        // $this->addJS("exam_control/standard/buttons.html5.min.js");
        // $this->addJS("exam_control/standard/buttons.print.min.js");
        // $this->addJS("exam_control/standard/buttons.colVis.min.js");

    }

    var $hs_hostel_details = "hs_hostel_details";
    var $hs_room_details = "hs_room_details";
    var $hs_student_allotment_list = "hs_student_allotment_list";
    var $hs_hostel_name = "hs_hostel_name";
    var $user_details = "user_details";
    var $emp_basic_details = "emp_basic_details";
    var $hs_warden_details = "hs_warden_details";
    var $hs_assigned_student_room = "hs_assigned_student_room";
    var $hs_assigned_student_room_temp = "hs_assigned_student_room_temp";
    var $hs_guest_contact = "hs_guest_contact";
    var $hs_exit_student_room_log = "hs_exit_student_room_log";
    var $user_auth_types = "user_auth_types";
    var $emaildata = "emaildata";
    var $stu_details = "stu_details";

    var $hs_otp_details = "hs_otp_details";

    var $stu_reg_details = "reg_regular_form";

    var $stu_fee_details = "reg_regular_fee";



    public function curr_time_stamp(){
		$curr_time = date_create();
		$ts = date_format($curr_time, 'Y-m-d H:i:s');
		return $ts;
	}


	public function get_session($ts){
		 $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){  
            $p_year = strval((int)$year);  // 2019
            $year = $p_year +1;  // if month is greater than 6 then year will be present year + 1 

        }
        else
        $p_year = strval((int)$year - 1);  // if month is less than 6 then year will be present year will be  year - 1
        return $p_year.'-'.$year;
    }
    

    public function no_dues_student_stop()
    {

        $sql = "select * from no_dues_capture_specific_student_request where status = 1";
        $query = $this->db->query($sql);
        return $query->result_array();

    }


    public function no_dues_student_stop_admin()
    {

        $sql = "SELECT * , a.id as capture_id from no_dues_capture_specific_student_request a inner join no_dues_start b on a.id = b.capture_specific_stu_id where b.access_to = 'admin' and b.status = 1";//exit;
        $query = $this->db->query($sql);
        if($query != false)
        {
        return $query->result_array();
        }
        else
        {
            return false;
        }

    }

    public function no_dues_student_stop_student()
    {

        $sql = "select * from no_dues_capture_specific_student_request a where a.status = 1 and a.access_to = 'stu'";
        $query = $this->db->query($sql);
        if($query != false)
        {
        return $query->result_array();
        }
        else
        {
            return false;
        }

    }

    public function check_admin_for_specific_student($admn_no,$id)
    {

        $sql = "select * from `no_dues_start` a where a.access_to = 'admin' and a.status = 1 and a.access_to_admn_no = ? and a.capture_specific_stu_id = ?";
        $query = $this->db->query($sql,array($admn_no,$id));
        if($query != false)
        {
        //echo $this->db->last_query(); die();
        //echo $query->num_rows(); 
        return $query->num_rows();
        }
        else
        {
            return false;
        }

    }

    public function check_student_for_specific_student($admn_no,$id)
    {

        $sql = "select * from `no_dues_capture_specific_student_request` a where a.access_to = 'stu' and a.status = 1 and a.admn_no = ? and a.id = ?";
        $query = $this->db->query($sql,array($admn_no,$id));
        // echo $this->db->last_query(); //die();
        //echo $query->num_rows(); 
        return $query->num_rows();

    }

    public function delete_student_request($capture_id)
    {
        $this->db->where('id',$capture_id)
                ->delete('no_dues_capture_specific_student_request');

                //echo $this->db->last_query(); //die();

    }

    public function get_mis_session(){

        $query = $this->db->select('*')
                 ->from('mis_session_year')
                 ->get();

        return $query->result_array();
    }


    // public function check_student_for_specific_student($admn_no,$id)
    // {

    //     $sql = "select * from `no_dues_capture_specific_student_request` a where a.access_to = 'stu' and a.status = 1 and a.id = ?";
    //     $query = $this->db->query($sql,array($id));
    //     //echo $this->db->last_query(); //die();
    //     //echo $query->num_rows(); 
    //     return $query->num_rows();

    // }

    
    public function no_dues_student_start_pending()
    {

        $ts=$this->curr_time_stamp();
        $session_year_curr=$this->get_session($ts);
        $date = date("Y-m-d H:i:s");

        //$sql = "SELECT a.* FROM no_dues_capture_specific_student_request a inner join no_dues_lists b on a.admn_no = b.admn_no WHERE a.session_year = ? and b.approv_reject_status_change IN ('0','1','3') and b.request_timestamp <= $date GROUP BY a.admn_no";
        $sql = "SELECT a.*,b.approv_reject_status_change,b.pay_status
        FROM no_dues_capture_specific_student_request a
        INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        WHERE a.session_year = '".$session_year_curr."' AND b.approv_reject_status_change IN ('0','1','3')
        GROUP BY a.admn_no";

        $query = $this->db->query($sql,array($session_year_curr));
        //echo $this->db->last_query(); die();
        if($query != false)
        {
        
        return $query->result_array();
        }
        else
        {
            return false;
        }


    }


    public function no_dues_student_start_pending_dues_not_assign()
    {

        $ts=$this->curr_time_stamp();
        $session_year_curr=$this->get_session($ts);
        $date = date("Y-m-d H:i:s");

        //$sql = "SELECT a.* FROM no_dues_capture_specific_student_request a inner join no_dues_lists b on a.admn_no = b.admn_no WHERE a.session_year = ? and b.approv_reject_status_change IN ('0','1','3') and b.request_timestamp <= $date GROUP BY a.admn_no";
        $sql = "SELECT a.*
        FROM no_dues_capture_specific_student_request a
        #INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        WHERE a.session_year = '".$session_year_curr."' and a.admn_no NOT IN (SELECT a.admn_no
        FROM no_dues_capture_specific_student_request a
        INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        WHERE a.session_year = '".$session_year_curr."' AND b.approv_reject_status_change IN ('0','1','3','2'))
        GROUP BY a.admn_no";

        $query = $this->db->query($sql,array($session_year_curr));
        // echo $this->db->last_query(); die();
        if($query != false)
        {
        
        return $query->result_array();
        }
        else
        {
            return false;
        }


    }

        public function no_dues_student_start_pending_approved()
        {
            
        $ts=$this->curr_time_stamp();
        $session_year_curr=$this->get_session($ts);
        $date = date("Y-m-d H:i:s");

        //$sql = "SELECT a.* FROM no_dues_capture_specific_student_request a inner join no_dues_lists b on a.admn_no = b.admn_no WHERE a.session_year = ? and b.approv_reject_status_change IN ('0','1','3') and b.request_timestamp <= $date GROUP BY a.admn_no";
        // $sql = "SELECT a.*
        // FROM no_dues_capture_specific_student_request a
        // #INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        // WHERE a.session_year = '".$session_year_curr."' and a.admn_no NOT IN (SELECT a.admn_no
        // FROM no_dues_capture_specific_student_request a
        // INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        // WHERE a.session_year = '".$session_year_curr."' AND b.approv_reject_status_change IN ('0','1','3') )
        // GROUP BY a.admn_no";

        $sql = "SELECT a.*,b.approv_reject_status_change,b.pay_status
        FROM no_dues_capture_specific_student_request a
        INNER JOIN no_dues_lists b ON a.admn_no = b.admn_no
        WHERE a.session_year = '".$session_year_curr."' AND b.approv_reject_status_change = '2'
        GROUP BY a.admn_no";

        $query = $this->db->query($sql,array($session_year_curr));
        //echo $this->db->last_query(); die();
        if($query != false)
        {
        
        return $query->result_array();
        }
        else
        {
            return false;
        }

        }

        public function no_dues_current_admin_slot()
        {

            $ts=$this->curr_time_stamp();
            $session_year_curr=$this->get_session($ts);
            $auth="admin";
            $sql = "select * from `no_dues_start` where session_year=? and access_to=? and status=1";
            $query = $this->db->query($sql,array($session_year_curr,$auth));
            if($query != false)
        {
            return $query->result_array();

        }

        else
        {
            return false;
        }


        }


        public function get_current_admin_end_date(){
		$ts=$this->curr_time_stamp();
		$session_year_curr=$this->get_session($ts);
		$auth="admin";
		$res=$this->db->query("SELECT end_date from no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status = 1 and `capture_specific_stu_id` = 0 ")->result_array();
        //echo $this->db->last_query();
        if(!$res)return 10;
		return $res[0]['end_date'];
		//return $res->result_array();
    }

    // public function get_current_admin_end_date(){
	// 	$ts=$this->curr_time_stamp();
	// 	$session_year_curr=$this->get_session($ts);
	// 	$auth="admin";
	// 	$res=$this->db->query("SELECT end_date from no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status = 1 and access_to_admn_no=''")->result_array();
	// 	if(!$res)return 10;
	// 	return $res[0]['end_date'];
	// 	//return $res->result_array();
    // }


        public function get_no_dues_running_status($s,$id){

		$ts=$this->curr_time_stamp();
		$session_year_curr=$this->get_session($ts);
		//echo "SELECT status FROM no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status='$s'"; 
        $res= $this->db->query("SELECT status FROM no_dues_capture_specific_student_request  where session_year='$session_year_curr' and status='$s' and id='$id'")->result_array();
        //echo $this->db->last_query();
        if(!$res) return 10;
        else return $res[0]['status'];
    
    }

    public function get_no_dues_running_status_admin($s,$id,$admn_no){

		$ts=$this->curr_time_stamp();
		$session_year_curr=$this->get_session($ts);
		//echo "SELECT status FROM no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status='$s'"; 
        $res= $this->db->query("SELECT b.status FROM no_dues_capture_specific_student_request a inner join no_dues_start b on a.id = b.capture_specific_stu_id where a.session_year='$session_year_curr' and a.status='$s' and a.id='$id' and b.access_to_admn_no = '$admn_no' and b.access_to = 'admin'")->result_array();
        //echo $this->db->last_query();
        if(!$res) {

        
        
        $session_year = explode('-',$session_year_curr);
        $session_year_part_1 = $session_year[0]-1;
        $session_year_part_2 = $session_year[1]-1;
        $new_session_year_array = array($session_year_part_1,$session_year_part_2);

        $new_session_year = implode('-',$new_session_year_array);

        $res= $this->db->query("SELECT b.status FROM no_dues_capture_specific_student_request a inner join no_dues_start b on a.id = b.capture_specific_stu_id where a.session_year='$new_session_year' and a.status='$s' and a.id='$id' and b.access_to_admn_no = '$admn_no' and b.access_to = 'admin'")->result_array();
        if(!res){
            return 10;
        }

        }
        else 
        {
        return $res[0]['status'];
        }
    
    }

    public function get_all_start_no_dues()
    {

        $sql = "select * from `no_dues_capture_specific_student_request` where `status` = 1";
        $query = $this->db->query($sql);
        return $query->num_rows();

    }

    public function get_hostel_no_dues()
    {

        $sql = "select * from no_dues_hs_individual where `is_deleted` = 0";
        $query = $this->db->query($sql);
        return $query->result_array();

    }

    public function other_no_dues()
    {
        $sql = "select * from no_dues_lists where `is_deleted` = 0";
        $query = $this->db->query($sql);
        if($query != false)
        {
        return $query->result_array();
        }
        else
        {
            echo false;
        }

    }

    public function get_admn_no($id)
    {

        $sql = "select admn_no from no_dues_capture_specific_student_request where id = ?";
        $query = $this->db->query($sql,array($id));
        $admn_array = $query->result_array();
        return $admn_array[0]['admn_no'];
     }

     public function check_no_dues_hs_not_vacanted($admn_no_dues)
     {

         $sql = "select * from `no_dues_hs_not_vacanted` a where a.hostel_not_vacant = 1 and a.admn_no = ? and a.is_deleted = 0";
         $query = $this->db->query($sql,array($admn_no_dues));
         //echo $this->db->last_query($query);
         return $query->num_rows();

     }

    public function other_no_dues_by_dept($dept_id)
    {

        $sql = "select * from `no_dues_lists` where `imposed_dept_id` = ?"; 
        $query = $this->db->query($sql,array($dept_id));
        if($query != false)
        {
        return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    public function get_details_edit($id)
    {
        $sql = "select * from no_dues_capture_specific_student_request where id = ?";
        $query = $this->db->query($sql,array($id));
        return $query->result_array();
    }

    public function edit_no_dues_start_specific($start_date,$end_date,$id,$prev_start_date,$prev_end_date)
    {


        $today_date=date('Y-m-d');

        $user_id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();

       

        $get_details_edit = $this->get_details_edit($id);

        // if($start_date <= $today_date && $end_date >= $today_date)

        // {


            $status = 1;
            $started_on = $ts;
            $started_by = $user_id;
            $stopped_on = $get_details_edit[0]['no_dues_portal_stopped_on'];
            $stopped_by = $get_details_edit[0]['no_dues_portal_stopped_by'];

            

        //}

        // else

        // {


        //     $status = 0;
        //     $started_on = 'Not Yet Started';
        //     $started_by = 'Not Yet Started';
        //     $stopped_on = 'No Action';
        //     $stopped_by = 'No Action';
            

        // }


        $datanew = array(


            'specific_student_request_id' => $id,
            'previous_start_date' => $prev_start_date,
            'previous_end_date' => $prev_end_date,
            'present_start_date' => $start_date,
            'present_end_date' => $end_date,
            'previous_access_to' => $get_details_edit[0]['access_to'],
            'present_access_to' => $get_details_edit[0]['access_to'],
            'modified_on' => date('d-m-Y H:i:s'),
            'modified_by' => $this->session->userdata['id'],
            'status_previous' => $get_details_edit[0]['status'],
            'status_present' => $status,
            'started_on' => $started_on,
            'started_by' => $started_by,
            'stopped_on' => $stopped_on,
            'stopped_by' => $stopped_by
    
       );

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();

        
        $data = array(

                'date_start' => $start_date,
                'date_end' => $end_date,
                'status' => $status,
                'last_modified_by' => $this->session->userdata['id'],
                'last_modified_date' =>  date('d-m-Y H:i:s'),
                'no_dues_portal_started_on' => $started_on,
                'no_dues_portal_started_by' => $started_by,
                'no_dues_portal_stopped_by' => $stopped_by,
                'no_dues_portal_stopped_on' => $stopped_on

        );

        $this->db->where('id', $id);
        $this->db->update('no_dues_capture_specific_student_request', $data);
        //echo $this->db->last_query(); die();

        $datamain = array(

            'start_date' => $start_date,
            'end_date' => $end_date,
         
        );

        $this->db->where('capture_specific_stu_id', $id);
        $this->db->where('status', 1);
        $this->db->update('no_dues_start', $datamain);



     
    }

    public function edit_no_dues_start_specific_student($start_date,$end_date,$id,$prev_start_date,$prev_end_date)
    {


        $today_date=date('Y-m-d');

        $user_id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();

       

        $get_details_edit = $this->get_details_edit($id);

        // if($start_date <= $today_date && $end_date >= $today_date)

        // {


            $status = 1;
            $started_on = $ts;
            $started_by = $user_id;
            $stopped_on = $get_details_edit[0]['no_dues_portal_stopped_on'];
            $stopped_by = $get_details_edit[0]['no_dues_portal_stopped_by'];

            

        //}

        // else

        // {


        //     $status = 0;
        //     $started_on = 'Not Yet Started';
        //     $started_by = 'Not Yet Started';
        //     $stopped_on = 'No Action';
        //     $stopped_by = 'No Action';
            

        // }


        $datanew = array(


            'specific_student_request_id' => $id,
            'previous_start_date' => $prev_start_date,
            'previous_end_date' => $prev_end_date,
            'present_start_date' => $start_date,
            'present_end_date' => $end_date,
            'previous_access_to' => $get_details_edit[0]['access_to'],
            'present_access_to' => $get_details_edit[0]['access_to'],
            'modified_on' => date('d-m-Y H:i:s'),
            'modified_by' => $this->session->userdata['id'],
            'status_previous' => $get_details_edit[0]['status'],
            'status_present' => $status,
            'started_on' => $started_on,
            'started_by' => $started_by,
            'stopped_on' => $stopped_on,
            'stopped_by' => $stopped_by
    
       );

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();

        
        $data = array(

                'date_start' => $start_date,
                'date_end' => $end_date,
                'status' => $status,
                'last_modified_by' => $this->session->userdata['id'],
                'last_modified_date' =>  date('d-m-Y H:i:s'),
                'no_dues_portal_started_on' => $started_on,
                'no_dues_portal_started_by' => $started_by,
                'no_dues_portal_stopped_by' => $stopped_by,
                'no_dues_portal_stopped_on' => $stopped_on

        );

        $this->db->where('id', $id);
        $this->db->update('no_dues_capture_specific_student_request', $data);
        //echo $this->db->last_query(); die();

        // $datamain = array(

        //     'start_date' => $start_date,
        //     'end_date' => $end_date,
         
        // );

        // $this->db->where('capture_specific_stu_id', $id);
        // $this->db->where('status', 1);
        // $this->db->update('no_dues_start', $datamain);



     
    }

    public function no_dues_student_edit()
    {

        $sql = "select * from `no_dues_capture_specific_student_request` where `status` = 1 and `access_to` = 'stu'";
        $query = $this->db->query($sql);
        //echo $this->db->last_query($sql); die();
        return $query->result_array();

    }

    public function no_dues_student_edit_admin()
    {

        $sql = "select * , a.id as capture_id from `no_dues_capture_specific_student_request` a inner join no_dues_start b on a.id = b.capture_specific_stu_id where b.access_to = 'admin' and b.status = 1";
        $query = $this->db->query($sql);
        //echo $this->db->last_query($sql); die();
        if($query != false)
        {
        return $query->result_array();
        }
        else
        {
            return false;
        }

    }

    public function get_end_start_date($id)
    {

        $sql = "select * from `no_dues_capture_specific_student_request` a where a.id = ? and  a.status = 1";
        $query = $this->db->query($sql,array($id));
        //echo $this->db->last_query($sql); die();
        return $query->result_array();


    }

    public function get_student_name($admn_no_dues)
    {

        $sql = "select a.first_name , a.middle_name, a.last_name from user_details a where id = ?";
        $query = $this->db->query($sql,array($admn_no_dues));
        $details = $query->result_array();
        if($details[0]['middle_name'] == '')
        {
            $middle_name = '';
        }
        else
        {
            $middle_name = $details[0]['middle_name'];
        }

        if($details[0]['last_name'] == '')
        {
            $last_name = '';
        }
        else
        {
            $last_name = $details[0]['last_name'];
        }

        return $details[0]['first_name'].' '.$middle_name.' '.$last_name;


    }

    // public function total_hostel_dues_amount_current_hostel_wise($admn_no)
    // {
    //     $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status IN ('no_action','success') and b.status IN ('0','Rejected')";
    //     $query = $this->db->query($sql);
    //     $details = $query->result_array();
    //     return $details[0]['hostel_amount'];
    // }

    public function total_hostel_dues_amount_hostel_wise($all_admission_number)
    {

        //$all_admission_number = '('.implode(',',$all_admission_number).')';

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no IN ('".implode("','",$all_admission_number)."')";//exit;
        $query = $this->db->query($sql);
        //echo $this->db->last_query(); //die();
        $details = $query->result_array();
        return $details[0]['hostel_amount'];

    }


    public function total_hostel_dues_amount_current_hostel_wise($all_admission_number)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no IN ('".implode("','",$all_admission_number)."')";//exit;
        $query = $this->db->query($sql);
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        return $details[0]['hostel_amount'];

    }

    public function total_hostel_dues_amount_pending_hostel_wise($all_admission_number)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Pending' and a.admn_no IN ('".implode("','",$all_admission_number)."')";//exit;
        $query = $this->db->query($sql);
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }


    public function total_hostel_dues_amount_approved_hostel_wise($all_admission_number)
    {

        $hostel_amount = 0;
        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no IN ('".implode("','",$all_admission_number)."')";//exit;
        $query = $this->db->query($sql);
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }


    public function total_hostel_dues_amount_hostel_wise_by_hostel($all_admission_number,$hostel_name,$session_year,$session)
    {

        //$all_admission_number = '('.implode(',',$all_admission_number).')';
        $sum_amount = 0;
        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$session_year,$session));
        //echo $this->db->last_query(); //die();
        $details = $query->result_array();

        if ($details[0]['hostel_amount'] == 0) {
           
            return $sum_amount;

        }

        else
        {

            return $details[0]['hostel_amount'];

        }
        //;

    }

    public function total_hostel_dues_amount_hostel_wise_by_hostel_block($all_admission_number,$hostel_name,$block_name,$session_year,$session)
    {

        //$all_admission_number = '('.implode(',',$all_admission_number).')';

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query(); //die();
        $details = $query->result_array();
        return $details[0]['hostel_amount'];

    }


    public function total_hostel_dues_amount_current_hostel_wise_by_hostel($all_admission_number,$hostel_name,$session_year,$session)
    {

        $sum_amount = 0;
        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        //return $details[0]['hostel_amount'];

        if ($details[0]['hostel_amount'] == 0) {
           
            return $sum_amount;

        }

        else
        {

            return $details[0]['hostel_amount'];

        }


    }

    public function total_hostel_dues_amount_current_hostel_wise_by_hostel_block($all_admission_number,$hostel_name,$block_name,$session_year,$session)
    {

        $sum_amount = 0;
        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        //return $details[0]['hostel_amount'];

        if ($details[0]['hostel_amount'] == 0) {
           
            return $sum_amount;

        }

        else
        {

            return $details[0]['hostel_amount'];

        }

    }

    public function total_hostel_dues_amount_pending_hostel_wise_by_hostel($all_admission_number,$hostel_name,$session_year,$session)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Pending' and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }


    public function total_hostel_dues_amount_pending_hostel_wise_by_hostel_block($all_admission_number,$hostel_name,$block_name,$session_year,$session)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Pending' and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }


    public function total_hostel_dues_amount_approved_hostel_wise_by_hostel($all_admission_number,$hostel_name,$session_year,$session)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }


    public function total_hostel_dues_amount_approved_hostel_wise_by_hostel_block($all_admission_number,$hostel_name,$block_name,$session_year,$session)
    {

        $sql = "select SUM(b.inventory_amount) as hostel_amount from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on b.id = c.hs_no_dues_id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no IN ('".implode("','",$all_admission_number)."') and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";//exit;
        $query = $this->db->query($sql,array($hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if($details[0]['hostel_amount'] == 0) {

            $hostel_amount = 0;
        }

        else
        {
            $hostel_amount = $details[0]['hostel_amount'];

        }

        return $hostel_amount;

    }




    public function total_hostel_dues_amount_approved()
    {

        $sum = 0;
        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status = 'success' and b.status = 'Approved' and a.is_deleted = 0";
        $query = $this->db->query($sql);
        $details = $query->result_array();

        if ($details[0]['hostel_amount'] == 0) {
            
            $sum = 0;
        }

        else
        {
            $sum = $details[0]['hostel_amount'];
        }
        return $sum;

    }

    public function total_hostel_dues_amount_pending()
    {

        $sum = 0;
        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status = 'success' and b.status = 'Pending' and a.is_deleted = 0";
        $query = $this->db->query($sql);
        $details = $query->result_array();

        if($details[0]['hostel_amount'] == 0)
        {

            $sum = 0;

        }
        else
        {

            $sum = $details[0]['hostel_amount'];

        }
        return $sum;

    }

    public function total_hostel_dues_amount_current()
    {

        $sum = 0;
        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status IN ('no_action','success') and b.status IN ('0','Rejected') and a.is_deleted = 0";
        $query = $this->db->query($sql);
        $details = $query->result_array();

        if ($details[0]['hostel_amount'] == 0) {
            
            $sum = 0;

        }

        else {

            $sum = $details[0]['hostel_amount'];
            
        }
        return $sum;

    }

    

    public function total_hostel_dues_amount()
    {
        $sum = 0;
        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a where a.is_deleted = 0";
        $query = $this->db->query($sql);
        $details = $query->result_array();
        //echo $details[0]['hostel_amount'];
        if ($details[0]['hostel_amount'] == 0)
        {
            $sum = 0;
        }

        else
        {
            $sum = $details[0]['hostel_amount'];
        }

        return $sum;
    }

    public function total_other_dues_amount($due_id)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }

    else
    {
        return false;
    }


    }


    public function total_other_dues_amount_hostel_wise($due_id,$admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.admn_no IN ('".implode("','",$admn_no)."') and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }

    public function total_other_dues_amount_current_hostel_wise($due_id,$admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status IN (0,2) and a.approv_reject_status_change IN (0,3) and a.admn_no IN ('".implode("','",$admn_no)."') and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }

    public function total_other_dues_amount_pending_hostel_wise($due_id,$admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status = '2' and a.approv_reject_status_change = '1' and a.admn_no IN ('".implode("','",$admn_no)."') and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }


    public function total_other_dues_amount_approved_hostel_wise($due_id,$admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status = '2' and a.approv_reject_status_change = '2' and a.admn_no IN ('".implode("','",$admn_no)."') and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;

    }
    else
    {
        return false;
    }


    }


    public function total_other_dues_amount_current($due_id)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status IN (0,2) and a.approv_reject_status_change IN (0,3) and a.is_deleted = 0"; // 
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }

    public function total_other_dues_amount_pending($due_id)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status = 2 and a.approv_reject_status_change = 1 and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }


    public function total_other_dues_amount_approved($due_id)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.imposed_from = ? and a.pay_status = 2 and a.approv_reject_status_change = 2 and a.is_deleted = 0";
        $query = $this->db->query($sql,array($due_id));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }

    


    public function total_other_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.admn_no = ?";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }


    public function current_other_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.pay_status IN (0,2) and a.approv_reject_status_change IN (0,3) and a.admn_no = ?"; // 
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }


    public function pending_other_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.pay_status = 2 and a.approv_reject_status_change = 1 and a.admn_no = ?";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }
    else
    {
        return false;
    }

    }


    public function approved_other_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.due_amt) as other_amount from no_dues_lists a where a.pay_status = 2 and a.approv_reject_status_change = 2";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        $details = $query->result_array();
        if($details[0]['other_amount'] == '')
        {
            $details_amount = 0;
        }

        else
        {
            $details_amount = $details[0]['other_amount'];
        }

        return $details_amount;
    }

    else
    {
        return false;
    }


    }

    public function approved_hostel_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status = 'success' and b.status = 'Approved'";
        $query = $this->db->query($sql);
        if($query != false)
        {
        $details = $query->result_array();
        return $details[0]['hostel_amount'];
        }
        else
        {
            return false;
        }

    }

    public function pending_hostel_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status = 'success' and b.status = 'Pending'";
        $query = $this->db->query($sql);
        if($query != false)
        {
        $details = $query->result_array();
        return $details[0]['hostel_amount'];
        }
        else
        {
            return false;
        }


    }

    public function current_hostel_dues_amount_student_wise($admn_no)
    {

        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a inner join no_dues_hs_payment b on a.id = b.hs_no_dues_id where b.payment_status IN ('no_action','success') and b.status IN ('0','Rejected')";
        $query = $this->db->query($sql);
        if($query != false)
        {
        $details = $query->result_array();
        return $details[0]['hostel_amount'];
        }
        else
        {
            return false;
        }

    }

    

    public function total_hostel_dues_amount_student_wise($admn_no)
    {
        $sql = "select SUM(a.inventory_amount) as hostel_amount from no_dues_hs_details a";
        $query = $this->db->query($sql);
        if($query != false)
        {
        $details = $query->result_array();
        return $details[0]['hostel_amount'];
        }
        else
        {
            return false;
        }


    }

    

    public function get_total_hostel_dues_current($admn_no)
    {
        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        //echo $this->db->last_query($query); //die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

            $sum = 0;

        }

        //echo $sum;

        return $sum;
    }

    else {
        
        return false;
    }

    }


    public function get_total_hostel_dues_current_by_hostel($admn_no,$hostel_name,$session_year,$session)
    {
        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no = ? and a.hostel_name = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$session_year,$session));
        if($query != false)
        {
        //echo $this->db->last_query($query); //die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

            $sum = 0;

        }

        //echo $sum;

        return $sum;
    }
    else
    {
        return false;
    }

    }

    public function get_total_hostel_dues_current_by_hostel_block($admn_no,$hostel_name,$block_name,$session_year,$session)
    {
        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status IN ('no_action','success') and c.status IN ('0','Rejected') and a.admn_no = ? and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$block_name,$session_year,$session));
        if($query != false)
        {
        //echo $this->db->last_query($query); //die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $sum;

        return $sum;
    }

    else
    {
        return false;
    }

    }


    public function get_total_hostel_dues_pending($admn_no)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'pending' and a.admn_no = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

            $sum = 0;

        }

        //echo $sum;

        return $sum;
    }
    else
    {
        return false;
    }

    }


    public function get_total_hostel_dues_pending_by_hostel($admn_no,$hostel_name,$session_year,$session)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'pending' and a.admn_no = ? and a.hostel_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name));
        if($query != false)
        {
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $sum;

        return $sum;

    }

    else
    {
        return false;
    }

    }

    public function get_total_hostel_dues_pending_by_hostel_block($admn_no,$hostel_name,$block_name,$session_year,$session)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'pending' and a.admn_no = ? and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$block_name,$session_year,$session));

        if($query != false)
        {
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $sum;

        return $sum;

    }

    else
    {
        return false;
    }

    }

    public function get_total_hostel_dues_approved($admn_no)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

            $sum = 0;

        }

        //echo $sum;

        return $sum;

    }

    public function get_total_hostel_dues_approved_by_hostel($admn_no,$hostel_name,$session_year,$session)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no = ? and a.hostel_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$session_year,$session));
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $sum;

        return $sum;

    }


    public function get_total_hostel_dues_approved_by_hostel_block($admn_no,$hostel_name,$block_name,$session_year,$session)
    {

        $sum = 0;
        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id inner join no_dues_hs_payment c on c.hs_no_dues_id = b.id where c.payment_status = 'success' and c.status = 'Approved' and a.admn_no = ? and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query($query); die();
        $details = $query->result_array();
        //echo count($details); 
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $sum;

        return $sum;

    }

    public function get_total_hostel_due_by_hostel($admn_no,$hostel_name,$session_year,$session)
    {

        $sum = 0;

        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no = ? and a.hostel_name = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

    
        return $sum;

        

    }

    public function get_total_hostel_due_by_hostel_block($admn_no,$hostel_name,$block_name,$session_year,$session)
    {

        $sum = 0;

        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no = ? and a.hostel_name = ? and a.block = ? and a.session_year = ? and a.session_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$hostel_name,$block_name,$session_year,$session));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

         }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        return $sum;

        

    }

    

    public function get_total_hostel_dues($admn_no)
    {

        $sum = 0;

        $sql = "select * from no_dues_hs_individual a inner join no_dues_hs_details b on a.id = b.assign_hs_no_dues_id where a.admn_no = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        //echo $this->db->last_query(); die();
        $details = $query->result_array();
        if(count($details) > 1)
        {

         foreach($details as $values)
         {

             $sum +=  $values['inventory_amount'];

        }

        

        }

        else if(count($details) == 1)
        {

            $sum = $details[0]['inventory_amount'];

        }

        else
        {

        }

        //echo $

        //echo $sum;

        return $sum;

        

    }


    public function get_total_other_dues($admn_no)
    {
        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and `is_deleted` = 0";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        //echo $this->db->last_query();
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

        }

        return $sum;

    }

    else
    {
        return false;
    }

    }


    

    public function get_other_dues($admn_no,$due_id)
    {
        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and imposed_from = ? and `is_deleted` = 0";
        $query = $this->db->query($sql,array($admn_no,$due_id));
        //echo $this->db->last_query(); die();
        if($query != false)
        {

        //echo 'entered'; exit;
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

            $sum = 0;

        }

        //echo 'sum'.$sum; exit;

        return $sum;

    }

    else
    {
        //echo 'entered2'; exit;
        return false;
    }

    }


    public function select_hostel_data($table, $order_by = "name", $value = "") {

        $this->db->select("name");
        $this->db->from($table);
        $this->db->where("is_deleted", 0);
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        $return_data = $this->db->get()->result_array();
        $opt_str = "";
        if (!$value) {
            $opt_str .= '<option selected="selected" value=""> Select  Value </option>';
        } else {
            $opt_str .= '<option value=""> Select  Value </option>';
        }
        if (!empty($return_data)) {
            foreach ($return_data as $return_data_value) {


                if($value == $return_data_value["name"])
                {
                
                
               // $data .= '<option value="' . $return_data_value["id"] . '" selected>' . $return_data_value["block_name"] . '</option>';
                    $opt_str .= '<option ';
                    $opt_str .= 'value="' . $return_data_value["name"] . '" selected>' . $return_data_value["name"] . '</option>';

                }

                else
                {

                   // $data .= '<option value="' . $return_data_value["id"] . '">' . $return_data_value["block_name"] . '</option>';
                    $opt_str .= '<option ';
                    $opt_str .= 'value="' . $return_data_value["name"] . '">' . $return_data_value["name"] . '</option>';

                }

                // if (!empty($return_data_value["name"])) {

                //      if (($value) && ($value == $return_data_value["name"])) {
                //         $opt_str .= 'selected="selected" ';
                //     }
                //     $opt_str .= '<option ';
                //     $opt_str .= 'value="' . $return_data_value["name"] . '">' . $return_data_value["name"] . '</option>';
                // }
            }
        }
        return $opt_str;
    }

    public function get_list_dues_based_on_hostel($session_year,$session,$hostel_name){

        $sql = "select * from `no_dues_hs_individual` a inner join `no_dues_hs_details` b on a.id = b.assign_hs_no_dues_id where a.session_year = ? and a.session_name = ? and a.hostel_name = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name));
        //echo $this->db->last_query(); //die();
        return $query->result_array();

    }

    public function get_list_dues_based_on_hostel_by_block($session_year,$session,$hostel_name,$block_name){

        $sql = "select * from `no_dues_hs_individual` a inner join `no_dues_hs_details` b on a.id = b.assign_hs_no_dues_id where a.session_year = ? and a.session_name = ? and a.hostel_name = ? and a.block = ? and a.is_deleted = 0";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name,$block_name));
        // echo $this->db->last_query(); die();
        return $query->result_array();

    }


    public function not_vacanted_hostel_list($session_year,$session,$hostel_name,$block_name){

        $sql = "select DISTINCT admn_no , student_name , hostel_name , session_name , session_year , room_no , block from `no_dues_hs_not_vacanted` where session_year = ? and session_name = ? and hostel_name = ? and block = ? and hostel_not_vacant = 1 and is_deleted = 0";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name,$block_name));
        //echo $this->db->last_query($query); die();
        
        return $query->result_array();

    }

    public function check_not_vacanted_hostel_list($admn_no,$session_year,$session,$hostel_name,$block_name){

        $sql = "select * from `no_dues_hs_not_vacanted` where session_year = ? and session_name = ? and hostel_name = ? and block = ? and hostel_not_vacant = 1 and admn_no = ? and is_deleted = 0";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name,$block_name,$admn_no));
        //echo $this->db->last_query($query); die();
        
        return $query->num_rows();

    }

    public function not_vacanted_hostel_list_general()
    {

        $sql = "select DISTINCT admn_no , student_name , hostel_name , session_name , session_year , room_no , block from `no_dues_hs_not_vacanted` where hostel_not_vacant = 1 and is_deleted = 0";
        $query = $this->db->query($sql);
        return $query->result_array();

    }


    public function not_vacanted_hostel_list_by_hostel($session_year,$session,$hostel_name){

        $sql = "select DISTINCT admn_no , student_name , hostel_name , session_name , session_year , room_no , block from `no_dues_hs_not_vacanted` where session_year = ? and session_name = ? and hostel_name = ? and hostel_not_vacant = 1 and is_deleted = 0";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name));
        return $query->result_array();

    }

    public function check_not_vacanted_hostel_list_by_hostel($admn_no_dues,$session_year,$session,$hostel_name){

        $sql = "select * from `no_dues_hs_not_vacanted` where session_year = ? and session_name = ? and hostel_name = ? and hostel_not_vacant = 1 and admn_no = ? and is_deleted = 0 GROUP BY admn_no";
        $query = $this->db->query($sql,array($session_year,$session,$hostel_name,$admn_no_dues));
        //echo $query->num_rows(); exit;
        return $query->num_rows();

    }

    public function get_block_name($id) {

        $sql = "select a.block_name from `hs_hostel_details` a where id = ?";
        $query = $this->db->query($sql,array($id));
        $block_name = $query->result_array();
        return $block_name[0]['block_name'];
    }


    public function get_other_dues_current($admn_no,$due_type)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and imposed_from = ? and pay_status IN (0,2) and approv_reject_status_change IN (0,3) and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$due_type));
        //echo $this->db->last_query();
        if($query != false)
        {
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

            $sum = 0;

        }

        return $sum;

    }

    else
    {
        return false;
    }

        


    }


    public function get_total_other_dues_current($admn_no)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and pay_status IN (0,2) and approv_reject_status_change IN (0,3) and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        //echo $this->db->last_query();
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

        }

        return $sum;

    }

    else

    {
        return false;
    }

        


    }

    public function get_other_dues_pending($admn_no,$due_type)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and imposed_from = ? and pay_status = 2 and approv_reject_status_change = 1 and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$due_type));
        //echo $this->db->last_query();
        if($query != false)
        {
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

            $sum = 0;

        }

        return $sum;
    }

    else
    {
        return false;
    }

    }


    public function get_total_other_dues_pending($admn_no)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and pay_status = 2 and approv_reject_status_change = 1 and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));

        if($query != false)
        {
        //echo $this->db->last_query();
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

        }

        return $sum;

    }

    else
    {
        return false;
    }

    }


    public function get_other_dues_approved($admn_no,$due_type)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and imposed_from = ? and pay_status = 2 and approv_reject_status_change = 2 and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no,$due_type));
        //echo $this->db->last_query();
        if($query != false)
        {
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

            $sum = 0;

        }

        return $sum;
    }

    else
    {
        return false;
    }

    }

    public function get_total_other_dues_approved($admn_no)
    {


        $sum = 0;
        $sql = "select * from `no_dues_lists` where admn_no = ? and pay_status = 2 and approv_reject_status_change = 2 and is_deleted = 0";
        $query = $this->db->query($sql,array($admn_no));
        if($query != false)
        {
        //echo $this->db->last_query();
        $other_no_dues = $query->result_array();
        if(count($other_no_dues) > 1)
        {

         foreach($other_no_dues as $values)
         {

             $sum +=  $values['due_amt'];

        }

        

        }

        else if(count($other_no_dues) == 1)
        {

            $sum = $other_no_dues[0]['due_amt'];

        }

        else
        {

        }

        return $sum;
    }

    else
    {
        return false;
    }

    }


    public function get_current_specific_start_date(){
		$ts=$this->curr_time_stamp();
		$session_year_curr=$this->get_session($ts);
		$auth="admin";
		$res=$this->db->query("SELECT start_date from no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status != 2 and capture_specific_stu_id = 0")->result_array();
		if(!$res)return 10;
		return $res[0]['start_date'];
		//return $res->result_array();
    }
    
    public function get_details_id_start($id)
    {

        $sql = "select * from no_dues_capture_specific_student_request where id = ?";
        $query = $this->db->query($sql,array($id));
        //echo $this->db->last_query(); die();
        return $query->result_array();

    }


    public function start_no_dues_specific_student_admin($start_date,$end_date,$id,$admn_no)
    {
        $s=0;
		$access_to="admin";
		$current_date=date('Y-m-d');
		$ts=$this->curr_time_stamp();
		$session_year=$this->get_session($ts);
		$user_id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();
		// if($start_date <= $current_date && $end_date >= $current_date) //$s=1;
		// // $q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
        // // 		"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
        // {

            $s = 1;
            //$started_on = $get_details_edit[0]['no_dues_portal_started_on'];
            $started_on = $ts;
            $started_by = $user_id;
            $stopped_on = 'No Action';
            $stopped_by = 'No Action';



        // }

        // else

        // {


        //     $s = 0;
        //     $started_on = 'Not Yet Started';
        //     $started_by = 'Not Yet Started';
        //     $stopped_on = 'No Action';
        //     $stopped_by = 'No Action';


        // }
        
        
        $get_details_id_start =  $this->get_details_id_start($id);

        $datanew = array(

            'status_previous' => $get_details_id_start['0']['status'],
            'specific_student_request_id' => $id,
            'previous_start_date' => $get_details_id_start['0']['date_start'],
            'previous_end_date' => $get_details_id_start['0']['date_end'],
            'present_start_date' => $start_date,
            'previous_access_to' => $get_details_id_start['0']['access_to'],
            'present_access_to' => $access_to,
            'status_present' => $s,
            'present_end_date' => $end_date,
            'started_on' => $started_on,
            'started_by' => $started_by,
            'stopped_on' => $stopped_on,
            'stopped_by' => $stopped_by,
            'modified_on' => $ts,
            'modified_by' => $this->session->userdata['id']


       );

       //print_r($datanew); //exit;

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();
        
        
        
        $data = array(

           'session_year' => $session_year,
           'date_start' => $start_date,
           'date_end' => $end_date,
           'status' => $s,
           'access_to' => $access_to,
           'no_dues_portal_started_on' => $started_on,
           'no_dues_portal_started_by' => $started_by,
           'no_dues_portal_stopped_on' => '',
           'no_dues_portal_stopped_by' => '',
           'last_modified_date' => date('d-m-Y H:i:s'),
           'last_modified_by' => $this->session->userdata['id']

        );

        $this->db->where('id', $id);
        $this->db->update('no_dues_capture_specific_student_request', $data);
        //echo $this->db->last_query(); die();

        $datamain = array(

            'access_to' => $access_to,
            'access_to_admn_no' => $admn_no,
            'capture_specific_stu_id' => $id,
            'session_year' => $session_year,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'started_by' => $this->session->userdata['id'],
            'timestamp' => $ts,
            'status' => 1

        );

        $this->db->insert('no_dues_start', $datamain);



    }
    
    
    public function start_no_dues_specific_student($start_date,$end_date,$id)
    {
        $s=0;
		$access_to="stu";
		$current_date=date('Y-m-d');
		$ts=$this->curr_time_stamp();
		$session_year=$this->get_session($ts);
		$user_id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();
		// if($start_date <= $current_date && $end_date >= $current_date) //$s=1;
		// // $q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
        // // 		"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
        // {

            $s = 1;
            //$started_on = $get_details_edit[0]['no_dues_portal_started_on'];
            $started_on = $ts;
            $started_by = $user_id;
            $stopped_on = 'No Action';
            $stopped_by = 'No Action';



        // }

        // else

        // {


        //     $s = 0;
        //     $started_on = 'Not Yet Started';
        //     $started_by = 'Not Yet Started';
        //     $stopped_on = 'No Action';
        //     $stopped_by = 'No Action';


        // }
        
        
        $get_details_id_start =  $this->get_details_id_start($id);

        $datanew = array(

            'status_previous' => $get_details_id_start['0']['status'],
            'specific_student_request_id' => $id,
            'previous_start_date' => $get_details_id_start['0']['date_start'],
            'previous_end_date' => $get_details_id_start['0']['date_end'],
            'present_start_date' => $start_date,
            'previous_access_to' => $get_details_id_start['0']['access_to'],
            'present_access_to' => $access_to,
            'status_present' => $s,
            'present_end_date' => $end_date,
            'started_on' => $started_on,
            'started_by' => $started_by,
            'stopped_on' => $stopped_on,
            'stopped_by' => $stopped_by,
            'modified_on' => $ts,
            'modified_by' => $this->session->userdata['id']


       );

       //print_r($datanew); //exit;

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();
        
        
        
        $data = array(

           'session_year' => $session_year,
           'date_start' => $start_date,
           'date_end' => $end_date,
           'status' => $s,
           'access_to' => $access_to,
           'no_dues_portal_started_on' => $started_on,
           'no_dues_portal_started_by' => $started_by,
           'no_dues_portal_stopped_on' => '',
           'no_dues_portal_stopped_by' => '',
           'last_modified_date' => date('d-m-Y H:i:s'),
           'last_modified_by' => $this->session->userdata['id']

        );

        $this->db->where('id', $id);
        $this->db->update('no_dues_capture_specific_student_request', $data);
        //echo $this->db->last_query(); die();

    }

    public function get_details_id($id){

        $sql = "select * from no_dues_capture_specific_student_request where status = 1 and id = ?";
        $query = $this->db->query($sql,array($id));
        //echo $this->db->last_query(); die();
        return $query->result_array();
    }

    public function stop_no_dues_specific_student_admin($id,$i){  //stop for admin

        $current_date=date('Y-m-d');
		$ts=$this->curr_time_stamp();
		$session_year=$this->get_session($ts);
        $user_id=$this->session->userdata['id'];

        $get_details = $this->get_details_id($id);

        //print_r($get_details);
        
        $datanew = array(

            'status_previous' => 1,
            'specific_student_request_id' => $id,
            'previous_start_date' => $get_details['0']['date_start'],
            'previous_end_date' => $get_details['0']['date_end'],
            'previous_start_date' => $get_details['0']['date_start'],
            'previous_access_to' => $get_details['0']['access_to'],
            'present_access_to' => '',
            'status_present' => 0,
            'present_end_date' => '',
            'started_on' => $get_details[0]['no_dues_portal_started_on'],
            'started_by' => $get_details[0]['no_dues_portal_started_by'],
            'stopped_on' => $ts,
            'stopped_by' => $user_id,
            'modified_on' => $ts,
            'modified_by' => $this->session->userdata['id']


       );

       //print_r($datanew); //exit;

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();

        $data = array(

           'session_year' => $session_year,
           'date_start' => '',
           'date_end' => '',
           //'status' => $s,
           'no_dues_portal_started_on' => '',
           'no_dues_portal_started_by' => '',
           'status' => 0,
           'access_to' => '',
           'no_dues_portal_stopped_on' => $ts,
           'no_dues_portal_stopped_by' => $user_id,
           'last_modified_date' => date('d-m-Y H:i:s'),
           'last_modified_by' => $this->session->userdata['id']


           
 
         );
 
         $this->db->where('id', $id);
         $this->db->update('no_dues_capture_specific_student_request', $data);


         $datanew = array(

            'status' => 2
         );

         $this->db->where('capture_specific_stu_id', $id);
         $this->db->where('status', 1);
         $this->db->update('no_dues_start', $datanew);

         //echo $this->db->last_query();

    }
    
    public function stop_no_dues_specific_student($id,$i){     // stop for student

        $current_date=date('Y-m-d');
		$ts=$this->curr_time_stamp();
		$session_year=$this->get_session($ts);
        $user_id=$this->session->userdata['id'];

        $get_details = $this->get_details_id($id);

        //print_r($get_details);
        
        $datanew = array(

            'status_previous' => 1,
            'specific_student_request_id' => $id,
            'previous_start_date' => $get_details['0']['date_start'],
            'previous_end_date' => $get_details['0']['date_end'],
            'previous_access_to' => $get_details['0']['access_to'],
            'present_access_to' => '',
            'present_start_date' => '',
            'status_present' => 0,
            'present_end_date' => '',
            'started_on' => $get_details[0]['no_dues_portal_started_on'],
            'started_by' => $get_details[0]['no_dues_portal_started_by'],
            'stopped_on' => $ts,
            'stopped_by' => $user_id,
            'modified_on' => $ts,
            'modified_by' => $this->session->userdata['id']


       );

       //print_r($datanew); //exit;

       $this->db->insert('no_dues_specific_student_portal_changed_logs', $datanew);
       //echo $this->db->last_query(); die();

        $data = array(

           'session_year' => $session_year,
           'date_start' => '',
           'date_end' => '',
           //'status' => $s,
           'no_dues_portal_started_on' => '',
           'no_dues_portal_started_by' => '',
           'status' => 0,
           'access_to' => '',
           'no_dues_portal_stopped_on' => $ts,
           'no_dues_portal_stopped_by' => $user_id,
           'last_modified_date' => date('d-m-Y H:i:s'),
           'last_modified_by' => $this->session->userdata['id']


           
 
         );
 
         $this->db->where('id', $id);
         $this->db->update('no_dues_capture_specific_student_request', $data);



	}


    // public function stop_no_dues_specific_student($start_date,$end_date,$id)
    // {
    //     $s=0;
	// 	$access_to="stu";
	// 	$current_date=date('Y-m-d');
	// 	$ts=$this->curr_time_stamp();
	// 	$session_year=$this->get_session($ts);
	// 	$user_id=$this->session->userdata['id'];
	// 	$ts =$this->curr_time_stamp();
	// 	if($start_date <= $current_date && $end_date >= $current_date) $s=1;
	// 	// $q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
    //     // 		"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
        
    //     $data = array(

    //        'session_year' => $session_year,
    //        'date_start' => $start_date,
    //        'date_end' => $end_date,
    //        'status' => $s,
    //        'no_dues_portal_started_on' => $ts,
    //        'no_dues_portal_started_by' => $user_id,

    //     );

    //     $this->db->where('id', $id);
    //     $this->db->update('no_dues_capture_specific_student_request', $data);
    //     //echo $this->db->last_query(); die();

    // }


}


?>