<?php

class Student_registration_details_model_new extends CI_Model {

    function __construct() {
        parent::__construct();
    } 
 
        
    function get_list($syear,$sess)
    {
		
		
		$sql="SELECT p.* from
(SELECT b.dept_id,a.course_id,a.branch_id,a.semester,c.duration*2 AS fsem,COUNT(a.admn_no)AS cnt FROM reg_regular_form a
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN cbcs_courses c ON c.id=a.course_id
INNER JOIN users d ON d.id=a.admn_no
WHERE a.session_year=? AND a.`session`=? AND d.`status`='A'
AND a.course_id!='jrf' 
GROUP BY b.dept_id,a.course_id,a.branch_id,a.semester)p
WHERE p.semester=p.fsem
GROUP BY p.dept_id,p.course_id,p.branch_id,p.semester
ORDER BY p.dept_id,p.course_id,p.branch_id,p.semester ";

        
        $query = $this->db->query($sql,array($syear,$sess));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	
	function get_student_admn_no($syear,$sess,$dept_id,$course_id,$branch_id,$semester){
		$sql="SELECT a.* FROM final_semwise_marks_foil_freezed a WHERE a.session_yr=? AND a.`session`=?
AND a.dept=? AND a.course=? AND a.branch=? AND a.semester=? GROUP BY a.admn_no ORDER BY a.cgpa DESC LIMIT 1";

        
        $query = $this->db->query($sql,array($syear,$sess,$dept_id,$course_id,$branch_id,$semester));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();;
        } else {
            return false;
        }
	}
	
