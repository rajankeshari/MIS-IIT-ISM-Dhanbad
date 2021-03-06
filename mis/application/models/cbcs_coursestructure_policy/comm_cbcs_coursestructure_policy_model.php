<?php
class comm_cbcs_coursestructure_policy_model extends CI_Model{
	function __construct() {
        parent::__construct();
    }

    //get curriculam policy list..
    public function get_curriculam_policy(){
    	$result=$this->db->query("SELECT * FROM `cbcs_credit_points_policy` WHERE id in(SELECT distinct(cbcs_credit_points_policy_id) FROM `cbcs_curriculam_policy`)");
    	return $result->result();
    }

    //Save new data..
    public function save_cbcs_coursestructure($data){
    	if($this->db->insert('cbcs_comm_coursestructure_policy',$data))
            return true;
        else
            return false;
    }

    //Get list from database 
    public function get_cbcs_coursestructure_list(){
        $result=$this->db->query("SELECT A.*,concat(B.name,'(',UPPER(B.id),')') AS course,concat(C.wef,'(',C.course_id,')') AS c_policy,concat(D.name,'(',D.id,')') AS d_comp FROM `cbcs_comm_coursestructure_policy` A JOIN `cbcs_courses` B ON A.course_id=B.id left JOIN `cbcs_credit_points_policy` C ON A.cbcs_curriculam_policy_id=C.id left JOIN `cbcs_course_component` D ON A.course_component=D.id ORDER BY A.id");
        return $result->result();
    }


    //Update data..
    public function update_cbcs_coursestructure($data,$rowid){
        $query = $this->db->query("INSERT INTO cbcs_coursestructure_policy_backup SELECT * FROM cbcs_comm_coursestructure_policy WHERE id='$rowid'");
        $this->db->where('id', $rowid);
        if($this->db->update('cbcs_comm_coursestructure_policy',$data))
            return true;
        else
            return false;

    }

    //Delete data...
  /*  public function delete_coursestructure($id){
        $this->db->query("INSERT INTO cbcs_coursestructure_policy_backup SELECT * FROM cbcs_comm_coursestructure_policy WHERE id='$id'");
        $user_id=$this->session->userdata('id');
        $date=date('Y-m-d H:i:s');
        $this->db->query("UPDATE `cbcs_coursestructure_policy_backup` SET `action`='delete',`last_updated_by`='$user_id'");
        if($this->db->query("DELETE FROM cbcs_comm_coursestructure_policy WHERE id='$id'"))
            return TRUE;
        else
            return FALSE;
    }*/
	
	 public function delete_coursestructure($id){
        $this->db->query("INSERT INTO cbcs_comm_coursestructure_policy_backup SELECT * FROM cbcs_comm_coursestructure_policy WHERE id='$id'");
        $user_id=$this->session->userdata('id');
        $date=date('Y-m-d H:i:s');
        $this->db->query("UPDATE `cbcs_comm_coursestructure_policy_backup` SET `action`='delete',`last_updated_by`='$user_id'");
        if($this->db->query("DELETE FROM cbcs_comm_coursestructure_policy WHERE id='$id'"))
            return TRUE;
        else
            return FALSE;
    }
	

    //Check duplicate entry when we insert new data.
    public function check_duplicate_coursestructure($course,$sem,$ccp,$comp,$sequence,$status,$rowid){
        if($rowid == '' || $rowid == 0)
            $qu='';
        else
            $qu="AND `id` != '$rowid'";
        $result=$this->db->query("SELECT * FROM `cbcs_comm_coursestructure_policy` WHERE `course_id`='$course' AND `sem`='$sem' AND `cbcs_curriculam_policy_id`='$ccp' AND `course_component`='$comp' AND `sequence`='$sequence' AND `status`='$status' $qu");
        return $result->num_rows();
    }


    public function ajax_coursestructure_data($course,$sem,$cgroup,$component,$sequence,$status){
		//echo $course.','.$sem.','.$cgroup.','.$component.','.$sequence.','.$status;
        $qu=$wh='';
        if($course != '' || $sem != '' || $cgroup != '' || $component != '' || $sequence != '' || $status != ''){
            $wh='WHERE';
        }

        if($course != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.course_id = '$course'";
        }

        if($sem != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.sem = '$sem'";
        }

        if($cgroup != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.cbcs_curriculam_policy_id = '$cgroup'";
        }

        if($component != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.course_component = '$component'";
        }

        if($sequence != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.sequence = '$sequence'";
        }

        if($status != ''){
            if($qu != '')
                $qu .= " AND ";
            $qu.=" A.status = '$status'";
        }
        $wh .= $qu;
        
		/*$result=$this->db->query("SELECT A.*,concat(B.name,'(',UPPER(B.id),')') AS course,concat(C.wef,'(',C.course_id,')') AS c_policy,concat(D.name,'(',D.id,')') AS d_comp FROM `cbcs_comm_coursestructure_policy` A JOIN `cbcs_courses` B ON A.course_id=B.id JOIN `cbcs_credit_points_policy` C ON A.cbcs_curriculam_policy_id=C.id JOIN `cbcs_course_component` D ON A.course_component=D.id $wh ORDER BY A.id");*/
        $result=$this->db->query("SELECT A.*,concat(B.name,'(',UPPER(B.id),')') AS course,concat(D.name,'(',D.id,')') AS d_comp FROM `cbcs_comm_coursestructure_policy` A JOIN `cbcs_courses` B ON A.course_id=B.id JOIN `cbcs_course_component` D ON A.course_component=D.id $wh ORDER BY A.id");
        return $result->result();
    }

}
?>