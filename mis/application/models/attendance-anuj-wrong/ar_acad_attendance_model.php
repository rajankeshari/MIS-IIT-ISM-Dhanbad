<?php

class Ar_acad_attendance_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function courses() {
        $this->load->database();
        $result1 = $this->db->query("SELECT id FROM courses  ");
        $result1 = $result1->result();
        $result = array();
        foreach ($result1 as $row) {
            $result[$row->id] = 0;
        }
        return $result;
    }

    public function depart() {
        $this->load->database();
        $result = $this->db->query("SELECT * FROM departments WHERE type = 'academic' ");
        $result = $result->result();
        return $result;
    }

    public function get_branches($data) {
        $this->load->database();
        if ($data['depart_id'] == "All") {
            $result = $this->db->query("SELECT DISTINCT C.map_id ,C.semester
									FROM course_branch as A
									INNER JOIN dept_course as B
									ON A.course_branch_id = B.course_branch_id
									INNER JOIN subject_mapping as C
									ON C.branch_id = A.branch_id
									AND C.course_id = A.course_id 
									WHERE C.session_year = '$data[session_year]'
									AND C.session = '$data[session]'
									ORDER BY C.course_id , C.branch_id ,C.semester");
            $result = $result->result();
        } else {
            $result = $this->db->query("SELECT DISTINCT C.map_id ,C.semester
										FROM course_branch as A
										INNER JOIN dept_course as B
										ON A.course_branch_id = B.course_branch_id
										INNER JOIN subject_mapping as C
										ON C.branch_id = A.branch_id
										AND C.course_id = A.course_id 
										WHERE B.dept_id = '$data[depart_id]'
										AND C.session_year = '$data[session_year]'
										AND C.session = '$data[session]'
										ORDER BY C.course_id , C.branch_id ,C.semester");
            $result = $result->result();
        }
        return $result;
    }

    function getCoursebyDept($data) {
        if ($data['depart_id'] == "All") {
            $result = $this->db->query("SELECT DISTINCT C.course_id
									FROM course_branch as A
									INNER JOIN dept_course as B
									ON A.course_branch_id = B.course_branch_id
									INNER JOIN subject_mapping as C
									ON C.branch_id = A.branch_id
									AND C.course_id = A.course_id 
									WHERE C.session_year = '$data[session_year]'
									AND C.session = '$data[session]'
									ORDER BY C.course_id , C.branch_id ,C.semester");
            return $result->result();
            return false;
        } else {
            $result = $this->db->query("SELECT DISTINCT C.course_id
										FROM course_branch as A
										INNER JOIN dept_course as B
										ON A.course_branch_id = B.course_branch_id
										INNER JOIN subject_mapping as C
										ON C.branch_id = A.branch_id
										AND C.course_id = A.course_id 
										WHERE B.dept_id = '$data[depart_id]'
										AND C.session_year = '$data[session_year]'
										AND C.session = '$data[session]'
										ORDER BY C.course_id , C.branch_id ,C.semester");

            if ($result->num_rows() > 0)
                return $result->result();
            return false;
        }
    }

    public function getStudents($data) {

        $session_year = $data['session_year'];
        $session = $data['session'];
        $depart = $data['depart_id'];

        $this->load->database();


        $i = 0;
        foreach ($data['map_id'] as $row) {

            $result1 = $this->db->query("select A.*,r.total_class from(
SELECT  a.admn_no as admission_id ,count(a.date) as total_absent,  a.sub_id as subject_id , b.subject_id as sub_code, b.name  as sub_name ,a.map_id,d.dept_id,d.course_id,d.branch_id,d.semester
									 	 FROM absent_table a
									 	 join subjects b on a.sub_id = b.id
									 	 join subject_mapping d on a.map_id=d.map_id
									 	 join reg_regular_form c on a.admn_no = c.admn_no and d.semester = c.semester and d.session_year = c.session_year and d.session = c.session
									 	 
									 	 WHERE a.status = 2 
										 AND  a.map_id = '$row->map_id'
										 group by a.admn_no,a.sub_id
										 ORDER BY admission_id) A 
										 
										join total_class_table r on A.map_id = r.map_id
										where r.sub_id =A.subject_id ");
            if ($result1->num_rows() > 0)
                $result[] = $result1->result();
        }


        $i = 0;
        foreach ($result as $va) {
            foreach ($va as $key => $val) {
                $res = $this->getModFromtech($val->subject_id, $val->admission_id);
                if ($res) {
                    $percent = ((($val->total_class) - ($val->total_absent)) + ($res->count)) * 100;
                } else {
                    $percent = ((($val->total_class) - ($val->total_absent))) * 100;
                }

                $percent = (float) (($percent / $val->total_class));
                $percent = round($percent, 2);

                $result[$i][$key]->percent = $percent;

                $temp1 = $this->db->query("SELECT first_name , middle_name , last_name
									   FROM user_details
									   WHERE id = '$val->admission_id'  ");
                ($temp1 = $temp1->result());
                $result[$i][$key]->stu_name = $temp1[0]->first_name . ' ' . $temp1[0]->middle_name . ' ' . $temp1[0]->last_name;
            }

            $i++;
        }
        //echo $this->db->last_query();
        //print_r($result); die();
        // foreach ($result as $row) 
        // {
        // 	$query = $this->db->query("SELECT name as sub_name , subject_id as sub_code  
        // 							   FROM subjects
        // 							   WHERE id = '$row->subject_id '  ");
        // 	($query = $query->result()) ;
        // 	$row->sub_name = $query[0]->sub_name;
        // 	$row->sub_code = $query[0]->sub_code;
        // }
        //print_r($result);
        //echo '<br>' ;
        // foreach ($result as $row) 
        // {
        // 	$query = $this->db->query("SELECT course_id , branch_id , semester as semster  
        // 							   FROM reg_regular_form
        // 							   WHERE admn_no = '$row->admission_id' and session_year='$session_year' and session='$session'");
        // 	($query = $query->result()) ;
        // 	$row->course_id = $query[0]->course_id;
        // 	$row->branch_id = $query[0]->branch_id;
        // 	$row->semester = $query[0]->semster;
        // }
        //print_r($result);
        // foreach ($result as $row) 
        // {
        // 	$temp = $this->db->query("SELECT total_class
        // 					FROM total_class_table
        // 					WHERE map_id = $row->map_id 
        // 					AND sub_id = '$row->subject_id' ");
        // 	$temp = $temp->result();
        // 	$row->total_class = $temp[0]->total_class;
        // 	$query=$this->db->query("SELECT count(date) as date FROM absent_table
        // 							 WHERE map_id = $row->map_id AND sub_id = '$row->subject_id'
        // 							 AND admn_no = '$row->admission_id' 
        // 							 AND Remark='none'
        // 							 AND status = 2 ");
        // 	($temp_1 = $query->result()) ;
        // 			$res=$this->getModFromtech($row->subject_id,$row->admission_id);
        // 	$percent = (($temp[0]->total_class - $temp_1[0]->date)+($res->count)) *100;
        // 	$percent = (float)(($percent/$temp[0]->total_class));
        // 	$percent = round($percent,2);
        // 	$row->percent = $percent;
        // }
        //print_r($result);
        // foreach ($result as $row) 
        // {
        // 	$temp1 = $this->db->query("SELECT first_name , middle_name , last_name
        // 							   FROM user_details
        // 							   WHERE id = '$row->admission_id'  ");
        // 	//$row->total_class = $temp[0]->total_class;
        // 	($temp1 = $temp1->result()) ;
        // 	$row->stu_name = $temp1[0]->first_name.' '.$temp1[0]->middle_name.' '.$temp1[0]->last_name;
        //  }
        // print_r($result);
        return ($result);
    }
    public function getStudents_dd_hons($data) {

        $session_year = $data['session_year'];
        $session = $data['session'];
        $depart = $data['depart_id'];

        $this->load->database();


        $i = 0;
        foreach ($data['map_id'] as $row) {

            $result1 = $this->db->query("select A.*,r.total_class from(
SELECT  a.admn_no as admission_id ,count(a.date) as total_absent,  a.sub_id as subject_id , b.subject_id as sub_code, b.name  as sub_name ,a.map_id,d.dept_id,d.course_id,d.branch_id,d.semester
									 	 FROM absent_table_dd_hons a
									 	 join subjects b on a.sub_id = b.id
									 	 join subject_mapping d on a.map_id=d.map_id
									 	 join reg_regular_form c on a.admn_no = c.admn_no and d.semester = c.semester and d.session_year = c.session_year and d.session = c.session
									 	 
									 	 WHERE a.status = 2 
										 AND  a.map_id = '$row->map_id'
										 group by a.admn_no,a.sub_id
										 ORDER BY admission_id) A 
										 
										join total_class_table_dd_hons r on A.map_id = r.map_id
										where r.sub_id =A.subject_id ");
            if ($result1->num_rows() > 0)
                $result[] = $result1->result();
        }


        $i = 0;
        foreach ($result as $va) {
            foreach ($va as $key => $val) {
                $res = $this->getModFromtech($val->subject_id, $val->admission_id);
                if ($res) {
                    $percent = ((($val->total_class) - ($val->total_absent)) + ($res->count)) * 100;
                } else {
                    $percent = ((($val->total_class) - ($val->total_absent))) * 100;
                }

                $percent = (float) (($percent / $val->total_class));
                $percent = round($percent, 2);

                $result[$i][$key]->percent = $percent;

                $temp1 = $this->db->query("SELECT first_name , middle_name , last_name
									   FROM user_details
									   WHERE id = '$val->admission_id'  ");
                ($temp1 = $temp1->result());
                $result[$i][$key]->stu_name = $temp1[0]->first_name . ' ' . $temp1[0]->middle_name . ' ' . $temp1[0]->last_name;
            }

            $i++;
        }
       
        return ($result);
    }

    public function get_remarks($data) {
        //print_r($data['result']);
        $this->load->database();
        foreach ($data['result'] as $row) {
            $temp = $this->db->query("SELECT enrollment_year as year
			                 FROM stu_academic
			                 WHERE admn_no = '$row->admission_id' 
			                  ");
            $temp = $temp->result();
            $year = (int) $temp[0]->year;
            //$cmp = (int)$
            if ($year >= 2013) {

                $temp_new = $this->db->query("SELECT Remark 
				                         FROM defaulter_remark_table
				                         WHERE sl_no = 1 ");
                $temp_new = $temp_new->result();
                $remark = $temp_new[0]->Remark;
                $row->remark = $remark . ' ' . $data['session_year'];
            } elseif (0) {
                
            } else {
                if ($row->percent < 60) {
                    //echo  'hello';
                    $temp_new = $this->db->query("SELECT Remark 
					                         FROM defaulter_remark_table
					                         WHERE sl_no = 2 ");
                    $temp_new = $temp_new->result();
                    $remark = $temp_new[0]->Remark;
                    $row->remark = $remark;
                } else {
                    $temp_new = $this->db->query("SELECT Remark 
					                         FROM defaulter_remark_table
					                         WHERE sl_no = 3 ");
                    $temp_new = $temp_new->result();
                    $remark = $temp_new[0]->Remark;
                    $row->remark = $remark;
                }
            }
        }
        //return $data;
    }

    public function get_remarks_new($result) {
        //print_r($data['result']);
        $this->load->database();
        foreach ($result as $row) {
            $temp = $this->db->query("SELECT enrollment_year as year
			                 FROM stu_academic
			                 WHERE admn_no = '$row->admission_id' 
			                  ");
            $temp = $temp->result();
            $year = (int) $temp[0]->year;
            //$cmp = (int)$
            if ($year >= 2013) {

                $temp_new = $this->db->query("SELECT Remark 
				                         FROM defaulter_remark_table
				                         WHERE sl_no = 1 ");
                $temp_new = $temp_new->result();
                $remark = $temp_new[0]->Remark;
                $row->remark = $remark . ' ' . $data['session_year'];
            } elseif (0) {
                
            } else {
                if ($row->percent < 60) {
                    //echo  'hello';
                    $temp_new = $this->db->query("SELECT Remark 
					                         FROM defaulter_remark_table
					                         WHERE sl_no = 2 ");
                    $temp_new = $temp_new->result();
                    $remark = $temp_new[0]->Remark;
                    $row->remark = $remark;
                } else {
                    $temp_new = $this->db->query("SELECT Remark 
					                         FROM defaulter_remark_table
					                         WHERE sl_no = 3 ");
                    $temp_new = $temp_new->result();
                    $remark = $temp_new[0]->Remark;
                    $row->remark = $remark;
                }
            }
        }
        //return $data;
    }

    function getModFromtech($subid, $stuid) {
        $q = $this->db->get_where('Attendance_remark_table', ['sub_id' => $subid, 'admn_no' => $stuid]);
        if ($q->num_rows() > 0)
            return $q->row();
        return false;
    }

    function get_dept_name($id) {

        $sql = "select name from departments where id=?";

        $query = $this->db->query($sql, array($id));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->name;
        } else {
            return false;
        }
    }
    function get_course_name($id) {

        $sql = "select name from cs_courses where id=?";

        $query = $this->db->query($sql, array($id));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->name;
        } else {
            return false;
        }
    }
      function get_branch_name($id) {

        $sql = "select b.name from stu_academic a inner join cs_branches b on a.branch_id=b.id where a.admn_no=?";

        $query = $this->db->query($sql, array($id));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->row()->name;
        } else {
            return false;
        }
    }
    

}

?>