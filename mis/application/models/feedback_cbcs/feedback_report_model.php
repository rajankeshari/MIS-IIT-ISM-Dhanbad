<?php
 
class Feedback_report_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
    }

    function get_faculty_class_hours($id,$syear,$sess) {
      $myquery="(SELECT g.group_no,null as session_id,a.id AS map_id,a.session_year,a.`session`,a.dept_id,a.course_id,
a.branch_id,a.semester, b.emp_no,b.coordinator,b.sub_id,c.name,
 CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name) AS f_name, 
a.sub_name,b.section,'c' AS rstatus,a.lecture,a.tutorial,a.practical
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details d ON d.id=b.emp_no
INNER JOIN cbcs_departments c ON a.dept_id=c.id
LEFT JOIN cbcs_prac_group_attendance g ON g.subject_id=CONCAT('c',a.id) AND g.sub_id=a.sub_code
WHERE a.dept_id=? AND a.session_year=? AND a.`session`=?
AND (a.sub_type='Theory' OR a.sub_type='Sessional' OR a.sub_type='Modular') 
AND a.lecture<>'0'
ORDER BY f_name,a.course_id,a.branch_id,a.semester,g.group_no
)union
(SELECT g.group_no,null as session_id,a.id AS map_id,a.session_year,a.`session`,a.dept_id,a.course_id,
a.branch_id,a.semester, b.emp_no,b.coordinator,b.sub_id,c.name,
 CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name) AS f_name, 
a.sub_name,b.section,'o' AS rstatus,a.lecture,a.tutorial,a.practical
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details d ON d.id=b.emp_no
INNER JOIN cbcs_departments c ON a.dept_id=c.id
LEFT JOIN cbcs_prac_group_attendance g ON g.subject_id=CONCAT('c',a.id) AND g.sub_id=a.sub_code
WHERE a.dept_id=? AND a.session_year=? AND a.`session`=?
AND (a.sub_type='Theory' OR a.sub_type='Sessional' OR a.sub_type='Modular') 
AND a.lecture<>'0'
ORDER BY f_name,a.course_id,a.branch_id,a.semester,g.group_no
)";
	  
        $query = $this->db->query($myquery,array($id,$syear,$sess,$id,$syear,$sess));

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
	//========================Departmental Common==================================
	
		    function get_faculty_dept_common($id,$syear,$sess) {
      $myquery="SELECT pp.* FROM ((SELECT g.group_no,null as session_id,a.id AS map_id,a.session_year,a.`session`,a.dept_id,a.course_id,
a.branch_id,a.semester, b.emp_no,b.coordinator,b.sub_id,c.name,
 CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name) AS f_name, 
