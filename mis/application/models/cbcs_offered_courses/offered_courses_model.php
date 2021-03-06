<?php
class Offered_courses_model Extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	
	
	// course mapping for 2020-2021 1st year by @bhi start

  function save_subject_list($form_id,$admn_no,$course_id,$branch_id,$sub_code,$save_pre){
		$where=array(
			"form_id"=>$form_id,
			"admn_no"=>$admn_no,
			"course"=>$course_id,
			"branch"=>$branch_id,
			"subject_code"=>$sub_code,
			"session"=>"Monsoon",
			"session_year"=>"2020-2021"
		);
		$this->db->select('*');
		$this->db->from('pre_stu_course');
		$this->db->where($where);
		$cnt=$this->db->get()->num_rows();
		if($cnt==0){
			if($this->db->insert('pre_stu_course', $save_pre)){
				//echo "inserted"; exit;
					return true;
			}else{
				//echo "not"; exit;
				return false;
			}
		}else{
				return false;
		}
	}
	function checkforSection($admn_no){
		$sql="select * from stu_section_data a where a.admn_no='$admn_no' and a.session_year='2020-2021'";
		$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		if($result->num_rows() > 0){
			return $result->result();;
		}else{
			return false;
		}
	}
	function get_student_list($program){
	/*	$sql="select a.*,b.dept_id from reg_regular_form  a
		inner join user_details b on a.admn_no=b.id
		where a.session_year='2020-2021' and a.`session`='Monsoon'  and a.hod_status='1' and a.acad_status='1' and a.admn_no like '$program%' ";
		$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result(); */
		if($program=='20JE'){
			$extrajoin="inner join stu_section_data c on a.admn_no=c.admn_no and c.session_year='2020-2021'";
			$extraClouse="";
		}else{
			$extrajoin="";
			$extraClouse="and a.admn_no like '$program%'";
		}
		$sql="select a.*,b.dept_id from reg_regular_form  a
		inner join user_details b on a.admn_no=b.id
		$extrajoin
		where a.session_year='2020-2021' and a.`session`='Monsoon'  and a.hod_status='1' and a.acad_status='1' $extraClouse ";
		$result=$this->db->query($sql);
	//	echo $this->db->last_query();exit;
		return $result->result();
		
		
	}
	function checkforPrep($admn_no){
		$sql="select * from stu_prep_data a where a.admn_no='$admn_no' and a.session_year='2020-2021'";
		$result=$this->db->query($sql);
		if($result->num_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
function get_subject_list($dept_id,$course_id,$branch_id,$section,$program){
		if($program=='20JE'){
			$course_id='comm';
			$branch_id='comm';
			$dept_id='comm';
			$extraClouse="and sub_group='$section'";
		}else{
			$extraClouse="";
		}
		$sql="SELECT *
					FROM cbcs_subject_offered a
					WHERE a.session_year='2020-2021' AND a.`session`='Monsoon'
					AND a.semester='1' AND a.dept_id='$dept_id' and a.course_id='$course_id' AND a.branch_id='$branch_id' $extraClouse";
		$result=$this->db->query($sql);
		//echo"<br>". $this->db->last_query();//exit;
		return $result->result();
	}

// course mapping for 2020-2021 1st year by @bhi end


	//get Department list
	function get_department_list(){
		$sql="SELECT a.* FROM cbcs_departments a WHERE a.type='academic' AND a.status='1' AND a.id not IN ('ap','ac','am') ORDER BY a.name";
		$result=$this->db->query($sql);
		return $result->result();
	}
	//get course list
	function get_course_list(){
		$sql="SELECT a.* FROM cbcs_courses a WHERE a.status='1' ORDER BY a.name";
		$result=$this->db->query($sql);
		return $result->result();
	}
	//get branch list
	function get_branch_list(){
		$sql="SELECT a.* FROM cbcs_branches a WHERE a.status='1' ORDER BY a.name";
		$result=$this->db->query($sql);
		return $result->result();
	}
	//get session year list
	function get_session_year_list(){
		$sql="SELECT a.session_year FROM mis_session_year a ORDER BY a.id desc";
		$result=$this->db->query($sql);
		return $result->result();
	}
	//Get Session list
	function get_session_list(){
		$sql="SELECT a.session FROM mis_session a";
		$result=$this->db->query($sql);
		return $result->result();
	}
	//Subject List
	function get_offered_subject_details($dept){
		$q='';
		if($dept != ''){
			$q=" WHERE a.dept_id='".$dept."' ";
		}
		$sql="SELECT a.*,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,e.name,c.course_credit_min,c.course_credit_max,d.course_id,d.mincp,d.maxcp
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id $q
group by a.id
ORDER BY a.id";
		$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result();

	}

	function get_base_structure($dept,$course,$sess,$sy){
		if($dept=='') $a="z.dept_id not IN ('ap','ac','am')";
		else $a="z.dept_id='$dept'";
		if($course=='') $a.=" AND 1=1";
		else $a.=" AND z.course_id='$course'";
		/*$sql="SELECT a.dept_id,b.course_id,b.branch_id,d.duration
FROM dept_course a
JOIN course_branch b ON a.course_branch_id=b.course_branch_id
JOIN cbcs_departments c ON c.id=a.dept_id 
JOIN cbcs_courses d ON d.id=b.course_id
JOIN  cbcs_branches e ON e.id=b.branch_id 
WHERE $a
GROUP BY a.dept_id,b.course_id,b.branch_id
ORDER BY d.duration,a.dept_id,b.branch_id";*/
		$sql="SELECT z.* FROM ((SELECT a.dept_id,b.course_id,b.branch_id,d.duration
FROM dept_course a
JOIN course_branch b ON a.course_branch_id=b.course_branch_id
JOIN cbcs_departments c ON c.id=a.dept_id
JOIN cbcs_courses d ON d.id=b.course_id
JOIN cbcs_branches e ON e.id=b.branch_id)
UNION 
(SELECT f.dept_id,f.course_id,f.branch_id,'' AS duration
FROM old_course_structure f
)) z
WHERE $a
GROUP BY z.dept_id,z.course_id,z.branch_id
ORDER BY z.dept_id,z.course_id,z.branch_id";
		$result=$this->db->query($sql);
		return $result->result();
	}

	function get_base_structure_try($dept,$course,$sess,$sy){
		if($dept=='') $a="z.dept_id not IN ('ap','ac','am')";
		else $a="z.dept_id='$dept'";
		if($course=='') $a.=" AND 1=1";
		else $a.=" AND z.course_id='$course'";
		/*$sql="SELECT a.dept_id,b.course_id,b.branch_id,d.duration
FROM dept_course a
JOIN course_branch b ON a.course_branch_id=b.course_branch_id
JOIN cbcs_departments c ON c.id=a.dept_id 
JOIN cbcs_courses d ON d.id=b.course_id
JOIN  cbcs_branches e ON e.id=b.branch_id 
WHERE $a
GROUP BY a.dept_id,b.course_id,b.branch_id
ORDER BY d.duration,a.dept_id,b.branch_id";*/
		$sql="SELECT z.* FROM ((SELECT a.dept_id,b.course_id,b.branch_id,d.duration
FROM dept_course a
JOIN course_branch b ON a.course_branch_id=b.course_branch_id
JOIN cbcs_departments c ON c.id=a.dept_id
JOIN cbcs_courses d ON d.id=b.course_id
JOIN cbcs_branches e ON e.id=b.branch_id)
UNION 
(SELECT f.dept_id,f.course_id,f.branch_id,'' AS duration
FROM old_course_structure f
)) z
WHERE $a
GROUP BY z.dept_id,z.course_id,z.branch_id
ORDER BY z.course_id";
		$result=$this->db->query($sql);
		return $result->result();
	}

	function get_base_structure_for_excel($dept,$course,$sess,$sy){
		if($dept=='') $a="z.dept_id not IN ('ap','ac','am')";
		else $a="z.dept_id='$dept'";
		if($course=='') $a.=" AND 1=1";
		else $a.=" AND z.course_id='$course'";
		
		$sql="SELECT z.* FROM ((SELECT a.dept_id,b.course_id,b.branch_id,d.duration,c.name as d_name
FROM dept_course a
JOIN course_branch b ON a.course_branch_id=b.course_branch_id
JOIN cbcs_departments c ON c.id=a.dept_id
JOIN cbcs_courses d ON d.id=b.course_id
JOIN cbcs_branches e ON e.id=b.branch_id)
UNION 
(SELECT f.dept_id,f.course_id,f.branch_id,'' AS duration,g.name AS d_name
FROM old_course_structure f
JOIN departments g ON g.id=f.dept_id
)) z
WHERE $a
GROUP BY z.dept_id/*,z.course_id,z.branch_id*/
ORDER BY z.dept_id,z.course_id,z.branch_id";
		$result=$this->db->query($sql);
		return $result->result();
	}

	function get_offered_subject_filter_structure($dept,$course,$sess,$sy){
		if($dept=='') {
			$a="a.dept_id not IN ('ap','ac','am')";
			$c="i.dept_id not IN ('ap','ac','am')";
		}
		else {
			$a="a.dept_id='$dept'";
			$c="i.dept_id='$dept'";
		}
		if($course=='') {
			$a.=" AND 1=1";
			$b="1=1";
			$c.=" AND 1=1";
		}
		else {
			$a.=" AND a.course_id='$course'";
			$b="a.course_id='$course'";
			$c.=" AND i.course_id='$course'";
		}
		//i.dept_id='cse' AND i.course_id='b.tech'
	/*$sql="(SELECT b.sem,b.id,CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name
,a.dept_id,a.course_id,a.branch_id
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' 
GROUP BY b.course_component,b.sequence,a.dept_id,a.course_id,a.branch_id
ORDER BY a.id)
UNION
(SELECT b.sem,b.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,a.dept_id,a.course_id,a.branch_id
FROM cbcs_subject_offered a
JOIN cbcs_comm_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component 
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' 
GROUP BY b.course_component,b.sequence,a.dept_id,a.course_id,a.branch_id
ORDER BY a.id)";*/
$sql="SELECT * FROM ((
SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,a.dept_id,a.course_id,a.branch_id,'' AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' AND b.course_component != 'ESO'
GROUP BY b.course_component,b.sequence,a.dept_id,a.course_id,a.branch_id,a.semester
ORDER BY a.id)
/*UNION
(SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,i.dept_id,i.course_id,i.branch_id,'fixed' AS ctype
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
WHERE $c AND i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type LIKE 'ESO%'
GROUP BY a.id
ORDER BY i.id) *//*UNION
(SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,a.dept_id,a.course_id,a.branch_id,'Open' AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE  $b AND a.`session`='$sess' AND a.session_year='$sy' AND a.sub_category like'ESO%' AND a.sub_category NOT IN(
SELECT CONCAT(b.course_component,b.sequence) AS sub_category
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
WHERE $c AND i.`session`='$sess' AND i.session_year='$sy' AND i.eso_type LIKE 'ESO%'
GROUP BY a.id
ORDER BY i.id)
GROUP BY b.id
ORDER BY a.id )*/
 UNION (
SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,a.dept_id,a.course_id,a.branch_id,'' AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_comm_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component /*AND e.course_id=a.course_id*/
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy'
GROUP BY b.course_component,b.sequence,a.dept_id,a.course_id,a.branch_id,a.semester
ORDER BY a.id)) s
GROUP BY s.id";
	$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
	return $result->result();
	}

	function get_offered_subject_filter_structure_eso_fixed($dept,$course,$branch,$sess,$sy,$et){
		$sql="SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,i.dept_id,i.course_id,i.branch_id,'fixed' AS ctype
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
WHERE  i.dept_id ='$dept' AND i.course_id='$course' AND i.branch_id='$branch' AND i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type LIKE '$et'
GROUP BY a.id
ORDER BY i.id";
$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
	return $result->result();
	}

	function get_offered_subject_filter_structure_eso($dept,$course,$branch,$sess,$sy,$et){
		$sql="

SELECT b.sem,a.id, CONCAT(b.course_component,b.sequence) AS sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,'$dept' as dept_id,'$course' as course_id,'$branch' as branch_id,'Open' AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE  a.course_id='$course' AND a.`session`='$sess' AND a.session_year='$sy' AND a.sub_category LIKE '$et' 
AND a.sub_category NOT IN(
SELECT CONCAT(b.course_component,b.sequence) AS sub_category
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
WHERE i.dept_id ='$dept' AND i.course_id='$course' AND i.branch_id='$branch' AND i.`session`='$sess' AND i.session_year='$sy' AND i.eso_type LIKE '$et'
GROUP BY a.id
ORDER BY i.id)
GROUP BY b.id
ORDER BY a.id";
$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
	return $result->result();
	}

	//Subject List With Filter
	function get_offered_subject_filter($dept,$course,$sess,$sy){

		 
	if($dept=='') $a="a.dept_id not IN ('ap','ac','am')";
	else $a="a.dept_id='$dept'";
	if($course=='') $a.=" AND 1=1";
	else $a.=" AND a.course_id='$course'";
$sql="(SELECT a.*,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,c.course_credit_min,c.course_credit_max,d.mincp,d.maxcp,f.name AS d_name,g.name AS c_name,h.name AS b_name,'' as ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' AND b.course_component!='ESO'
group by a.id
ORDER BY a.id)
UNION
(SELECT a.*,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,c.course_credit_min,c.course_credit_max,d.mincp,d.maxcp,f.name AS d_name,g.name AS c_name,h.name AS b_name,'' as ctype
FROM cbcs_subject_offered a
JOIN cbcs_comm_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component /*AND e.course_id=a.course_id*/
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' 
GROUP BY a.id
ORDER BY a.id)";
		$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result();
	}

	function get_offered_subject_filter_for_excel($dept,$course,$sess,$sy,$eso){
		if($dept=='') {
			$a="a.dept_id not IN ('ap','ac','am')";
			$i="i.dept_id not IN ('ap','ac','am')";
		}
		else {
			$a="a.dept_id='$dept'";
			$i="i.dept_id='$dept'";
		}
		if($course=='') {
			$a.=" AND 1=1";
			$i.=" AND 1=1";
		}
		else {
			$a.=" AND a.course_id='$course'";
			$i.=" AND i.course_id='$course'";
		}
		$sql="select x.* from (
(SELECT a.*,'' as map_id,GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',j.salutation,j.first_name,j.middle_name,j.last_name))) AS instructor,'' as ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
LEFT join cbcs_subject_offered_desc i ON i.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=i.emp_no
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy'  AND b.course_component!='ESO'
GROUP BY a.id
ORDER BY a.id)
/*UNION(
SELECT a.id,a.session_year,a.`session`,i.dept_id,i.course_id,i.branch_id,a.semester,a.unique_sub_pool_id,a.unique_sub_id,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.sub_type,a.wef_year,a.wef_session,a.pre_requisite,a.pre_requisite_subcode,a.fullmarks,a.no_of_subjects,i.eso_type AS sub_category,a.sub_group,a.criteria,a.minstu,a.maxstu,a.remarks,a.created_by,a.created_on,a.last_updated_by,a.last_updated_on,a.`action`,'' as map_id,GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',j.salutation,j.first_name,j.middle_name,j.last_name))) AS instructor,'Fixed' as ctype
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
LEFT join cbcs_subject_offered_desc k ON k.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=k.emp_no
WHERE $i and i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type in ($eso)
GROUP BY a.id
ORDER BY i.id)*/ UNION (
SELECT a.*,'' as map_id,GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',j.salutation,j.first_name,j.middle_name,j.last_name))) AS instructor,'' as ctype
FROM cbcs_subject_offered a
JOIN cbcs_comm_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component /*AND e.course_id=a.course_id*/
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
LEFT join cbcs_subject_offered_desc i ON i.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=i.emp_no
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy' 
GROUP BY a.id
ORDER BY a.id)
union(
SELECT a.*,GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',j.salutation,j.first_name,j.middle_name,j.last_name))) AS instructor,'' as ctype
FROM old_subject_offered a
LEFT JOIN departments f ON f.id=a.dept_id
LEFT JOIN cs_courses g ON g.id=a.course_id
LEFT JOIN cs_branches h ON h.id=a.branch_id
LEFT join old_subject_offered_desc i ON i.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=i.emp_no
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy'
GROUP BY a.id
ORDER BY a.id)) x
order by x.dept_id,x.course_id,x.branch_id,x.semester";
$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result();
	}

	function get_e_so_subject_from_open_eso_excel($dept,$course,$branch,$sess,$sy,$et){
	
		$sql="SELECT a.id,a.session_year,a.`session`,'$dept' as dept_id,'$course' as course_id,'$branch' as branch_id,a.semester,a.unique_sub_pool_id,
a.unique_sub_id,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,
a.sub_type,a.wef_year,a.wef_session,a.pre_requisite,a.pre_requisite_subcode,a.fullmarks,a.no_of_subjects,
a.sub_category,a.sub_group,a.criteria,a.minstu,a.maxstu,a.remarks,a.created_by,a.created_on,
a.last_updated_by,a.last_updated_on,a.`action`,'' as map_id,'' AS instructor,'Open' as ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE a.course_id='$course' and a.`session`='$sess' AND a.session_year='$sy' AND a.sub_category LIKE 'ESO%' 
AND a.sub_category NOT IN(
SELECT i.eso_type
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
LEFT join cbcs_subject_offered_desc k ON k.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=k.emp_no
WHERE i.dept_id='$dept' AND i.course_id='$course' AND i.branch_id='$branch' and i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type IN ($et)
GROUP BY a.id
ORDER BY i.id)
GROUP BY a.id
ORDER BY a.id LIMIT 1";
$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result();
    }

	function get_offered_subject_filter_non_cbcs($dept,$course,$sess,$sy){
		if($dept=='') $a="a.dept_id not IN ('ap','ac','am')";
		else $a="a.dept_id='$dept'";
		if($course=='') $a.=" AND 1=1";
		$sql="SELECT a.*,b.sub_offered_id FROM old_subject_offered a 
		left join old_subject_offered_desc b on b.sub_offered_id=a.id 
		where a.session_year='$sy' and a.`session`='$sess' and $a AND if('$sess'='Monsoon',a.semester in (1,3,5,7,9),a.semester in (2,4,6,8,10)) group by a.id ORDER BY a.sub_category,a.created_on";
		$result=$this->db->query($sql);
		return $result->result();
	}

	function get_offered_subject_filter_structure_new($dept,$course,$branch,$sem,$sess,$sy,$cc){
		$sql="SELECT a.sub_category,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE a.dept_id='$dept' AND a.course_id='$course' AND a.branch_id='$branch' AND a.semester='$sem'AND a.`session`='$sess' AND a.session_year='$sy' and b.course_component='$cc'
GROUP BY b.course_component,b.sequence
ORDER BY a.id";

	$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
	return $result->result();
	}


	function get_offered_subject_filter_new($dept,$course,$branch,$sem,$sess,$sy,$cc){

		$sql="SELECT a.*,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,c.course_credit_min,c.course_credit_max,d.course_id,d.mincp,d.maxcp,f.name AS d_name,g.name AS c_name,h.name AS b_name
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE a.dept_id='$dept' AND a.course_id='$course' AND a.branch_id='$branch' AND a.semester='$sem'AND a.`session`='$sess' AND a.session_year='$sy' and b.course_component='$cc'
ORDER BY a.id";
		$result=$this->db->query($sql);
		//echo $this->db->last_query();exit;
		return $result->result();
	}

