<?php
class Viewandprint_model extends CI_Model
{
	
	
	
// Session Wise Registraion Details by @abhi Start

	function get_sess_wise_list($sy,$sess,$dept=null,$course=null,$branch=null,$semester=null){
	#	echo $course;exit;
		#where z.course_id='' and z.branch='' and z.semester=''
		if($course==null){
			$course_id="";
		}else{
			$course_id=$course;
		}
		if($branch==null){
			$branch_id="";
		}else{
			$branch_id=$branch;
		}
		if($semester==null){
			$semesters="";
		}else{
			$semesters=$semester;
		}
		$where=array(
			"z.dept_id"=>$dept,
			"z.course_id"=>$course_id,
			"z.branch"=>$branch,
			"z.semester"=>$semester,
		);
//print_r(array_filter($where));
$whereClouse="";
if(count(array_filter($where)) > 0){
	$j=0;

	foreach (array_filter($where) as $key => $value) {
		$j++;
		// code...
		if($j==1){
				$w=" WHERE ";
				$and=" and ";
		}else{
			$w="";
		}
		if( $j > 0 && $j < count(array_filter($where)) ){
				$and=" and ";
		}else{
			$and="  ";
		}
		$whereClouse.=$w.$key."="."'".$value."'".$and;
		 //$key."=".$value;
	}
//echo $whereClouse; //exit;

}
//exit;
		$sql="
		SELECT z.*,count(z.subject_code) as cnt_subs ,GROUP_CONCAT(z.subject_code,' ( ', z.subject_name,' )' SEPARATOR '|') AS opted_subs
FROM (
SELECT a.session_year,a.`session`,a.admn_no,a.semester, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS stu_name,c.dept_id,a.course_id,b.branch,b.subject_code,b.subject_name
FROM reg_regular_form a
/* INNER JOIN pre_stu_course b ON a.admn_no=b.admn_no AND a.form_id=b.form_id and a.session_year=b.session_year and a.`session`=b.`session`  */
INNER JOIN (select * from cbcs_stu_course a
where a.session_year='$sy' and a.`session`='$sess'
union
select * from old_stu_course a
where a.session_year='$sy' and a.`session`='$sess') as b ON a.admn_no=b.admn_no AND a.form_id=b.form_id and a.session_year=b.session_year and a.`session`=b.`session`
INNER JOIN user_details c ON a.admn_no=c.id
inner join users d on d.id=a.admn_no and d.status='A'
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.hod_status='1' AND a.acad_status='1' AND a.admn_no <> '0') z
		$whereClouse
		group by z.admn_no order by z.subject_code ,z.admn_no ,z.semester asc";
		$query = $this->db->query($sql);
	  //echo $this->db->last_query(); die();
			 if ($this->db->affected_rows() >= 0) {
					 return $query->result();
			 } else {
					 return false;
			 }

	}
// Session Wise Registraion Details by @abhi end

	
	function get_student_pre_semester_registration_subjects($from_id){
        
        //$sql = "SELECT * FROM pre_stu_course WHERE form_id=?   ";
		//$sql="select z.* from (SELECT * FROM pre_stu_course WHERE form_id=?) z order by cast(SUBSTR(z.sub_category,3) as UNSIGNED) asc";
		$sql="
		SELECT *,CASE WHEN sub_category_cbcs_offered IS NULL THEN sub_category ELSE sub_category_cbcs_offered END AS sub_category_new
 FROM pre_stu_course WHERE form_id=? and (remark2='1'|| remark2 is null || remark2 ='3') order by cast(SUBSTR(sub_category_new,3) as UNSIGNED) asc,priority,id
		";

	   $query = $this->db->query($sql,array($from_id));

      // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
		function count_category($from_id,$sub_category){
        
        //$sql = "SELECT count(a.sub_category)AS cnt FROM pre_stu_course a WHERE a.form_id=? AND a.sub_category=? GROUP BY a.sub_category ";
		$sql = "SELECT * FROM pre_stu_course a WHERE a.form_id=? AND a.sub_category=? ";

        $query = $this->db->query($sql,array($from_id,$sub_category));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	function get_sub_count($from_id){
        
       
		//$sql = "SELECT sub_category FROM pre_stu_course a WHERE a.form_id=? GROUP BY sub_category order by cast(SUBSTR(sub_category,3) as UNSIGNED) asc";
		
			$sql="SELECT sub_category,sub_category_cbcs_offered,
CASE
    WHEN sub_category_cbcs_offered IS null THEN sub_category
    ELSE sub_category_cbcs_offered
END AS sub_category_new

FROM pre_stu_course a WHERE a.form_id=? GROUP BY sub_category_new ORDER BY cast(sub_category_new as UNSIGNED) asc,id";
		

        $query = $this->db->query($sql,array($from_id));

      // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	/*function get_student_pre_semester_registration_subjects_main($id){
		
		  $sql = "SELECT id,form_id,admn_no,subject_code,subject_name,sub_category FROM cbcs_stu_course WHERE form_id=?
					UNION
					SELECT id,form_id,admn_no,subject_code,subject_name,sub_category FROM old_stu_course WHERE form_id=? ";

        $query = $this->db->query($sql,array($from_id,$from_id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	*/
	
	function get_regular_student_list($syear,$sess,$cid,$bid,$sem){
        
        $sql = " SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS sname,b.dept_id,c.name AS dname,d.name AS cname, e.name AS bname FROM reg_regular_form a 
		INNER JOIN user_details b ON b.id=a.admn_no
		INNER JOIN cbcs_departments c ON c.id=b.dept_id
INNER JOIN cbcs_courses d ON d.id=a.course_id
INNER JOIN cbcs_branches e ON e.id=a.branch_id
inner join users f on f.id=a.admn_no and f.status='A'
		WHERE 1=1 and a.hod_status='1' and a.acad_status='1' "; 
		
		if($syear!='none'){
			$sql.=" and a.session_year='".$syear."'";
		}
		if($sess!='none'){
			$sql.=" and a.session='".$sess."'";
		}
		if($cid!='none'){
			$sql.=" and a.course_id='".$cid."'";
		}
		if($bid!='none'){
			$sql.=" and a.branch_id='".$bid."'";
		}
		if($sem){
			$sql.=" and a.semester='".$sem."'";
		}
		$sql.=" order by b.dept_id,a.course_id,a.branch_id,a.semester,a.admn_no";
        $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
	}
		function get_enrollment($id){
			$sql = " SELECT enrollment_year FROM stu_academic WHERE admn_no=? ";
			$query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
			
			
		}
    
	
	//========================================06-11-2019----------------------------------------
	//===================================================================Modules for course wise reproting======================================================		
	function get_course_bydept_cs($syear,$sess,$dept_id)
	{

		$query = $this->db->query("SELECT p.* FROM (
SELECT 
a.id, a.sub_code,a.sub_name,a.lecture,a.tutorial,a.practical,concat(a.sub_code,' - ',a.sub_name,' [',a.lecture,'-',a.tutorial,'-',a.practical,']') AS subject
FROM cbcs_subject_offered a WHERE  session_year='".$syear."' AND SESSION='".$sess."' AND a.dept_id='".$dept_id."' GROUP BY a.sub_code
UNION
SELECT 
a.id,a.sub_code,a.sub_name,a.lecture,a.tutorial,a.practical,concat(a.sub_code,' - ',a.sub_name,' [',a.lecture,'-',a.tutorial,'-',a.practical,']') AS subject
FROM old_subject_offered a WHERE  session_year='".$syear."' AND SESSION='".$sess."' AND a.dept_id='".$dept_id."' GROUP BY a.sub_code
)p
ORDER BY p.sub_code");
		 //   echo  $this->db->last_query();	die();
                if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
	}
	
	function get_course_wise_student($syear,$sess,$course){
		$query = $this->db->query("(SELECT 
'c' as rstatus,a.id,a.dept_id,b.course,b.branch,a.semester,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,
count(b.subject_code)AS stu_count,c.name AS cname,d.name AS bname
FROM cbcs_subject_offered a
INNER JOIN cbcs_stu_course b ON b.sub_offered_id=a.id
INNER JOIN cbcs_courses c ON c.id=b.course
INNER JOIN cbcs_branches d ON d.id=b.branch
inner join reg_regular_form e on e.form_id=b.form_id and e.admn_no=b.admn_no
inner join users f on f.id=e.admn_no and f.status='A'
 WHERE  a.session_year='".$syear."' AND a.`session`='".$sess."'  and a.sub_code='".$course."'
 and e.hod_status='1' and e.acad_status='1'
 GROUP BY b.course,b.branch,a.semester)
 union
 (SELECT 
'o' as rstatus,a.id,a.dept_id,b.course,b.branch,a.semester,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,
count(b.subject_code)AS stu_count,c.name AS cname,d.name AS bname
FROM old_subject_offered a
INNER JOIN old_stu_course b ON b.sub_offered_id=a.id
INNER JOIN cbcs_courses c ON c.id=b.course
INNER JOIN cbcs_branches d ON d.id=b.branch
inner join reg_regular_form e on e.form_id=b.form_id and e.admn_no=b.admn_no
inner join users f on f.id=e.admn_no and f.status='A'
 WHERE  a.session_year='".$syear."' AND a.`session`='".$sess."'  and a.sub_code='".$course."'
 and e.hod_status='1' and e.acad_status='1'
 GROUP BY b.course,b.branch,a.semester)");
		    //echo  $this->db->last_query();	die();
                if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
		
	}
	//Pre
		function get_course_wise_student_pre($syear,$sess,$course){
		$query = $this->db->query("(
SELECT 'c' AS rstatus,a.id,a.dept_id,b.course,b.branch,a.semester,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours, COUNT(b.subject_code) AS stu_count,c.name AS cname,d.name AS bname
FROM cbcs_subject_offered a
INNER JOIN pre_stu_course b ON b.sub_offered_id=CONCAT('c',a.id)
INNER JOIN cbcs_courses c ON c.id=b.course
INNER JOIN cbcs_branches d ON d.id=b.branch
inner join reg_regular_form e on e.form_id=b.form_id and e.admn_no=b.admn_no
inner join users f on f.id=e.admn_no and f.status='A'
WHERE a.session_year='$syear' AND a.`session`='$sess' AND a.sub_code='$course'
and e.hod_status='1' and e.acad_status='1' AND b.remark2='3'
GROUP BY b.course,b.branch,a.semester) UNION (
SELECT 'o' AS rstatus,a.id,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours, COUNT(b.subject_code) AS stu_count,c.name AS cname,d.name AS bname
FROM old_subject_offered a
INNER JOIN pre_stu_course b ON b.sub_offered_id=CONCAT('o',a.id)
INNER JOIN cbcs_courses c ON c.id=b.course
INNER JOIN cbcs_branches d ON d.id=b.branch
inner join reg_regular_form e on e.form_id=b.form_id and e.admn_no=b.admn_no
inner join users f on f.id=e.admn_no and f.status='A'
WHERE a.session_year='$syear' AND a.`session`='$sess' AND a.sub_code='$course'
and e.hod_status='1' and e.acad_status='1' AND b.remark2='3'
GROUP BY b.course,b.branch,a.semester)");
		   // echo  $this->db->last_query();	die();
                if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
		
	}
	
	function get_regular_student_list_subject_offered_id_wise($rstatus,$sub_offered_id,$cid,$bid){
		
		if($rstatus=='o'){ $tbl="old_stu_course"; }
		if($rstatus=='c'){ $tbl="cbcs_stu_course"; }
		
		$query = $this->db->query("SELECT a.*,CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name)AS sname,c.name AS cname,d.name AS bname,
e.name AS dname,h.domain_name as email,g.mobile_no
FROM ".$tbl." a 
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN cbcs_courses c ON c.id=a.course
INNER JOIN cbcs_branches d ON d.id=a.branch
INNER JOIN cbcs_departments e ON e.id=b.dept_id
inner join reg_regular_form f on f.form_id=a.form_id and f.admn_no=a.admn_no
inner join users i on i.id=f.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE a.sub_offered_id='".$sub_offered_id."' AND c.`status`!='0' AND d.`status`!='0' and f.hod_status='1' and f.acad_status='1' AND a.course='$cid' AND a.branch='$bid' GROUP BY a.admn_no");


		    //echo  $this->db->last_query();	die();
                if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
	}
	
	function get_regular_student_list_subject_offered_id_wise_pre($rstatus,$sub_offered_id,$cid,$bid){
		
		$sid=$rstatus.$sub_offered_id;
		
		$query = $this->db->query("SELECT a.*, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS sname,c.name AS cname,d.name AS bname, e.name AS dname,h.domain_name as email,g.mobile_no
FROM pre_stu_course a
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN cbcs_courses c ON c.id=a.course
INNER JOIN cbcs_branches d ON d.id=a.branch
INNER JOIN cbcs_departments e ON e.id=b.dept_id
inner join reg_regular_form f on f.form_id=a.form_id and f.admn_no=a.admn_no
inner join users i on i.id=f.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE a.sub_offered_id='$sid' AND c.`status`!='0' AND d.`status`!='0'
AND a.course='$cid' AND a.branch='$bid' and (a.remark2='1' || a.remark2='3')
and f.hod_status='1' and f.acad_status='1' GROUP BY a.admn_no");
		    //echo  $this->db->last_query();	die();
                if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
	}
	


	
	
	function get_syear_pre(){
		$query = $this->db->query("SELECT a.session_year FROM pre_stu_course a GROUP BY a.session_year ORDER BY a.session_year desc ");
		   
         if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
	}
		function get_sess_pre(){
		$query = $this->db->query("SELECT a.session FROM pre_stu_course a GROUP BY a.session ORDER BY a.session ASC ");
		   
         if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
		
	}
    
	function get_syear_final(){
		
		$query = $this->db->query("SELECT p.* FROM(
(SELECT a.session_year FROM cbcs_stu_course a)
UNION 
(SELECT a.session_year FROM old_stu_course a)
)p GROUP BY p.session_year
ORDER BY p.session_year DESC
 ");
		   
         if($query->num_rows() > 0)
			return $query->result();
		else
			return false;

		
	}
		function get_sess_final(){
		
		$query = $this->db->query("SELECT  CONCAT( UCASE( LEFT(p.session, 1)), LCASE(SUBSTRING(p.session, 2))) as session FROM(
(SELECT a.session FROM cbcs_stu_course a)
UNION 
(SELECT a.session FROM old_stu_course a)
)p GROUP BY p.session
/*ORDER BY p.session desc*/
 ");
		   
         if($query->num_rows() > 0)
			return $query->result();
		else
			return false;

		
	}
	
	function get_depts()
	{
		$query = $this->db->query("SELECT * from cbcs_departments WHERE TYPE='academic' AND STATUS='1' ORDER BY name ");
		   
         if($query->num_rows() > 0)
			return $query->result();
		else
			return false;
	}
	function get_student_final_semester_registration_subjects($from_id){
        
        $sql = "(SELECT form_id,admn_no,sub_offered_id,subject_code,subject_name,sub_category,course,branch,session_year,session,CASE WHEN sub_category_cbcs_offered IS NULL THEN sub_category ELSE sub_category_cbcs_offered END AS sub_category_new
 FROM old_stu_course WHERE form_id=?)union (SELECT form_id,admn_no,sub_offered_id,subject_code,subject_name,sub_category,course,branch,session_year,session, CASE WHEN sub_category_cbcs_offered IS NULL THEN sub_category ELSE sub_category_cbcs_offered END AS sub_category_new
 FROM cbcs_stu_course WHERE form_id=?) ";

        $query = $this->db->query($sql,array($from_id,$from_id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	//30-10-2019
	
	function get_course_details($syear, $sess,$course)
	{
		$sql = "SELECT a.sub_code,a.sub_name,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.session_year,a.`session`
FROM old_subject_offered a WHERE a.session_year=? AND a.`session`=? AND a.sub_code=? group BY a.sub_code
union 
SELECT a.sub_code,a.sub_name,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.session_year,a.`session`
FROM cbcs_subject_offered a WHERE a.session_year=? AND a.`session`=? AND a.sub_code=? group BY a.sub_code";

        $query = $this->db->query($sql,array($syear, $sess,$course,$syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	function get_course_details_pre($syear, $sess,$course)
	{
		$sql = "(SELECT a.sub_offered_id,a.subject_code,a.subject_name,b.lecture,b.tutorial,b.practical,b.credit_hours,
b.contact_hours,b.session_year,b.`session`
FROM pre_stu_course a 
inner JOIN cbcs_subject_offered b ON CONCAT('c',b.id)=a.sub_offered_id
inner join reg_regular_form c on c.form_id=a.form_id and c.admn_no=a.admn_no
inner join users d on d.id=c.admn_no and d.status='A'
 WHERE b.session_year=? AND b.`session`=? AND a.subject_code=? 
 and c.hod_status='1' and c.acad_status='1'
 group BY a.subject_code)
 union
 (SELECT a.sub_offered_id,a.subject_code,a.subject_name,b.lecture,b.tutorial,b.practical,b.credit_hours,
b.contact_hours,b.session_year,b.`session`
FROM pre_stu_course a 
inner JOIN old_subject_offered b ON CONCAT('o',b.id)=a.sub_offered_id
inner join reg_regular_form c on c.form_id=a.form_id and c.admn_no=a.admn_no
inner join users d on d.id=c.admn_no and d.status='A'
 WHERE b.session_year=? AND b.`session`=? AND a.subject_code=? 
 and c.hod_status='1' and c.acad_status='1'
 group BY a.subject_code)";

        $query = $this->db->query($sql,array($syear, $sess,$course,$syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function count_old_table($syear, $sess,$course){
		$sql = "SELECT count(a.admn_no) AS stu_count
FROM old_stu_course a 
INNER JOIN reg_regular_form c ON c.form_id=a.form_id AND c.admn_no=a.admn_no
inner join users d on d.id=c.admn_no and d.status='A'
where c.hod_status='1' AND c.acad_status='1' and a.sub_offered_id IN ( SELECT a.id FROM old_subject_offered a WHERE a.session_year=? AND a.`session`=? AND a.sub_code=?)";

        $query = $this->db->query($sql,array($syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->stu_count;
        } else {
            return false;
        }
		
		
	}
	function count_cbcs_table($syear, $sess,$course){
		$sql = "SELECT count(a.admn_no) AS stu_count
FROM cbcs_stu_course a 
INNER JOIN reg_regular_form c ON c.form_id=a.form_id AND c.admn_no=a.admn_no
inner join users d on d.id=c.admn_no and d.status='A'
where c.hod_status='1' AND c.acad_status='1' and a.sub_offered_id IN ( SELECT a.id FROM cbcs_subject_offered a WHERE a.session_year=? AND a.`session`=? AND a.sub_code=? )";

        $query = $this->db->query($sql,array($syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->stu_count;
        } else {
            return false;
        }
		
		
	}
	
		function count_pre_table($syear, $sess,$course){
		$sql = "SELECT count(a.admn_no) AS stu_count FROM pre_stu_course a 
		INNER JOIN reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
		inner join users d on d.id=c.admn_no and d.status='A'
		where a.session_year=? AND a.`session`=? AND a.subject_code=? and b.hod_status='1' and b.acad_status='1' and (a.remark2='1' || a.remark2='3')";

        $query = $this->db->query($sql,array($syear, $sess,$course));

      // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->stu_count;
        } else {
            return false;
        }
		
		
	}
	
	function get_course_wise_student_final_all($syear, $sess,$course)
	{
		
		$sql = "(SELECT a.form_id,a.admn_no,CONCAT_WS(' ',f.first_name,f.middle_name,f.last_name)AS sname,a.sub_offered_id,a.subject_code,a.subject_name,b.session_year,b.`session`,
b.semester,f.dept_id,a.course,a.branch,h.domain_name as email,g.mobile_no,a.sub_category_cbcs_offered AS section
 FROM old_stu_course a 
INNER JOIN old_subject_offered b ON b.id=a.sub_offered_id 
INNER JOIN user_details f ON f.id=a.admn_no
inner join reg_regular_form e on e.form_id=a.form_id and e.admn_no=a.admn_no
inner join users i on i.id=e.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.sub_code=?
and e.hod_status='1' and e.acad_status='1'
ORDER BY b.dept_id,b.course_id,b.branch_id,b.semester,a.admn_no)
UNION
(SELECT a.form_id,a.admn_no,CONCAT_WS(' ',f.first_name,f.middle_name,f.last_name)AS sname,a.sub_offered_id,a.subject_code,a.subject_name,b.session_year,b.`session`,
b.semester,f.dept_id,a.course,a.branch,h.domain_name as email,g.mobile_no,a.sub_category_cbcs_offered AS section
 FROM cbcs_stu_course a 
INNER JOIN cbcs_subject_offered b ON b.id=a.sub_offered_id
INNER JOIN user_details f ON f.id=a.admn_no
inner join reg_regular_form e on e.form_id=a.form_id and e.admn_no=a.admn_no
inner join users i on i.id=e.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.sub_code=?
and e.hod_status='1' and e.acad_status='1'
ORDER BY b.dept_id,b.course_id,b.branch_id,b.semester,a.admn_no)
";

        $query = $this->db->query($sql,array($syear, $sess,$course,$syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
             return $query->result();
        } else {
            return false;
        }
		
		
	}
	function get_course_wise_student_pre_all($syear, $sess,$course)
	{
		
		/*$sql = "(SELECT a.form_id,a.admn_no,CONCAT_WS(' ',f.first_name,f.middle_name,f.last_name)AS sname,a.sub_offered_id,a.subject_code,a.subject_name,b.session_year,b.`session`,
b.semester,b.dept_id,b.course_id,b.branch_id
 FROM pre_stu_course a 
INNER JOIN old_subject_offered b ON b.id=a.sub_offered_id
INNER JOIN user_details f ON f.id=a.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.sub_code=?
ORDER BY b.dept_id,b.course_id,b.branch_id,b.semester,a.admn_no)";*/

$sql="(
SELECT a.form_id,a.admn_no, CONCAT_WS(' ',f.first_name,f.middle_name,f.last_name) AS 
sname,a.sub_offered_id,a.subject_code,a.subject_name,b.session_year,b.`session`, b.semester,b.dept_id,b.course_id,b.branch_id,h.domain_name as email,g.mobile_no
FROM
 pre_stu_course a
INNER JOIN old_subject_offered b ON CONCAT('o',b.id)=a.sub_offered_id
INNER JOIN user_details f ON f.id=a.admn_no
inner join reg_regular_form e on e.form_id=a.form_id and e.admn_no=a.admn_no
inner join users i on i.id=e.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.sub_code=?
and e.hod_status='1' and e.acad_status='1' AND (a.remark2='1' || a.remark2='3')
ORDER BY b.dept_id,b.course_id,b.branch_id,b.semester,a.admn_no) UNION
 (
SELECT a.form_id,a.admn_no, CONCAT_WS(' ',f.first_name,f.middle_name,f.last_name) AS 
sname,a.sub_offered_id,a.subject_code,a.subject_name,b.session_year,b.`session`, b.semester,b.dept_id,b.course_id,b.branch_id,h.domain_name as email,g.mobile_no
FROM
 pre_stu_course a
INNER JOIN cbcs_subject_offered b ON CONCAT('c',b.id)=a.sub_offered_id
INNER JOIN user_details f ON f.id=a.admn_no
inner join reg_regular_form e on e.form_id=a.form_id and e.admn_no=a.admn_no
inner join users i on i.id=e.admn_no and i.status='A'
inner join user_other_details g on g.id=a.admn_no
left join emaildata h on h.admission_no=a.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.sub_code=?
and e.hod_status='1' and e.acad_status='1' AND (a.remark2='1' || a.remark2='3')
ORDER BY b.dept_id,b.course_id,b.branch_id,b.semester,a.admn_no)";

        $query = $this->db->query($sql,array($syear, $sess,$course,$syear, $sess,$course));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function get_program_details_pre($syear, $sess,$did,$course,$branch,$semester){

if($course=='prep' && $branch=='prep'){
if($sess == 'Winter'){
$semester=0;
}elseif($sess == 'Monsoon'){
$semester=-1;
}
$sql="SELECT b.*, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS sname,c.dept_id,b.semester,b.course_id,b.branch_id,d.fee_amt,d.transaction_id,d.fee_date
FROM  reg_regular_form b
INNER JOIN users g ON b.admn_no=g.id
INNER JOIN user_details c ON c.id=b.admn_no
INNER JOIN reg_regular_fee d ON b.form_id=d.form_id
INNER JOIN pre_stu_course e ON e.form_id=d.form_id
inner JOIN stu_academic f ON f.admn_no=e.admn_no
WHERE b.session_year='$syear' AND b.`session`='$sess' AND e.course='prep' AND e.branch='prep' AND f.semester='$semester' AND b.hod_status='1' AND b.acad_status='1' AND (b.`status` IS  NULL || b.`status`='0'  || b.`status`='') AND g.status='A' /*AND b.admn_no IN ('16dr000231','18dr0113','19dr0038','19dr0174')*/
GROUP BY b.form_id
ORDER BY b.admn_no";
}else{
if($syear!='' && $syear!='none'){
$year= "b.session_year='$syear'";
}
else{
$year="1=1";
}
if($sess!='' && $sess!='none'){
$se= "b.`session`='$sess'";
}
else{
$se="1=1";
}
if($did!='' && $did != 'none'){
$dept= "c.dept_id='$did'";
}
else{
$dept="1=1";
}
if($course!='' && $course != 'none'){
$cid= "b.course_id='$course'";
}
else{
$cid="1=1";
}
if($branch!='' && $branch!='none'){
$bid= "b.branch_id='$branch'";
}
else{
$bid="1=1";
}
if($semester!='' && $semester!='none'){
$sem= "b.semester='$semester'";
}
else{
$sem="1=1";
}
if($course=='phd'){
$course='jrf';
$cid= "b.course_id='$course'";
}
$sql="SELECT b.*, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS sname,c.dept_id,b.semester,b.course_id,b.branch_id,d.fee_amt,d.transaction_id,d.fee_date
FROM  reg_regular_form b
INNER JOIN users g ON b.admn_no=g.id
INNER JOIN user_details c ON c.id=b.admn_no
INNER JOIN reg_regular_fee d ON b.form_id=d.form_id
WHERE $year AND $se AND $dept AND $cid AND $bid AND $sem AND b.hod_status='1' AND b.acad_status='1' AND (b.`status` IS  NULL || b.`status`='0'  || b.`status`='') AND g.status='A' /*AND b.admn_no IN ('16dr000231','18dr0113','19dr0038','19dr0174')*/
GROUP BY b.form_id
ORDER BY b.admn_no";
}
$query=$this->db->query($sql);
//echo $this->db->last_query();// die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }

}

	
	

function get_program_details_pre_only($syear, $sess,$did,$course,$branch,$semester){

if($course=='prep' && $branch=='prep'){
if($sess == 'Winter'){
$semester=0;
}elseif($sess == 'Monsoon'){
$semester=-1;
}
$sql="SELECT a.*, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS sname,c.dept_id,b.semester,b.course_id,b.branch_id,d.fee_amt,d.transaction_id,d.fee_date
FROM reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN user_details c ON c.id=b.admn_no
INNER JOIN reg_regular_fee d ON b.form_id=d.form_id
inner JOIN stu_academic f ON f.admn_no=a.admn_no
inner join users g on g.id=b.admm_no and g.status='A'
WHERE b.session_year='$syear' AND b.`session`='$sess' AND a.course='prep' AND a.branch='prep' AND f.semester='$semester' and a.remark2='3'
AND b.hod_status='1' and b.acad_status='1' and  a.course_aggr_id LIKE 'verified|%'
GROUP BY b.admn_no";
}else{
if($syear!='' && $syear!='none'){
$year= "b.session_year='$syear'";
}
else{
$year="1=1";
}
if($sess!='' && $sess!='none'){
$se= "b.`session`='$sess'";
}
else{
$se="1=1";
}
if($did!='' && $did != 'none'){
$dept= "c.dept_id='$did'";
}
else{
$dept="1=1";
}
if($course!='' && $course!='none'){
$cid= "b.course_id='$course'";
}
else{
$cid="1=1";
}
if($branch!='' && $branch!='none'){
$bid= "b.branch_id='$branch'";
}
else{
$bid="1=1";
}
if($semester!='' && $semester!='none'){
$sem= "b.semester='$semester'";
}
else{
$sem="1=1";
}
$sql="SELECT  CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS sname,c.dept_id,b.semester,b.course_id,b.branch_id,d.fee_amt,d.transaction_id,d.fee_date
FROM  reg_regular_form b
INNER JOIN user_details c ON c.id=b.admn_no
INNER JOIN reg_regular_fee d ON b.form_id=d.form_id
inner join users e on e.id=b.admm_no and e.status='A'
WHERE $year AND $se AND $dept AND $cid AND $bid AND $sem /*and a.remark2='3'
AND b.hod_status='1' and b.acad_status='1' and  a.course_aggr_id LIKE 'verified|%' */
AND (b.`status` IS  NULL || b.`status`='0'  || b.`status`='')
GROUP BY b.admn_no";
}
       //echo $this->db->last_query(); die();
$query=$this->db->query($sql);
//echo '<br><br>'.$this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
}

	function get_pre_registration_details($id){
		
		$sql="SELECT * FROM pre_stu_course WHERE form_id=?";

        $query = $this->db->query($sql,array($id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	function get_program_details_final($syear, $sess,$course,$branch,$semester){
		/*$sql="SELECT p.* FROM(SELECT a.*,CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name)AS sname,c.dept_id,b.semester,b.course_id,b.branch_id
FROM cbcs_stu_course a
INNER JOIN reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN user_details c ON c.id=b.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.course_id=? AND b.branch_id=? AND b.semester=?
GROUP BY b.admn_no
UNION
SELECT a.*,CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name)AS sname,c.dept_id,b.semester,b.course_id,b.branch_id
FROM old_stu_course a
INNER JOIN reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN user_details c ON c.id=b.admn_no
WHERE b.session_year=? AND b.`session`=? AND b.course_id=? AND b.branch_id=? AND b.semester=?
GROUP BY b.admn_no)p 
GROUP BY p.admn_no";

        $query = $this->db->query($sql,array($syear, $sess,$course,$branch,$semester,$syear, $sess,$course,$branch,$semester));*/

      // echo $this->db->last_query(); die();
	  
	  if($course== 'phd'){
        	$course='jrf';
        }
		
		if($course<>'none'  && $course<>'' && $course<>null)
		{
		$crs_append="  AND b.course_id=? "	;
		$sec_array[]= $course;
		}
		else
		{
			$crs_append=" ";
			
		}
	    if($branch<>'none'  && $branch<>'' && $branch<>null)
		{
		$br_append=" AND b.branch_id=? "	;
		$sec_array[]= $branch;
		
		}
		else
		{
			$br_append=" ";
			
		}
		   if($semester<>'none'  && $semester<>'' && $semester<>null)
		{
		$sem_append=" AND b.semester=? "	;
		$sec_array[]= $semester;
		
		}
		else
		{
			$sem_append=" ";
			
		}
		
		if(count($sec_array)>0)
		
		$sec_array1= array_merge(array($syear,$sess),$sec_array);
		else
			
		$sec_array1=array($syear,$sess);
		//print_r($sec_array1); die();
		
        $sql="SELECT b.*, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS sname,c.dept_id,b.semester,b.course_id,b.branch_id
FROM reg_regular_form b 
INNER JOIN user_details c ON c.id=b.admn_no
inner join users d on d.id=b.admn_no and d.status='A'
WHERE b.hod_status='1' and b.acad_status='1' and b.session_year=? AND b.`session`=?   $crs_append $br_append $sem_append
AND b.re_id LIKE 'verified%'
GROUP BY b.admn_no";
$query = $this->db->query($sql,$sec_array1);
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	function get_post_registration_details($id){
		
		$sql="SELECT subject_code,subject_name,sub_category FROM cbcs_stu_course WHERE form_id=?
UNION
SELECT subject_code,subject_name,sub_category FROM old_stu_course WHERE form_id=? ";

        $query = $this->db->query($sql,array($id,$id));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
		
	}
	
	
	
	
	//==============================================================================================================
	
	
	function get_post_fees_details($formid,$admn_no){
		$sql="select a.* from reg_regular_fee a where a.form_id='$formid' and a.admn_no='$admn_no'";
		$query = $this->db->query($sql);
		//echo $this->db->last_query();
		 if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
	}
	function check_reg_status($admm_no,$syear,$sess){
        
        $sql = "select a.* from reg_regular_form a  inner join users b on b.id=a.admn_no where a.admn_no=?  and a.session_year=? and a.`session`=? and a.hod_status='1' and a.acad_status='1' and b.status='A'
                ORDER BY timestamp DESC    LIMIT 1;";

        $query = $this->db->query($sql,array($admm_no,$syear,$sess));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row();;
        } else {
            return false;
        }
        
    }
function tranfer_student_to_table($form_id,$admn_no){
		$query=$this->db->query("select a.* from pre_stu_course a where a.form_id='$form_id' and a.admn_no='$admn_no'");
		$result=$query->result();
		// echo "<pre>";
		// print_r($result);
		// echo "</pre>";
		foreach ($result as $r) {
			if(substr($r->sub_offered_id,0,1) == 'c'){
				$tbl='cbcs_stu_course';
				// $data=array (
    //         'form_id' => $r->form_id,
    //         'admn_no' => $r->admn_no,
    //         'sub_offered_id' => preg_replace("/[^a-zA-Z]/", "", $r->sub_offered_id),
    //         'subject_code' => $r->subject_code,
    //         'course_aggr_id' => $r->course_aggr_id,
    //         'subject_name' => $r->subject_name,
    //         'priority' => $r->priority,
    //         'sub_category' => $r->sub_category,
    //         'sub_category_cbcs_offered' => $r->sub_category_cbcs_offered,
    //         'course' => $r->course,
    //         'branch' => $r->branch,
    //         'session_year' => $r->session_year,
    //         'session' => $r->session,
    //         'updated_at' => $r->updated_at
    //     );
			}elseif(substr($r->sub_offered_id,0,1) == 'o'){
				$tbl='old_stu_course';
				// $data=array (
		  //           'form_id' => $r->form_id,
		  //           'admn_no' => $r->admn_no,
		  //           'sub_offered_id' => preg_replace("/[^a-zA-Z]/", "", $r->sub_offered_id),
		  //           'subject_code' => $r->subject_code,
		  //           'course_aggr_id' => $r->course_aggr_id,
		  //           'subject_name' => $r->subject_name,
		  //           'priority' => $r->priority,
		  //           'sub_category' => $r->sub_category,
		  //           'sub_category_cbcs_offered' => $r->sub_category_cbcs_offered,
		  //           'course' => $r->course,
		  //           'branch' => $r->branch,
		  //           'session_year' => $r->session_year,
		  //           'session' => $r->session,
		  //           'updated_at' => $r->updated_at
		  //       );
			}
			$data=array (
            'form_id' => $r->form_id,
            'admn_no' => $r->admn_no,
            //'sub_offered_id' => preg_replace("/[^a-zA-Z]/", "", $r->sub_offered_id),
            'sub_offered_id' => substr($r->sub_offered_id, 1),
            'subject_code' => $r->subject_code,
            'course_aggr_id' => $r->course_aggr_id,
            'subject_name' => $r->subject_name,
            'priority' => $r->priority,
            'sub_category' => $r->sub_category,
            'sub_category_cbcs_offered' => $r->sub_category_cbcs_offered,
            'course' => $r->course,
            'branch' => $r->branch,
            'session_year' => $r->session_year,
            'session' => $r->session,
            'updated_at' => $r->updated_at
        );

		
		if($r->remark2 == 1){
			$this->db->insert($tbl, $data);
			$id=$r->id;
			$user_id=$this->session->userdata('id');
			$date=date('Y-m-d H:i:s');
			$verified='verified|'.$user_id.'|'.$date;
			$sub_code=$r->subject_code;
			$this->db->query("UPDATE pre_stu_course SET course_aggr_id='$verified',remark2='3' where id='$id' and subject_code='$sub_code'");
			//$this->db->query("UPDATE reg_regular_form SET re_id='$verified',status='1' where form_id='$form_id' and admn_no='$admn_no'");
		}

		//if($r->remark2 == 0 || $r->remark2 == 1){
			//$this->db->insert($tbl, $data);
		//	$id=$r->id;
			// $user_id=$this->session->userdata('id');
			// $date=date('Y-m-d H:i:s');
			// $verified='verified|'.$user_id.'|'.$date;
			// //$this->db->query("UPDATE pre_stu_course SET course_aggr_id='$verified',remark2='3' where id='$id'");
			// $this->db->query("UPDATE reg_regular_form SET re_id='$verified',status='1' where form_id='$form_id' and admn_no='$admn_no'");
		//}

			
			//echo $this->db->last_query();exit;
		}
		$user_id=$this->session->userdata('id');
			$date=date('Y-m-d H:i:s');
			$verified='verified|'.$user_id.'|'.$date;
			//$this->db->query("UPDATE pre_stu_course SET course_aggr_id='$verified',remark2='3' where id='$id'");
			//$this->db->query("UPDATE reg_regular_form SET re_id='$verified',status='1' where form_id='$form_id' and admn_no='$admn_no'");
			$this->db->query("UPDATE reg_regular_form SET re_id='$verified',status='1' where form_id='$form_id' and admn_no='$admn_no' and hod_status='1' and acad_status='1'");
			//$query=$this->db->query("SELECT * FROM stu_academic a  WHERE a.admn_no='$admn_no'");
			$query=$this->db->query("SELECT * FROM reg_regular_form a  WHERE a.admn_no='$admn_no' and a.form_id='$form_id' and a.hod_status='1' and a.acad_status='1'");
			$result=$query->result();
			//$sem=$result[0]->semester+1;
			$sem=$result[0]->semester;
			$this->db->query("UPDATE stu_academic set semester='$sem' WHERE admn_no='$admn_no'");
			//echo  $this->db->last_query();exit;
	}
	/*function tranfer_student_to_table($form_id,$admn_no){
		$query=$this->db->query("select a.* from pre_stu_course a where a.form_id='$form_id' and a.admn_no='$admn_no'");
		$result=$query->result();
		
		foreach ($result as $r) {
			if(substr($r->sub_offered_id,0,1) == 'c'){
				$tbl='cbcs_stu_course';
				
			}elseif(substr($r->sub_offered_id,0,1) == 'o'){
				$tbl='old_stu_course';
				
			}
			$data=array (
            'form_id' => $r->form_id,
            'admn_no' => $r->admn_no,
            //'sub_offered_id' => preg_replace("/[^a-zA-Z]/", "", $r->sub_offered_id),
            'sub_offered_id' => substr($r->sub_offered_id, 1),
            'subject_code' => $r->subject_code,
            'course_aggr_id' => $r->course_aggr_id,
            'subject_name' => $r->subject_name,
            'priority' => $r->priority,
            'sub_category' => $r->sub_category,
            'sub_category_cbcs_offered' => $r->sub_category_cbcs_offered,
            'course' => $r->course,
            'branch' => $r->branch,
            'session_year' => $r->session_year,
            'session' => $r->session,
            'updated_at' => $r->updated_at
        );

		
		if($r->remark2 == 1){
			$this->db->insert($tbl, $data);
			$id=$r->id;
			$user_id=$this->session->userdata('id');
			$date=date('Y-m-d H:i:s');
			$verified='verified|'.$user_id.'|'.$date;
			$sub_code=$r->subject_code;
			$this->db->query("UPDATE pre_stu_course SET course_aggr_id='$verified',remark2='3' where id='$id' and subject_code='$sub_code'");
		
		}

		

			
			
		}
		$user_id=$this->session->userdata('id');
		$date=date('Y-m-d H:i:s');
		$verified='verified|'.$user_id.'|'.$date;
		
		$this->db->query("UPDATE reg_regular_form SET re_id='$verified',status='1' where form_id='$form_id' and admn_no='$admn_no'");
	}*/
	
	//new work-------------
	function student_details($sy,$sess,$admn_no){
		$sql="SELECT a.*,b.status as u_status
FROM reg_regular_form a
INNER JOIN users b ON a.admn_no=b.id 
WHERE a.admn_no='$admn_no' AND a.session_year='$sy' AND a.`session`='$sess'
and a.hod_status='1' and a.acad_status='1' and b.status='A'";
		$query=$this->db->query($sql);
		return $query->result();
	}
	

}

?>