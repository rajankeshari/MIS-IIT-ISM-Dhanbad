<?php
/**
 * Created by PhpStorm.
 * User: Jay Doshi
 * Date: 25/5/15
 * Time: 11:01 AM
 */

class No_dues_query_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}

	public function curr_time_stamp(){
        $curr_time = date_create();
        $ts = date_format($curr_time, 'Y-m-d H:i:s');
        return $ts;
    }

    public function initialise($admn_no,$sess){

        $q = "SELECT id FROM no_dues_current WHERE admn_no = '$admn_no' ";
        $res= $this->db->query($q)->result_array();
        //print_r($res);
        if(count($res) == 0)
           return 1;
        else
            return 0;
    }

    public function get_no_dues_dept_list(){
        $ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $q = "SELECT * FROM no_dues_dept where session_year='$sess' order by `part`,`dept_id`";
        $res = $this->db->query($q)->result_array();
        $r = array();
        for ($i = 0; $i < count($res); $i++){
            $temp=$res[$i]['dept_id'];
            $p = $this->get_dept_name($temp);
            $r[$i] = array();
            $r[$i]['dept_id'] = $res[$i]['dept_id'];
            $r[$i]['part'] = $res[$i]['part'];
            // $r[$i]['status'] = $res[$i]['status'];
            if ($p != 'undf'){
                $r[$i]['dept_name'] = $p;
            }
            else{
                $r[$i]['dept_name'] = $res[$i]['dept_id'];
            } 
        }
        return $r;
    }
     public function get_no_dues_dept_list_2(){
        $ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $q = "SELECT * FROM no_dues_dept__for_dropouts where session_year='$sess' order by `part`,`dept_id`";
        $res = $this->db->query($q)->result_array();
        $r = array();
        for ($i = 0; $i < count($res); $i++){
            $temp=$res[$i]['dept_id'];
            $p = $this->get_dept_name($temp);
            $r[$i] = array();
            $r[$i]['dept_id'] = $res[$i]['dept_id'];
            $r[$i]['part'] = $res[$i]['part'];
            // $r[$i]['status'] = $res[$i]['status'];
            if ($p != 'undf'){
                $r[$i]['dept_name'] = $p;
            }
            else{
                $r[$i]['dept_name'] = $res[$i]['dept_id'];
            } 
        }
        return $r;
    }
    public function get_dept_name($dept_id){
        $q = "SELECT name FROM departments WHERE id LIKE '$dept_id' ";
        $res = $this->db->query($q)->result_array();
        if (count($res))
            return $res[0]['name'];
        return "undf";
    }

    // public function set_status($admn_no,$sess)
    // {
    //     $ts =$this->curr_time_stamp();
    //     $q = "INSERT INTO no_dues_current (admn_no,session_year,timestamp, status) VALUES ('$admn_no','$sess','$ts','1')";
    //     $res= $this->db->query($q);
    // }
    public function get_session($ts){
        
        $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){
            $p_year = strval((int)$year);
            $year = $p_year +1;

        }
        else
        $p_year = strval((int)$year - 1);
        return $p_year.'-'.$year;
    }
       public function get_sessionyear(){
        $ts=$this->curr_time_stamp();
        $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){
            $p_year = strval((int)$year);
            $year = $p_year +1;

        }
        else
        $p_year = strval((int)$year - 1);
        return $p_year.'-'.$year;
    }

    // public function get_no_dues_status(){
    //     return $this->db->query("SELECT status FROM no_dues_start WHERE name = 'initialise'")->result_array();
    // }
    public function get_course_name($course_id){
        $q = "SELECT name FROM courses WHERE id LIKE '$course_id'";
        $res = $this->db->query($q)->result_array();
        return $res[0]['name'];
    }
    public function get_student_status($admn_no,$sess){
    	$q = "SELECT admn_no,dept_id, due_amt,due_list, remarks, pay_status FROM no_dues_lists WHERE admn_no = '$admn_no' AND session_year = '$sess'";
    	$res = $this->db->query($q)->result_array();
        return $res;
    }
    // public function get_student_status_2($admn_no,$sess){
    //     $q = "SELECT admn_no,dept_id, due_amt,due_list, remarks, pay_status FROM no_dues_lists_for_dropouts WHERE admn_no = '$admn_no' AND session_year = '$sess'";
    //     $res = $this->db->query($q)->result_array();
    //     return $res;
    // }


    public function get_dept($admn_no){
        $sem = $this->session->userdata['semester'];
        $course_id = $this->session->userdata['course_id'];
        $ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $q_l = "SELECT duration FROM courses WHERE id = '$course_id'";
        $leave_year = $this->db->query($q_l)->result_array();

        $dur = (int)$leave_year[0]['duration'] * 2;

        if ((int)$sem == $dur){
            /*
             * for the students that are attending their last semesters.
             */
            $q_dept = "SELECT dept_id FROM no_dues_dept where session_year='$sess' ";
        }
        else{
            /*
             * for the students that are not in their final semesters.
             */
            $q_dept = "SELECT dept_id FROM no_dues_dept WHERE valid_for = '0' and session_year='$sess' ";
        }
        $dept = $this->db->query($q_dept)->result_array();
        $res = array();
        for ($i = 0; $i < count($dept); $i++){
            $type = $this->get_dept_type($dept[$i]['dept_id']);
            
            if ($type == 'academic' && $dept[$i]['dept_id'] == $this->session->userdata['dept_id']){
                array_push($res, array("dept_id" => $this->session->userdata['dept_id']));
            }
            if ($type != 'academic'){
                array_push($res, array("dept_id"=>$dept[$i]['dept_id']));
            }
        }
        return $res;

    }
