<?php
class distribution_model extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->load->database();
		date_default_timezone_set('Asia/Calcutta');
	}

//Get ESO subject list :: as per session year, session and HOD's department id......
	function get_eso_list($sy,$sess,$dept_id){
		$sql="SELECT * FROM ((SELECT a.*,'' AS map_id,'CBCS' AS type
FROM cbcs_subject_offered a
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.dept_id='$dept_id' /*GROUP BY a.sub_code ORDER BY a.sub_code*/)
UNION
(SELECT a.*,'OLD' AS type
FROM old_subject_offered a
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.dept_id='$dept_id' /*GROUP BY a.sub_code ORDER BY a.sub_code*/)) X
GROUP BY X.sub_code ORDER BY X.sub_code
";

		$query=$this->db->query($sql);
		return $query->result();
	}

//Get opted course detail..............
	function course_opted_details($sy,$sess,$course,$type){
		/*if($type == 'CBCS'){
			$sql="SELECT a.*,COUNT(a.id) as total_count
			FROM cbcs_stu_course a 
			WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course'
			GROUP BY a.course,a.branch";
		}else{
			$sql="SELECT a.*,COUNT(a.id) as total_count
			FROM old_stu_course a 
			WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course'
			GROUP BY a.course,a.branch";
		}*/
        $sql="(SELECT a.*,COUNT(a.id) as total_count,'CBCS' AS type
            FROM cbcs_stu_course a 
            JOIN users b ON b.id=a.admn_no
            WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND b.auth_id='stu' AND b.`status`='A'
            GROUP BY a.course,a.branch
        )union(
            SELECT a.*,COUNT(a.id) as total_count,'OLD' AS type
            FROM old_stu_course a 
            JOIN users b ON b.id=a.admn_no
            WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND b.auth_id='stu' AND b.`status`='A'
            GROUP BY a.course,a.branch)";
		$query=$this->db->query($sql);
		return $query->result();
	}

//Get department list...............
	function get_department_list(){
		$sql="SELECT a.* FROM cbcs_departments a  WHERE a.`type`='academic' AND a.`status`=1";
		$query=$this->db->query($sql);
		return $query->result();

	}

//get faculty list: as per department.............
	function get_faculty_list($dept_id){
		$sql="SELECT b.id, concat_ws(' ',a.salutation, a.first_name, a.middle_name, a.last_name) AS name, a.dept_id
FROM user_details a
JOIN users b ON b.id = a.id
join emp_basic_details c ON b.id=c.emp_no
WHERE a.dept_id = '$dept_id' AND b.auth_id = 'emp' and ( c.auth_id='ft' or c.designation='spo') and b.status='A'
group by b.id
ORDER BY a.first_name ASC";
		$query=$this->db->query($sql);
		return $query->result();
	}

