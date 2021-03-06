
<?php

class No_dues_dept_query_model extends CI_Model{
	function __construct(){
		parent::__construct();
	}

	/*
	*basic triggers
	************************************************************************************************
	*/

	// public function canArchive(){
	// 	$q = "SELECT * FROM no_dues_start";
	// 	$res = $this->db->query($q)->result_array();
	// 	return $res[1]['status'];
	// }
	// public function getStartStatus(){
	// 	$res = $this->db->query("SELECT status FROM no_dues_start WHERE name = 'initialise'")->result_array();
	// 	return $res[0]['status'];
	// }
	// // public function archive_table(){
	// // 	$q = "SELECT admn_no, session_year, dept_id, due_amt, remarks, receipt_path FROM no_dues_list";
	// // 	$res = $this->db->query($q)->result_array();
	// // 	for ($i = 0; $i<count($res); $i++){
	// // 		$admn_no = $res[$i]['admn_no'];
	// // 		$session_year = $res[$i]['session_year'];
	// // 		$dept_id = $res[$i]['dept_id'];
	// // 		$due_amt = $res[$i]['due_amt'];
	// // 		$remarks = $res[$i]['remarks'];
	// // 		$receipt_path = $res[$i]['receipt_path'];
	// // 		$q = "INSERT INTO no_dues_archives (admn_no, session_year, dept_id, due_amt, remarks, receipt_path) ".
	// // 			"VALUES ('$admn_no', '$session_year', '$dept_id', '$due_amt', '$remarks', '$receipt_path')";
	// // 		$this->db->query($q);
	// // 	}
	// // 	$this->db->query('DELETE FROM no_dues_list WHERE 1');
	// // }

	// // public function no_dues_dept_init(){
	// // 	$q = "UPDATE no_dues_dept SET status = '0'";
	// // 	$this->db->query($q);
	// // }

	// // // public function clear_no_dues_current(){
	// // // 	$this->db->query("DELETE FROM no_dues_current WHERE 1");
	// // // }

	// // // public function setArchiveFlag($s){
	// // // 	$this->db->query("UPDATE no_dues_start SET status = '$s' WHERE name='archive'");
	// // // }
	// public function start_no_dues_admin($s){
	// 	$this->db->query("UPDATE no_dues_start SET status ='$s' WHERE name='initialise'");
	// }
	
	// public function getStartStatus(){
	// 	$res = $this->db->query("SELECT status FROM no_dues_start  ")->result_array();
	// 	return $res[0]['status'];
	// }
		

	
	public function get_no_dues_current_list($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$q = "SELECT start_date,end_date FROM no_dues_start where access_to='$auth' AND session_year='$session_year_curr' and status!=2";
		$res = $this->db->query($q)->result_array();
		if($res==NULL){
			$this->session->set_flashdata("flashError", "No Dues for Admin are not in progress.You can Start Here.");
			if($auth=="admin") redirect('no_dues/no_dues_admin_edit/start_dues_admin');
			else redirect('no_dues/no_dues_admin_edit/start_dues_student');
		}
		$r = array();
		$i=0;
		$r[$i]['start_date'] = $res[$i]['start_date'];
		$r[$i]['end_date'] = $res[$i]['end_date'];
		return $r;
	}

	public function get_no_dues_current_list_2($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$q = "SELECT start_date,end_date FROM no_dues_start_for_dropouts where access_to='$auth' AND session_year='$session_year_curr' and status!=2";
		$res = $this->db->query($q)->result_array();
		if($res==NULL){
			$this->session->set_flashdata("flashError", "No Dues for Admin are not in progress.You can Start Here.");
			if($auth=="admin") redirect('no_dues/no_dues_admin_edit/start_dues_admin_2');
			else redirect('no_dues/no_dues_admin_edit/start_dues_student_2');
		}
		$r = array();
		$i=0;
		$r[$i]['start_date'] = $res[$i]['start_date'];
		$r[$i]['end_date'] = $res[$i]['end_date'];
		return $r;
	}

	
	public function get_current_admin_end_date(){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$auth="admin";
		$res=$this->db->query("SELECT end_date from no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status=1 ")->result_array();
		if(!$res)return 10;
		return $res[0]['end_date'];
		//return $res->result_array();
	}

	public function get_current_admin_end_date_2(){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$auth="admin";
		$res=$this->db->query("SELECT end_date from no_dues_start_for_dropouts where session_year='$session_year_curr' and access_to='$auth' and status=1 ")->result_array();
		if(!$res)return 10;
		return $res[0]['end_date'];
		//return $res->result_array();
	}
	public function get_current_student_start_date(){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$auth="stu";
		$res=$this->db->query("SELECT start_date from no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status!=2")->result_array();
		if(!$res)return 10;
		return $res[0]['start_date'];
		//return $res->result_array();
	}

	public function get_current_student_start_date_2(){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		$auth="stu";
		$res=$this->db->query("SELECT start_date from no_dues_start_for_dropouts where session_year='$session_year_curr' and access_to='$auth' and status!=2")->result_array();
		if(!$res)return 10;
		return $res[0]['start_date'];
		//return $res->result_array();
	}
	public function get_no_dues_status($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
        $res=$this->db->query("SELECT * FROM no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status!=2")->result_array();
        if(!$res) return 10;
        else return $res;
    }
    public function get_no_dues_status_2($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
        $res=$this->db->query("SELECT * FROM no_dues_start_for_dropouts where session_year='$session_year_curr' and access_to='$auth' and status!=2")->result_array();
        if(!$res) return 10;
        else return $res;
    }
    public function no_dues_status($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$sess=$this->ndqm->get_session($ts);
        $res= $this->db->query("SELECT * FROM no_dues_start where session_year='$sess' and access_to='$auth' and status!=2 ")->result_array();
        if(!$res)return 10;
        else return $res[0]['status'];

    }
     public function no_dues_status_2($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
        $res= $this->db->query("SELECT * FROM no_dues_start_for_dropouts where session_year='$session_year_curr' and access_to='$auth' and status!=2 ")->result_array();
        if(!$res)return 10;
        else return $res[0]['status'];

    }
    public function get_no_dues_running_status($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
        $res= $this->db->query("SELECT status FROM no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status='$s' ")->result_array();
        if(!$res) return 10;
        else return $res[0]['status'];
    
	}
	 public function get_no_dues_running_status_2($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
        $res= $this->db->query("SELECT status FROM no_dues_start_for_dropouts where session_year='$session_year_curr' and access_to='$auth' and status='$s' ")->result_array();
        if(!$res) return 10;
        else return $res[0]['status'];
    
	}
	public function start_no_dues_admin($start_date,$end_date){
		$s=0;
		$access_to="admin";
		$current_date=date('Y-m-d');
		$ts=$this->ndqm->curr_time_stamp();
		$session_year=$this->ndqm->get_session($ts);
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
		$this->db->query($q);
	}
	public function start_no_dues_admin_2($start_date,$end_date){
		$s=0;
		$access_to="admin";
		$current_date=date('Y-m-d');
		$ts=$this->ndqm->curr_time_stamp();
		$session_year=$this->ndqm->get_session($ts);
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
		$this->db->query($q);
	}
	public function start_no_dues_student($start_date,$end_date){
		$s=0;
		$access_to="stu";
		$current_date=date('Y-m-d');
		$ts=$this->ndqm->curr_time_stamp();
		$session_year=$this->ndqm->get_session($ts);
		$id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
		$this->db->query($q);
	}
	public function start_no_dues_student_2($start_date,$end_date){
		$s=0;
		$access_to="stu";
		$current_date=date('Y-m-d');
		$ts=$this->ndqm->curr_time_stamp();
		$session_year=$this->ndqm->get_session($ts);
		$id=$this->session->userdata['id'];
		$ts =$this->curr_time_stamp();
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$access_to','$session_year','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
		$this->db->query($q);
	}
	public function stop_no_dues_1($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$current_date=date('Y-m-d');
		$q="UPDATE no_dues_start SET status = '$s',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='1' ";
		$this->db->query($q);
	}
	public function stop_no_dues_1_2($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$current_date=date('Y-m-d');
		$q="UPDATE no_dues_start_for_dropouts SET status = '$s',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='1' ";
		$this->db->query($q);
	}
	public function stop_no_dues_2($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$q="DELETE FROM no_dues_start  WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$s' ";
		$this->db->query($q);
	}
	public function stop_no_dues_2_2($auth,$s){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$q="DELETE FROM no_dues_start_for_dropouts  WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$s' ";
		$this->db->query($q);
	}
	