function get_offered_coursestructure_filter($dept,$course,$branch_id,$sess,$sy){
	if($dept=='') $a="a.dept_id not IN ('ap','ac','am')";
	else $a="a.dept_id='$dept'";
	if($course=='') {
		$a.=" AND 1=1";
		$b="1=1";
	}
	else {
		$a.=" AND a.course_id='$course'";
		$b="a.course_id='$course'";
	}
	if($branch_id=='') $a.=" AND 1=1";
	else $a.=" AND a.branch_id='$branch_id'";
	$sql="(SELECT a.sem,a.`status`,a.lecture AS c_lecture,a.tutorial AS c_tutorial, a.practical AS c_practical,a.course_component,a.sequence,e.name,c.course_credit_min,c.course_credit_max,a.course_id,'$dept' as dept_id,'$branch_id' as branch_id ,d.mincp,d.maxcp 

FROM cbcs_coursestructure_policy a

JOIN cbcs_curriculam_policy c ON c.id=a.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=a.course_component AND e.course_id=a.course_id

WHERE a.course_id='$course' AND if('$sess'='Monsoon',a.sem in (1,3,5,7,9),a.sem in (2,4,6,8,10))
and concat(a.course_component,a.sequence) not in (
SELECT k.* FROM
((SELECT if(a.unique_sub_pool_id = 'NA' OR a.unique_sub_pool_id = '',a.sub_category,a.unique_sub_pool_id) AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy'
ORDER BY a.id) UNION (
SELECT if(a.unique_sub_pool_id = 'NA' OR a.unique_sub_pool_id = '',a.sub_category,a.unique_sub_pool_id) AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE $b AND a.`session`='$sess' AND a.session_year='$sy' AND a.sub_category LIKE'ESO%'
GROUP BY b.id
ORDER BY a.id)) k
))
union 
(SELECT a.sem,a.`status`,a.lecture AS c_lecture,a.tutorial AS c_tutorial, a.practical AS c_practical,a.course_component,a.sequence,e.name,c.course_credit_min,c.course_credit_max,d.course_id,'$dept' as dept_id,'$branch_id' as branch_id ,d.mincp,d.maxcp 

FROM cbcs_comm_coursestructure_policy a

JOIN cbcs_curriculam_policy c ON c.id=a.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=a.course_component 

WHERE a.course_id='$course' AND if('$sess'='Monsoon',a.sem in (1,3,5,7,9),a.sem in (2,4,6,8,10))
and concat(a.course_component,a.sequence) not in (
SELECT if(a.unique_sub_pool_id = 'NA' OR a.unique_sub_pool_id = '',a.sub_category,a.unique_sub_pool_id)
FROM cbcs_subject_offered a
JOIN cbcs_comm_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component 
WHERE $a AND a.`session`='$sess' AND a.session_year='$sy'
ORDER BY a.id))";
$result=$this->db->query($sql);
//echo $this->db->last_query();exit;
return $result->result();

}

