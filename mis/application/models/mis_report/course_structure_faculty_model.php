<?php

class Course_structure_faculty_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_regular_student($syear, $sess, $did, $cid, $bid, $sem) {

      
               $sql="(select (T.subject_id) as sub_code,T.name,T.credit_hours,group_concat(T.emp_no) as faculty,
group_concat(concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name))as emp_name,d.name as dname,group_concat(uod.mobile_no) as mobile_no,T.ltp ,'Core' as paper_type  from
(select (B.subject_id),B.name,B.credit_hours,concat_ws(' ',B.lecture,B.tutorial,B.practical)as ltp,(A.emp_no) from
(select * from subject_mapping_des where map_id in
(SELECT q.map_id
FROM (
SELECT DISTINCT a.course_aggr_id
FROM reg_regular_form a
INNER JOIN user_details b ON a.admn_no=b.id
WHERE a.session_year=? AND a.`session`=? AND b.dept_id=? AND a.course_id=? AND a.branch_id=?
 AND a.semester=? AND a.hod_status='1' AND a.acad_status='1')p
INNER JOIN subject_mapping q ON q.aggr_id=p.course_aggr_id
WHERE q.session_year=? AND q.`session`=? AND q.dept_id=? AND q.course_id=? AND q.branch_id=?
AND q.semester=?))A
inner join subjects B on B.id=A.sub_id and A.coordinator='1'
order by B.subject_id)T
inner join user_details ud on ud.id=T.emp_no
inner join departments d on d.id=ud.dept_id
inner join user_other_details uod on uod.id=ud.id
group by T.subject_id
order by T.subject_id)
union
(
select d.subject_id,d.name,d.credit_hours,b.emp_no,concat_ws(' ',e.first_name,e.middle_name,e.last_name) emp_name,f.name as dname,
g.mobile_no,concat_ws(' ',d.lecture,d.tutorial,d.practical)as ltp,'Honour' as paper_type
 from subject_mapping a
inner join subject_mapping_des b on a.map_id=b.map_id
inner join course_structure c on c.aggr_id=a.aggr_id and c.semester=a.semester
inner join subjects d on d.id=c.id
inner join user_details e on e.id=b.emp_no
inner join departments f on f.id=e.dept_id
inner join user_other_details g on g.id=e.id
where a.session_year=? and a.`session`=? and a.dept_id=?
and a.course_id='honour' and a.branch_id=? and a.semester=? and b.coordinator='1'
group by d.id
)
union
(
select d.subject_id,d.name,d.credit_hours,b.emp_no,concat_ws(' ',e.first_name,e.middle_name,e.last_name) emp_name,f.name as dname,
g.mobile_no,concat_ws(' ',d.lecture,d.tutorial,d.practical)as ltp,'Minor' as paper_type
 from subject_mapping a
inner join subject_mapping_des b on a.map_id=b.map_id
inner join course_structure c on c.aggr_id=a.aggr_id and c.semester=a.semester
inner join subjects d on d.id=c.id
inner join user_details e on e.id=b.emp_no
inner join departments f on f.id=e.dept_id
inner join user_other_details g on g.id=e.id
where a.session_year=? and a.`session`=? and a.dept_id=?
and a.course_id='minor' and a.branch_id=? and a.semester=? and b.coordinator='1'
group by d.id
)";
  $query = $this->db->query($sql, array($syear, $sess, $did, $cid, $bid, $sem,$syear, $sess, $did, $cid, $bid, $sem,$syear, $sess, $did,$bid,$sem,$syear, $sess, $did,$bid,$sem)); 
            
             if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }
        
    }
    
    function get_jrf_student($syear, $sess, $did){
        
      
               $sql="select c.subject_id as sub_code,c.name,c.credit_hours,b.emp_no as faculty,concat_ws(' ',d.first_name,d.middle_name,d.last_name)as emp_name,
e.name as dname,uod.mobile_no,CONCAT_WS(' ',c.lecture,c.tutorial,c.practical) AS ltp
from subject_mapping a
inner join subject_mapping_des b on a.map_id=b.map_id
inner join subjects c on c.id=b.sub_id
inner join user_details d on d.id=b.emp_no
inner join departments e on e.id=a.dept_id
INNER JOIN user_other_details uod ON uod.id=d.id
where a.session_year=? and a.`session`=?
and a.dept_id=? and a.course_id='jrf' and b.coordinator='1'";
               $query = $this->db->query($sql, array($syear, $sess, $did)); 

               //echo $this->db->last_query();die();
    if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }

       
    }
    function get_other_student($syear, $sess, $did, $cid, $bid, $sem){
        $sql="select c.subject_id as sub_code,c.name,c.credit_hours,'N/A' as faculty,'N/A'as emp_name,e.name as dname,'N/A' as mobile_no,
concat_ws(' ',c.lecture,c.tutorial,c.practical)as ltp,'Other' as paper_type
from reg_other_form a
inner join reg_other_subject b on a.form_id=b.form_id
inner join subjects c on c.id=b.sub_id
inner join user_details d on d.id=a.admn_no
inner join departments e on e.id=d.dept_id
where a.session_year=? and a.`session`=?
and d.dept_id=? and a.course_id=? and a.branch_id=? and a.semester=?
and a.hod_status='1' and a.acad_status='1'
group by b.sub_id";
               $query = $this->db->query($sql, array($syear, $sess, $did, $cid, $bid, $sem)); 
            
    if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }
    }
    
    function get_common_student($syear, $sess, $did, $cid, $bid, $sem){
        
      
               $sql="
select c.subject_id as sub_code,c.name,c.credit_hours,b.emp_no as faculty,concat_ws(' ',d.first_name,d.middle_name,d.last_name)as emp_name,
e.name as dname,uod.mobile_no,CONCAT_WS(' ',c.lecture,c.tutorial,c.practical) AS ltp
from subject_mapping a
inner join subject_mapping_des b on a.map_id=b.map_id
inner join subjects c on c.id=b.sub_id
inner join user_details d on d.id=b.emp_no
inner join departments e on e.id=a.dept_id
INNER JOIN user_other_details uod ON uod.id=d.id
where a.session_year=? and a.`session`=?
and a.dept_id=? and a.course_id=? and a.branch_id=? and section=? and b.coordinator='1'";
               $query = $this->db->query($sql, array($syear, $sess, $did, $cid, $bid, $sem)); 
          //  echo $this->db->last_query();die();
    if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }

       
    }
    function get_prep_student($syear, $sess){
        
      
               $sql="select c.subject_id as sub_code,c.name,c.credit_hours,b.emp_no as faculty,concat_ws(' ',d.first_name,d.middle_name,d.last_name)as emp_name,
e.name as dname,uod.mobile_no,CONCAT_WS(' ',c.lecture,c.tutorial,c.practical) AS ltp, 'core' as paper_type
from subject_mapping a
inner join subject_mapping_des b on a.map_id=b.map_id
inner join subjects c on c.id=b.sub_id
inner join user_details d on d.id=b.emp_no
inner join departments e on e.id=a.dept_id
INNER JOIN user_other_details uod ON uod.id=d.id
where a.session_year=? and a.`session`=?
 and a.course_id='prep' and a.branch_id='prep' and b.coordinator='1'";
               $query = $this->db->query($sql, array($syear, $sess, $did, $cid, $bid, $sem)); 
          //  echo $this->db->last_query();die();
    if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }

       
    }
    
    function get_department($id){
        $sql="select name from departments where id=?";
               $query = $this->db->query($sql, array($id)); 
            
            if ($this->db->affected_rows() >= 0) {
                return $query->row()->name;
            } else {
                return false;
            }
        
    }
    function get_course($id){
        $sql="select name from cs_courses where id=?";
               $query = $this->db->query($sql, array($id)); 
            
            if ($this->db->affected_rows() >= 0) {
                return $query->row()->name;
            } else {
                return false;
            }
        
    }
    function get_branch($id){
        $sql="select name from cs_branches where id=?";
               $query = $this->db->query($sql, array($id)); 
            
            if ($this->db->affected_rows() >= 0) {
                return $query->row()->name;
            } else {
                return false;
            }
        
    }
	function cbcs_course_faculty($syear,$sess,$did,$cid,$bid,$sem){
		
		if($cid=='comm')
		{
			if($sess=='Monsoon'){
					$sec=$sem;
					$sem=1;
					
				
			}
			if($sess=='Winter'){
				$sec=$sem;
				$sem=2;
			}
			$con=" and b.section='$sec'";
		}else{
			$con="";
		}
		
		$sql="(SELECT a.sub_code,a.sub_name AS name,a.credit_hours,
GROUP_CONCAT(b.emp_no SEPARATOR ',') AS faculty,
GROUP_CONCAT(CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name)) AS emp_name,
d.name AS dname,
GROUP_CONCAT(uod.mobile_no) AS mobile_no,
CONCAT_WS(' ',a.lecture,a.tutorial,a.practical) AS ltp,
a.sub_type as paper_type,e.offered_to_name,e.co_emp_id
 FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON b.sub_offered_id=a.id