	public function edit_no_dues_start_admin($start_date,$end_date){
		$auth="admin";
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		$s=0;
		$current_date=date('Y-m-d');
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$z=1;
		$res=$this->ndqm->get_no_dues_running_status($auth,$z);
		if($res==10){
			$ss=2;
			$temp=$this->ndqm->get_no_dues_running_status($auth,$ss);
			$ss=0;
			$temp2=$this->ndqm->get_no_dues_running_status($auth,$ss);
			if($temp==10){
				$q="UPDATE no_dues_start SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' ";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2==10){
				$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2!=10){
				$z=0;
				$q="UPDATE no_dues_start SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='$z' ";
				$this->db->query($q);
			}
		}
		else{
			$sss=2;
			$ss=1;
			$q="UPDATE no_dues_start SET status = '$sss',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$ss' ";
			$this->db->query($q);

			$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
			$this->db->query($q);
		}
	}

	//For dropouts
	public function edit_no_dues_start_admin_2($start_date,$end_date){
		$auth="admin";
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		$s=0;
		$current_date=date('Y-m-d');
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$z=1;
		$res=$this->ndqm->get_no_dues_running_status_2($auth,$z);
		if($res==10){
			$ss=2;
			$temp=$this->ndqm->get_no_dues_running_status_2($auth,$ss);
			$ss=0;
			$temp2=$this->ndqm->get_no_dues_running_status_2($auth,$ss);
			if($temp==10){
				$q="UPDATE no_dues_start_for_dropouts SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' ";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2==10){
				$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2!=10){
				$z=0;
				$q="UPDATE no_dues_start_for_dropouts SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='$z' ";
				$this->db->query($q);
			}
		}
		else{
			$sss=2;
			$ss=1;
			$q="UPDATE no_dues_start_for_dropouts SET status = '$sss',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$ss' ";
			$this->db->query($q);

			$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
			$this->db->query($q);
		}
	}


	public function edit_no_dues_start_student($start_date,$end_date){
		$auth="stu";
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$s=0;
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		$current_date=date('Y-m-d');
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$z=1;
		$res=$this->ndqm->get_no_dues_running_status($auth,$z);
		if($res==10){
			$ss=2;
			$temp=$this->ndqm->get_no_dues_running_status($auth,$ss);
			$ss=0;
			$temp2=$this->ndqm->get_no_dues_running_status($auth,$ss);
			if($temp==10){
				$q="UPDATE no_dues_start SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' ";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2==10){
				$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2!=10){
				$z=0;
				$q="UPDATE no_dues_start SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='$z' ";
				$this->db->query($q);
			}
		}
		else{
			$sss=2;
			$ss=1;
			$q="UPDATE no_dues_start SET status = '$sss',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$ss' ";
			$this->db->query($q);

			$q = "INSERT INTO no_dues_start (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
			$this->db->query($q);
		}
	}

	//for dropouts

	public function edit_no_dues_start_student_2($start_date,$end_date){
		$auth="stu";
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts); 
		$s=0;
		$id=$this->session->userdata['id'];
		//$ts =$this->curr_time_stamp();
		$current_date=date('Y-m-d');
		if($start_date <= $current_date && $end_date >= $current_date) $s=1;
		$z=1;
		$res=$this->ndqm->get_no_dues_running_status_2($auth,$z);
		if($res==10){
			$ss=2;
			$temp=$this->ndqm->get_no_dues_running_status_2($auth,$ss);
			$ss=0;
			$temp2=$this->ndqm->get_no_dues_running_status_2($auth,$ss);
			if($temp==10){
				$q="UPDATE no_dues_start_for_dropouts SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' ";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2==10){
				$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
				$this->db->query($q);
			}
			else if($temp!=10 && $temp2!=10){
				$z=0;
				$q="UPDATE no_dues_start_for_dropouts SET status = '$s',start_date='$start_date',end_date='$end_date' WHERE session_year='$session_year_curr' AND access_to='$auth' and status='$z' ";
				$this->db->query($q);
			}
		}
		else{
			$sss=2;
			$ss=1;
			$q="UPDATE no_dues_start_for_dropouts SET status = '$sss',end_date='$current_date' WHERE session_year='$session_year_curr' AND access_to='$auth' AND status='$ss' ";
			$this->db->query($q);

			$q = "INSERT INTO no_dues_start_for_dropouts (access_to,session_year,start_date,end_date,started_by,timestamp,status) ".
				"VALUES ('$auth','$session_year_curr','$start_date', '$end_date','$id',CURRENT_TIMESTAMP,'$s')";
			$this->db->query($q);
		}
	}

public  function get_no_dues_dropout_list($sess){
	$q = "SELECT * FROM no_dues_dropouts where session_year='$sess' ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['admn_no']=$res[$i]['admn_no'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
}



	//*End Triggers