	function get_student_registration($admn_no){
		$sql="SELECT a.* FROM reg_regular_form a WHERE a.admn_no=? AND a.hod_status='1' AND a.acad_status='1'";

        
        $query = $this->db->query($sql,array($admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();;
        } else {
            return false;
        }
		
		
	}
	function get_registration_session_sessionyear_wise_comm($aggr_id,$semester,$section,$form_id)
	{
		$sem1=($semester.'_'.$section);
		$sql="(SELECT a.semester,b.subject_id,b.name FROM course_structure a
INNER JOIN subjects b ON b.id=a.id
WHERE a.aggr_id='".$aggr_id."' AND a.semester='".$sem1."' AND a.sequence NOT LIKE '%.%'
ORDER BY a.sequence+0)
UNION
(SELECT c.semester,b.subject_id,b.name FROM reg_regular_elective_opted a
INNER JOIN subjects b ON b.id=a.sub_id
INNER JOIN course_structure c ON c.id=b.id
 WHERE a.form_id='".$form_id."')";

        
        $query = $this->db->query($sql);

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();;
        } else {
            return false;
        }
		
	}
	function get_registration_session_sessionyear_wise($aggr_id,$semester,$form_id)
	{
		$sql="(SELECT a.semester,b.subject_id,b.name FROM course_structure a
INNER JOIN subjects b ON b.id=a.id
WHERE a.aggr_id='".$aggr_id."' AND a.semester='".$semester."' AND a.sequence NOT LIKE '%.%' AND b.name NOT LIKE '%Project%'
ORDER BY a.sequence+0)
UNION
(SELECT c.semester,b.subject_id,b.name FROM reg_regular_elective_opted a
INNER JOIN subjects b ON b.id=a.sub_id
INNER JOIN course_structure c ON c.id=b.id
 WHERE a.form_id='".$form_id."')";

        
        $query = $this->db->query($sql);

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();;
        } else {
            return false;
        }
		
	}
	
	function get_registration_session_sessionyear_wise_cbcs($form_id,$admn_no){
		
		$sql=" SELECT b.semester,a.subject_code AS subject_id,a.subject_name AS name FROM cbcs_stu_course a
INNER JOIN reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
 WHERE  a.form_id =? and a.admn_no=? and b.hod_status='1' and b.acad_status='1'
union
SELECT b.semester,a.subject_code AS subject_id,a.subject_name AS name FROM old_stu_course a 
INNER JOIN reg_regular_form b ON b.form_id=a.form_id AND b.admn_no=a.admn_no
WHERE  a.form_id =? and a.admn_no=?  and b.hod_status='1' and b.acad_status='1'";

        
        $query = $this->db->query($sql,array($form_id,$admn_no,$form_id,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();;
        } else {
            return false;
        }
		
		
	}
	
	function get_grade($admn_no,$sub_code){
		
		$sql=" 
SELECT a.* FROM final_semwise_marks_foil_desc_freezed a
INNER JOIN final_semwise_marks_foil_freezed b ON b.id=a.foil_id
WHERE a.admn_no=? AND a.sub_code=? AND b.tot_cr_hr IS NOT null ";

        
        $query = $this->db->query($sql,array($admn_no,$sub_code));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();;
        } else {
            return false;
        }
		
		
	}
	
	function get_offered_course_session_sessionyear_wise_cbcs($dept_id,$course_id,$branch_id,$semester,$sy,$sess)
	{
		
		$sql=" SELECT a.sub_code,a.sub_name FROM cbcs_subject_offered a WHERE a.dept_id=? AND a.course_id=?
AND a.branch_id=? AND a.semester=? AND a.session_year=? AND a.`session`=?
UNION
SELECT a.sub_code,a.sub_name FROM old_subject_offered a WHERE a.dept_id=? AND a.course_id=?
AND a.branch_id=? AND a.semester=? AND a.session_year=?  AND a.`session`=?";

        
        $query = $this->db->query($sql,array($dept_id,$course_id,$branch_id,$semester,$sy,$sess,$dept_id,$course_id,$branch_id,$semester,$sy,$sess));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();;
        } else {
            return false;
        }
		
	}
	function get_deptartment($admn_no){
		$sql=" SELECT dept_id FROM user_details WHERE id=?";

        
        $query = $this->db->query($sql,array($admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();;
        } else {
            return false;
        }
		
	}
	
	function count_subjects($admn_no,$foil_id){
		//$sql=" SELECT COUNT(a.sub_code)AS cnt FROM final_semwise_marks_foil_desc_freezed a WHERE a.admn_no=? and a.foil_id=? AND a.grade NOT IN ('F','I') ";
		
		$sql="SELECT  COUNT(p.sub_code)AS cnt FROM(
SELECT a.*
FROM final_semwise_marks_foil_desc_freezed a
WHERE a.admn_no=? AND a.foil_id=? AND a.grade NOT IN ('F','I')
GROUP BY a.sub_code)p";

        
        $query = $this->db->query($sql,array($admn_no,$foil_id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row()->cnt;
        } else {
            return false;
        }
		
		
	}
	//summer
	function count_subjects_summer($admn_no,$foil_id){
		//$sql=" SELECT COUNT(a.sub_code)AS cnt FROM final_semwise_marks_foil_desc_freezed a WHERE a.admn_no=? and a.foil_id=? AND a.grade NOT IN ('F','I') AND a.current_exam='y' ";
		$foil_id = "'" . implode("','", explode(',', $foil_id)) . "'";
		$sql="SELECT  COUNT(p.sub_code)AS cnt FROM(
SELECT a.*
FROM final_semwise_marks_foil_desc_freezed a
WHERE a.admn_no=? AND a.foil_id in (".$foil_id.") AND a.grade NOT IN ('F','I') AND a.current_exam='y' 
GROUP BY a.sub_code)p";

        
        $query = $this->db->query($sql,array($admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row()->cnt;
        } else {
            return false;
        }
		
		
	}
	
	function get_student_list_all($session_year,$session,$course_id,$branch_id,$semester){
		
		$sql=" SELECT a.* FROM reg_regular_form a WHERE a.session_year = ? AND a.`session` = ?  AND a.course_id=? AND a.branch_id=?  AND a.semester=?
				AND a.hod_status='1' AND a.acad_status='1' order by a.admn_no asc";

        
        $query = $this->db->query($sql,array($session_year,$session,$course_id,$branch_id,$semester));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
		
		
	}
	
	function get_student_name($id){
		$sql=" SELECT CONCAT_WS(' ',first_name,middle_name,last_name)AS sname FROM user_details WHERE id=? ";

        
        $query = $this->db->query($sql,array($id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
		
		
	}
	
	function get_details_for_transcript($id){
               /* $sql="SELECT t.*,
@running_total:=@running_total + 1 AS exam_attempt
 from
(select a.*,
(CASE a.session
      WHEN 'Monsoon' THEN '1'
      WHEN 'Winter' THEN '2' 
						WHEN 'Summer' THEN '3'   
   END) as order_list
from final_semwise_marks_foil_freezed a where a.admn_no=? and a.course<>'MINOR' 
GROUP BY a.session_yr,order_list,a.semester
ORDER BY a.session_yr,order_list,a.semester)t
JOIN (SELECT @running_total:=0) r";*/

/*$sql="SELECT t2.*, @running_total:=@running_total + 1 AS exam_attempt FROM
(SELECT t1.* from
(SELECT t.*,(CASE t.session WHEN 'Monsoon' THEN '1' WHEN 'Winter' THEN '2' WHEN 'Summer' THEN '3' END) AS order_list from
(SELECT a.* FROM final_semwise_marks_foil_freezed a
WHERE a.admn_no=? AND a.course<>'MINOR'
ORDER BY a.semester DESC LIMIT 100000)t 
GROUP BY t.semester,t.session_yr,order_list
ORDER BY t.semester desc,t.session_yr,order_list)t1 ORDER BY t1.semester ASC LIMIT 100000)t2
JOIN (
SELECT @running_total:=0) r";*/

/*$sql="SELECT t1.*, @running_total:=@running_total + 1 AS exam_attempt
FROM (
SELECT t.*,GROUP_CONCAT(t.id)AS id1,(CASE t.session WHEN 'Monsoon' THEN '1' WHEN 'Winter' THEN '2' WHEN 'Summer' THEN '3' END) AS order_list
FROM (
SELECT a.*
FROM final_semwise_marks_foil_freezed a
WHERE a.admn_no=? AND a.course<>'MINOR'
ORDER BY a.semester DESC,a.admn_no,a.actual_published_on DESC
LIMIT 100000)t
GROUP BY t.session_yr,order_list
ORDER BY t.session_yr,order_list)t1
JOIN (
SELECT @running_total:=0) r";*/

/*
$sql="
SELECT t1.*, @running_total:=@running_total + 1 AS exam_attempt
FROM (
select t.*,  GROUP_CONCAT(t.id) AS id1,(CASE t.session WHEN 'Monsoon' THEN '1' WHEN 'Winter' THEN '2' WHEN 'Summer' THEN '3' END) AS order_list 
from ( 
SELECT t.*
FROM 
(
SELECT a.*
FROM final_semwise_marks_foil_freezed a
WHERE a.admn_no=? AND a.course<>'MINOR'
ORDER BY a.admn_no,a.semester DESC,a.actual_published_on DESC  LIMIT 1000000 )t
GROUP BY t.admn_no, t.session_yr, t.session,t.semester
ORDER BY t.admn_no,t.semester DESC,t.actual_published_on DESC  LIMIT 1000000 
)t
GROUP BY t.session_yr,order_list
ORDER BY t.session_yr,order_list  limit 1000000 )t1
JOIN (
SELECT @running_total:=0) r
";*/

/*$sql="SELECT t1.*, @running_total:=@running_total + 1 AS exam_attempt
FROM (
SELECT t.*,GROUP_CONCAT(distinct(t.id) order by t.id ) AS id1,
group_concat( distinct(t.semester) order by t.semester ) as sem_summer_list ,
GROUP_CONCAT(distinct(t.examtype)   ORDER BY t.examtype) AS examtype_summer_list, 
GROUP_CONCAT( distinct( t.sem_code)  ORDER BY t.sem_code) AS sem_code_summer_list, 
(CASE t.session WHEN 'Monsoon' THEN '1' WHEN 'Winter' THEN '2' WHEN 'Summer' THEN '3' END) AS order_list
FROM (
SELECT t.*
FROM (

SELECT a.session_yr,a.session,a.admn_no,a.id,a.semester,a.actual_published_on , null as wsms, a.type AS examtype, null as sem_code 
FROM final_semwise_marks_foil_freezed a
WHERE a.admn_no=? AND a.course<>'MINOR' and a.published_on is not null

union all
(SELECT  A.session_yr,A.session,A.admn_no,A.id,A.semester,A.actual_published_on ,A.wsms,A.examtype,A.sem_code 
FROM (
SELECT   a.ysession as  session_yr, a.wsms,  a.examtype, (case when  a.wsms='ZS' then 'Summer'  when a.wsms='MS' then 'Monsoon' when a.wsms='WS' then 'Winter' else a.wsms end)   as  session, a.adm_no as  admn_no, a.id, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS semester ,  null as actual_published_on ,a.sem_code
FROM tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%'
 GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
    ORDER BY semester, a.ysession , a.wsms ,a.examtype  limit 10000000
)A
)

ORDER BY admn_no,semester DESC, (case when actual_published_on is null then session_yr end) ,
                                (case when actual_published_on is null then wsms else actual_published_on  end)  ,
                                CASE actual_published_on WHEN null THEN wsms END ASC,
                                CASE actual_published_on WHEN not null THEN actual_published_on END desc,
                                (case when actual_published_on is null then examtype  end)




LIMIT 1000000)t
GROUP BY t.admn_no, t.session_yr, t.session,t.semester
ORDER BY t.admn_no,t.semester DESC,t.actual_published_on DESC
LIMIT 1000000)t
GROUP BY t.session_yr,order_list,(case when t.session<>'Summer' then     t.semester end)
ORDER BY t.session_yr,order_list,(case when t.session<>'Summer' then     t.semester end)

LIMIT 1000000)t1
JOIN (
SELECT @running_total:=0) r";*/

$sql="SELECT t1.*, @running_total:=@running_total + 1 AS exam_attempt
FROM (
SELECT t.*, GROUP_CONCAT(DISTINCT(t.id)
ORDER BY t.id) AS id1, GROUP_CONCAT(DISTINCT(t.semester)
ORDER BY t.semester) AS sem_summer_list, GROUP_CONCAT(DISTINCT(t.examtype)
ORDER BY t.examtype) AS examtype_summer_list, GROUP_CONCAT(DISTINCT(t.sem_code)
ORDER BY t.sem_code) AS sem_code_summer_list, (CASE t.session WHEN 'Monsoon' THEN '1' WHEN 'Winter' THEN '2' WHEN 'Summer' THEN '3' END) AS order_list
FROM (
SELECT t.*
FROM (

SELECT a.* FROM(
SELECT a.session_yr,a.session,a.admn_no,a.id,a.semester,a.actual_published_on, NULL AS wsms, a.type AS examtype, NULL AS sem_code
FROM final_semwise_marks_foil_freezed a
WHERE lower(a.admn_no)=? AND a.course<>'MINOR' AND a.course<>'PREP'  ORDER BY a.admn_no,a.semester DESC,a.session_yr,a.session,a.actual_published_on DESC limit 100000
) a

GROUP BY   a.admn_no,a.semester ,a.session_yr,a.session,a.examtype
UNION ALL (
SELECT A.session_yr,A.session,A.admn_no,A.id,A.semester,A.actual_published_on,A.wsms,A.examtype,A.sem_code
FROM (
SELECT a.ysession AS session_yr, a.wsms, a.examtype, (CASE WHEN a.wsms='ZS' THEN 'Summer' WHEN a.wsms='MS' THEN 'Monsoon' WHEN a.wsms='WS' THEN 'Winter' ELSE a.wsms END) AS SESSION, a.adm_no AS admn_no, a.id, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS semester, NULL AS actual_published_on,a.sem_code
FROM tabulation1 a
WHERE lower(a.adm_no)=? AND a.sem_code NOT LIKE 'PREP%'
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY semester, a.ysession, a.wsms,a.examtype
LIMIT 10000000)A)
ORDER BY admn_no,semester DESC, 
         (CASE WHEN actual_published_on IS NULL THEN session_yr END),
		   (CASE WHEN actual_published_on IS NULL THEN wsms ELSE actual_published_on END), 
   	    CASE actual_published_on WHEN NULL THEN wsms END ASC, 
          CASE actual_published_on WHEN NOT NULL THEN actual_published_on END DESC, 
	      (CASE WHEN actual_published_on IS NULL THEN examtype END)
		  
LIMIT 1000000)t
GROUP BY t.admn_no, t.session_yr, t.session,t.semester
ORDER BY t.admn_no,t.semester DESC,t.actual_published_on DESC
LIMIT 1000000)t
GROUP BY t.session_yr,order_list,(CASE WHEN t.session<>'Summer' THEN t.semester END)
ORDER BY t.session_yr,order_list,(CASE WHEN t.session<>'Summer' THEN t.semester END)
LIMIT 1000000)t1
JOIN (
SELECT @running_total:=0) r";

                
                    $query = $this->db->query($sql,array(strtolower($id),strtolower($id)));
		//echo $this->db->last_query(); die();	
                    if ($query->num_rows() > 0)
                    { 
                      
                         return $query->result();
                    }
                    else
                    {
                        return false;
                    }
            }
			
			//before summer
	
	function count_subjects_beforecbcs($admn_no,$foil_id){
		//$sql=" SELECT COUNT(a.sub_code)AS cnt FROM final_semwise_marks_foil_desc_freezed a WHERE a.admn_no=? and a.foil_id=? AND a.grade NOT IN ('F','I') ";
		
		$sql="
SELECT COUNT(p.sub_code)AS cnt FROM(
SELECT a.*
FROM final_semwise_marks_foil_desc_freezed a
INNER JOIN subjects b ON b.id=a.mis_sub_id
WHERE a.admn_no=? AND a.foil_id=? AND a.grade NOT IN ('F','I') AND b.name!='Project'
)p";

        
        $query = $this->db->query($sql,array($admn_no,$foil_id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row()->cnt;
        } else {
            return false;
        }
		
		
	}
	//summer
	function count_subjects_summer_beforecbcs($admn_no,$foil_id){
		//$sql=" SELECT COUNT(a.sub_code)AS cnt FROM final_semwise_marks_foil_desc_freezed a WHERE a.admn_no=? and a.foil_id=? AND a.grade NOT IN ('F','I') AND a.current_exam='y' ";
		$foil_id = "'" . implode("','", explode(',', $foil_id)) . "'";
		$sql="SELECT COUNT(p.sub_code)AS cnt FROM(
SELECT a.*
FROM final_semwise_marks_foil_desc_freezed a
INNER JOIN subjects b ON b.id=a.mis_sub_id
WHERE a.admn_no=?  AND a.foil_id in (".$foil_id.") AND a.grade NOT IN ('F','I') AND b.name!='Project' AND a.current_exam='y' 
)p
";

        
        $query = $this->db->query($sql,array($admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row()->cnt;
        } else {
            return false;
        }
		
		
	}
	
	
	function get_current_cgpa($sy,$sess,$admn_no)
	{
		$sql=" 
SELECT a.ctotcrhr,a.ctotcrpts,a.cgpa FROM final_semwise_marks_foil_freezed a WHERE a.session_yr=? AND a.`session`=?  AND a.admn_no=? ";

        
        $query = $this->db->query($sql,array($sy,$sess,$admn_no));

       
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
		
		
	}
	
	

    
}

?>