a.sub_name,b.section,'c' AS rstatus,a.lecture,a.tutorial,a.practical
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details d ON d.id=b.emp_no
INNER JOIN cbcs_departments c ON a.dept_id=c.id
LEFT JOIN cbcs_prac_group_attendance g ON g.subject_id=CONCAT('c',a.id) AND g.sub_id=a.sub_code
WHERE a.dept_id='comm' AND a.session_year=? AND a.`session`=?
AND (a.sub_type='Theory' OR a.sub_type='Sessional' OR a.sub_type='Modular') 
AND a.lecture<>'0'
ORDER BY f_name,a.course_id,a.branch_id,a.semester,g.group_no
)union
(SELECT g.group_no,null as session_id,a.id AS map_id,a.session_year,a.`session`,a.dept_id,a.course_id,
a.branch_id,a.semester, b.emp_no,b.coordinator,b.sub_id,c.name,
 CONCAT(d.first_name,' ',d.middle_name,' ',d.last_name) AS f_name, 
a.sub_name,b.section,'o' AS rstatus,a.lecture,a.tutorial,a.practical
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details d ON d.id=b.emp_no
INNER JOIN cbcs_departments c ON a.dept_id=c.id
LEFT JOIN cbcs_prac_group_attendance g ON g.subject_id=CONCAT('c',a.id) AND g.sub_id=a.sub_code
WHERE a.dept_id='comm' AND a.session_year=? AND a.`session`=?
AND (a.sub_type='Theory' OR a.sub_type='Sessional' OR a.sub_type='Modular') 
AND a.lecture<>'0'
ORDER BY f_name,a.course_id,a.branch_id,a.semester,g.group_no
))pp
INNER JOIN user_details qq ON qq.id=pp.emp_no
WHERE qq.dept_id=?";
	  
        $query = $this->db->query($myquery,array($syear,$sess,$syear,$sess,$id));

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
 
	
	
	//================================================================================
    
        function get_faculty_byDept($id) {
        $myquery = "select a.id,concat(a.first_name,' ',a.middle_name,' ',a.last_name) as f_name,b.auth_id from 
user_details a 
inner join emp_basic_details b on a.id=b.emp_no
inner join users c on c.id=b.emp_no
where a.dept_id='".$id."' and b.auth_id='ft' and c.status='A' order by f_name
 " ;
      
        $query = $this->db->query($myquery);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }
    function get_total_classes($mid,$subid,$dfrom,$dto,$emp_no)
    {
		//echo $mid;echo $subid; echo $dfrom; echo $dto;echo $emp_no;die();
    if($dfrom=='01-01-1970' && $dto='01-01-1970')
    {
     $myquery = "  SELECT a.* FROM cbcs_class_engaged a where a.engaged_by='".$emp_no."' AND a.subject_offered_id='".$mid."' ORDER BY id desc" ;
    }
    else{    
    $myquery = " SELECT a.* FROM cbcs_class_engaged a WHERE a.engaged_by='".$emp_no."' AND a.subject_offered_id='".$mid."' ORDER BY id desc
	 and STR_TO_DATE(date, '%d-%m-%Y')   BETWEEN STR_TO_DATE('".$dfrom."', '%d-%m-%Y')     AND STR_TO_DATE('".$dto."', '%d-%m-%Y')" ;
    }
      
    
        $query = $this->db->query($myquery);
        //echo $this->db->last_query();die();
       
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
        
    }
    
    function get_date_of_classes($mid,$subid,$dfrom,$dto,$gid,$emp_no)
    {
     
        if($gid>=1){
        $gr="and group_no=".$gid; 
        }else{
            $gr="";
        }
    if($dfrom=='01-01-1970' && $dto='01-01-1970')
    {
        //$myquery = " select * from class_engaged where map_id=".$mid." and sub_id='".$subid."' ".$gr."  order by STR_TO_DATE(date, '%d-%m-%Y')" ;
		
		$myquery = " SELECT a.* FROM cbcs_class_engaged a where a.engaged_by='".$emp_no."' AND a.subject_offered_id='".$mid."' ".$gr."  order by STR_TO_DATE(date, '%d-%m-%Y')" ;
 
       }
       else{    
       $myquery = " SELECT a.* FROM cbcs_class_engaged a where a.engaged_by='".$emp_no."' AND a.subject_offered_id='".$mid."' ".$gr."  and 
    STR_TO_DATE(date, '%d-%m-%Y') BETWEEN STR_TO_DATE('".$dfrom."', '%d-%m-%Y') 
    AND STR_TO_DATE('".$dto."', '%d-%m-%Y') 
    order by STR_TO_DATE(date, '%d-%m-%Y')" ;
       }


           $query = $this->db->query($myquery);
          // echo $this->db->last_query();

           if ($query->num_rows() > 0) 
           {
               return $query->result();
           } else {
               return FALSE;
           }
        
    }
    
    function get_aggr_id_year($subid,$sem)
    {
       $myquery = "select aggr_id from course_structure where id='".$subid."' and semester='".$sem."' " ;
       $query = $this->db->query($myquery);
           if ($query->num_rows() > 0) 
           {
               return $query->row();
           } else {
               return FALSE;
           }
        
    }

    function get_map_details($mapid,$empno) {
		
		if(substr($mapid, 0, 1)=='c'){
			$tbl=" cbcs_subject_offered ";
			$tbl_desc=" cbcs_subject_offered_desc ";
			
		}
		if(substr($mapid, 0, 1)=='o'){
			$tbl=" old_subject_offered ";
			$tbl_desc="old_subject_offered_desc ";
		}
		
        $myquery = "(SELECT CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name)AS fname,
d.name AS cname,e.name AS bname,b.semester,b.sub_name,CONCAT_WS('-',b.lecture,b.tutorial,b.practical)AS ltp,b.sub_code from ".$tbl_desc." a
inner join ".$tbl." b on b.id=a.sub_offered_id
INNER JOIN user_details c ON c.id=a.emp_no
INNER JOIN cbcs_courses d ON d.id=b.course_id
INNER JOIN cbcs_branches e ON e.id=b.branch_id
where a.emp_no =? AND b.id=?)" ;
//echo $myquery;      
        $query = $this->db->query($myquery,array($empno,substr($mapid,1)));
      
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    //=======================================Feedback Report=========================

    function get_faculty_subject_list($syear,$sess,$emp_no,$dept_id){

      if($emp_no=='all'){

            $myquery = "			
			SELECT t1.*,CONCAT_WS(' ',t2.salutation,t2.first_name,t2.middle_name,t2.last_name)AS fname,t3.name AS dname from
(SELECT t.* from
(SELECT CONCAT('c',a.id) AS sub_offered_id,b.emp_no,a.sub_code,b.section,
b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('c_',a.sub_code) AS sub_code1 FROM cbcs_subject_offered a 
INNER JOIN cbcs_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional')
UNION all
SELECT CONCAT('o',a.id) AS sub_offered_id,b.emp_no,a.sub_code,
b.section,b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('o_',a.sub_code) AS sub_code1 FROM old_subject_offered a 
INNER JOIN old_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional'))t
INNER JOIN user_details u ON u.id=t.emp_no
WHERE u.dept_id=?
GROUP BY t.sub_code,t.emp_no)t1
INNER JOIN user_details t2 ON t2.id=t1.emp_no
INNER JOIN departments t3  ON t3.id=t2.dept_id
ORDER BY t2.dept_id,t2.first_name,t2.middle_name,t2.last_name
" ;


        $query = $this->db->query($myquery,array($syear,$sess,$syear,$sess,$dept_id));

      }else{

      $myquery = "SELECT t1.*,CONCAT_WS(' ',t2.salutation,t2.first_name,t2.middle_name,t2.last_name)AS fname,t3.name AS dname from
(SELECT t.* from
(SELECT CONCAT('c',a.id) AS sub_offered_id,b.emp_no,a.sub_code,b.section,
b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('c_',a.sub_code) AS sub_code1 FROM cbcs_subject_offered a 
INNER JOIN cbcs_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional')
UNION all
SELECT CONCAT('o',a.id) AS sub_offered_id,b.emp_no,a.sub_code,
b.section,b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('o_',a.sub_code) AS sub_code1 FROM old_subject_offered a 
INNER JOIN old_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional'))t
WHERE t.emp_no=?
GROUP BY t.sub_code,t.emp_no)t1
INNER JOIN user_details t2 ON t2.id=t1.emp_no
INNER JOIN departments t3  ON t3.id=t2.dept_id ORDER BY t2.dept_id,t2.first_name,t2.middle_name,t2.last_name" ;


        $query = $this->db->query($myquery,array($syear,$sess,$syear,$sess,$emp_no));
      }
      
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }


    }
    //=================================DEPT ID WISE=======================================================

function get_faculty_subject_list_all($syear,$sess){

      $myquery = "SELECT t1.*,CONCAT_WS(' ',t2.salutation,t2.first_name,t2.middle_name,t2.last_name)AS fname,t3.name AS dname from
(SELECT t.*
FROM (
SELECT CONCAT('c',a.id) AS sub_offered_id,b.emp_no,a.sub_code,b.section, b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('c_',a.sub_code) AS sub_code1
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional') UNION ALL
SELECT CONCAT('o',a.id) AS sub_offered_id,b.emp_no,a.sub_code, b.section,b.coordinator,a.course_id,a.branch_id,a.session_year,a.`session`,a.sub_name,a.semester, CONCAT('o_',a.sub_code) AS sub_code1
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON b.sub_offered_id=a.id
WHERE a.session_year=? AND a.`session`=? AND a.course_id!='jrf' AND (a.sub_type='Theory' || a.sub_type='Sessional'))t
INNER JOIN user_details u ON u.id=t.emp_no
GROUP BY t.sub_code,t.emp_no)t1
INNER JOIN user_details t2 ON t2.id=t1.emp_no
INNER JOIN departments t3  ON t3.id=t2.dept_id ORDER BY t2.dept_id,t2.first_name,t2.middle_name,t2.last_name" ;


        $query = $this->db->query($myquery,array($syear,$sess,$syear,$sess));
      
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }


    }



    //======================================================================================================

    function get_total_student_count($sub_offered_id){

      if($sub_offered_id[0]=='o'){ $tbl='old_stu_course'; }
      if($sub_offered_id[0]=='c'){ $tbl='cbcs_stu_course'; }
      $id=substr($sub_offered_id, 1);

      $myquery = "SELECT COUNT(a.admn_no)AS cnt FROM ".$tbl." a WHERE a.sub_offered_id=?" ;

         $query = $this->db->query($myquery,array($id));
    
         if ($query->num_rows() > 0) {
             return $query->row()->cnt;
         } else {
             return FALSE;
         }


    }