//	****************************************************************************************************
//	*/

	public function curr_time_stamp(){
		$curr_time = date_create();
		$ts = date_format($curr_time, 'Y-m-d H:i:s');
		return $ts;
	}


	public function get_session($ts){
		 $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){
            $p_year = strval((int)$year);
            $year = $p_year +1;

        }
        else
        $p_year = strval((int)$year - 1);
        return $p_year.'-'.$year;
	}
	public function get_session2($ts){
		 $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){
            $p_year = strval((int)$year);
            $year = $p_year +1;

        }
        else
        $p_year = strval((int)$year - 1);

    	$p_year=$p_year-1;
    	$year=$year-1;
        return $p_year.'-'.$year;
	}
	public function get_session3($ts){
		 $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){
            $p_year = strval((int)$year);
            $year = $p_year +1;

        }
        else
        $p_year = strval((int)$year - 1);

    	$p_year=$p_year+1;
    	$year=$year+1;
        return $p_year.'-'.$year;
	}

	public function get_all_courses(){
		/*
		*Returns all the courses and id
		*If no course is selected then it returns all the courses
		*used by no_dues_ajax/populate_courses
		*/
		$dept_id = $this->session->userdata['dept_id'];
		$dept_type = $this->session->userdata['dept_type'];
		$q = "";
		$f = array();
		if ($dept_type == "academic"){
			//$q = "SELECT course_id FROM course_branch WHERE branch_id = '$dept_id'";
			$q = "SELECT distinct cb.course_id ".
				"FROM course_branch AS cb ".
				"INNER JOIN dept_course AS dc ".
				"ON cb.course_branch_id = dc.course_branch_id ".
				"WHERE dc.dept_id = '$dept_id'";
			$res = $this->db->query($q)->result_array();
			for ($i = 0; $i < count($res); $i++){
				$cn = $this->get_course_name($res[$i]['course_id']);
				if ($cn != 'undf'){
					array_push($f, array('id' => $res[$i]['course_id'], 'name' => $cn));
				}
			}
			return $f;
		}
		else{
			$q = "SELECT id, name FROM courses";
			$res = $this->db->query($q)->result_array();
			return $res;
		}
	}

	public function get_all_reference_no($id){
		$q="SELECT ref_no from no_dues_stu_ref_id where admn_no='$id' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_reference_no_sem($id,$sem){
		$q="SELECT ref_no from no_dues_stu_ref_id_sem where admn_no='$id' and sem='$sem' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_reference_no_2($id){
		$q="SELECT ref_no from no_dues_stu_ref_id_for_dropouts where admn_no='$id' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_reference_no_status($id){
		$q="SELECT status from no_dues_stu_ref_id where admn_no='$id' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_reference_no_status_sem($id,$sem){
		$q="SELECT status from no_dues_stu_ref_id_sem where admn_no='$id' and sem='$sem' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_reference_no_status_2($id){
		$q="SELECT status from no_dues_stu_ref_id_for_dropouts where admn_no='$id' order by status DESC";
		$res=$this->db->query($q)->result_array();
		// if($res==NULL) return 10;
		return $res;
	}
	public function get_all_depts(){
		/*
		*returns all the academic departments
		*/
		$q = "SELECT id, name FROM departments WHERE type = 'academic'";
		$res = $this->db->query($q)->result_array();
		return $res;
	}


	public function get_all_dept(){
		$q = "SELECT id, name FROM departments  ";
		$res = $this->db->query($q)->result_array();
		return $res;
	}
	public function get_student_name($id){
		$q="SELECT `Full Name` from stuadsuser where Adm_no='$id' ";
		$res = $this->db->query($q)->result_array();
		if(!$res) return 10;
		return $res[0]['Full Name'];
	}
	public function get_student_dept($id){
		$q="SELECT Dept from stuadsuser where Adm_no='$id' ";
		$res = $this->db->query($q)->result_array();
		return $res[0]['Dept'];
	}

	public function student_details_table($dept, $course, $sem){
		/*
		*Returns the details to the no_dues_ajax class for populating the table
		*/
		/*
		Field name changed in stu_academic table id->admn_no
		==========================================================================================================
		*/
		// $q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id ".
		// 	"FROM user_details ".
		// 	"INNER JOIN stu_academic ".
		// 	"ON user_details.id = stu_academic.admn_no ".
		// 	"WHERE user_details.id NOT IN (SELECT admn_no FROM no_dues_list) ".
		// 	"AND user_details.dept_id LIKE '$dept' ".
		// 	"AND stu_academic.semester LIKE '$sem' ".
		// 	"AND stu_academic.course_id LIKE '$course'";


				$q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id ".
			"FROM user_details ".
			"INNER JOIN stu_academic ".
			"ON user_details.id = stu_academic.admn_no ".
			// "WHERE user_details.id NOT IN (SELECT admn_no FROM no_dues_list) ".
			"WHERE  user_details.dept_id LIKE '$dept' ".
			"AND stu_academic.semester LIKE '$sem' ".
			"AND stu_academic.course_id LIKE '$course'";
		/*
		==========================================================================================================
		*/
		$res = $this->db->query($q)->result_array();
		for ($i = 0; $i<count($res); $i++){
			$res[$i]['dept_name'] = $this->get_dept_name($res[$i]['dept_id']);
			$res[$i]['course_name'] = $this->get_course_name($res[$i]['course_id']);
		}
		return $res;
	}
	public function student_details_table_2($dept, $course, $sem){
	  
		// $q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id ".
		// 	"FROM user_details ".
		// 	"INNER JOIN stu_academic ".
		// 	"ON stu_academic.admn_no= user_details.id".
		// 	"INNER JOIN no_dues_dropouts".
		// 	"ON  no_dues_dropouts.admn_no=user_details.id  ".
		// 	// "WHERE user_details.id NOT IN (SELECT admn_no FROM no_dues_list) ".
		// 	"WHERE  user_details.dept_id LIKE '$dept' ".
		// 	"AND stu_academic.semester LIKE '$sem' ".
		// 	"AND stu_academic.course_id LIKE '$course'";
		// /*
		// ==========================================================================================================
		// */
		$q="SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id FROM user_details INNER JOIN stu_academic ON stu_academic.admn_no= user_details.id INNER JOIN no_dues_dropouts ON no_dues_dropouts.admn_no=user_details.id WHERE user_details.dept_id LIKE '$dept' AND stu_academic.semester LIKE '$sem' AND stu_academic.course_id LIKE '$course' ";
		$res = $this->db->query($q)->result_array();
		for ($i = 0; $i<count($res); $i++){
			$res[$i]['dept_name'] = $this->get_dept_name($res[$i]['dept_id']);
			$res[$i]['course_name'] = $this->get_course_name($res[$i]['course_id']);
		}
		return $res;
	}

		public function no_dues_edit_list($dept, $course, $sem){

				$q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id ".
			"FROM user_details ".
			"INNER JOIN stu_academic ".
			"ON user_details.id = stu_academic.admn_no ".
			// "WHERE user_details.id NOT IN (SELECT admn_no FROM no_dues_list) ".
			"WHERE  user_details.dept_id LIKE '$dept' ".
			"AND stu_academic.semester LIKE '$sem' ".
			"AND stu_academic.course_id LIKE '$course'";
		
		$res = $this->db->query($q)->result_array();
		for ($i = 0; $i<count($res); $i++){
			$res[$i]['dept_name'] = $this->get_dept_name($res[$i]['dept_id']);
			$res[$i]['course_name'] = $this->get_course_name($res[$i]['course_id']);
		}
		return $res;
	}

    
    public function get_edit_list($course,$sem,$dept_id,$admn_no){

		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no WHERE no_dues_lists.pay_status=0 and no_dues_lists.change_status=0 and no_dues_lists.dept_id like '$dept_id' and super.semester like '$sem' and course_id='$course'
		and admn_no like'$admn_no' ";
$res=$this->db->query($query)->result_array();
		return $res;	

	}

	public function get_edit_list_all($course,$sem,$dept_id,$admn_no){

		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no WHERE no_dues_lists.pay_status=0 and no_dues_lists.change_status<>2 and no_dues_lists.dept_id like '$dept_id' and super.semester like '$sem' and course_id='$course'
		and admn_no like'$admn_no' ";
$res=$this->db->query($query)->result_array();
		return $res;	

	}


public function get_edit_list_2($course,$sem,$dept_id,$admn_no){

		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no INNER JOIN no_dues_dropouts ON no_dues_dropouts.admn_no=no_dues_lists.admn_no WHERE no_dues_lists.pay_status=0 and no_dues_lists.change_status=0 and  no_dues_lists.dept_id like '$dept_id' and super.semester like '$sem' and course_id='$course' and admn_no like'$admn_no' ";
$res=$this->db->query($query)->result_array();
		return $res;	

	}
	public function get_history($start_date,$end_date,$course,$sem,$dept_id){

  $end_date=$end_date." 23:59:59";
		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no WHERE  no_dues_lists.dept_id like '$dept_id' and super.semester like'$sem' and course_id='$course'and no_dues_lists.timestamp>='$start_date' and no_dues_lists.timestamp<='$end_date' order by no_dues_lists.admn_no,no_dues_lists.change_status ";
$res=$this->db->query($query)->result_array();

//echo $this->db->last_query();
//die();
		return $res;


	}

	public function get_history_all($start_date,$end_date,$course,$sem){

  $end_date=$end_date." 23:59:59";
		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no WHERE super.semester like'$sem' and course_id='$course'and no_dues_lists.timestamp>='$start_date' and no_dues_lists.timestamp<='$end_date' order by no_dues_lists.admn_no,no_dues_lists.change_status ";
$res=$this->db->query($query)->result_array();

//echo $this->db->last_query();
//die();
		return $res;


	}

	////for dropouts
	public function get_history_2($start_date,$end_date,$course,$sem,$dept_id){

  $end_date=$end_date." 23:59:59";
		$query="select * FROM no_dues_lists INNER join (select sa.course_id,sa.semester,first_name,middle_name,last_name,ud.id as l,sa.branch_id from user_details as ud INNER join stu_academic as sa on sa.admn_no=ud.id) as super on super.l=no_dues_lists.admn_no 
		INNER JOIN no_dues_dropouts ON no_dues_dropouts.admn_no=no_dues_lists.admn_no WHERE  no_dues_lists.dept_id like '$dept_id' and super.semester like'$sem' and course_id='$course'and no_dues_lists.timestamp>='$start_date' and no_dues_lists.timestamp<='$end_date' order by no_dues_lists.admn_no,no_dues_lists.change_status ";
$res=$this->db->query($query)->result_array();

		return $res;


	}

	public function delete_no_dues_lists($id,$reason){
		$emp_id=$this->session->userdata('id');
      $name=$this->get_emp_name($emp_id);
		$reason="Deleted by ".$name."(".$emp_id.") because ".$reason;
		$query="update no_dues_lists set change_status='2', reason='$reason' where id='$id'";
		$this->db->query($query);
	}
