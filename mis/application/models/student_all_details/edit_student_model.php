<?php
  
class Edit_student_model extends CI_Model { 

    function __construct() {
        parent::__construct();
    }

        
    function check_user_details($id){
        $sql="select a.* from user_details a where a.id=?";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {  
            return FALSE;
        }
    }


    function get_basic_details($id)
    {
       
        $sql="SELECT a.id, a.first_name,a.middle_name,a.last_name,a.sex, 
b.father_name,b.mother_name,b.mobile_no,a.email,a.dept_id,c.name AS dname,d.branch_id,a.photopath,
a.dob,a.category,a.physically_challenged
FROM user_details a
INNER JOIN user_other_details b ON b.id=a.id
INNER JOIN departments c ON c.id=a.dept_id
inner join stu_academic d on d.admn_no=a.id
WHERE a.id=?";

        
        $query = $this->db->query($sql,array($id));

       
        if ($this->db->affected_rows() >= 0) {
            return $query->row();
        } else {
            return false;
        }
    }
     
    function update_record($all_text,$tbl_name,$admn_no,$col1,$col2)
    {

        $query = $this->db->query("UPDATE $tbl_name SET $col1='".$all_text."' WHERE $col2='".$admn_no."'");
            
        if($this->db->affected_rows()){
          return true;
        }
        else{
          return false;
        }

    }
	function update_record_address($all_text,$tbl_name,$admn_no,$col1,$col2)
    {

        $query = $this->db->query("UPDATE $tbl_name SET $col1='".$all_text."' WHERE $col2='".$admn_no."' and type='permanent'");
            
        if($this->db->affected_rows()){
          return true;
        }
        else{
          return false;
        }

    }
    function insert_edited_value($data){
        if ($this->db->insert('stu_edit_backup', $data))
        //return $this->db->insert_id();
            return TRUE;
        else
            return FALSE;

    }
	
	function get_permanent_address($id)
    {
       
        $sql="SELECT a.* from user_address a WHERE a.id=? AND a.`type`='permanent'";

        
        $query = $this->db->query($sql,array($id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
	
	//06-07-2021
	function get_education($admn_no)
	{
		$sql="SELECT * from stu_prev_education WHERE admn_no=?";

        
        $query = $this->db->query($sql,array($admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
	}
	function update_education_table($exam,$specialization,$institute,$year,$grade,$division,$row,$admn_no)
	{
		$sql="update stu_prev_education SET exam=?,specialization=?,institute=?,YEAR=?,grade=?,division=? WHERE sno=? AND admn_no=?  ";

        $query = $this->db->query($sql,array($exam,$specialization,$institute,$year,$grade,$division,$row,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
		
	}
	function get_data_from_stu_prev_education($row,$admn_no)
	
	{
		$sql="SELECT * from stu_prev_education WHERE sno=? AND admn_no=?";

        
        $query = $this->db->query($sql,array($row,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
	}
	
	function insert_stu_prev_education_olddata($data)
	{
		if ($this->db->insert('stu_prev_education_olddata', $data))
        //return $this->db->insert_id();
            return TRUE;
        else
            return FALSE;
	}
	


}

?>