///for dropouts
    public function get_dept_2($admn_no){
        $sem = $this->session->userdata['semester'];
        $course_id = $this->session->userdata['course_id'];
        $ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $q_l = "SELECT duration FROM courses WHERE id = '$course_id'";
        $leave_year = $this->db->query($q_l)->result_array();

        $dur = (int)$leave_year[0]['duration'] * 2;

        if ((int)$sem == $dur){
            /*
             * for the students that are attending their last semesters.
             */
            $q_dept = "SELECT dept_id FROM no_dues_dept__for_dropouts where session_year='$sess' ";
        }
        else{
            /*
             * for the students that are not in their final semesters.
             */
            $q_dept = "SELECT dept_id FROM no_dues_dept__for_dropouts WHERE valid_for = '0' and session_year='$sess' ";
        }
        $dept = $this->db->query($q_dept)->result_array();
        $res = array();
        for ($i = 0; $i < count($dept); $i++){
            $type = $this->get_dept_type($dept[$i]['dept_id']);
            
            if ($type == 'academic' && $dept[$i]['dept_id'] == $this->session->userdata['dept_id']){
                array_push($res, array("dept_id" => $this->session->userdata['dept_id']));
            }
            if ($type != 'academic'){
                array_push($res, array("dept_id"=>$dept[$i]['dept_id']));
            }
        }
        return $res;

    }


    public function get_am($admn, $dept_id){
        $q = "SELECT dept_id, due_amt, remarks FROM no_dues_lists WHERE admn_no = '$admn' AND dept_id = '$dept_id'";
        $result = $this->db->query($q)->result_array();
        return $result;
    }

    // public function get_am_2($admn, $dept_id){
    //     $q = "SELECT dept_id, due_amt, remarks FROM no_dues_lists_for_dropouts WHERE admn_no = '$admn' AND dept_id = '$dept_id'";
    //     $result = $this->db->query($q)->result_array();
    //     return $result;
    // }

    public function get_dept_type($d){
        $q = "SELECT type FROM departments WHERE id = '$d'";
        $res = $this->db->query($q)->result_array();
        if (count($res) != 0){
            return $res[0]['type'];
        }
        return 'undefined';
    } 

    public function change_status($admn_no,$sess,$dept_id){
        $q = "UPDATE no_dues_lists SET pay_status = 2 WHERE admn_no = '$admn_no' AND session_year = '$sess' AND dept_id = '$dept_id'";
        $result = $this->db->query($q);
        $qry = "SELECT pay_status FROM no_dues_lists WHERE admn_no = '$admn_no' AND session_year  = '$sess'";
        $result1 = $this->db->query($qry)->result_array();
        //print_r($result1);
        if($result1[0]['pay_status'] == 2)
            return 1;
        else
            return 0;

    }
