<?php

class Migration_certificate_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    
    
    function get_student_details($admn_no)
    {
		//x.session DESC this  has been deleted from both query due to summer case 14-08-2018@anuj
          
        $sql = "(SELECT A.*,x.semester,x.session_yr,x.`session`, CASE WHEN x.type ='R' THEN 'Regular' WHEN x.type ='O' THEN 'Other' WHEN x.type ='S' THEN 'Special' WHEN x.type ='J' THEN 'Regular' WHEN x.type ='JS' THEN 'Special' WHEN x.type ='E' THEN 'Special' ELSE 'Error' END AS type,x.`status`,x.hstatus
FROM (
SELECT a.salutation,UPPER(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)) AS stu_name, CASE WHEN c.name_in_hindi IS NULL THEN 'Not Available' WHEN c.name_in_hindi='0' THEN 'Not Available' ELSE c.name_in_hindi END AS name_in_hindi,d.father_name,d.mother_name,a.id, DATE_FORMAT(c.admn_date,'%M-%Y') AS admn_date,b.course_id,b.branch_id,b.enrollment_year,e.line1,e.line2,e.city,e.state,e.pincode,a.email,
/*f.name as bname,g.name as cname*/
CASE WHEN b.auth_id='jrf' THEN 'PHD'  ELSE g.name  END AS cname,
CASE WHEN b.auth_id='jrf' THEN h.name  ELSE f.name  END AS bname,
a.dob,a.category,b.auth_id
FROM user_details a
INNER JOIN stu_academic b ON a.id=b.admn_no
INNER JOIN stu_details c ON c.admn_no=a.id
INNER JOIN user_other_details d ON d.id=a.id
INNER JOIN user_address e ON e.id=a.id
left join cs_branches f on f.id=b.branch_id
left join cs_courses g on g.id=b.course_id
left join departments h on h.id=a.dept_id
WHERE a.id=? AND e.`type`='permanent')A
left JOIN final_semwise_marks_foil x ON x.admn_no=A.id
ORDER BY x.semester DESC,x.session_yr DESC,x.tot_cr_pts DESC
LIMIT 1)union(SELECT A.*,x.semester,x.session_yr,x.`session`, CASE WHEN x.type ='R' THEN 'Regular' WHEN x.type ='O' THEN 'Other' WHEN x.type ='S' THEN 'Special' WHEN x.type ='J' THEN 'Regular' WHEN x.type ='JS' THEN 'Special' WHEN x.type ='E' THEN 'Special' ELSE 'Error' END AS type,x.`status`,x.hstatus
FROM (
SELECT a.salutation,UPPER(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)) AS stu_name, CASE WHEN c.name_in_hindi IS NULL THEN 'Not Available' WHEN c.name_in_hindi='0' THEN 'Not Available' ELSE c.name_in_hindi END AS name_in_hindi,d.father_name,d.mother_name,a.id, DATE_FORMAT(c.admn_date,'%M-%Y') AS admn_date,b.course_id,b.branch_id,b.enrollment_year,e.line1,e.line2,e.city,e.state,e.pincode,a.email,
/*f.name as bname,g.name as cname*/
CASE WHEN b.auth_id='jrf' THEN 'PHD'  ELSE g.name  END AS cname,
CASE WHEN b.auth_id='jrf' THEN h.name  ELSE f.name  END AS bname,
a.dob,a.category,b.auth_id
FROM alumni_user_details a
INNER JOIN alumni_stu_academic b ON a.id=b.admn_no
left JOIN alumni_stu_details c ON c.admn_no=a.id
INNER JOIN alumni_user_other_details d ON d.id=a.id
INNER JOIN alumni_user_address e ON e.id=a.id
LEFT JOIN cs_branches f ON f.id=b.branch_id
LEFT JOIN cs_courses g ON g.id=b.course_id
LEFT JOIN departments h ON h.id=a.dept_id
WHERE a.id=? AND e.`type`='permanent')A
left JOIN alumni_final_semwise_marks_foil x ON x.admn_no=A.id
ORDER BY x.semester DESC,x.session_yr DESC,x.tot_cr_pts DESC
LIMIT 1)";

        $query = $this->db->query($sql,array($admn_no,$admn_no));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    
    function insert($data)
	{
		if($this->db->insert('stu_migration_certificate',$data))
			return $this->db->insert_id();
		else
			return FALSE;
	}
        
        function check_exists($id){
            $sql = "select * from stu_migration_certificate where admn_no=?";

        $query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
        }
        function get_migration_details($id){
             $sql = "select * from stu_migration_certificate where admn_no=? and (dr_status='0' || dr_status='1') ";

        $query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row();
        } else {
            return false;
        }
        }
        function get_migration_details_already($id){
             $sql = "select * from stu_migration_certificate where admn_no=? and dr_status='1'";

        $query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row();
        } else {
            return false;
        }
        }
        
        function get_student_for_approval($id){
            
            $sql = "select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname
 from stu_migration_certificate a 
inner join user_details b on b.id=a.admn_no
inner join stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id
/*where a.da_acad_status='0'*/";
            
            if($id=='acad_da2'){
                $sql.='where a.da_acad_status="1" ';
            }
            if($id=='acad_ar'){
                $sql.='where a.so_acad_status="1" ';
            }
            if($id=='exam_da1'){
                $sql.='where a.ar_status="1" ';
            }
            if($id=='exam_no'){
                $sql.='where a.da_exam_status="1" ';
            }
             if($id=='exam_dr'){
                $sql.='where a.so_exam_status="1" ';
            }
            
            
            $query = $this->db->query($sql);

      // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
        }
        
        //==========final print======================
        
        function get_student_for_approval_print(){
            
            $sql = "(select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname,g.father_name
 from stu_migration_certificate a 
inner join user_details b on b.id=a.admn_no
inner join stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id
left join user_other_details g on g.id=b.id
where (a.dr_status='0' || a.dr_status='0' ))union (select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname,g.father_name
 from stu_migration_certificate a 
inner join alumni_user_details b on b.id=a.admn_no
inner join alumni_stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id
left join alumni_user_other_details g on g.id=b.id
where (a.dr_status='0' || a.dr_status='0' ))"; 
            
            
            
            
            $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
        }
        
        function get_student_for_approval_print_already(){
            
            $sql = "select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname
 from stu_migration_certificate a 
inner join user_details b on b.id=a.admn_no
inner join stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id
where a.dr_status='1'";
            
            
            
            
            $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
        }
        function get_all_migration(){
            
            $sql = "(select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname
 from stu_migration_certificate a 
inner join user_details b on b.id=a.admn_no
inner join stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id)union(select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname
 from stu_migration_certificate a 
INNER JOIN alumni_user_details b ON b.id=a.admn_no
INNER JOIN alumni_stu_academic c ON c.admn_no=a.admn_no
INNER JOIN departments d ON d.id=b.dept_id
LEFT JOIN cs_courses e ON e.id=c.course_id
LEFT JOIN cs_branches f ON f.id=c.branch_id)";
            
            
            
            
            $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
        }
        
        
        //=========================================
        function get_student_by_id($id){
         /*   $sql = "select a.*,concat_ws(' ',b.first_name,b.middle_name,b.last_name)as stu_name,d.name as dname,
e.name as cname,f.name as bname
 from stu_migration_certificate a 
inner join user_details b on b.id=a.admn_no
inner join stu_academic c on c.admn_no=a.admn_no
inner join departments d on d.id=b.dept_id
left join cs_courses e on e.id=c.course_id
left join cs_branches f on f.id=c.branch_id
where /a.da_acad_status='0' and/ a.id=?";*/
            
            $sql="(SELECT a.*, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name,d.name AS dname, 
CASE WHEN c.auth_id='jrf' THEN 'PHD'  ELSE e.name  END AS cname,
CASE WHEN c.auth_id='jrf' THEN d.name  ELSE f.name  END AS bname
FROM stu_migration_certificate a
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN stu_academic c ON c.admn_no=a.admn_no
INNER JOIN departments d ON d.id=b.dept_id
LEFT JOIN cs_courses e ON e.id=c.course_id
LEFT JOIN cs_branches f ON f.id=c.branch_id
WHERE a.id=?)union(SELECT a.*, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name,d.name AS dname, 
CASE WHEN c.auth_id='jrf' THEN 'PHD'  ELSE e.name  END AS cname,
CASE WHEN c.auth_id='jrf' THEN d.name  ELSE f.name  END AS bname
FROM stu_migration_certificate a
INNER JOIN alumni_user_details b ON b.id=a.admn_no
INNER JOIN alumni_stu_academic c ON c.admn_no=a.admn_no
INNER JOIN departments d ON d.id=b.dept_id
LEFT JOIN cs_courses e ON e.id=c.course_id
LEFT JOIN cs_branches f ON f.id=c.branch_id
WHERE a.id=?)";
            
            $query = $this->db->query($sql,array($id,$id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row();
        } else {
            return false;
        }
            
        }
        
        function da_acad_status_updation($form,$stu_id,$data){
           $this->db->update('stu_migration_certificate', $data, array('id' => $form,'admn_no'=>$stu_id));
           return true;
            
            
        }
        
        function get_all_authID($id){
            $sql = "select id from user_auth_types where auth_id=?";
            $query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
            
        }
        
        function next_migration_number(){
            $sql = "select * from stu_migration_certificate order by id desc limit 1";
            $query = $this->db->query($sql);

            //echo $this->db->last_query(); die();
             if ($this->db->affected_rows() > 0) {
                 return $query->row();
             } else {
                 return false;
             }
        }
        function first_migration_number(){
            $sql = "select * from stu_migration_begin";
            $query = $this->db->query($sql);

            //echo $this->db->last_query(); die();
             if ($this->db->affected_rows() > 0) {
                 return $query->row();
             } else {
                 return false;
             }
            
        }
        function update_dr_status_field($id){
            $sql = " update stu_migration_certificate set dr_status='1',dr_remark='".$this->session->userdata('id')."',dr_approve_time=now()
 where admn_no=?";
            $query = $this->db->query($sql,array($id));

            //echo $this->db->last_query(); die();
             if ($this->db->affected_rows() > 0) {
                 return true;
             } else {
                 return false;
             }
        }
        function fetch_father_name($id){
            $sql = "(select father_name from user_other_details where id=?)union(select father_name from alumni_user_other_details where id=?)";
            $query = $this->db->query($sql,array($id,$id));
            //echo $this->db->last_query(); die();
             if ($this->db->affected_rows() > 0) {
                 return $query->row();
             } else {
                 return false;
             }
        }
        function update_father_name($fname,$id){
            $sql = " update user_other_details set father_name=? where id=?";
            $query = $this->db->query($sql,array($fname,$id));

            //echo $this->db->last_query(); die();
             if ($this->db->affected_rows() > 0) {
                 return true;
             } else {
                 return false;
             }
            
            
        }
        

}

?>