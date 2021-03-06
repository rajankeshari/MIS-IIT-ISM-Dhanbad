<?php

class Exam_tabulation_model_group extends CI_Model {

function __construct(){

    parent::__construct();
}


 function get_student_list_with_limit($dept,$course,$branch,$session_year,$session,$semester,$start_value,$records_per_page){

    $sql = "SELECT B.admn_no
            FROM (
            (SELECT course_id,branch_id
            FROM dept_course a
            JOIN course_branch b ON a.course_branch_id=b.course_branch_id AND a.dept_id='".$dept."'
            GROUP BY b.course_id,b.branch_id)A
            INNER JOIN (
            SELECT admn_no,course_id,branch_id,semester,hod_status,acad_status
            FROM reg_regular_form
            WHERE hod_status='1' AND acad_status='1' and session_year = '".$session_year."' and `session` = '".$session."' and semester = '".$semester."' AND UPPER(course_id)='".$course."' AND branch_id='".$branch."')B ON A.course_id=B.course_id AND A.branch_id=B.branch_id
            LEFT JOIN user_details ud ON ud.id=B.admn_no)
            GROUP BY B.admn_no
            ORDER BY B.admn_no LIMIT ".$start_value.", ".$records_per_page."";


       $query = $this->db->query($sql);

       return $query->result_array();

 }


 function get_student_list($dept,$course,$branch,$session_year,$session,$semester){

    $sql = "SELECT B.admn_no
            FROM (
            (SELECT course_id,branch_id
            FROM dept_course a
            JOIN course_branch b ON a.course_branch_id=b.course_branch_id AND a.dept_id='".$dept."'
            GROUP BY b.course_id,b.branch_id)A
            INNER JOIN (
            SELECT admn_no,course_id,branch_id,semester,hod_status,acad_status
            FROM reg_regular_form
            WHERE hod_status='1' AND acad_status='1' and session_year = '".$session_year."' and `session` = '".$session."' and semester = '".$semester."' AND UPPER(course_id)='".$course."' AND branch_id='".$branch."')B ON A.course_id=B.course_id AND A.branch_id=B.branch_id
            LEFT JOIN user_details ud ON ud.id=B.admn_no)
            GROUP BY B.admn_no
            ORDER BY B.admn_no";


       $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();

       return $query->result_array();

 }


 function get_student_list_with_limit_new($dept,$course,$branch,$session_year,$session,$semester,$offset_1,$offset_2){

    if($offset_1 == 1){

        $offset_1 = 0;
    }

    else {

        $offset_1 = $offset_1-1;
    }


    $sql = "SELECT B.admn_no
            FROM (
            (SELECT course_id,branch_id
            FROM dept_course a
            JOIN course_branch b ON a.course_branch_id=b.course_branch_id AND a.dept_id='".$dept."'
            GROUP BY b.course_id,b.branch_id)A
            INNER JOIN (
            SELECT admn_no,course_id,branch_id,semester,hod_status,acad_status
            FROM reg_regular_form
            WHERE hod_status='1' AND acad_status='1' and session_year = '".$session_year."' and `session` = '".$session."' and semester = '".$semester."' AND UPPER(course_id)='".$course."' AND branch_id='".$branch."')B ON A.course_id=B.course_id AND A.branch_id=B.branch_id
            LEFT JOIN user_details ud ON ud.id=B.admn_no)
            GROUP BY B.admn_no
            ORDER BY B.admn_no LIMIT ".$offset_1.",50";


       $query = $this->db->query($sql);

       //echo $this->db->last_query(); die();

       return $query->result_array();

 }





}




?>