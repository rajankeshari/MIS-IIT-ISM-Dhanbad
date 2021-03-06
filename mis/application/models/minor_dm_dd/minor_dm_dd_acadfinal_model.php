<?php

class Minor_dm_dd_acadfinal_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

	
	function get_student_deptwise_doublemajor($session_year,$session)
    {
      $sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.applied_for='doublemajor' and (a.status='1' || a.status='2') and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	function get_student_deptwise_dualdegree_categoryA($session_year,$session)
    {
      $sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.applied_for='dualdegree_categoryA' and (a.status='1' || a.status='2') and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	function get_student_deptwise_dualdegree_categoryB($session_year,$session)
    {
      $sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.applied_for='dualdegree_categoryB' and (a.status='1' || a.status='2') and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	function get_student_deptwise_dualdegree_categoryC($session_year,$session)
    {
      $sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.applied_for='dualdegree_categoryC' and (a.status='1' || a.status='2') and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	function get_student_deptwise_minor($session_year,$session)
    {
      $sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.applied_for='minor' and (a.status='1' || a.status='2') and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	function get_institute_criteria($session_year,$session){
		$sql="SELECT * from major_minor_dual_criteria where session_year=? and session=? and status='1'";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	function get_department_criteria($session_year,$session){
		$sql="SELECT * from major_minor_dual_criteria_dept where session_year=? and session=? and status='1' ";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function get_student_from_maintable($session_year,$session)
	{
		$sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_temp a INNER JOIN user_details b ON b.id=a.admn_no 
where a.session_year=? and a.session=?
order by a.applied_for,a.priority  ";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
		
	}
	
	 function update_major_minor_dual_final($id,$admn_no,$applied_for,$remark){
		
		$sql="UPDATE major_minor_dual_final SET STATUS='0',remark1='".$remark."' WHERE id=? AND admn_no=? AND applied_for LIKE '%".$applied_for."%' ";

        $query = $this->db->query($sql,array($id,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return '1';
        } else {
            return '0';
        }
		
	}
	function update_major_minor_dual_final_acad($id,$admn_no,$applied_for,$remark){
		
		$sql="UPDATE major_minor_dual_final SET STATUS='2',remark1='".$remark."' WHERE id=? AND admn_no=? ";

        $query = $this->db->query($sql,array($id,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return '1';
        } else {
            return '0';
        }
		
	}
	
	function update_major_minor_dual_final_pop($id,$admn_no,$remark){
		
		$sql="UPDATE major_minor_dual_final SET STATUS='0' ,remark1='".$remark."' WHERE id=? AND admn_no=?  ";

        $query = $this->db->query($sql,array($id,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return '1';
        } else {
            return '0';
        }
		
	}
	
	function get_disallowed_student($session_year,$session)
	{
		$sql="SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id
FROM major_minor_dual_final a INNER JOIN user_details b ON b.id=a.admn_no WHERE  a.status='0' and a.session_year=? and a.session=?";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	function get_record_from_final_table($id,$admn_no)
	{
		$sql="SELECT a.* FROM major_minor_dual_final a  WHERE  a.id=? and a.admn_no=?";
  
			

        $query = $this->db->query($sql,array($id,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
	}
	
	function insert_final_allowed_disallowed($data){
		
		if($this->db->insert('major_minor_dual_final_allowed_disallowed',$data))
			//return TRUE;
                        return $this->db->insert_id();
		else
			return FALSE;
		
	}
	
	function get_normal_list($session_year,$session){
		$sql="SELECT p.* from
(SELECT a.admn_no,
CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,
b.dept_id,
a.course_id,a.branch_id,a.semester,
GROUP_CONCAT(a.applied_for separator '<br>')AS applied_for ,
GROUP_CONCAT(a.opt_title separator '<br>')AS opt_title ,
GROUP_CONCAT(a.opt_dept_id separator '<br>')AS opt_dept_id ,
GROUP_CONCAT(COALESCE(a.opt_course_id,'NA') separator '<br>')AS opt_course_id,
GROUP_CONCAT(COALESCE(a.opt_branch_id,'NA') separator '<br>')AS opt_branch_id,
GROUP_CONCAT(COALESCE(a.priority,'NA') separator '<br>')AS priority,
COUNT(a.applied_for) AS cnt,
a.session_year,a.`session`,a.obtained_cgpa,a.opt_minor_count
FROM major_minor_dual_final a 
INNER JOIN user_details b ON b.id=a.admn_no
WHERE a.`status`!=0 
GROUP BY a.admn_no,a.applied_for)p
WHERE p.cnt=1 AND p.session_year=? AND p.session=? /*or p.cnt!=p.opt_minor_count*/
ORDER BY p.dept_id,p.course_id,p.branch_id,p.semester,p.applied_for,p.admn_no";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function get_conflict_list($session_year,$session){
		$sql="SELECT p.* from
(SELECT a.admn_no,
CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS stu_name,
b.dept_id,
a.course_id,a.branch_id,a.semester,
GROUP_CONCAT(a.applied_for separator '<br>')AS applied_for ,
GROUP_CONCAT(a.opt_title separator '<br>')AS opt_title ,
GROUP_CONCAT(a.opt_dept_id separator '<br>')AS opt_dept_id ,
GROUP_CONCAT(COALESCE(a.opt_course_id,'NA') separator '<br>')AS opt_course_id,
GROUP_CONCAT(COALESCE(a.opt_branch_id,'NA') separator '<br>')AS opt_branch_id,
GROUP_CONCAT(COALESCE(a.priority,'NA') separator '<br>')AS priority,
COUNT(a.applied_for) AS cnt,
a.session_year,a.`session`,a.obtained_cgpa,a.opt_minor_count
FROM major_minor_dual_final a 
INNER JOIN user_details b ON b.id=a.admn_no
WHERE a.`status`!=0 
GROUP BY a.admn_no,a.applied_for)p
WHERE p.cnt>1 AND p.session_year=? AND p.session=? /*or p.cnt!=p.opt_minor_count*/
ORDER BY p.dept_id,p.course_id,p.branch_id,p.semester,p.applied_for,p.admn_no";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	
	function get_individual_student($syear,$sess,$admn_no,$applied_for)
	{
		$sql="SELECT a.*,CONCAT_WS(' ', b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id  FROM major_minor_dual_final a 
		INNER JOIN user_details b ON b.id=a.admn_no
		WHERE a.session_year=? AND a.`session`=?  and a.admn_no=? AND a.`status`!=0 AND applied_for LIKE '%".$applied_for."%' ";
  
			

        $query = $this->db->query($sql,array($syear,$sess,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function get_log_list($session_year,$session){
		
		$sql="SELECT a.*,CONCAT_WS(' ', b.first_name,b.middle_name,b.last_name)AS stu_name,b.dept_id,c.remark1 AS fremark  FROM major_minor_dual_final_allowed_disallowed a 
		INNER JOIN user_details b ON b.id=a.admn_no
		INNER JOIN major_minor_dual_final c ON c.id=a.id
		WHERE a.session_year=? AND a.`session`=?  ";
  
			

        $query = $this->db->query($sql,array($session_year,$session));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	
	
	
	function find_department($syear,$sess)
	{
		$sql="SELECT DISTINCT major_minor_dual.opt_dept_id,departments.name AS dname,major_minor_dual.applied_for FROM major_minor_dual
INNER JOIN departments ON departments.id=major_minor_dual.opt_dept_id
WHERE major_minor_dual.session_year=? and major_minor_dual.`session`=?
GROUP BY opt_dept_id,applied_for
ORDER BY opt_dept_id,applied_for

  ";
  
			

        $query = $this->db->query($sql,array($syear,$sess));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
	}
	
	function get_total_applied_student($session_year,$session,$dept_id,$applied_for)
	{
		
		$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual a
WHERE a.session_year=? AND a.`session`=? AND a.opt_dept_id=? AND a.applied_for=?;

  ";
  
			

        $query = $this->db->query($sql,array($session_year,$session,$dept_id,$applied_for));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
		
	}
	
	function get_total_eligible_student($session_year,$session,$dept_id,$applied_for)
	{
		$tmp=explode("_",$applied_for);
		
		if($applied_for=='doublemajor'){
			$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_temp a
WHERE a.session_year=? AND a.`session`=?
AND a.opt_dept_id=?  AND a.applied_for=? AND a.obtained_cgpa>=8 AND a.backlog_paper='0' AND a.drop_paper='0' ";
		}
		if($tmp[0]=="dualdegree")
			
			{
				$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_temp a
WHERE a.session_year=? AND a.`session`=?
AND a.opt_dept_id=?  AND a.applied_for=? AND a.obtained_cgpa>=7 AND a.backlog_paper='0' AND a.drop_paper='0' ";
				
			}
			
			if($applied_for=='minor')
			
			{
				$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_temp a
WHERE a.session_year=? AND a.`session`=? AND a.opt_dept_id=?  AND a.applied_for=? ";
				
			}
		
		
  
			

        $query = $this->db->query($sql,array($session_year,$session,$dept_id,$applied_for));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
		
	}
	
	function get_total_alloted_student($session_year,$session,$dept_id,$applied_for)
	{
		$tmp=explode("_",$applied_for);
		
		if($applied_for=='doublemajor'){
			$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_final a
WHERE a.session_year=? AND a.`session`=?
AND a.opt_dept_id=?  AND a.applied_for=? AND a.obtained_cgpa>=8 AND a.backlog_paper='0' AND a.drop_paper='0' and a.status!='0' ";
		}
		if($tmp[0]=="dualdegree")
			
			{
				$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_final a
WHERE a.session_year=? AND a.`session`=?
AND a.opt_dept_id=?  AND a.applied_for=? AND a.obtained_cgpa>=7 AND a.backlog_paper='0' AND a.drop_paper='0' and a.status!='0'";
				
			}
			
			if($applied_for=='minor')
			
			{
				$sql="SELECT a.opt_dept_id,a.applied_for,COUNT(a.admn_no) AS cnt FROM major_minor_dual_final a
WHERE a.session_year=? AND a.`session`=? AND a.opt_dept_id=?  AND a.applied_for=? and a.status!='0'";
				
			}
		
		
  
			

        $query = $this->db->query($sql,array($session_year,$session,$dept_id,$applied_for));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
		
	}
	
    

}

?>