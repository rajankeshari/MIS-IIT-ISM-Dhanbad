<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Not_reported_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_user()
    {
       $query=$this->db->select('*')->from('users')->join('user_details', 'users.id=user_details.id')
       ->join('stu_academic', 'users.id=stu_academic.admn_no')->where('users.status','I')->get();
       if($query->num_rows() > 0)
        {
            $row=$query->result_array();
            return $row;
        }
        else
        {
            return false;
        }
    }

    function get_branch($branch_id)
	{
		$this->db->where('id',$branch_id);
		$query=$this->db->get('branches');
        $row=$query->row_array();
		return $row;
    }
    
    function get_course($course_id)
	{
		$this->db->where('id',$course_id);
		$query=$this->db->get('courses');
        $row=$query->row_array();
		return $row;
    }
    
    function get_department($dept_id)
	{
		$this->db->where('id',$dept_id);
		$query=$this->db->get('departments');
        $row=$query->row_array();
		return $row;
    }
    
    function insert_new_adm_repoting($new_adm_reporting)
	{
        $this->db->insert('new_adm_reporting',$new_adm_reporting);
        return TRUE;
    }

    function insert_new_adm_not_rpt_stu($new_adm_not_rpt_stu)
    {
        $this->db->insert('new_adm_not_rpt_stu',$new_adm_not_rpt_stu);
        return TRUE;
    }

    
    function update_users_status($admn_no)
	{
        $this->db->update('users',array('status' => 'A'),array('id' => $admn_no));
        return TRUE;
    }
    
    function check_counter_repoting($admn_no)
	{
        $this->db->where('admn_no',$admn_no);
		$query=$this->db->get('new_adm_reporting');
        if($query->num_rows() > 0)
        {
            $row=$query->row_array();
            return $row;
        }
        else
        {
            return false;
        }
        
    }
    
    function delete_stu_model($admn_no)
	{
        $this->db->where('id',$admn_no);
        $query=$this->db->get('users');
        if($query->num_rows() > 0)
        {
            //$this->config->base_url()  
            //$path = PUBPATH.'assets/images/student/'.$admn_no;
            $path = FCPATH.'assets/images/student/'.strtolower($admn_no);
            $this->load->helper("file"); // load the helper
            
            delete_files($path, true); // delete all files/folders
            rmdir($path); // Delete the folder     
            
            // Table 1
            $this->db->where('id', $admn_no);
            $this->db->delete('users');
            // Table 2
            $this->db->where('id', $admn_no);
            $this->db->delete('user_address');
            // Table 3
            $this->db->where('id', $admn_no);
            $this->db->delete('user_details');
            // Table 4
            $this->db->where('id', $admn_no);
            $this->db->delete('user_other_details');
            // Table 5
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_academic');
            // Table 6
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_admn_fee');
            // Table 7
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_details');
            // Table 8
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_enroll_passout');
            // Table 9
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_other_details');
            // Table 10
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_prev_certificate');
            // Table 11
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_prev_education');
            // Table 12
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('hs_assigned_student_room');
            // Table 13
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('fb_student_feedback');
            // Table 14
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('stu_prep_data');
            // Table 15
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('reg_regular_form');
            // Table 16
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('hs_student_allotment_list');
            // Table 17
            $this->db->where('admn_no', $admn_no);
            $this->db->delete('reg_regular_fee');
            

            return true;
        
        }
        else
        {
            return false;
        }
        
	}

}