// ////for dropouts
// 	public function delete_no_dues_lists_2($id,$reason){
// 		$emp_id=$this->session->userdata('id');
//       $name=$this->get_emp_name($emp_id);
// 		$reason="Deleted by ".$name."(".$emp_id.") because ".$reason;
// 		$query="update no_dues_lists_for_dropouts set change_status='2', reason='$reason' where id='$id'";
// 		$this->db->query($query);
// 	}

  public function get_name($id){
  	$query="select id,first_name,middle_name,last_name from user_details where id='$id";
  	$res=$this->db->query($query);
  	$name="";
  	if($res[0]['first_name'])
  		$name=$name.$res[0]['first_name'];
  	if($res[0]['middle_name'])
  		$name=$name.$res[0]['middle_name'];

  		if($res[0]['last_name'])
  		$name=$name.$res[0]['last_name'];
  	return $name;

  }
  public function get_course_id_by_admn_no($admn_no){
  	$q="SELECT course_id from stu_academic where admn_no='$admn_no' ";
  	$res = $this->db->query($q)->result_array();
  	if (count($res))
			return $res[0]['course_id'];
		else 
			return "undf";
  }
  public function get_course($course_id){
  	$q="SELECT name from courses where id='$course_id' ";
  	$res = $this->db->query($q)->result_array();
  	if (count($res))
			return $res[0]['name'];
		else 
			return "undf";
  }
	public function get_course_name($course_id){
		$q = "SELECT name FROM courses WHERE id LIKE '$course_id'";
		$res = $this->db->query($q)->result_array();
		if (count($res))
			return $res[0]['name'];
		else 
			return "undf";
	}

	public function get_dept_name($dept_id){
		$q = "SELECT name FROM departments WHERE id LIKE '$dept_id' ";
		$res = $this->db->query($q)->result_array();
		if (count($res))
			return $res[0]['name'];
		return "undf";
	}

	public function get_course_id_from_name($course_id){
		$q = "SELECT name from course WHERE name = '$course_id'";
		$res = $this->db->query($q)->result_array();
		return $res[0]['id'];
	}
	public function get_duration_of_course($course_id){
		$q = "SELECT duration FROM courses WHERE id = '$course_id'";
		$res = $this->db->query($q)->result_array();
		return $res[0]['duration'];
	}

	public function get_student_status($admn_no,$id){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);
		$dept = $this->session->userdata['dept_id'];
		$qn = "SELECT first_name, middle_name, last_name FROM user_details WHERE id = '$admn_no'";
		$qs = "SELECT branch_id, course_id FROM stu_academic WHERE admn_no = '$admn_no'";
		$q = "SELECT * FROM no_dues_lists WHERE  id='$id' AND dept_id = '$dept'";


		$resn = $this->db->query($qn)->result_array();
		$res = $this->db->query($q)->result_array();
		$resa = $this->db->query($qs)->result_array();
		 //print_r($res);
		//echo $id."i am id whaT THE FFFUOGJHC";

		$name = $resn[0]['first_name'];
		if (strlen($resn[0]['middle_name']) != 0){
			$name = $name." ".$resn[0]['middle_name'];
		}
		if (strlen($resn[0]['last_name']) != 0){
			$name = $name." ".$resn[0]['last_name'];
		}
		$res[0]['name'] = $name;

		$course_name = $this->get_course_name($resa[0]['course_id']);
		$branch_name = $this->get_dept_name($resa[0]['branch_id']);

		$res[0]['course_name'] = $course_name;
		$res[0]['branch_name'] = $branch_name;
		$res[0]['session_year'] = $sess;
		// print_r($res);
		// echo "dfjhvdf0";
		return $res;
	}

	// public function get_student_status_2($admn_no,$id){
	// 	$ts = $this->curr_time_stamp();
	// 	$sess = $this->get_session($ts);
	// 	$dept = $this->session->userdata['dept_id'];
	// 	$qn = "SELECT first_name, middle_name, last_name FROM user_details WHERE id = '$admn_no'";
	// 	$qs = "SELECT branch_id, course_id FROM stu_academic WHERE admn_no = '$admn_no'";
	// 	$q = "SELECT * FROM no_dues_lists_for_dropouts WHERE  id='$id' AND dept_id = '$dept'";


	// 	$resn = $this->db->query($qn)->result_array();
	// 	$res = $this->db->query($q)->result_array();
	// 	$resa = $this->db->query($qs)->result_array();
	// 	 //print_r($res);
	// 	//echo $id."i am id whaT THE FFFUOGJHC";

	// 	$name = $resn[0]['first_name'];
	// 	if (strlen($resn[0]['middle_name']) != 0){
	// 		$name = $name." ".$resn[0]['middle_name'];
	// 	}
	// 	if (strlen($resn[0]['last_name']) != 0){
	// 		$name = $name." ".$resn[0]['last_name'];
	// 	}
	// 	$res[0]['name'] = $name;

	// 	$course_name = $this->get_course_name($resa[0]['course_id']);
	// 	$branch_name = $this->get_dept_name($resa[0]['branch_id']);

	// 	$res[0]['course_name'] = $course_name;
	// 	$res[0]['branch_name'] = $branch_name;
	// 	$res[0]['session_year'] = $sess;
	// 	// print_r($res);
	// 	// echo "dfjhvdf0";
	// 	return $res;
	// }


	public function delete_dues_status($admn_no, $due_amt, $remarks){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);
		$dept_id = $this->session->userdata['dept_id'];
		// //$dept_id = $this->session->userdata['dept_id'];
		// //$q = "SELECT distinct admn_no FROM no_dues_lists WHERE dept_id = '$dept_id'";
		// //$res = $this->db->query($q)->result_array();
		// $title = "Dues Deleted in ".$this->session->userdata['dept_name'];
		// $description = "Your Due was deleted from ".$this->session->userdata['dept_name'];
		// $link = 'no_dues/no_dues_student_init/view_dues_2';
		
		// $this->notification->notify($admn_no,"stu",$title,$description,$link,"");
		$qi = "DELETE FROM no_dues_lists WHERE admn_no='$admn_no' AND due_amt='$due_amt' AND remarks='$remarks";

		$this->db->query($qi);
		// $this->ndqm->send_dues_notification($admn_no);
	}

	// ////for dropouts
	// public function delete_dues_status_2($admn_no, $due_amt, $remarks){
	// 	$ts = $this->curr_time_stamp();
	// 	$sess = $this->get_session($ts);
	// 	$dept_id = $this->session->userdata['dept_id'];
	// 	// //$dept_id = $this->session->userdata['dept_id'];
	// 	// //$q = "SELECT distinct admn_no FROM no_dues_lists WHERE dept_id = '$dept_id'";
	// 	// //$res = $this->db->query($q)->result_array();
	// 	// $title = "Dues Deleted in ".$this->session->userdata['dept_name'];
	// 	// $description = "Your Due was deleted from ".$this->session->userdata['dept_name'];
	// 	// $link = 'no_dues/no_dues_student_init/view_dues_2';
		
	// 	// $this->notification->notify($admn_no,"stu",$title,$description,$link,"");
	// 	$qi = "DELETE FROM no_dues_lists_for_dropouts WHERE admn_no='$admn_no' AND due_amt='$due_amt' AND remarks='$remarks";

	// 	$this->db->query($qi);
	// 	// $this->ndqm->send_dues_notification($admn_no);
	// }

	public function update_dues_status($admn_no, $due_amt,$due_list, $remarks){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);
		$dept_id = $this->session->userdata['dept_id'];
		$remarks=$remarks;
		$emp_id=$this->session->userdata['id'];
		$qi = "INSERT INTO no_dues_lists ".
			"(admn_no, session_year, dept_id,emp_id, timestamp, due_amt,due_list ,remarks) ".
			"VALUES ('$admn_no', '$sess', '$dept_id' ,'$emp_id','$ts', '$due_amt','$due_list', '$remarks')";
		$this->db->query($qi);

       $sem_course=$this->get_semester_course_id($admn_no);
		$course_id=$sem_course[0]['course_id'];
		$sem=$sem_course[0]['semester'];
		   $year=$this->get_duration_of_course($course_id);
            $year=$year+$year;
          $temp=$this->check_for_dropouts($admn_no);
       if($year==$sem || $temp!=0){
		$q="UPDATE no_dues_stu_ref_id set status=0 where admn_no='$admn_no' and status=1";
		$this->db->query($q);
		$this->load->model('no_dues/no_dues_dept_query_model', 'ndqm');
		$this->ndqm->send_dues_notification($admn_no);
	}
	else{
		$q="UPDATE no_dues_stu_ref_id_sem set status=0 where admn_no='$admn_no' and sem='$sem' and status=1";
		$this->db->query($q);
	}

	}

	// ////for dropouts
	// public function update_dues_status_2($admn_no, $due_amt,$due_list, $remarks){
	// 	$ts = $this->curr_time_stamp();
	// 	$sess = $this->get_session($ts);
	// 	$dept_id = $this->session->userdata['dept_id'];
	// 	$remarks=$remarks;
	// 	$emp_id=$this->session->userdata['id'];
	// 	$qi = "INSERT INTO no_dues_lists_for_dropouts ".
	// 		"(admn_no, session_year, dept_id,emp_id, timestamp, due_amt,due_list ,remarks) ".
	// 		"VALUES ('$admn_no', '$sess', '$dept_id' ,'$emp_id','$ts', '$due_amt','$due_list', '$remarks')";
	// 	$this->db->query($qi);

	// 	$q="UPDATE no_dues_stu_ref_id_for_dropouts set status=0 where admn_no='$admn_no' and status=1";
	// 	$this->db->query($q);
	// 	$this->load->model('no_dues/no_dues_dept_query_model', 'ndqm');
	// 	$this->ndqm->send_dues_notification($admn_no);

	// }

	// public function test($admn_no,$due_amt,$dept_id){
	// 	$query="SELECT id FROM no_dues_list Where dept_id='$dept_id' AND admn_no='$admn_no'order by  id DESC ";
		
	// 	$query="select * from user_auth_types where id='806'";
	// 	$res=$this->db->query($query)->result_array();
	// 	print_r($res);
	// 	// echo $res[0]['id']." iam id rey ";  
	// 	// $id =$res[0]['id'];
	// 	// $q="UPDATE no_dues_list set due_amt='$due_amt' WHERE id='$id'";
	// 	// $this->db->query($q);


	// 	//return $res;

	// }

	public function edit_dues_status($admn_no, $dues_amt,$due_list, $remarks){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);
		$dept_id = $this->session->userdata['dept_id'];

			$query="SELECT id FROM no_dues_lists Where dept_id='$dept_id' AND admn_no='$admn_no'  order by  id DESC ";
		$res=$this->db->query($query)->result_array();
		echo $id = $res[0]['id'];  	 	 	 	
		//return $res;

       
		echo $q = "UPDATE no_dues_lists    ".
			"SET timestamp = '$ts', due_amt = '$dues_amt',due_list='$due_list' ,remarks = '$remarks' ".
			"Where id='$id'"; exit;
			//"WHERE admn_no = '$admn_no' AND dept_id = '$dept_id' AND session_year = '$sess'";

		$this->db->query($q);
	}

	public function get_student_acad_details($admn_no){

		$q = "SELECT course_id, branch_id FROM stu_academic WHERE admn_no = '$admn_no'";

		$res = $this->db->query($q)->result_array();
		return $res;
	}

	public function update_no_dues_table($res){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);

		for ($i = 0; $i < count($res['values']); $i++){
			$admn_no = $res['values'][$i][$res['fields'][0]];
			$due_amt = $res['values'][$i][$res['fields'][1]];
			$remarks = $res['values'][$i][$res['fields'][2]];
			$det = $this->get_student_acad_details($admn_no);
			$course_id = $det[0]['course_id'];
			$branch_id = $det[0]['branch_id'];
			$dept_name = $this->session->userdata['dept_name'];

			$q = "INSERT INTO no_dues_lists (admn_no, session_year, course_id, branch_id, dept_name, timestamp, due_amt, remarks) VALUES ('$admn_no', '$sess', '$course_id', '$branch_id', '$dept_name', '$ts', '$due_amt', '$remarks')";
			$this->db->query($q);
		}
	}