function get_feedback_count($sub_offered_id,$sub_code){
  $id=substr($sub_offered_id, 1);
  $scode=$sub_offered_id[0].'_'.$sub_code;
  $myquery = "SELECT count(a.subject_id)AS cnt from fbs_feedback_details a WHERE a.map_id=?  AND a.subject_id=?; " ;
        $query = $this->db->query($myquery,array($id,$scode));
      
        if ($query->num_rows() > 0) {
            return $query->row()->cnt;
        } else {
            return FALSE;
        }

    }
    
    function get_faculty_name_dept($id){

      $myquery = "SELECT CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)AS fname,b.name AS dname FROM user_details a 
INNER JOIN departments b ON b.id=a.dept_id
WHERE a.id=?" ;
        $query = $this->db->query($myquery,array($id));
      
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }

    }

 function get_count_students_registered($syear,$sess,$sub_code,$emp_no){

  $myquery = " SELECT COUNT(p.admn_no) AS ctr
FROM((
SELECT a.*
FROM reg_regular_form a
INNER JOIN cbcs_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN cbcs_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=?) UNION (
SELECT a.*
FROM reg_regular_form a
INNER JOIN old_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN old_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=? ))p" ;
        $query = $this->db->query($myquery,array($syear,$sess,$sub_code,$emp_no,$syear,$sess,$sub_code,$emp_no));
      
        if ($query->num_rows() > 0) {
            return $query->row()->ctr;
        } else {
            return FALSE;
        }

 }
  function get_count_students_registered_common($syear,$sess,$sub_code,$emp_no,$section){

  $myquery = " SELECT COUNT(p.admn_no) AS ctr
FROM((
SELECT a.*
FROM reg_regular_form a
INNER JOIN cbcs_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN cbcs_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
INNER JOIN stu_section_data d ON d.admn_no=a.admn_no AND d.session_year='".$syear."'
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=? AND d.section='".$section."') UNION (
SELECT a.*
FROM reg_regular_form a
INNER JOIN old_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN old_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
INNER JOIN stu_section_data d ON d.admn_no=a.admn_no AND d.session_year='".$syear."'
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=? AND d.section='".$section."' ))p" ;
        $query = $this->db->query($myquery,array($syear,$sess,$sub_code,$emp_no,$syear,$sess,$sub_code,$emp_no));
      
        if ($query->num_rows() > 0) {
            return $query->row()->ctr;
        } else {
            return FALSE;
        }

 }

 function get_count_students_given_feedback($syear,$sess,$sub_code,$emp_no){
  $myquery = " 
SELECT COUNT(x.admn_no) AS ctr
FROM(
SELECT (a.admn_no)
FROM fb_student_feedback_cbcs a
WHERE a.admn_no IN (
SELECT p.admn_no
FROM((
SELECT a.*
FROM reg_regular_form a
INNER JOIN cbcs_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN cbcs_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=?) UNION (
SELECT a.*
FROM reg_regular_form a
INNER JOIN old_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN old_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=?))p) AND a.feedback_papers LIKE '%".$sub_code."%'
GROUP BY a.admn_no)x" ;
        $query = $this->db->query($myquery,array($syear,$sess,$sub_code,$emp_no,$syear,$sess,$sub_code,$emp_no));
      
        if ($query->num_rows() > 0) {
            return $query->row()->ctr;
        } else {
            return FALSE;
        }


 }
 function get_count_students_given_feedback_common($syear,$sess,$sub_code,$emp_no,$section){
  $myquery = " 
SELECT COUNT(x.admn_no) AS ctr
FROM(
SELECT (a.admn_no)
FROM fb_student_feedback_cbcs a
WHERE a.admn_no IN (
SELECT p.admn_no
FROM((
SELECT a.*
FROM reg_regular_form a
INNER JOIN cbcs_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN cbcs_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
INNER JOIN stu_section_data d ON d.admn_no=a.admn_no AND d.session_year='".$syear."'
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=? AND d.section='".$section."') UNION (
SELECT a.*
FROM reg_regular_form a
INNER JOIN old_stu_course b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
INNER JOIN old_subject_offered_desc c ON c.sub_offered_id=b.sub_offered_id
INNER JOIN stu_section_data d ON d.admn_no=a.admn_no AND d.session_year='".$syear."'
WHERE a.session_year=? AND a.`session`=? AND a.hod_status='1' 
AND a.acad_status='1' AND b.subject_code=? AND c.emp_no=? AND d.section='".$section."'))p) AND a.feedback_papers LIKE '%".$sub_code."%'
GROUP BY a.admn_no)x" ;
        $query = $this->db->query($myquery,array($syear,$sess,$sub_code,$emp_no,$syear,$sess,$sub_code,$emp_no));
      
        if ($query->num_rows() > 0) {
            return $query->row()->ctr;
        } else {
            return FALSE;
        }


 }

 function avg_feedback($sub_code,$emp_no){

      $myquery = "SELECT COUNT(b.feedback_id)AS cnt,SUM(b.rating)AS sum_feed, 
SUM(b.rating)/COUNT(b.feedback_id) AS 'avg' from fbs_feedback_details a 
INNER JOIN fbs_feedback b ON a.feedback_id=b.feedback_id
WHERE a.subject_id=? AND a.teacher_id=? ";
      $query = $this->db->query($myquery,array($sub_code,$emp_no));
      if ($query->num_rows() > 0) {
             return $query->row();
         } else {
             return FALSE;
         }

 }
  function avg_feedback_common($sub_code,$emp_no,$section){

      $myquery = "SELECT COUNT(b.feedback_id)AS cnt,SUM(b.rating)AS sum_feed, 
SUM(b.rating)/COUNT(b.feedback_id) AS 'avg' from fbs_feedback_details a 
INNER JOIN fbs_feedback b ON a.feedback_id=b.feedback_id
INNER JOIN cbcs_subject_offered_desc c ON c.sub_offered_id=a.map_id AND c.section='".$section."'
WHERE a.subject_id=? AND a.teacher_id=? ";
      $query = $this->db->query($myquery,array($sub_code,$emp_no));
      if ($query->num_rows() > 0) {
             return $query->row();
         } else {
             return FALSE;
         }

 }



//     function get_total_student_count($session_year,$session,$course_id,$branch_id,$semester){
// $myquery = "SELECT COUNT(a.admn_no)AS total_cnt FROM reg_regular_form a WHERE a.session_year=? AND a.`session`=?
// AND a.course_id=? AND a.branch_id=?  AND a.semester=?
// AND a.hod_status='1' AND a.acad_status='1'" ;


//         $query = $this->db->query($myquery,array($session_year,$session,$course_id,$branch_id,$semester));
      
//         if ($query->num_rows() > 0) {
//             return $query->row()->total_cnt;
//         } else {
//             return FALSE;
//         }


//     }

//     function get_feedback_count($sub_offered_id){

// $myquery = "SELECT COUNT(a.sub_id)AS cnt FROM fb_student_subject_desc a 
// INNER JOIN fb_student_subject_main b ON b.id=a.main_id
// WHERE a.sub_offered_id=? " ;


//         $query = $this->db->query($myquery,array($sub_offered_id));
      
//         if ($query->num_rows() > 0) {
//             return $query->row()->cnt;
//         } else {
//             return FALSE;
//         }

//     }
    
        

}