INNER JOIN user_details ud ON ud.id=b.emp_no
INNER JOIN cbcs_departments d ON d.id=ud.dept_id
INNER JOIN user_other_details uod ON uod.id=ud.id
LEFT JOIN cbcs_assign_course_coordinator e ON e.sub_code=a.sub_code
WHERE a.session_year=? AND a.`session`=? and
a.dept_id=? AND a.course_id=? AND a.branch_id=? AND a.course_id!='jrf'
AND a.semester=? ".$con."
GROUP BY a.sub_code
ORDER BY a.sub_code)
union
(SELECT a.sub_code,a.sub_name AS name,a.credit_hours,
GROUP_CONCAT(b.emp_no SEPARATOR ',') AS faculty,
GROUP_CONCAT(CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name)) AS emp_name,
d.name AS dname,
GROUP_CONCAT(uod.mobile_no) AS mobile_no,
CONCAT_WS(' ',a.lecture,a.tutorial,a.practical) AS ltp,
a.sub_type as paper_type,e.offered_to_name,e.co_emp_id
 FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON b.sub_offered_id=a.id
INNER JOIN user_details ud ON ud.id=b.emp_no
INNER JOIN cbcs_departments d ON d.id=ud.dept_id
INNER JOIN user_other_details uod ON uod.id=ud.id
LEFT JOIN cbcs_assign_course_coordinator e ON e.sub_code=a.sub_code
WHERE a.session_year=? AND a.`session`=? and
a.dept_id=? AND a.course_id=? AND a.branch_id=? AND a.course_id!='jrf'
AND a.semester=? ".$con." 
GROUP BY a.sub_code
ORDER BY a.sub_code)";
//echo $sql;
               $query = $this->db->query($sql, array($syear,$sess,$did,$cid,$bid,$sem,$syear,$sess,$did,$cid,$bid,$sem)); 
            
            if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }
		
		
	}
	//JRF
	
	function cbcs_course_faculty_jrf_prep($syear,$sess,$did,$type){
		
	
		
		$sql="(SELECT a.sub_code,a.sub_name AS name,a.credit_hours,
GROUP_CONCAT(b.emp_no SEPARATOR ',') AS faculty,
GROUP_CONCAT(CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name)) AS emp_name,
d.name AS dname,
GROUP_CONCAT(uod.mobile_no) AS mobile_no,
CONCAT_WS(' ',a.lecture,a.tutorial,a.practical) AS ltp,
a.sub_type as paper_type,e.offered_to_name,e.co_emp_id
 FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON b.sub_offered_id=a.id