public function multiple_fines($admn_no,$due_amt,$due_list,$remarks){
		$ts = $this->curr_time_stamp();
	
		
		$session_year=$this->get_session($ts);
		$dept_id=$this->session->userdata('dept_id');
		$emp_id=$this->session->userdata('id');
		$query="INSERT INTO no_dues_lists (admn_no,session_year,dept_id,emp_id,`timestamp`,due_amt,due_list,remarks) VALUES
		 ('$admn_no','$session_year','$dept_id','$emp_id','$ts','$due_amt','$due_list','$remarks') ";

		$this->db->query($query);
		$q="UPDATE no_dues_stu_ref_id set status=0 where admn_no='$admn_no' and status=1";
		$this->db->query($q);


	}
	public function approve_reject($data, $status){
		$admn_no = $data['admn_no'];
		$sess = $data['session_year'];
		$rem = $data['remarks'];
		$id=$data['id'];
		$ts = $this->curr_time_stamp();
		$dept = $this->session->userdata['dept_id'];
		if (strlen($rem) != 0){
			$q = "UPDATE no_dues_lists SET pay_status = '$status', remarks = '$rem' ".
				"WHERE  id='$id' ";
				$this->db->query($q);
		}
		else{
			$q = "UPDATE no_dues_lists SET pay_status = '$status' ".
				"WHERE id='$id'";
				$this->db->query($q);
		}
		
	}	

	// public function get_list_upload_status(){
	// 	$ts = $this->curr_time_stamp();
	// 	$sess = $this->get_session($ts);

	// 	$dept = $this->session->userdata['dept_id'];
	// 	$this->session->set_flashdata("flashError", "No Dues Info already Uploaded",$dept);

	// 	$q = "SELECT status FROM no_dues_dept WHERE dept_id = '$dept'";
	// 	$res = $this->db->query($q)->result_array();
	
	// 	if ($res[0]['status'] == 0) return false;
	// 	return true;
	// }
	// public function set_list_upload_status(){
	// 	$ts = $this->curr_time_stamp();
	// 	$sess = $this->get_session($ts);

	// 	$dept = $this->session->userdata['dept_id'];

	// 	$q = "UPDATE no_dues_dept SET status=1 WHERE dept_id='$dept'";
	// 	$res = $this->db->query($q);
	// }

	public function get_no_dues_dept_list(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "SELECT * FROM no_dues_dept where session_year='$sess' order by `part` ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			// $r[$i]['status'] = $res[$i]['status'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
	}
	//for dropouts
	public function get_no_dues_dept_list_2(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "SELECT * FROM no_dues_dept_for_dropouts where session_year='$sess' order by `part` ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			// $r[$i]['status'] = $res[$i]['status'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
	}


	public function get_no_dues_prev_dept_list(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session2($ts);
		$q = "SELECT * FROM no_dues_dept where session_year='$sess' order by `part` ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			// $r[$i]['status'] = $res[$i]['status'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
	}

	///for dropouts
	public function get_no_dues_prev_dept_list_2(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session2($ts);
		$q = "SELECT * FROM no_dues_dept_for_dropouts where session_year='$sess' order by `part` ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			// $r[$i]['status'] = $res[$i]['status'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
	}


	public function add_dept_details_prev_yr(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session2($ts);
		$res=$this->db->query("select * from no_dues_dept where session_year='$sess' ")->result_array();
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $r = array();
        for ($i = 0; $i < count($res); $i++){
        	$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$t_dept=$r[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			$t_part=$r[$i]['part'];
			$temp=$this->db->query("select * from no_dues_dept where session_year='$sess' and dept_id='$t_dept' ")->result_array();
	  		if(!$temp){
        		$id=$this->session->userdata['id'];
				$q = "INSERT INTO no_dues_dept (dept_id, part,session_year,added_by,timestamp) VALUES ('$t_dept', '$t_part','$sess','$id',CURRENT_TIMESTAMP)";
				$this->db->query($q);
			}
		}
	}

	///for dropouts

	public function add_dept_details_prev_yr_2(){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session2($ts);
		$res=$this->db->query("select * from no_dues_dept_for_dropouts where session_year='$sess' ")->result_array();
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
        $r = array();
        for ($i = 0; $i < count($res); $i++){
        	$p = $this->get_dept_name($res[$i]['dept_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$t_dept=$r[$i]['dept_id'];
			$r[$i]['part'] = $res[$i]['part'];
			$t_part=$r[$i]['part'];
			$temp=$this->db->query("select * from no_dues_dept_for_dropouts where session_year='$sess' and dept_id='$t_dept' ")->result_array();
	  		if(!$temp){
        		$id=$this->session->userdata['id'];
				$q = "INSERT INTO no_dues_dept_for_dropouts (dept_id, part,session_year,added_by,timestamp) VALUES ('$t_dept', '$t_part','$sess','$id',CURRENT_TIMESTAMP)";
				$this->db->query($q);
			}
		}
	}

	public function get_no_dues_nda_list(){
		$q = "SELECT * FROM no_dues_nda where status = '0' ";
		$res = $this->db->query($q)->result_array();
		$r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_dept_name($res[$i]['dept_id']);
			$q= $this->get_emp_name($res[$i]['emp_id']);
			$r[$i] = array();
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['id'] = $res[$i]['id'];
			//$r[$i]['status'] = $res[$i]['status'];
			$r[$i]['name'] = $q;
			$r[$i]['emp_id']=$res[$i]['emp_id'];
			if ($p != 'undf'){
				$r[$i]['dept_name'] = $p;
			}
			else{
				$r[$i]['dept_name'] = $res[$i]['dept_id'];
			} 
		}
		return $r;
	}

	public function get_all_emp_id($dept_id)

		{

			$dept_name=$this->get_dept_name($dept_id);
$q="select `emp no` as emp_id,`full name` as name from empadsuser where `Dept` like '$dept_name' and `emp no` not in (select `emp_id` as `emp no` from no_dues_nda where status='0')";

			//$q="select emp_no as emp_id from emp_basic_details where  emp_no not in (select emp_id as emp_no from no_dues_nda where 1)";

		$query = $this->db->query($q);
			return $query->result_array();
		
	}
	

	// public function get_deligated_list($nda){
	// 	$query="select * from no_dues_nda_dnda where nda_emp_id='$nda'";
	// 	$res=$this->db->query($query)->result_array();
 //        $r = array();
	// 	for ($i = 0; $i < count($res); $i++){
	// 		$p = $this->get_emp_name($res[$i]['dnda_emp_id']);
	// 		$r[$i] = array();
	// 		$r[$i]['dnda_emp_id'] = $res[$i]['dnda_emp_id'];
	// 		$r[$i]['status'] = $res[$i]['status'];
	// 		$r[$i]['dept_id'] = $res[$i]['dept_id'];
	// 		$r[$i]['name']=$p;
	// 	}
	// 	return $r;
	// }
public function get_dnda_details($nda,$dnda){
	$query="select * from no_dues_nda_dnda where nda_emp_id='$nda' and dnda_emp_id='$dnda'";
		$res=$this->db->query($query)->result_array();
        $r = array();
		for ($i = 0; $i < count($res); $i++){
			$p = $this->get_emp_name($res[$i]['dnda_emp_id']);
			$r[$i] = array();
			$r[$i]['dnda_emp_id'] = $res[$i]['dnda_emp_id'];
			$r[$i]['status'] = $res[$i]['status'];
			$r[$i]['dept_id'] = $res[$i]['dept_id'];
			$r[$i]['name']=$p;
		}
		return $r;
}

public function modify_deligated_details($nda_emp_id,$dnda_emp_id,$status){

if($status==2){

	$query="delete from user_auth_types where id='$dnda_emp_id' and auth_id='dnda'";
	$this->db->query($query);
	$query="delete from no_dues_nda_dnda where nda_emp_id='$nda_emp_id' and dnda_emp_id='$dnda_emp_id' ";
	$this->db->query($query);
	//delele
}
else if($status==0){
	$query="delete from user_auth_types where id='$dnda_emp_id' and auth_id='dnda'";
	$this->db->query($query);
	$query="update no_dues_nda_dnda set status='$status'where nda_emp_id='$nda_emp_id' and dnda_emp_id='$dnda_emp_id' ";
	$this->db->query($query);

}
else {
	$query="update no_dues_nda_dnda set status='$status'where nda_emp_id='$nda_emp_id' and dnda_emp_id='$dnda_emp_id' ";
	$this->db->query($query);

}

}
    public function user_auth_type_insert($empid){
    	//user_auth_types      id auth_id  ... $empid dnda
    	$nda='nda';
    	
    	$query="select * from user_auth_types where id='$empid'and auth_id='$nda'";
    	$res=$this->db->query($query)->result_array();
    	if(!$res){
    		$query="insert into user_auth_types (id,auth_id) values('$empid','$nda')";
    		$this->db->query($query);

    	}
     }
    public function valid_emp_id($empid){
    	$q="Select * from empadsuser where `Emp no` = '$empid'";
    	$res=$this->db->query($q)->result_array();
    	if(!$res) return false;
    	else return true;
    }

    public function get_emp_details($empid){
    	$q="Select * from empadsuser where `Emp no` = '$empid'";
    	$res=$this->db->query($q)->result_array();
    	return $res[0];

    }

   public function get_emp_name($empid){
    	$q="Select `Full Name` from empadsuser where `Emp no` = '$empid'";
    	$res=$this->db->query($q)->result_array();
    	return $res[0]['Full Name'];

    }    public function insert_dnda($nda,$dnda){
    	$query="select * from no_dues_nda_dnda where nda_emp_id='$nda' and dnda_emp_id='$dnda'";
    	 $dept_id=$this->session->userdata['dept_id'];
    	$res=$this->db->query($query)->result_array();

    	if(!$res){

    		$query="insert into no_dues_nda_dnda (nda_emp_id,dnda_emp_id,status,dept_id) values('$nda','$dnda','1','$dept_id')";
    		$this->db->query($query);
    		return TRUE;

    	}
    	else{
    		$data="You have deligated power to   ";
    		return $data;
    	}

    }

	public function get_dept_details($dept){
		//get the valid field of the dept
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "SELECT * FROM no_dues_dept WHERE dept_id = '$dept' and session_year='$sess' ";
		$qi = "SELECT name FROM departments WHERE id = '$dept'";
		$res = $this->db->query($q)->result_array();
		$resn = $this->db->query($qi)->result_array();
		if (count($resn) == 0){
			$res[0]['dept_name'] = $dept;
		}
		else{
			$res[0]['dept_name'] = $resn[0]['name'];
		}
		return $res;
	}

	public function get_dept_details_2($dept){
		//get the valid field of the dept
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "SELECT * FROM no_dues_dept_for_dropouts WHERE dept_id = '$dept' and session_year='$sess' ";
		$qi = "SELECT name FROM departments WHERE id = '$dept'";
		$res = $this->db->query($q)->result_array();
		$resn = $this->db->query($qi)->result_array();
		if (count($resn) == 0){
			$res[0]['dept_name'] = $dept;
		}
		else{
			$res[0]['dept_name'] = $resn[0]['name'];
		}
		return $res;
	}


	public function modify_dept_details($part, $dept){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "UPDATE no_dues_dept SET part = '$part' WHERE dept_id = '$dept' and session_year='$sess' ";
		$this->db->query($q);
	}

	public function modify_dept_details_2($part, $dept){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "UPDATE no_dues_dept_for_dropouts SET part = '$part' WHERE dept_id = '$dept' and session_year='$sess' ";
		$this->db->query($q);
	}

	public function remove_dept_details($dept){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "DELETE FROM no_dues_dept WHERE dept_id = '$dept' and session_year='$sess' ";
		$this->db->query($q);
	}
	public function remove_dept_details_2($dept){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$q = "DELETE FROM no_dues_dept_for_dropouts WHERE dept_id = '$dept' and session_year='$sess' ";
		$this->db->query($q);
	}

	public function get_all_no_dues_depts(){
		//Returns all the department that are already not in the no dues dept table
		$q = "SELECT id, name FROM departments WHERE id NOT IN (SELECT dept_id FROM no_dues_dept where session_year='$sess')";
		$res = $this->db->query($q)->result_array();
		return $res;
	}

	///for dropouts
	public function get_all_no_dues_depts_2(){
		//Returns all the department that are already not in the no dues dept table
		$q = "SELECT id, name FROM departments WHERE id NOT IN (SELECT dept_id FROM no_dues_dept_for_dropouts where session_year='$sess')";
		$res = $this->db->query($q)->result_array();
		return $res;
	}


	public function get_all_depts_nda(){
		//Returns all the department that are already not in the no dues dept table
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
		$query="Select dept_id from no_dues_dept where session_year='$sess' ";
		$res=$this->db->query($query)->result_array();
		$data=array();
		for($i=0;$i<count($res);$i++){
			$name=$this->get_dept_name($res[$i]['dept_id']);
			$data[$i]=array();
			$data[$i]['name']=$name;
			$data[$i]['id'] =$res[$i]['dept_id'];
		}

		// $q = "SELECT id, name FROM departments WHERE 1";
		// $res = $this->db->query($q)->result_array();
		return $data;
	}

	public function no_dues_add_dept($dept_id, $part){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
	    $res=$this->db->query("select * from no_dues_dept where dept_id='$dept_id'  and session_year='$sess' ")->result_array();
	  	if(!$res){
	  		$ts = $this->curr_time_stamp();
        	$sess = $this->get_session($ts);
        	$id=$this->session->userdata['id'];
			$q = "INSERT INTO no_dues_dept (dept_id, part,session_year,added_by,timestamp) VALUES ('$dept_id', '$part','$sess','$id',CURRENT_TIMESTAMP)";
			$this->db->query($q);
		}
	}

	public function no_dues_add_dept_2($dept_id, $part){
		$ts = $this->curr_time_stamp();
        $sess = $this->get_session($ts);
	    $res=$this->db->query("select * from no_dues_dept_for_dropouts where dept_id='$dept_id'  and session_year='$sess' ")->result_array();
	  	if(!$res){
	  		$ts = $this->curr_time_stamp();
        	$sess = $this->get_session($ts);
        	$id=$this->session->userdata['id'];
			$q = "INSERT INTO no_dues_dept_for_dropouts (dept_id, part,session_year,added_by,timestamp) VALUES ('$dept_id', '$part','$sess','$id',CURRENT_TIMESTAMP)";
			$this->db->query($q);
		}
	}
	public function no_dues_add_nda($dept_id, $emp_id){
		//$dept_name = str_replace(" ", "\ ", $dept_name);
        
        $ts=$this->curr_time_stamp();
        $query="select * from no_dues_nda where emp_id='$emp_id'and  status='0' ";
        $res=$this->db->query($query)->result_array();
        if(!$res){
			$q = "INSERT INTO no_dues_nda (dept_id, emp_id,start_time) VALUES ('$dept_id', '$emp_id','$ts')";
			$this->db->query($q);
			$this->user_auth_type_insert($emp_id);
		}
	}
// 	public function no_dues_add_fines($data){

// 		$ts = $this->curr_time_stamp();
// 		$admn_no=$data['admn_no'];
// 		$data['timestamp']=$ts;
// 		$session_year=$this->get_session($ts);
// 		$dept_id=$data['dept_id'];
// 		$due=$data['due'];
// 		$due_list=$data['due_list'];
// 		$remarks=$data['remarks'];
// 		$pay_status=$data['pay_status'];
// 		$emp_id=$data['emp_id'];
// 		//print_r($data);
// // $q = "INSERT INTO no_dues_list (admn_no, session_year, course_id, branch_id, dept_name, timestamp, due_amt, remarks) VALUES ('$admn_no', '$sess', '$course_id', '$branch_id', '$dept_name', '$ts', '$due_amt', '$remarks')";
// // 			$this->db->query($q);

// 		$query="INSERT INTO no_dues_lists (admn_no,session_year,dept_id,emp_id,`timestamp`,due_amt,due_list,remarks) VALUES
// 		 ('$admn_no','$session_year','$dept_id','$emp_id','$ts','$due','$due_list','$remarks') ";

// 		$this->db->query($query);
// 		$q="UPDATE no_dues_stu_ref_id set status=0 where admn_no='$admn_no' and status=1";
// 		$this->db->query($q);

// 	//echo "sadf";
// 	}

	public function no_dues_add_fines($data){

		$ts = $this->curr_time_stamp();
		$admn_no=$data['admn_no'];
		$data['timestamp']=$ts;
		$session_year=$this->get_session($ts);
		$dept_id=$data['dept_id'];
		$due=$data['due'];
		$due_list=$data['due_list'];
		$remarks=$data['remarks'];
		$pay_status=$data['pay_status'];
		$emp_id=$data['emp_id'];


		$sem_course=$this->get_semester_course_id($admn_no);
		$course_id=$sem_course[0]['course_id'];
		$sem=$sem_course[0]['semester'];
		   $year=$this->get_duration_of_course($course_id);
            $year=$year+$year;
			$temp=$this->check_for_dropouts($admn_no);
		//print_r($data);
// $q = "INSERT INTO no_dues_list (admn_no, session_year, course_id, branch_id, dept_name, timestamp, due_amt, remarks) VALUES ('$admn_no', '$sess', '$course_id', '$branch_id', '$dept_name', '$ts', '$due_amt', '$remarks')";
// 			$this->db->query($q);

		$flag=$this->finecheck($admn_no,$due,$due_list,$remarks);// added to check whether same fine imposed 
		if($flag){

			$query="INSERT INTO no_dues_lists (admn_no,session_year,dept_id,emp_id,`timestamp`,due_amt,due_list,remarks) VALUES
			 ('$admn_no','$session_year','$dept_id','$emp_id','$ts','$due','$due_list','$remarks') ";

			$this->db->query($query);

			if($year==$sem || $temp!=0){
			$q="UPDATE no_dues_stu_ref_id set status=0 where admn_no='$admn_no' and status=1";
			$this->db->query($q);
		}

		else {

			$q="UPDATE no_dues_stu_ref_id_sem set status=0 where admn_no='$admn_no' and sem='$sem' and status=1";
			$this->db->query($q);

		}
		
		}
		return $flag;
	//echo "sadf";
	}

	public function finecheck($admn_no,$due_amt,$due_list,$remarks)

	{
		$query="Select * from no_dues_lists where admn_no='$admn_no' and due_amt='$due_amt' and due_list='$due_list'
		and remarks='$remarks'  ";

		$res=$this->db->query($query)->result_array();
		if($res)return false;
		else return true;


	}


public function get_semester_course_id($id){

$query="select semester,course_id from stu_academic where admn_no='$id' ";

$res=$this->db->query($query)->result_array();
		

		//print_r($res);
		
		return $res;



}


	





public function edit_no_dues_lists($id,$dues_amt,$due_list,$remarks,$reason){

	$res=$this->db->query("Select admn_no from no_dues_lists where id='$id'")->result_array();
	#echo $this->db->last_query(); echo '<br>';
	#$admn_no=$res[0]['admn_no'];
	$admn_no=$res[0]['admn_no'];
	$ts = $this->curr_time_stamp();
	$session_year=$this->get_session($ts);
	$dept_id=$this->session->userdata['dept_id'];
	$emp_id=$this->session->userdata['id'];
   	$c=$this->db->query("select change_status from no_dues_lists where id='$id'")->result_array();
   	#echo $this->db->last_query(); echo '<br>';
	echo $status=$c[0]['change_status'];
	if(isset($status))
	 {

 	$reason="Modified by ".$this->get_emp_name($emp_id)."(".$emp_id.") because ".$reason;
	$q="update no_dues_lists set change_status=1, reason ='$reason', due_amt='$dues_amt', due_list='$due_list', remarks='$remarks' where id='$id'";
	$this->db->query($q);
	#echo $this->db->last_query(); echo '<br>';
	}else{
			$query="INSERT INTO no_dues_lists (admn_no,session_year,dept_id,emp_id,`timestamp`,due_amt,due_list,remarks) VALUES
			 ('$admn_no','$session_year','$dept_id','$emp_id','$ts','$dues_amt','$due_list','$remarks') ";


			$this->db->query($query);

			#echo $this->db->last_query(); echo '<br>';
		}
	}

public function get_student_by_course_branch_for_fines($course,$branch,$sem)
	{
		$course_duration = $this->db->get_where('cs_courses',array('id'=>$course))->result();
		$this->db->where("course_id",$course);
		$this->db->where("branch_id",$branch);
		$this->db->where("stu_academic.semester > $sem");
		$this->db->join("user_details","user_details.id = stu_academic.admn_no");
		$this->db->order_by("UPPER(first_name),UPPER(last_name)","asc");
		$query = $this->db->get('stu_academic');
		return $query->result();
}
	 function get_session_year()
    {
        $query=$this->db->query("SELECT DISTINCT session_year
		                        FROM no_dues_lists  
								");
        	$result=$query->result_array();

        	
        	// print_r($result);
        	return $result;
    }
	public function no_dues_add_dept_csv($data){// adding departments by uploading  csv file 
		 
		  $a=$data['dept_id'];
		  $b=$data['part'];
		  $ts = $this->curr_time_stamp();
          $sess = $this->get_session($ts);
		  $id=$this->session->userdata('id');
		  $temp="Select dept_id from no_dues_dept WHERE dept_id like '$a' and session_year='$sess'";
		  $flag=$this->db->query($temp)->result_array();
		  if(!$flag){ // to insert unique dept_id
		 $q = "INSERT INTO no_dues_dept (dept_id, part,session_year,added_by,timestamp) VALUES ('$a', '$b','$sess','$id',CURRENT_TIMESTAMP)";
		$this->db->query($q);
      	}
      }
////for dropouts
      public function no_dues_add_dept_csv_2($data){// adding departments by uploading  csv file 
		 
		  $a=$data['dept_id'];
		  $b=$data['part'];
		  $ts = $this->curr_time_stamp();
          $sess = $this->get_session($ts);
		  $id=$this->session->userdata('id');
		  $temp="Select dept_id from no_dues_dept_for_dropouts WHERE dept_id like '$a' and session_year='$sess'";
		  $flag=$this->db->query($temp)->result_array();
		  if(!$flag){ // to insert unique dept_id
		 $q = "INSERT INTO no_dues_dept_for_dropouts (dept_id, part,session_year,added_by,timestamp) VALUES ('$a', '$b','$sess','$id',CURRENT_TIMESTAMP)";
		$this->db->query($q);
      	}
      }
  public function valid_dept_id($dept_id){


  	$res=$this->db->query("select * from departments where id='$dept_id'")->result_array();
  	if($res)return true;
  	else return false;

  }

      public function no_dues_add_nda_csv($data){// adding nda
		  $emp_id=$data['emp_id'];
		  $ts=$this->curr_time_stamp();
		  $query="select * from no_dues_nda where emp_id ='$emp_id' and status ='0'";
		  $res=$this->db->query($query)->result_array();
		  if(!$res){
				  	$dept_id=$this->get_emp_dept($emp_id);
				  	// echo $emp_id;
				  	// echo $dept_id;
				  	if($dept_id){
				  		$this->user_auth_type_insert($emp_id);
				  		$q="insert into no_dues_nda (emp_id,dept_id,start_time) values('$emp_id','$dept_id','$ts')";
				  		$this->db->query($q);
				  	}
		  }
  }



		public function remove_nda($id){
  
			$emp=$this->db->query("select emp_id from no_dues_nda where id ='$id'")->result_array();
			$emp_id=$emp['0']['emp_id'];
       	
			$ts=$this->curr_time_stamp();
			$q="update no_dues_nda set end_time='$ts' , status='1' where id='$id'";
		     $this->db->query($q);
	        $query="delete from user_auth_types where id='$emp_id' and auth_id='nda'";
	        $this->db->query($query);


		}
		public function no_dues_add_dropout($id){
			$ts = $this->curr_time_stamp();
			$sess = $this->get_session($ts);
			$dept_id=$this->get_dept_id($id);
			$emp_id=$this->session->userdata('id');
			$q = "INSERT INTO no_dues_dropouts (admn_no,dept_id,session_year,added_by,timestamp) ".
				"VALUES ('$id','$dept_id','$sess','$emp_id','$ts')";
			 $this->db->query($q);
		}

		public function remove_dropout($id){
  			$ts = $this->curr_time_stamp();
			$sess = $this->get_session($ts);
			$q="DELETE FROM no_dues_dropouts where admn_no='$id' and session_year='$sess' ";
			$this->db->query($q);
		}
	
        function get_emp_dept($id){
            $sql = "select  dept_id from user_details where id=?";
            $query = $this->db->query($sql,array($id));
            if ($this->db->affected_rows() > 0) {
                return $query->row()->dept_id;
            } else {
            return false;
            }
        }
      	
      function get_dept_id($id){
            $q = "select  branch_id from stu_academic where admn_no='$id' ";
       		$res = $this->db->query($q)->result_array(); 
       		if(!$res) return 10;
       		return $res[0]['branch_id'];
        }
      	

	public function get_rejected_no_dues(){
		$dept = $this->session->userdata['dept_id'];
		$q = "SELECT id ,admn_no, session_year, timestamp, pay_status, due_amt,due_list, remarks, receipt_path ".
			"FROM no_dues_lists ".
			"WHERE dept_id = '$dept' AND pay_status != 1 and change_status='0' ";

		$res = $this->db->query($q)->result_array();   
		for ($i = 0; $i < count($res); $i++){
			$admn_no = $res[$i]['admn_no'];

			$qi = "SELECT course_id, semester FROM stu_academic WHERE admn_no = '$admn_no'";

			$qj = "SELECT first_name, middle_name, last_name FROM user_details WHERE id = '$admn_no'";
			$a = $this->db->query($qi)->result_array();
			$b = $this->db->query($qj)->result_array();

			$res[$i]['course_name'] = $this->get_course_name($a[0]['course_id']);
			$res[$i]['semester'] = $a[0]['semester'];
			$res[$i]['first_name'] = $b[0]['first_name'];
			$res[$i]['middle_name'] = $b[0]['middle_name'];
			$res[$i]['last_name'] = $b[0]['last_name'];
		}
		return $res;
	}

	public function get_rejected_no_dues_2(){
		$dept = $this->session->userdata['dept_id'];
		// $q = "SELECT no_dues_lists.id,no_dues_lists.admn_no, no_dues_lists.session_year, no_dues_lists.timestamp, pay_status, due_amt,due_list, remarks, receipt_path FROM no_dues_lists INNER JOIN no_dues_dropouts ON no_dues_lists.admn_no=no_dues_dropouts.admn_no WHERE no_dues_lists.dept_id = '$dept' AND pay_status != 1 and change_status='0' ";
		$q="SELECT no_dues_lists.id ,no_dues_lists.admn_no, no_dues_lists.session_year, no_dues_lists.timestamp, pay_status, due_amt,due_list, remarks, receipt_path
		FROM no_dues_lists 
			INNER JOIN no_dues_dropouts
			ON no_dues_lists.admn_no=no_dues_dropouts.admn_no
			WHERE no_dues_lists.dept_id = '$dept' AND pay_status != 1 and change_status='0'";

		$res = $this->db->query($q)->result_array();   
		for ($i = 0; $i < count($res); $i++){
			$admn_no = $res[$i]['admn_no'];

			$qi = "SELECT course_id, semester FROM stu_academic WHERE admn_no = '$admn_no'";
			$qj = "SELECT first_name, middle_name, last_name FROM user_details WHERE id = '$admn_no'";
			$a = $this->db->query($qi)->result_array();
			$b = $this->db->query($qj)->result_array();

			$res[$i]['course_name'] = $this->get_course_name($a[0]['course_id']);
			$res[$i]['semester'] = $a[0]['semester'];
			$res[$i]['first_name'] = $b[0]['first_name'];
			$res[$i]['middle_name'] = $b[0]['middle_name'];
			$res[$i]['last_name'] = $b[0]['last_name'];
		}
		return $res;
	}

	public function no_dues_view($course_id, $dept_id, $sem){
		$dept = $this->session->userdata['dept_id'];
		$dept_type = $this->session->userdata['dept_type'];
		/*
		field name changed in stu_academic id->admn_no
		==========================================================================================================
		*/
		$q = "SELECT dl.admn_no, dl.session_year, dl.timestamp, dl.due_amt,dl.due_list, dl.remarks, ".
			"ud.first_name, ud.middle_name, ud.last_name, ud.dept_id ".
			"FROM no_dues_lists AS dl ".
			"INNER JOIN user_details AS ud ON dl.admn_no = ud.id ".
			"INNER JOIN stu_academic AS st ON dl.admn_no = st.admn_no ".
			"WHERE st.semester = $sem ";
		/*==========================================================================================================*/
		if ($dept_type == 'academic'){
			$q = $q."AND dl.dept_id = '$dept_id'";
		}
		$res = $this->db->query($q)->result_array();
		$res1 = array();
		$res1['dept_name'] = $this->get_dept_name($dept_id);
		$res1['course_name'] = $this->get_course_name($course_id);
		$res1['sem'] = $sem;
		return array($res, $res1);
	}

	public function send_dues_notification($admn_no){
		$dept_id = $this->session->userdata['dept_id'];
		//$q = "SELECT distinct admn_no FROM no_dues_lists WHERE dept_id = '$dept_id'";
		//$res = $this->db->query($q)->result_array();
		$title = "Dues posted in ".$this->session->userdata['dept_name'];
		$description = "You have Dues in ".$this->session->userdata['dept_name'];
		$link = 'no_dues/no_dues_student_init/view_dues';
		// for ($i = 0; $i < count ($res); $i++){
			$this->notification->notify($admn_no,"stu",$title,$description,$link,"");
		// }
	}

	public function send_dues_notification_2($admn_no){
		$dept_id = $this->session->userdata['dept_id'];
		//$q = "SELECT distinct admn_no FROM no_dues_lists WHERE dept_id = '$dept_id'";
		//$res = $this->db->query($q)->result_array();
		$title = "Dues posted in ".$this->session->userdata['dept_name'];
		$description = "You have Dues in ".$this->session->userdata['dept_name'];
		$link = 'no_dues/no_dues_student_init/view_dues_2';
		// for ($i = 0; $i < count ($res); $i++){
			$this->notification->notify($admn_no,"stu",$title,$description,$link,"");
		// }
	}



	// new functions 

	public function student_details_table_admn_no($admn_no){


				$q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id, semester, course_id ".
			"FROM user_details ".
			"INNER JOIN stu_academic ".
			"ON user_details.id = stu_academic.admn_no ".
			
			"WHERE  user_details.id='$admn_no'";
		/*
		==========================================================================================================
		*/
		$res = $this->db->query($q)->result_array();

		if(!$res) return false;
		return $res;
	}
	public function student_details_table_admn_no_2($admn_no){


				$q = "SELECT user_details.id, first_name, middle_name, last_name, user_details.dept_id,course_id,semester
		FROM user_details
		INNER JOIN no_dues_dropouts 
		ON user_details.id = no_dues_dropouts.admn_no 
        INNER JOIN stu_academic
        ON user_details.id=stu_academic.admn_no
		WHERE  user_details.id='$admn_no' ";
		/*
		==========================================================================================================
		*/
		$res = $this->db->query($q)->result_array();

		if(!$res) return false;
		return $res;
	}
	public function check_for_dropouts($id){
		$ts = $this->curr_time_stamp();
		$sess = $this->get_session($ts);
		$q="SELECT * from no_dues_dropouts where admn_no='$id' and session_year='$sess' ";
		$res=$this->db->query($q)->result_array();
		if(!$res) return 0;
		else return 1;
	}

	

	public function check_valid_student($id){
		$q="SELECT * from stuadsuser where Adm_no='$id' ";
		$res=$this->db->query($q)->result_array();
		if(!$res) return 0;
		else return 1;
	}

}