function get_offered_coursestructure_filter_new($dept,$course,$branch,$sem,$sess,$sy,$cc){
	$sql="SELECT a.`status`,a.lecture AS c_lecture,a.tutorial AS c_tutorial, a.practical AS c_practical,a.course_component,a.sequence,e.name,c.course_credit_min,c.course_credit_max,d.course_id,d.mincp,d.maxcp 

FROM cbcs_coursestructure_policy a

JOIN cbcs_curriculam_policy c ON c.id=a.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=a.course_component AND e.course_id=a.course_id

WHERE a.course_id='$course' AND a.sem='$sem' AND a.course_component='$cc'
and concat(a.course_component,a.sequence) not in (
SELECT if(a.unique_sub_pool_id = 'NA' OR a.unique_sub_pool_id = '',a.sub_category,a.unique_sub_pool_id)
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
WHERE a.dept_id='$dept' AND a.course_id='$course' AND a.branch_id='$branch' AND a.semester='$sem' AND a.`session`='$sess' AND a.session_year='$sy'
ORDER BY a.id)";
$result=$this->db->query($sql);
return $result->result();
}


function get_eso_list($dept,$course,$sess,$sy){
	if($dept=='') $a="a.dept_id not IN ('ap','ac','am')";
	else $a="a.dept_id='$dept'";
	if($course==''){ 
		$a.=" AND 1=1";
		$b='1=1';
	}else{
		$a.=" AND a.course_id='$course'";
		$b="a.course_id='$course'";
	}
	
       $sql="SELECT * from
(SELECT a.sub_category FROM cbcs_subject_offered a WHERE $b /*AND 
a.branch_id='cse' */AND a.session_year='$sy' AND a.`session`='$sess' AND a.sub_category LIKE 'eso%'
UNION  
SELECT a.eso_type as sub_category FROM cbcs_guided_eso a WHERE $a AND a.session_year='$sy' AND a.`session`='$sess' AND a.eso_type LIKE 'eso%') X 
GROUP BY X.sub_category";
    $query=$this->db->query($sql);
    //echo $this->db->last_query();exit;
    return $query->result();
    }

    function get_e_so_subject_from_guided_eso($dept,$course,$branch,$sess,$sy,$et){
    if($dept=='') $a="i.dept_id not IN ('ap','ac','am')";
	else $a="i.dept_id='$dept'";
	if($course==''){ 
		$a.=" AND 1=1";
	}else{
		$a.=" AND i.course_id='$course'";
	}
	if($branch=='') " AND 1=1";
	else " AND i.branch_id='$branch'";
        $sql="SELECT a.id,a.session_year,a.`session`,i.dept_id,i.course_id,i.branch_id,a.semester,a.unique_sub_pool_id,a.unique_sub_id,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.sub_type,a.wef_year,a.wef_session,a.pre_requisite,a.pre_requisite_subcode,a.fullmarks,a.no_of_subjects,i.eso_type AS sub_category,a.sub_group,a.criteria,a.minstu,a.maxstu,a.remarks,a.created_by,a.created_on,a.last_updated_by,a.last_updated_on,a.`action`,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,c.course_credit_min,c.course_credit_max,d.mincp,d.maxcp,f.name AS d_name,g.name AS c_name,h.name AS b_name
,'Fixed' AS ctype
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
WHERE $a AND i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type='$et'
GROUP BY a.id
ORDER BY i.id";
    $query=$this->db->query($sql);
//echo $this->db->last_query();
    return $query->result();
    }

    
    function get_e_so_subject_from_guided_eso_excel($dept,$course,$branch,$sess,$sy,$et){
    
        $sql="SELECT a.id,a.session_year,a.`session`,i.dept_id,i.course_id,i.branch_id,i.semester,a.unique_sub_pool_id,a.unique_sub_id,a.sub_name,a.sub_code,a.lecture,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.sub_type,a.wef_year,a.wef_session,a.pre_requisite,a.pre_requisite_subcode,a.fullmarks,a.no_of_subjects,i.eso_type AS sub_category,a.sub_group,a.criteria,a.minstu,a.maxstu,a.remarks,a.created_by,a.created_on,a.last_updated_by,a.last_updated_on,a.`action`,'' as map_id,GROUP_CONCAT(DISTINCT(CONCAT_WS(' ',j.salutation,j.first_name,j.middle_name,j.last_name))) AS instructor,'Fixed' as ctype
FROM cbcs_guided_eso i
JOIN cbcs_subject_offered a ON  a.id=i.sub_offered_id AND a.session_year=i.session_year AND a.`session`=i.`session`
JOIN cbcs_coursestructure_policy b ON b.course_id=i.course_id AND b.sem=i.semester AND CONCAT(b.course_component,b.sequence)=i.eso_type
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=i.course_id
LEFT JOIN cbcs_departments f ON f.id=i.dept_id
LEFT JOIN cbcs_courses g ON g.id=i.course_id
LEFT JOIN cbcs_branches h ON h.id=i.branch_id
LEFT join cbcs_subject_offered_desc k ON k.sub_offered_id=a.id
LEFT JOIN user_details j ON j.id=k.emp_no
WHERE i.dept_id='$dept' and i.course_id='$course' and i.branch_id='$branch' and i.`session`='$sess' AND i.session_year='$sy' AND 
i.eso_type='$et'
GROUP BY a.id
ORDER BY i.id";
    $query=$this->db->query($sql);
//echo $this->db->last_query();
    return $query->result();
    }

    function get_e_so_subject_from_offered_eso($course,$sess,$sy,$et){
    	if($course==''){ 
			$a.="1=1";
		}else{
			$a.="a.course_id='$course'";
		}
        $sql="SELECT a.*,b.`status`,b.lecture AS c_lecture,b.tutorial AS c_tutorial, b.practical AS c_practical,b.course_component,b.sequence,e.name,c.course_credit_min,c.course_credit_max,d.mincp,d.maxcp,f.name AS d_name,g.name AS c_name,h.name AS b_name
,'Open' AS ctype
FROM cbcs_subject_offered a
JOIN cbcs_coursestructure_policy b ON b.course_id=a.course_id AND b.sem=a.semester AND (CONCAT(b.course_component,b.sequence)=a.sub_category OR CONCAT(b.course_component,b.sequence)=a.unique_sub_pool_id)
JOIN cbcs_curriculam_policy c ON c.id=b.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy d ON d.id=c.cbcs_credit_points_policy_id
JOIN cbcs_course_component e ON e.id=b.course_component AND e.course_id=a.course_id
LEFT JOIN cbcs_departments f ON f.id=a.dept_id
LEFT JOIN cbcs_courses g ON g.id=a.course_id
LEFT JOIN cbcs_branches h ON h.id=a.branch_id
WHERE  $a AND a.`session`='$sess' AND a.session_year='$sy' AND a.sub_category='$et' 
GROUP BY a.id
ORDER BY a.id LIMIT 1";
    $query=$this->db->query($sql);
//echo $this->db->last_query();
    return $query->result();
    }


}

?>