INNER JOIN user_details ud ON ud.id=b.emp_no
INNER JOIN cbcs_departments d ON d.id=ud.dept_id
INNER JOIN user_other_details uod ON uod.id=ud.id
LEFT JOIN cbcs_assign_course_coordinator e ON e.sub_code=a.sub_code
WHERE a.session_year=? AND a.`session`=? and
a.dept_id=? AND a.course_id='".$type."'
GROUP BY a.sub_code
ORDER BY a.sub_code)
union
(SELECT a.sub_code,a.sub_name AS name,a.credit_hours,
GROUP_CONCAT(b.emp_no SEPARATOR ',') AS faculty,
GROUP_CONCAT(CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name)) AS emp_name,
d.name AS dname,
GROUP_CONCAT(uod.mobile_no) AS mobile_no,
CONCAT_WS(' ',a.lecture,a.tutorial,a.practical) AS ltp,
a.sub_type as paper_type,e.offered_to_name,e.co_emp_id
 FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON b.sub_offered_id=a.id
INNER JOIN user_details ud ON ud.id=b.emp_no
INNER JOIN cbcs_departments d ON d.id=ud.dept_id
INNER JOIN user_other_details uod ON uod.id=ud.id
LEFT JOIN cbcs_assign_course_coordinator e ON e.sub_code=a.sub_code
WHERE a.session_year=? AND a.`session`=? and
a.dept_id=? AND a.course_id='".$type."'
GROUP BY a.sub_code
ORDER BY a.sub_code)";
//echo $sql;
               $query = $this->db->query($sql, array($syear,$sess,$did,$syear,$sess,$did)); 
            
            if ($this->db->affected_rows() >= 0) {
                return $query->result();
            } else {
                return false;
            }
		
		
	}
    
    
}

?>