// get subject code details
    function get_subject_details($course_id,$branch_id,$sub_id,$type,$sy,$sess){
    	if($type=='CBCS'){
        	$sql="SELECT a.* FROM cbcs_stu_course a WHERE a.subject_code='$sub_id' AND a.course='$course_id' AND a.branch='$branch_id' and a.session_year='$sy' and a.session='$sess' LIMIT 1";
    	}else{
    		$sql="SELECT a.* FROM old_stu_course a WHERE a.subject_code='$sub_id' AND a.course='$course_id' AND a.branch='$branch_id' and a.session_year='$sy' and a.session='$sess' LIMIT 1";
    	}
        $query=$this->db->query($sql);
        //echo $this->db->last_query();exit;
        return $query->result();
    }

    function no_of_student_count($sy,$sess,$course_id,$branch_id,$sub_id,$type){
    	/*$sql="SELECT b.session_year,b.`session`,b.course_id,b.branch_id,a.*,COUNT(a.id) as total_count
FROM cbcs_stu_course a 
JOIN reg_regular_form b ON a.form_id=b.form_id AND b.hod_status='1' AND b.acad_status='1'
WHERE a.session_year='$sy' AND a.`session`='$sess'  AND a.sub_offered_id='$sub_id'
AND a.course='$course_id' AND a.branch='$branch_id' 
GROUP BY a.course,a.branch";*/
		if($type == 'CBCS'){
			$sql="SELECT a.*,COUNT(a.id) as total_count
			FROM cbcs_stu_course a 
            JOIN users b ON b.id=a.admn_no
			WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$sub_id' AND a.course='$course_id' AND a.branch='$branch_id' AND b.auth_id='stu' AND b.`status`='A'
			GROUP BY a.course,a.branch";
		}else{
			$sql="SELECT a.*,COUNT(a.id) as total_count
			FROM old_stu_course a 
            JOIN users b ON b.id=a.admn_no
			WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$sub_id' AND a.course='$course_id' AND a.branch='$branch_id' AND b.auth_id='stu' AND b.`status`='A'
			GROUP BY a.course,a.branch";
		}
		$query=$this->db->query($sql);
		return $query->result();
    }

    function save_attendance_group_formation($data){
    	$query=$this->db->get_where('cbcs_optional_mapping', $data);
    	//print_r($query);
    	//echo $query->num_rows();
    	if($query->num_rows() > 0){
    		$result=$query->result();
    		return $result[0]->id;
    	}else{
			$data['timestamp'] =date("Y-m-d H:i:s");
    		$insert=$this->db->insert('cbcs_optional_mapping', $data);
    		$insert_id = $this->db->insert_id();
			return  $insert_id;
		}

    }

    function save_attendance_group_formation_desc($data1,$data2,$data3,$type){
    	$query=$this->db->insert('cbcs_optional_mapping_desc', $data1);
    	$insert_id = $this->db->insert_id();
    	//$query1=$this->db->get_where('cbcs_subject_offered_desc', $data2);
    	//echo $this->db->last_query();
    	/*if($query1->num_rows() == 0){
    		if($type=='CBCS'){
    			$query2=$this->db->insert('cbcs_subject_offered_desc', $data3);
    		}else{
    			$query2=$this->db->insert('old_subject_offered_desc', $data3);
    		}
    		//echo '<br>'.$this->db->last_query();
    	}*/
		return  $insert_id;
    }

    function get_group_formaton_list($dept_id){
    	/*$sql="(SELECT a.*,b.emp_no, concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name) AS emp_name,d.dept_id AS dept,e.sub_name FROM cbcs_optional_mapping a
    	JOIN cbcs_optional_mapping_desc b ON b.map_id=a.id
    	JOIN user_details c ON c.id=b.emp_no
    	JOIN user_details d ON d.id=a.user_id
    	JOIN cbcs_subject_offered e ON  e.sub_code=a.sub_code
    	WHERE d.dept_id='$dept_id')union(SELECT a.*,b.emp_no, concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name) AS emp_name,d.dept_id AS dept,e.sub_name FROM cbcs_optional_mapping a
JOIN cbcs_optional_mapping_desc b ON b.map_id=a.id
JOIN user_details c ON c.id=b.emp_no
JOIN user_details d ON d.id=a.user_id
JOIN old_subject_offered e ON  e.sub_code=a.sub_code
WHERE d.dept_id='$dept_id')";*/
        $sql="SELECT *,GROUP_CONCAT(CONCAT(X.emp_name,' [',X.emp_no,']')) as emp FROM ((
SELECT a.*,b.emp_no, CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) AS emp_name,d.dept_id AS dept,e.sub_name
FROM cbcs_optional_mapping a
JOIN cbcs_optional_mapping_desc b ON b.map_id=a.id
JOIN user_details c ON c.id=b.emp_no
JOIN user_details d ON d.id=a.user_id
JOIN cbcs_subject_offered e ON e.sub_code=a.sub_code
WHERE d.dept_id='$dept_id'
GROUP BY a.id,b.emp_no) UNION(
SELECT a.*,b.emp_no, CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) AS emp_name,d.dept_id AS dept,e.sub_name
FROM cbcs_optional_mapping a
JOIN cbcs_optional_mapping_desc b ON b.map_id=a.id
JOIN user_details c ON c.id=b.emp_no
JOIN user_details d ON d.id=a.user_id
JOIN old_subject_offered e ON e.sub_code=a.sub_code
WHERE d.dept_id='$dept_id'
GROUP BY a.id,b.emp_no)) X 
GROUP BY X.id";
		$query=$this->db->query($sql);
		return $query->result();
    }

    function delete_selected_data($id,$table,$faculty,$sub_offered_id){
    	$sql="DELETE FROM cbcs_optional_mapping WHERE id='$id'";
    	$this->db->query($sql);
    	$sql1="DELETE FROM cbcs_optional_mapping_desc WHERE map_id='$id'";
    	$this->db->query($sql1);
       // $sql2="DELETE FROM $table WHERE `sub_offered_id`='$sub_offered_id' AND `emp_no`='$faculty'";
        //$this->db->query($sql2);
    }

    function get_count_details($sy,$sess,$course,$type,$course_id,$branch_id){
    	if($type=='CBCS'){
    		$sql="SELECT X.*,count(*) AS total_c,'CBCS' AS sub_type
FROM 
(SELECT a.*,SUM(b.total_count) AS total
FROM cbcs_stu_course a 
LEFT JOIN cbcs_optional_mapping b ON a.session_year=b.session_year AND a.`session`=b.`session` AND  CONCAT('c',a.sub_offered_id)=b.sub_offered_id AND a.course=b.course_id AND a.branch=b.branch_id
JOIN users c ON c.id=a.admn_no
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND a.course='$course_id' AND a.branch='$branch_id' AND
c.auth_id='stu' AND c.`status`='A'
GROUP BY a.id) X ";
    	}else{
    		$sql="SELECT X.*,count(*) AS total_c,'OLD' AS sub_type
FROM 
(SELECT a.*,SUM(b.total_count) AS total
FROM old_stu_course a 
LEFT JOIN cbcs_optional_mapping b ON a.session_year=b.session_year AND a.`session`=b.`session` AND  CONCAT('o',a.sub_offered_id)=b.sub_offered_id AND a.course=b.course_id AND a.branch=b.branch_id
JOIN users c ON c.id=a.admn_no
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND a.course='$course_id' AND a.branch='$branch_id' AND
c.auth_id='stu' AND c.`status`='A'
GROUP BY a.id) X ";
    	}
    /*    $sql="SELECT * FROM ((SELECT X.*,count(*) AS total_c,'CBCS' AS sub_type
FROM 
(SELECT a.*,SUM(b.total_count) AS total
FROM cbcs_stu_course a 
LEFT JOIN cbcs_optional_mapping b ON a.session_year=b.session_year AND a.`session`=b.`session` AND  CONCAT('c',a.sub_offered_id)=b.sub_offered_id AND a.course=b.course_id AND a.branch=b.branch_id
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND a.course='$course_id' AND a.branch='$branch_id'
GROUP BY a.id) X 
        )UNION(
            SELECT X.*,count(*) AS total_c,'OLD' AS sub_type
FROM 
(SELECT a.*,SUM(b.total_count) AS total
FROM old_stu_course a 
LEFT JOIN cbcs_optional_mapping b ON a.session_year=b.session_year AND a.`session`=b.`session` AND  CONCAT('o',a.sub_offered_id)=b.sub_offered_id AND a.course=b.course_id AND a.branch=b.branch_id
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.subject_code='$course' AND a.course='$course_id' AND a.branch='$branch_id'
GROUP BY a.id) X )) Y WHERE Y.id IS NOT null";*/
    	$query=$this->db->query($sql);
		return $query->result();
    }

}