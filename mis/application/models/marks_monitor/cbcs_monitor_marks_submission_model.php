<?php

class cbcs_monitor_marks_submission_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }


    function get_submitted_grade($syear,$sess)
    {

      

       /* $sql=" 
SELECT p.*,
(case when  o.sub_code=p.sub_code then o.sub_code  ELSE c.sub_code  end)AS sub_code_new, 
(case when  o.sub_code=p.sub_code then CONCAT('o',o.id)  ELSE CONCAT('c',c.id)  end)AS sub_offered_id_new,
(case when  o.sub_code=p.sub_code then o.sub_type  ELSE c.sub_type  end)AS sub_type,
(case when  o.sub_code=p.sub_code then o.sub_name  ELSE c.sub_name  end)AS sub_name
FROM(
SELECT *
FROM (
SELECT DISTINCT a.subject_code
FROM old_stu_course a UNION
SELECT DISTINCT b.subject_code
FROM cbcs_stu_course b) d
JOIN cbcs_assign_course_coordinator e ON d.subject_code = e.sub_code
WHERE d.subject_code NOT IN (
SELECT DISTINCT c.sub_code
FROM cbcs_marks_send_to_coordinator c
WHERE c.dean_ac_status = '1'))p
LEFT  JOIN 
old_subject_offered o ON o.sub_code=p.sub_code
LEFT  JOIN 
cbcs_subject_offered c ON  c.sub_code=p.sub_code
WHERE p.session_year=? AND p.session=? GROUP BY p.sub_code";

        
        $query = $this->db->query($sql,array($syear,$sess));
*/
$sql=" SELECT t6.*,CONCAT_WS(' ',t7.salutation,t7.first_name,t7.middle_name,t7.last_name)AS fname FROM
(SELECT t5.* ,
(case when  o.sub_code=t5.subject_code then o.sub_type  ELSE c.sub_type  end)AS sub_type,
(case when  o.sub_code=t5.subject_code then o.sub_name  ELSE c.sub_name  end)AS sub_name,
(CASE WHEN o.sub_code=t5.subject_code THEN o.sub_group ELSE c.sub_group END) AS sub_group

FROM(
SELECT t3.*,t4.dean_ac_status,t4.updated_at,t4.status from
(SELECT t1.*,t2.co_emp_id,t2.exam_type from
(SELECT p.* from
(SELECT CONCAT('o',a.sub_offered_id)AS sub_offered_id_new,a.sub_offered_id,a.subject_code,COUNT(a.admn_no)AS stu_cnt,a.session_year,a.`session` FROM old_stu_course a 
WHERE a.session_year=? AND a.`session`=? GROUP BY a.subject_code
union
SELECT CONCAT('c',a.sub_offered_id)AS sub_offered_id_new ,a.sub_offered_id,a.subject_code,COUNT(a.admn_no)AS stu_cnt,a.session_year,a.`session` FROM cbcs_stu_course a 
WHERE a.session_year=? AND a.`session`=? GROUP BY a.subject_code)p
GROUP BY p.subject_code
ORDER BY p.subject_code)t1
left join
(SELECT a.* from cbcs_assign_course_coordinator a WHERE a.session_year=? AND a.`session`=?)t2
ON t1.subject_code=t2.sub_code)t3
LEFT join
(SELECT a.* from cbcs_marks_send_to_coordinator a WHERE a.session_year=? AND a.`session`=? 
AND a.dean_ac_status!=2 and a.status !='2' )t4
on t4.sub_code=t3.subject_code)t5
LEFT  JOIN 
old_subject_offered o ON CONCAT('o',o.id)=t5.sub_offered_id_new
LEFT  JOIN 
cbcs_subject_offered c ON CONCAT('c',c.id)=t5.sub_offered_id_new)t6
LEFT JOIN user_details t7 ON t7.id=t6.co_emp_id GROUP BY t6.subject_code ORDER BY t6.subject_code";

        
        $query = $this->db->query($sql,array($syear,$sess,$syear,$sess,$syear,$sess,$syear,$sess));


       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	
	// dept wise
	
	function get_submitted_grade_hod($syear,$sess,$dept_id)
    {

$sql=" SELECT t6.*,CONCAT_WS(' ',t7.salutation,t7.first_name,t7.middle_name,t7.last_name)AS fname FROM
(SELECT t5.* ,
(case when  o.sub_code=t5.subject_code then o.sub_type  ELSE c.sub_type  end)AS sub_type,
(case when  o.sub_code=t5.subject_code then o.sub_name  ELSE c.sub_name  end)AS sub_name,
(CASE WHEN o.sub_code=t5.subject_code THEN o.sub_group ELSE c.sub_group END) AS sub_group

FROM(
SELECT t3.*,t4.dean_ac_status,t4.updated_at,t4.status from
(SELECT t1.*,t2.co_emp_id,t2.exam_type from
(


SELECT p.*
FROM (
SELECT CONCAT('o',a.sub_offered_id) AS sub_offered_id_new,a.sub_offered_id,a.subject_code, COUNT(a.admn_no) AS stu_cnt,a.session_year,a.`session`
,b.offering_dept_id AS dept_id
FROM old_stu_course a
INNER JOIN cbcs_dept_code b ON  (b.course_code=LEFT(a.subject_code,3) || b.course_code=LEFT(a.subject_code,2))
WHERE a.session_year=? AND a.`session`=? AND b.offering_dept_id=? AND right(a.subject_code,3)!='599'
GROUP BY a.subject_code UNION
SELECT CONCAT('c',a.sub_offered_id) AS sub_offered_id_new,a.sub_offered_id,a.subject_code, COUNT(a.admn_no) AS stu_cnt,a.session_year,a.`session`
,b.offering_dept_id AS dept_id
FROM cbcs_stu_course a
INNER JOIN cbcs_dept_code b ON  (b.course_code=LEFT(a.subject_code,3) || b.course_code=LEFT(a.subject_code,2))
WHERE a.session_year=? AND a.`session`=? AND b.offering_dept_id=? AND right(a.subject_code,3)!='599'
GROUP BY a.subject_code)p
GROUP BY p.subject_code
ORDER BY p.subject_code

)t1
left join
(SELECT a.* from cbcs_assign_course_coordinator a WHERE a.session_year=? AND a.`session`=?)t2
ON t1.subject_code=t2.sub_code)t3
LEFT join
(SELECT a.* from cbcs_marks_send_to_coordinator a WHERE a.session_year=? AND a.`session`=? 
AND a.dean_ac_status!=2 and a.status !='2' )t4
on t4.sub_code=t3.subject_code)t5
LEFT  JOIN 
old_subject_offered o ON CONCAT('o',o.id)=t5.sub_offered_id_new
LEFT  JOIN 
cbcs_subject_offered c ON CONCAT('c',c.id)=t5.sub_offered_id_new)t6
LEFT JOIN user_details t7 ON t7.id=t6.co_emp_id GROUP BY t6.subject_code ORDER BY t6.subject_code";

        
        $query = $this->db->query($sql,array($syear,$sess,$dept_id,$syear,$sess,$dept_id,$syear,$sess,$syear,$sess));


      // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	



}

?>