// ////for dropouts
//     public function change_status_2($admn_no,$sess,$dept_id){
//         $q = "UPDATE no_dues_lists_for_dropouts SET pay_status = 2 WHERE admn_no = '$admn_no' AND session_year = '$sess' AND dept_id = '$dept_id'";
//         $result = $this->db->query($q);
//         $qry = "SELECT pay_status FROM no_dues_lists_for_dropouts WHERE admn_no = '$admn_no' AND session_year  = '$sess'";
//         $result1 = $this->db->query($qry)->result_array();
//         //print_r($result1);
//         if($result1[0]['pay_status'] == 2)
//             return 1;
//         else
//             return 0;

//     }

    public function check_status($admn_no,$sess,$dept_id){
        $qry = "SELECT pay_status FROM no_dues_lists WHERE admn_no = '$admn_no' AND session_year  = '$sess' AND dept_id= '$dept_id'";
        $result1 = $this->db->query($qry)->result_array();
        return $result1[0]['pay_status'];
    }
 //public function check_status_2($admn_no,$sess,$dept_id){
    //     $qry = "SELECT pay_status FROM no_dues_lists_for_dropouts WHERE admn_no = '$admn_no' AND session_year  = '$sess' AND dept_id= '$dept_id'";
    //     $result1 = $this->db->query($qry)->result_array();
    //     return $result1[0]['pay_status'];
    // }

    public function print_data($admn_no,$sess,$dept_id){
        $q = "SELECT dept_id, due_amt,due_list, remarks,pay_status FROM no_dues_lists WHERE admn_no = '$admn_no' AND dept_id= '$dept_id' and change_status='0'";
        $res = $this->db->query($q)->result_array();
       // print_r($res);
        return $res;
    }

    // public function print_data_2($admn_no,$sess,$dept_id){
    //     $q = "SELECT dept_id, due_amt,due_list, remarks,pay_status FROM no_dues_lists_for_dropouts WHERE admn_no = '$admn_no' AND dept_id= '$dept_id' and change_status='0'";
    //     $res = $this->db->query($q)->result_array();
    //    // print_r($res);
    //     return $res;
    // }

    public function no_dues_submit_set(){
        $ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $admn_no = $this->session->userdata['id'];
        $q = "UPDATE no_dues_current SET status = 2 WHERE admn_no = '$admn_no' AND session_year = '$sess'";
        $this->db->query($q);
    }

     public function update_receipt_path_and_status($receipt_path, $id,$ref,$dateof){
       
        //$ts = $this->curr_time_stamp();
       // $sess = $this->get_session($ts);
        //echo $receipt_path;

        $date=explode('-', $dateof)[2]."-".explode('-', $dateof)[1]."-".explode('-', $dateof)[0];
         //dateofpayment
        $query = "UPDATE no_dues_lists SET receipt_path = '$receipt_path',dateofpayment='$date',receipt_no='$ref',pay_status='2' WHERE id = '$id'";
        $this->db->query($query);
       
    }

    // public function update_receipt_path($receipt_path, $dept_id){
    //     $admn_no = $this->session->userdata['id'];
    //     $ts = $this->curr_time_stamp();
    //     $sess = $this->get_session($ts);

    //     $q = "UPDATE no_dues_lists SET receipt_path = '$receipt_path' WHERE admn_no = '$admn_no' AND session_year = '$sess' AND dept_id = '$dept_id'";
    //     $this->db->query($q);
    // }
    public function get_account_no($admn_no)
    {
        $qry = "SELECT account_no from stu_other_details WHERE admn_no = '$admn_no'";
        $res= $this->db->query($qry)->result_array();
        return $res;
    }
    public function get_enroll_yr($admn_no)
    {

        $qry = "SELECT enrollment_year FROM stu_academic WHERE admn_no = '$admn_no'";

        $res = $this->db->query($qry)->result_array();
        return $res;
    }
    public function add_reference_number($id){
        $ref_no=uniqid();
        $val=1;
        //$q= "INSERT INTO no_dues_stu_ref_id(admn_no,ref_no,status) VALUES ('$id', '$ref_no','$val')";
        $q = "INSERT INTO no_dues_stu_ref_id (admn_no,ref_no,status) VALUES ('$id', '$ref_no','$val')";
                    
        $this->db->query($q);
    }
     public function add_reference_number_sem($id,$sem){
        $ref_no=uniqid();
        $val=1;
        //$q= "INSERT INTO no_dues_stu_ref_id(admn_no,ref_no,status) VALUES ('$id', '$ref_no','$val')";
        $q = "INSERT INTO no_dues_stu_ref_id_sem (admn_no,ref_no,status,sem) VALUES ('$id', '$ref_no','$val','$sem')";
                    
        $this->db->query($q);
    }
    public function get_reference_no($id){
        $q="SELECT ref_no from no_dues_stu_ref_id where admn_no='$id' and status=1";
        $res=$this->db->query($q)->result_array();
        if($res==NULL) return 10;
        else return $res[0]['ref_no'];
    }
       public function get_reference_no_sem($id,$sem){

        $q="SELECT ref_no from no_dues_stu_ref_id_sem where admn_no='$id' and sem='$sem' and status=1";
        $res=$this->db->query($q)->result_array();
        if($res==NULL) return 10;
        else return $res[0]['ref_no'];
    }
    // public function check_upload($dept_id)
    // {
    //     $qry= "SELECT status FROM no_dues_dept WHERE dept_id = '$dept_id' ";
    //     $res = $this->db->query($qry)->result_array();
    //     return $res;

    // }
    public function check_dues($admn_no,$sess,$dept_id)
    {
        $q = "SELECT pay_status FROM no_dues_lists WHERE admn_no = '$admn_no' AND session_year = '$sess' AND dept_id = '$dept_id'";
        $res = $this->db->query($q)->result_array();
        return $res;
    }
    public function check_submit($admn_no){
        $q = "SELECT submit_status From no_dues_current WHERE admn_no = '$admn_no'";
        $res = $this->db->query($q)->result_array();
        return $res;
    }
    public function reject_app_count($admn_no,$sess)
    {
        $q = "SELECT COUNT(*) FROM no_dues_lists WHERE admn_no = '$admn_no'  AND session_year = '$sess' AND pay_status = 1";
        $res =$this->db->query($q)->result_array();
        return $res;

    }
    // public function get_dept_name($dept_id)
    // {
    //     $q = "SELECT name FROM departments WHERE id = '$dept_id'";
    //     $res= $this->db->query($q)->result_array();
    //     //print_r($res);
    //     return $res;
    // }
     public function get_no_due_stu_list($admn_no)
    {
        $q = "SELECT * FROM no_dues_lists WHERE admn_no = '$admn_no' and change_status='0'";
        $res= $this->db->query($q)->result_array();
		
		
		for ($i = 0; $i<count($res); $i++){
			$res[$i]['dept_name'] = $this->get_dept_name($res[$i]['dept_id']);
			
		}
		
		
        //print_r($res);
        return $res;
    }



      public function get_no_due_stu_list_id($id)// for verification before uploading fine 
    {
        $q = "SELECT * FROM no_dues_lists WHERE id = '$id'";
        $res= $this->db->query($q)->result_array();
        //print_r($res);
        return $res;
    }

    public function check_submit_no_dues($admn_no,$sess)
    {
        $q ="SELECT status FROM no_dues_current WHERE admn_no ='$admn_no' AND session_year = '$sess'";
        $res =$this->db->query($q)->result_array();
        // echo "ye hai status";
        // print_r($res);
        return $res;
    }
    public function receipt_no($admn_no,$sess)
    {
        $q ="SELECT id FROM no_dues_current WHERE admn_no = '$admn_no' AND session_year = '$sess'";
        $res =$this->db->query($q)->result_array();
        return $res[0]['id'];
    }
    // public function get_auth($dept_id){
    //     $q = "SELECT auth_id FROM no_dues_dept WHERE dept_id = '$dept_id'";
    //     $res= $this->db->query($q)->result_array();
    //     return $res;
    // }
    public function get_id($auth_id){
        $q = "SELECT id FROM user_auth_types WHERE auth_id ='$auth_id'";
        $res= $this->db->query($q)->result_array();
        return $res;
    }
    public function match_id($id,$dept_id)
    {
        $q = "SELECT dept_id FROM user_details WHERE id = '$id'";
        $res= $this->db->query($q)->result_array();
      // print_r($res);
        if($res[0]['dept_id'] == $dept_id)
            return 1;
        else
            return 0;
    }


}