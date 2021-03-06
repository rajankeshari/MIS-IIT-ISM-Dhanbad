<?php

class Course_coordinator_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

	  function getpendingSubjectList(){
      $sql="select a.sub_code from exam_sub_code a where a.`status`='0' group by a.sub_code";
      $query = $this->db->query($sql);
      if ($query->num_rows() > 0)
      return $query->result();
        else
      return false;
    }


	// marks Verification by course coordinator start by @bhijeet
    function CCVerificationInfo($session,$session_year){
      $sql="select x.*,concat_ws(' ',d.salutation,d.first_name,d.middle_name,d.last_name) as ft_name from
      (SELECT a.`session`,a.session_year,a.sub_code,c.sub_name,a.dept,a.course,a.branch,a.coordinator_emp_id,b.id,b.verified_by,b.updated_at
      FROM cbcs_marks_send_to_coordinator a
      inner join cbcs_subject_offered c on a.sub_code=c.sub_code
      LEFT JOIN cbcs_marks_verified_by_cc b ON a.sub_code=b.sub_code AND a.`session`=b.`session` AND a.session_year=b.session_year AND a.coordinator_emp_id=b.verified_by
      WHERE a.`session`= ? AND a.session_year=? AND a.`status`='1' AND a.dean_ac_status='1'
      GROUP BY a.sub_code
      union
      SELECT a.`session`,a.session_year,a.sub_code,c.sub_name,a.dept,a.course,a.branch,a.coordinator_emp_id,b.id,b.verified_by,b.updated_at
      FROM cbcs_marks_send_to_coordinator a
      inner join old_subject_offered c on a.sub_code=c.sub_code
      LEFT JOIN cbcs_marks_verified_by_cc b ON a.sub_code=b.sub_code AND a.`session`=b.`session` AND a.session_year=b.session_year AND a.coordinator_emp_id=b.verified_by
      WHERE a.`session`= ? AND a.session_year=? AND a.`status`='1' AND a.dean_ac_status='1'
      GROUP BY a.sub_code) x
      left join user_details d on x.coordinator_emp_id=d.id";
      //echo $sql;exit;
            $query = $this->db->query($sql,array($session,$session_year,$session,$session_year));
        //echo $this->db->last_query(); die();
            if ($query->num_rows() > 0)
            return $query->result();
              else
            return false;
    }
    function saveVerificationInfo($savedata){
      $this->db->select('*');
      $this->db->from('cbcs_marks_verified_by_cc');
      $this->db->where($savedata);
    //  $cnt = $this->db->get()->num_rows();
      if($this->db->get()->num_rows() == 0){
        $this->db->insert('cbcs_marks_verified_by_cc', $savedata);
        return true;
      }else{
        return false;
      }
    }

    function verificationStatus($session,$session_year,$sub_code){
      $sql="select * from cbcs_marks_verified_by_cc where session= ? and session_year = ? and sub_code= ?";
      $query = $this->db->query($sql,array($session,$session_year,$sub_code));
        //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
        return $query->num_rows();
          else
        return 0;
    }
    function CCverificationDate($session,$session_year){
      $sql="select * from cbcs_marks_cc_verification_window where session= ? and session_year = ? order by id desc limit 1";
      $query = $this->db->query($sql,array($session,$session_year));
      //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
        return $query->result();
          else
        return false;
    }

    function saveVerificationWindow($data){
      $this->db->insert('cbcs_marks_cc_verification_window', $data);
      //echo  $this->db->last_query();die();
      if($this->db->affected_rows() > 0){
        return true;
      }else{
          return false;
      }
    }
    function getMarksVerificationWindow(){
      $sql="select * from cbcs_marks_cc_verification_window order by id desc";
      $query = $this->db->query($sql);
      //  echo $this->db->last_query();
        if ($query->num_rows() > 0)
        return $query->result();
          else
        return false;
    }
  // marks Verification by course coordinator end


	   // course coordinator details PICEXAM start

    function  getCoordinatorDetails($session,$session_year){
      $sql="SELECT x.sub_code,x.sub_name,x.sub_type,x.dept_id,x.course_id,x.branch_id,x.marks_upload_rights,
            (CASE WHEN x.co_emp_id IS NULL THEN 'Not Assigned' ELSE CONCAT_WS(' - ',x.offered_to_name,x.co_emp_id) END) AS sub_course_coordinator
            FROM(
            SELECT a.id,a.sub_code,a.sub_name,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_type,
            (select group_concat(DISTINCT concat_ws('-',concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name),z.emp_no)) from cbcs_subject_offered_desc z
            inner join user_details ud on z.emp_no=ud.id
            where z.sub_id=a.sub_code and z.coordinator='1' group by z.sub_id) as marks_upload_rights
            ,c.offered_to,c.offered_to_name,c.co_emp_id
            FROM cbcs_subject_offered a
            INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
            LEFT JOIN cbcs_assign_course_coordinator c ON a.sub_code=c.sub_code and b.emp_no=c.co_emp_id and a.`session`=c.session and a.session_year=c.session_year
            WHERE a.session='$session' AND a.session_year='$session_year'
            GROUP BY (case when a.sub_type='Modular' then a.id else 1=1 end),a.sub_code
            UNION
            SELECT a.id,a.sub_code,a.sub_name,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_type,
            (select group_concat(DISTINCT concat_ws('-',concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name),z.emp_no)) from old_subject_offered_desc z
            inner join user_details ud on z.emp_no=ud.id
            where z.sub_id=a.sub_code and z.coordinator='1' group by z.sub_id) as marks_upload_rights
            ,c.offered_to,c.offered_to_name,c.co_emp_id
            FROM old_subject_offered a
            INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
            LEFT JOIN cbcs_assign_course_coordinator c ON a.sub_code=c.sub_code and b.emp_no=c.co_emp_id and a.`session`=c.session and a.session_year=c.session_year
            WHERE a.session='$session' AND a.session_year='$session_year'
            GROUP BY (case when a.sub_type='Modular' then a.id else 1=1 end),a.sub_code) x
            order by x.sub_code asc";
            $query = $this->db->query($sql);
              //echo $this->db->last_query(); die();
              if ($query->num_rows() > 0)
              return $query->result();
              else
              return false;
      }

        // course coordinator details PICEXAM end

	function marks_correction_history_sub_wise($sub_code,$course_id,$branch_id,$session_year,$session){
      $sql="SELECT a.correction_log_id,a.admn_no,a.form_id,a.session_year,a.`session`,a.sub_code,a.sub_offered_id,a.course_id,a.branch_id,
            a.marks_upload_id,a.marks_upload_dis_id,a.dist_name,a.dist_id,a.old_marks,a.corrected_marks,a.old_total,a.new_total,a.new_grade,a.old_grade,a.corrected_by,a.updated_at,b.id AS logid,b.submit_to_exam_status AS st
            FROM cbcs_marks_correction_backup a
            INNER JOIN cbcs_marks_correction_log b ON a.correction_log_id=b.id AND a.admn_no=b.admn_no AND a.session_year=b.session_year AND a.`session`=b.`session` AND a.sub_code=b.sub_code
            where a.sub_code='$sub_code' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.session_year='$session_year' and a.`session`='$session'
            ORDER BY b.submit_to_exam_status,b.update_status,b.id DESC";
            $query = $this->db->query($sql);
              //echo $this->db->last_query(); die();
              if ($query->num_rows() > 0)
              return $query->result();
              else
              return false;
    }


    function saveOrUpdateCourseCordinator($data){
      $selectValue=array(
        "session"=>$data['session'],
        "session_year"=>$data['session_year'],
        "sub_code"=>$data['sub_code'],
      );

    $this->db->select('*');
    $this->db->from('cbcs_assign_course_coordinator');
    $this->db->where($selectValue);
    $cnt = $this->db->get()->num_rows();
      //echo  $this->db->last_query();die();
    if($cnt==0){
      $this->db->insert('cbcs_assign_course_coordinator', $data);
      //echo  $this->db->last_query();die();
      if($this->db->affected_rows() != 1){

    										echo"cbcs_assign_course_coordinator :".	$this->db->_error_message();

    									}else{
    										echo "Course Coordinator Assigned Successfully";
    									}
    }else{
      $this->db->where($selectValue);
      $this->db->update('cbcs_assign_course_coordinator', $data);
    //echo  $this->db->last_query();die();
      if($this->db->affected_rows() != 1){

                        echo"cbcs_assign_course_coordinator :".	$this->db->_error_message();

                      }else{
                        echo "Course Coordinator Updated Successfully";
                      }
    }
    }
    function get_teaching_faultyforprint($sub_code,$session,$session_year){
      $sql = "(select a.sub_code,a.sub_name,b.emp_no,GROUP_CONCAT(concat_ws(',',concat_ws('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as offered_to ,
    GROUP_CONCAT(concat_ws(' - ',concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no ))) as name,
    IF(acc.`status`,(concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned')  as course_coordinator
    from cbcs_subject_offered a
    inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id
    Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code  and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$sub_code' group by a.sub_code)
     union
    (select a.sub_code,a.sub_name,b.emp_no,GROUP_CONCAT(concat_ws(',',concat_ws('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as offered_to ,
    GROUP_CONCAT(concat_ws(' - ',concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) as name,
    IF(acc.`status`,(concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned')  as course_coordinator
    from old_subject_offered a
    inner join old_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id
    Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code  and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$sub_code' group by a.sub_code)";
        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
        //  echo  $this->db->last_query();die();
            return $query->result();
        } else {
            return false;
        }
    }
    function getCoName($session_year,$session,$sub_code){
      $sql = "select concat_ws(' ',b.first_name,b.middle_name,b.last_name) as emp_name from(select a.co_emp_id as emp_id  from cbcs_assign_course_coordinator a
where a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year')x
inner join user_details b on x.emp_id=b.id";
        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
        //  echo  $this->db->last_query();
            return $query->result();
        } else {
            return false;
        }
    }
    function get_teaching_faulty_single($subject,$session,$session_year){
      $sql = "(select a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
    ,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from cbcs_subject_offered a
    inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id
    #Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$subject' group by b.emp_no)
    union
    (select a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
    ,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from old_subject_offered a
    inner join old_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id

    #Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code  and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$subject' group by b.emp_no)";
        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
        //  echo  $this->db->last_query();
            return $query->result();
        } else {
            return false;
        }
    }
      function get_teaching_faulty($subject,$session,$session_year){
        $sql = "(select a.session_year,a.`session`,a.dept_id,d.name as dept_name,bb.name as branch_name,cc.name as course_name,a.semester,a.sub_code,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from cbcs_subject_offered a
    inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id
    inner join cbcs_departments d on a.dept_id=d.id
    inner join cbcs_branches bb on a.branch_id=bb.id
    inner join cbcs_courses cc on a.course_id=cc.id
    Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$subject')
    union all
    (select a.session_year,a.`session`,a.dept_id,d.name as dept_name,bb.name as branch_name,cc.name as course_name,a.semester,a.sub_code,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from old_subject_offered a
    inner join old_subject_offered_desc b on a.id=b.sub_offered_id
    inner join user_details ud on b.emp_no=ud.id
    inner join cbcs_departments d on a.dept_id=d.id
    inner join cbcs_branches bb on a.branch_id=bb.id
    inner join cbcs_courses cc on a.course_id=cc.id
    Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to
    where a.session_year='$session_year' and a.`session`='$session'
    and a.sub_code='$subject')";
          $query = $this->db->query($sql);
          if ($this->db->affected_rows() > 0) {
          //  echo  $this->db->last_query();
              return $query->result();
          } else {
              return false;
          }
      }
      function reOpenMarksSubmission($co_assign_id,$marks_child_id,$sub_code,$session,$session_year){
        $sql="update cbcs_marks_dist_child set marks_upload_status='0' where id='$marks_child_id'";
        $query = $this->db->query($sql);
      //   echo  $this->db->last_query(); die();
      $sqlCoAssign="update cbcs_marks_send_to_coordinator set status='2' where marks_master_id='$co_assign_id'";
      $queryCoAssign = $this->db->query($sqlCoAssign);
        if ($this->db->affected_rows() > 0) {
          $sqlupdate="update cbcs_marks_send_to_coordinator set dean_ac_status='2' where sub_code='$sub_code' and session='$session' and session_year='$session_year' and status='2'";
          $sqlupdates = $this->db->query($sqlupdate);
          $msg="Marks submission Re-open Successfully.";
            return $msg;
        } else {
          $msg="Marks submission already Open.";
            return $msg;
        }
      }
      function reOpenMarksbifercationSubmission($co_assign_id,$marks_child_id,$sub_code,$session,$session_year,$open_req_id,$sub_offer_id){

	  $sql="update cbcs_marks_dist_child set marks_upload_status='0' where pk='$marks_child_id'";
        $query = $this->db->query($sql);
      //   echo  $this->db->last_query(); die();
      $sqlCoAssign="update cbcs_marks_send_to_coordinator set status='2' where sub_code='$sub_code' and sub_offered_id='$sub_offer_id' and coordinator_emp_id='$co_assign_id' and status='1'";
      $queryCoAssign = $this->db->query($sqlCoAssign);
    //echo  $this->db->last_query(); die();

      $sqlupdate="update cbcs_marks_send_to_coordinator set dean_ac_status='2' where sub_code='$sub_code' and sub_offered_id='$sub_offer_id' and session='$session' and session_year='$session_year'";
      $sqlupdates = $this->db->query($sqlupdate);

      $sqlreqUpdate="update cbcs_marks_submission_reopen_req set open_status='1' where id='$open_req_id' and session='$session' and session_year='$session_year'";
      $sqlreqUpdates = $this->db->query($sqlreqUpdate);


      $msg="Marks submission Re-open Successfully.";
      return $msg;

      }
      function get_marks_bifercation_for_CO($marks_dist_id){
        $sql="select * from cbcs_marks_dist_child where id='$marks_dist_id'";
        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
      //   echo  $this->db->last_query();
            return $query->result();
        } else {
            return false;
        }
      }
	  //======================================================Anuj As per Abhijit======================
	   function getallStudentMarks($sub_code,$session,$session_year,$exam_type,$offered_id,$sub_type){
        //echo $sub_code;exit;
        if($sub_type=='Modular'){
        	    $extrajoin="inner join cbcs_modular_paper_details h on x.subject_code in (h.after_mid) and x.admn_no=h.admn_no";

// LEFT (5th) cbcs_marks_master is changed to inner to avoid first year data for WS 20-21 baklog entry - 4 place (two in IF and other two in ELSE) (line no: 339, 350, 369, 381) being at 331
                $sql="select x.* from ((select a.id as ids,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
        concat('c',a.sub_offered_id) as sub_offered_id,a.subject_code,b.semester,g.id,g.marks_master_id,g.total,g.grade from cbcs_stu_course a
        inner join cbcs_subject_offered b on a.sub_offered_id=b.id
        inner join user_details c on a.admn_no=c.id
        inner join cbcs_courses d on a.course=d.id
        inner join cbcs_branches e on a.branch=e.id

        inner join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
        LEFT join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
        where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' AND f.id=g.marks_master_id)
         union
        (select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
        concat('o',a.sub_offered_id) as sub_offered_id,a.subject_code,b.semester,g.id,g.marks_master_id,g.total,g.grade from old_stu_course a
        inner join old_subject_offered b on a.sub_offered_id=b.id
        inner join user_details c on a.admn_no=c.id
        inner join cbcs_courses d on a.course=d.id
        inner join cbcs_branches e on a.branch=e.id

        inner join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
        Left join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
        where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' AND f.id=g.marks_master_id)) x

        inner join cbcs_modular_paper_details h on x.subject_code in (h.$exam_type) and x.admn_no=h.admn_no
        ";
        } else{
         if($sub_type=='Modular'){
                  $extraParam="";
                  $extraClouse="AND f.id=g.marks_master_id";
                }
                $sql="(select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
        concat('c',a.sub_offered_id) as sub_offered_id,a.subject_code,b.semester,g.id,g.marks_master_id,g.total,g.grade from cbcs_stu_course a
        inner join cbcs_subject_offered b on a.sub_offered_id=b.id
        inner join user_details c on a.admn_no=c.id
        inner join cbcs_courses d on a.course=d.id
        inner join cbcs_branches e on a.branch=e.id
		inner join users us on a.admn_no=us.id and us.status='A'
        
		inner join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
        LEFT join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
        where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse)
         union
        (select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
        concat('o',a.sub_offered_id) as sub_offered_id,a.subject_code,b.semester,g.id,g.marks_master_id,g.total,g.grade from old_stu_course a
        inner join old_subject_offered b on a.sub_offered_id=b.id
        inner join user_details c on a.admn_no=c.id
        inner join cbcs_courses d on a.course=d.id
        inner join cbcs_branches e on a.branch=e.id
		inner join users us on a.admn_no=us.id and us.status='A'
        
		inner join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
        Left join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
        where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse) ";


        	  }
        $query = $this->db->query($sql);
      //  echo $this->db->last_query();exit;
        if ($this->db->affected_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }
      }
	  
	  
	  //=================================================================================================
	  
      function getallStudentMarks_13_04_2021($sub_code,$session,$session_year,$exam_type,$offered_id,$sub_type){
 if($sub_type=='Modular'){
	    $extrajoin="inner join cbcs_modular_paper_details h on x.subject_code in (h.after_mid) and x.admn_no=h.admn_no";

        $sql="select * from (select x.* from ((select a.id as ids,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
concat('c',a.sub_offered_id) as sub_offered_id,a.subject_code,a.`session`,a.session_year,b.semester,g.id as mks_id,g.marks_master_id,g.total,g.grade from cbcs_stu_course a
inner join cbcs_subject_offered b on a.sub_offered_id=b.id
inner join user_details c on a.admn_no=c.id
inner join cbcs_courses d on a.course=d.id
inner join cbcs_branches e on a.branch=e.id

LEFT join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
LEFT join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' AND f.id=g.marks_master_id)
 union
(select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
concat('o',a.sub_offered_id) as sub_offered_id,a.subject_code,a.`session`,a.session_year,b.semester,g.id as mks_id,g.marks_master_id,g.total,g.grade from old_stu_course a
inner join old_subject_offered b on a.sub_offered_id=b.id
inner join user_details c on a.admn_no=c.id
inner join cbcs_courses d on a.course=d.id
inner join cbcs_branches e on a.branch=e.id

Left join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
Left join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' AND f.id=g.marks_master_id)) x

inner join cbcs_modular_paper_details h on x.subject_code in (h.$exam_type) and x.admn_no=h.admn_no) a
INNER JOIN reg_regular_form rg ON a.session_year=rg.session_year AND a.`session`=rg.`session` AND a.admn_no=rg.admn_no AND rg.hod_status='1' AND rg.acad_status='1'
INNER JOIN users u ON a.admn_no=u.id AND u.status='A' ";
} else{
 if($sub_type=='Modular'){
          $extraParam="";
          $extraClouse="AND f.id=g.marks_master_id";
        }
        $sql="select * from ((select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
concat('c',a.sub_offered_id) as sub_offered_id,a.subject_code,a.`session`,a.session_year,b.semester,g.id as mks_id,g.marks_master_id,g.total,g.grade from cbcs_stu_course a
inner join cbcs_subject_offered b on a.sub_offered_id=b.id
inner join user_details c on a.admn_no=c.id
inner join cbcs_courses d on a.course=d.id
inner join cbcs_branches e on a.branch=e.id
LEFT join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
LEFT join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse)
 union
(select a.id,a.form_id,a.admn_no,CONCAT_WS(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name,d.name as course,e.name as branch,
concat('o',a.sub_offered_id) as sub_offered_id,a.subject_code,a.`session`,a.session_year,b.semester,g.id as mks_id,g.marks_master_id,g.total,g.grade from old_stu_course a
inner join old_subject_offered b on a.sub_offered_id=b.id
inner join user_details c on a.admn_no=c.id
inner join cbcs_courses d on a.course=d.id
inner join cbcs_branches e on a.branch=e.id
Left join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`
Left join cbcs_marks_subject_description g on f.id=g.marks_master_id and a.admn_no=g.admn_no
where a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse)) a
INNER JOIN reg_regular_form rg ON a.session_year=rg.session_year AND a.`session`=rg.`session` AND a.admn_no=rg.admn_no AND rg.hod_status='1' AND rg.acad_status='1'
INNER JOIN users u ON a.admn_no=u.id AND u.status='A'";


	  }
        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
      //   echo  $this->db->last_query(); die();
            return $query->result();
        } else {
            return false;
        }
      }

      function getsubjectinfo($sub_code,$session,$session_year,$sub_type,$sub_offer_id,$course_id,$branch_id){
        if($sub_type=="Modular" && $course_id == "comm" && $branch_id == "comm"){
          $extraParam="AND a.sub_type='$sub_type' AND a.id='$sub_offer_id'";
          //$extraClouseCBCS="AND concat('c',a.id)=md.map_id";
         // $extraClouseOLD="AND concat('o',a.id)=md.map_id";
		  $extraClouseCBCS="and a.course_id=md.course_id and a.branch_id=md.branch_id and a.sub_group=md.`group`";
          $extraClouseOLD="and a.course_id=md.course_id and a.branch_id=md.branch_id and a.sub_group=md.`group`";
         $sql = "(
 SELECT msc.id as cc_id,msc.`status` as sendToCStatus,IF(md.id,md.id,'0') AS marks_dist_id,msc.sub_offered_id,a.id, (
 SELECT COUNT(mdc.marks_upload_status)
 FROM cbcs_marks_dist_child mdc
 WHERE mdc.id=marks_dist_id AND mdc.marks_upload_status='1') AS submittedcnt, (
 SELECT COUNT(mdc.id)
 FROM cbcs_marks_dist_child mdc
 WHERE mdc.id=marks_dist_id
 GROUP BY mdc.id) AS totalcnt,acc.id AS cc_id, a.sub_code,a.sub_name,b.emp_no, '$session' AS SESSION,'$session_year' AS session_year, (CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester))) AS offered_to,  CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no) AS name,
 IF(acc.`status`,(CONCAT_WS('',acc.co_emp_id)), 'Not Assigned') AS course_coordinator
 FROM cbcs_subject_offered a
 INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id and b.coordinator='1'
 INNER JOIN user_details ud ON b.emp_no=ud.id
 INNER join cbcs_stu_course cs on a.sub_code=cs.subject_code and a.id=cs.sub_offered_id and a.session_year=cs.session_year and a.session=cs.`session`
 LEFT JOIN cbcs_assign_course_coordinator acc ON  a.sub_code=acc.sub_code and acc.sub_offered_id=a.id
 LEFT JOIN cbcs_marks_dist md ON b.emp_no=md.emp_no AND a.sub_code=md.sub_code AND a.session_year=md.session_year AND a.`session`=md.`session` $extraClouseCBCS
 LEFT JOIN cbcs_marks_send_to_coordinator msc ON md.sub_code=msc.sub_code AND md.course_id=msc.course AND md.branch_id=msc.branch and b.emp_no=msc.instructor_emp_id and msc.`status`=1 and (case when a.sub_type='Modular' then concat('c',a.id)=msc.sub_offered_id else 1=1 end)
 WHERE a.session_year='$session_year' AND a.`session`='$session' AND a.sub_code='$sub_code' AND a.sub_type='Modular' AND a.id='$sub_offer_id'
  ) union
  (
 SELECT msc.id as cc_id,msc.`status` as sendToCStatus,IF(md.id,md.id,'0') AS marks_dist_id,msc.sub_offered_id,a.id, (
 SELECT COUNT(mdc.marks_upload_status)
 FROM cbcs_marks_dist_child mdc
 WHERE mdc.id=marks_dist_id AND mdc.marks_upload_status='1') AS submittedcnt, (
 SELECT COUNT(mdc.id)
 FROM cbcs_marks_dist_child mdc
 WHERE mdc.id=marks_dist_id
 GROUP BY mdc.id) AS totalcnt,acc.id AS cc_id, a.sub_code,a.sub_name,b.emp_no, '$session' AS SESSION, '$session_year' AS session_year, (CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester))) AS offered_to,  CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no) AS name,
 IF(acc.`status`,(CONCAT_WS('',acc.co_emp_id)), 'Not Assigned') AS course_coordinator
 FROM old_subject_offered a
 INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id and b.coordinator='1'
 INNER JOIN user_details ud ON b.emp_no=ud.id
 INNER join old_stu_course cs on a.sub_code=cs.subject_code and a.id=cs.sub_offered_id and a.session_year=cs.session_year and a.session=cs.`session`
 LEFT JOIN cbcs_assign_course_coordinator acc ON  a.sub_code=acc.sub_code and acc.sub_offered_id=a.id
 LEFT JOIN cbcs_marks_dist md ON b.emp_no=md.emp_no AND a.sub_code=md.sub_code AND a.session_year=md.session_year AND a.`session`=md.`session` $extraClouseOLD
 LEFT JOIN cbcs_marks_send_to_coordinator msc ON md.sub_code=msc.sub_code AND md.course_id=msc.course AND md.branch_id=msc.branch and b.emp_no=msc.instructor_emp_id and msc.`status`=1 and (case when a.sub_type='Modular' then concat('o',a.id)=msc.sub_offered_id else 1=1 end)
 WHERE a.session_year='$session_year' AND a.`session`='$session' AND a.sub_code='$sub_code' AND a.sub_type='Modular' AND a.id='$sub_offer_id' )";
        }else{

        $sql = "(
SELECT msc.id AS cc_id,msc.`status` AS sendToCStatus, IFNULL(md.id,'0') AS marks_dist_id,msc.sub_offered_id, (
SELECT COUNT(mdc.marks_upload_status)
FROM cbcs_marks_dist_child mdc
WHERE mdc.id=marks_dist_id AND mdc.marks_upload_status='1') AS submittedcnt, (
SELECT COUNT(mdc.id)
FROM cbcs_marks_dist_child mdc
WHERE mdc.id=marks_dist_id
GROUP BY mdc.id) AS totalcnt,acc.id AS cc_id, a.sub_code,a.sub_name,b.emp_no,'$session' AS SESSION,'$session_year' AS session_year, (CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester,b.section))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id AND b.coordinator=1
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN cbcs_stu_course cs ON a.sub_code=cs.subject_code AND a.id=cs.sub_offered_id AND a.session_year=cs.session_year AND a.session=cs.`session`
LEFT JOIN cbcs_assign_course_coordinator acc ON a.sub_code=acc.sub_code AND acc.sub_offered_id=a.id AND b.emp_no=acc.co_emp_id
LEFT JOIN cbcs_marks_dist md ON b.emp_no=md.emp_no AND a.sub_code=md.sub_code AND a.session_year=md.session_year AND a.`session`=md.`session` AND a.branch_id=md.branch_id AND a.course_id=md.course_id AND (CASE WHEN b.section !='' THEN b.section=md.section ELSE 1=1 END)
LEFT JOIN cbcs_marks_send_to_coordinator msc ON md.sub_code=msc.sub_code AND md.course_id=msc.course AND md.branch_id=msc.branch AND b.emp_no=msc.instructor_emp_id and msc.`status`=1
WHERE a.session_year='$session_year' AND a.`session`='$session' AND a.sub_code='$sub_code' AND b.coordinator='1'
GROUP BY a.sub_code,a.branch_id,a.course_id,a.dept_id,(CASE WHEN b.section !='' THEN b.section ELSE 1=1 END))
UNION(
SELECT msc.id AS cc_id,msc.`status` AS sendToCStatus, IFNULL(md.id,'0') AS marks_dist_id,msc.sub_offered_id, (
SELECT COUNT(mdc.marks_upload_status)
FROM cbcs_marks_dist_child mdc
WHERE mdc.id=marks_dist_id AND mdc.marks_upload_status='1') AS submittedcnt, (
SELECT COUNT(mdc.id)
FROM cbcs_marks_dist_child mdc
WHERE mdc.id=marks_dist_id
GROUP BY mdc.id) AS totalcnt,acc.id AS cc_id, a.sub_code,a.sub_name,b.emp_no,'$session' AS SESSION,'$session_year' AS session_year, (CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester,b.section))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id AND b.coordinator=1
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN old_stu_course cs ON a.sub_code=cs.subject_code AND a.id=cs.sub_offered_id AND a.session_year=cs.session_year AND a.session=cs.`session`
LEFT JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code
LEFT JOIN cbcs_marks_dist md ON b.emp_no=md.emp_no AND a.sub_code=md.sub_code AND a.session_year=md.session_year AND a.`session`=md.`session` AND a.branch_id=md.branch_id AND a.course_id=md.course_id AND (CASE WHEN b.section !='' THEN b.section=md.section ELSE 1=1 END)
LEFT JOIN cbcs_marks_send_to_coordinator msc ON md.sub_code=msc.sub_code AND md.course_id=msc.course AND md.branch_id=msc.branch AND b.emp_no=msc.instructor_emp_id and msc.`status`=1
WHERE a.session_year='$session_year' AND a.`session`='$session' AND a.sub_code='$sub_code' AND b.coordinator='1'
GROUP BY a.sub_code,a.branch_id,a.course_id,a.dept_id,(CASE WHEN b.section !='' THEN b.section ELSE 1=1 END))";
}
 //echo $sql; exit;
          $query = $this->db->query($sql);

          if ($this->db->affected_rows() > 0) {
           // echo  $this->db->last_query();
          // echo "<pre>";print_r($query->result()); exit;
              return $query->result();
          } else {
              return false;
          }
      }
      function UpdateGrades($data,$gradeval){

		// up 1 grade for session winter and session year 2019-20 start

	/*  comment on 17/11/2020 
	
	
	if($gradeval=="F"){
          $gradeval="D";
        }elseif($gradeval=="D"){
          $gradeval="C";
        }elseif($gradeval=="C"){
          $gradeval="C+";
        }elseif($gradeval=="C+"){
          $gradeval="B";
        }elseif($gradeval=="B"){
          $gradeval="B+";
        }elseif($gradeval=="B+"){
          $gradeval="A";
        }elseif($gradeval=="A"){
          $gradeval="A+";
        }
		
*/ 
		// end

        $updateVal=array(
          "grade"=>$gradeval
        );
        $this->db->where($data);
        $this->db->update('cbcs_marks_subject_description', $updateVal);
      }
      function SaveGrades($GradeData){
        $whereClouse=array(
          "session"=>$GradeData['session'],
          "session_year"=>$GradeData['session_year'],
          "sub_code"=>$GradeData['sub_code'],
		  "sub_offered_id"=>$GradeData['sub_offered_id'],
          "grade"=>$GradeData['grade'],
          "created_by"=>$this->session->userdata("id")
        );
        $updateData=array(
          "min_marks"=>$GradeData['min_marks'],
          "max_marks"=>$GradeData['max_marks'],
        );
        $this->db->select('*');
        $this->db->from('cbcs_grading_range');
        $this->db->where($whereClouse);
        $cnt=$this->db->get()->result_array();
        //$cnt=$this->db->last_query(); die();
        $count=count($cnt);
          if($count==0){
              $this->db->insert('cbcs_grading_range', $GradeData);
          }else{
            $this->db->where($whereClouse);
            $this->db->update('cbcs_grading_range', $GradeData);
          }
      }
      function getGrades($sub_code,$session,$session_year,$sub_offerd_id){
        $sql = "select a.* from cbcs_grading_range a where a.`session`='$session' and a.session_year='$session_year' and a.sub_code ='$sub_code' and a.sub_offered_id='$sub_offerd_id' ";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
	  function getGrades_test($sub_code,$session,$session_year){
        $sql = "select a.* from cbcs_grading_range a where a.`session`='$session' and a.session_year='$session_year' and a.sub_code ='$sub_code'  ";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }

      function GetsendToExamStatus($sub_code,$session,$session_year,$offered_id,$exam_type,$course_id,$branch_id){
		  if($course_id=='comm' && $branch_id=='comm'){
			  $extraClouse="and a.sub_offered_id='$offered_id'";
		  }
        $sql = "select count(a.sub_code) as sub_cnt,sum(IF(a.dean_ac_status like '%1%',1,0)) as dean_ac_status,a.updated_at as senddate from cbcs_marks_send_to_coordinator a
        where a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' and a.status='1' $extraClouse";
          $query = $this->db->query($sql);
        // echo  $this->db->last_query();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }

      function subject_grading_info($sub_code,$session,$session_year,$sub_type,$offered_id,$exam_type,$course_id,$branch_id){
if($sub_type=='Modular' && $course_id=='comm' &&  $branch_id=='comm'){
	 $sql="select y.*,sum(y.stu_cnt) as stu_cnt ,GROUP_CONCAT(y.branch_stu_info1) as branch_stu_info from (select count(x.admn_no) as stu_cnt,x.*,concat_ws(' / ',x.branch_stu,count(x.admn_no)) as branch_stu_info1 from (select a.subject_name as sub,a.subject_code,a.admn_no,b.section
,GROUP_CONCAT(DISTINCT
(concat(a.course,' / ',a.branch,
  ' / ',b.section, ' / ' ,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name)))) as branch_stu
,a.subject_name,a.`session`,a.session_year,(select max(updated_at) from cbcs_marks_send_to_coordinator where sub_code='$sub_code' and session_year='$session_year' and session='$session') as submitted_date
from cbcs_stu_course a

LEFT join cbcs_subject_offered_desc b on a.sub_offered_id=b.sub_offered_id
LEFT join user_details c on b.emp_no=c.id

where  a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' and b.coordinator='1'  and b.sub_offered_id=SUBSTR('$offered_id',2)  group by a.admn_no,b.section) x
inner join cbcs_modular_paper_details d on x.subject_code in (d.$exam_type) and x.admn_no=d.admn_no and x.section=d.section group by x.subject_code,x.section ) y";


}else{

 if($sub_type=='Modular'){

        $offered_id=  substr($offered_id, 1);
        $extraClouse="";
          //$extraClouse=" and a.sub_offered_id='$offered_id'";
          $join_old_m="(select count(l.admn_no) from old_subject_offered_desc k
            inner join old_subject_offered m on k.desc_id=m.id
            inner join cbcs_modular_paper_details l on k.sub_id=l.before_mid OR k.sub_id=l.after_mid and k.section=l.section
            where k.sub_id='$sub_code' and k.emp_no=b.emp_no and k.section=l.section and l.session_year='$session_year' and l.`session`='$session' and l.branch_id=a.branch and l.course_id=a.course)";

            $join_cbcs_m="(select count(l.admn_no) from cbcs_subject_offered_desc k
              inner join cbcs_subject_offered m on k.desc_id=m.id
              inner join cbcs_modular_paper_details l on k.sub_id=l.before_mid OR k.sub_id=l.after_mid and k.section=l.section
              where k.sub_id='$sub_code' and k.emp_no=b.emp_no and k.section=l.section and l.session_year='$session_year' and l.`session`='$session' and l.branch_id=a.branch and l.course_id=a.course)";


        }else{
            $oldjoin="(select count(id) from  old_stu_course d where d.branch=a.branch and d.course=a.course and d.subject_code='$sub_code' and d.`session`='$session' and d.session_year='$session_year')";
            $cbcsjoin="(select count(id) from  cbcs_stu_course d where d.branch=a.branch and d.course=a.course and d.subject_code='$sub_code' and d.`session`='$session' and d.session_year='$session_year')";
        }

        $sql = "select count(DISTINCT(a.admn_no)) as stu_cnt,a.subject_name
,GROUP_CONCAT(DISTINCT
(concat(a.course,' / ',a.branch,' / ',
$oldjoin  $join_old_m  ,' / ',b.section, ' / ' ,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name)))) as branch_stu_info
,a.subject_name,a.`session`,a.session_year,(select max(updated_at) from cbcs_marks_send_to_coordinator where sub_code='$sub_code' and session_year='2019-2020' and session='$session') as submitted_date
from old_stu_course a
LEFT join old_subject_offered_desc b on a.sub_offered_id=b.sub_offered_id
LEFT join user_details c on b.emp_no=c.id
where  a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' and b.coordinator='1' $extraClouse group by a.subject_code
UNION
select count(DISTINCT(a.admn_no)) as stu_cnt,a.subject_name
,GROUP_CONCAT(DISTINCT
(concat(a.course,' / ',a.branch,' / ',
$cbcsjoin  $join_cbcs_m,' / ',b.section, ' / ' ,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name)))) as branch_stu_info
,a.subject_name,a.`session`,a.session_year,(select max(updated_at) from cbcs_marks_send_to_coordinator where sub_code='$sub_code' and session_year='$session_year' and session='$session') as submitted_date
from cbcs_stu_course a

LEFT join cbcs_subject_offered_desc b on a.sub_offered_id=b.sub_offered_id
LEFT join user_details c on b.emp_no=c.id
where  a.subject_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' and b.coordinator='1' $extraClouse group by a.subject_code";
	  }

		  $query = $this->db->query($sql);
      //    echo  $this->db->last_query(); die();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }

	     function get_sub_type_for_st($sub_code,$session,$session_year,$sub_type,$offered_id){
        $sql="
        select a.*,null as ex from cbcs_subject_offered a
        where a.session_year='$session_year' and a.`session`='$session' and a.sub_code='$sub_code' group by a.sub_code
        union
        select a.* from old_subject_offered a
        where a.session_year='$session_year' and a.`session`='$session' and a.sub_code='$sub_code'  group by a.sub_code
        ";
        $query = $this->db->query($sql);
    //    echo  $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }
      }

      function marksAvg($sub_code,$session,$session_year){
        $sql = "select avg(b.total) as avg_marks
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code' and b.total <= 100";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
      function Calculate_SD($sub_code,$session,$session_year){
        $sql = "select b.total as marks
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code' and b.total <= 100 ";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
      function gradestaticspercentagelesslike($sub_code,$session,$session_year,$exam_type,$sub_type,$sub_offerd_id){
		  if($sub_type =="Modular"){
          $extrajoin="inner join cbcs_modular_paper_details c on b.admn_no=c.admn_no and a.subject_id in (c.$exam_type)";
		  $extraClouse="and (case when c.course_id='comm' && c.branch_id='comm' then a.sub_map_id='$sub_offerd_id' else '1=1' end)";
        }

        $sql = "select count(b.id) as total_stu,sum(IF(b.grade=null OR b.grade='',1,0)) as gradingStatus
               ,sum(IF(b.grade like 'A' OR b.grade like 'A+' OR b.grade like 'B+',1,0)) as 	agrade
               ,sum(IF(b.grade like 'B' OR b.grade like 'C' OR b.grade like 'C+',1,0)) as 	bgrade
               ,sum(IF(b.grade like 'D' OR b.grade like 'F',1,0)) as 	cgrade
			    ,sum(IF(b.grade like 'I',1,0)) as 	igrade
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
				$extrajoin
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code' $extraClouse";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query(); die();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
      function gradeStaticslike($sub_code,$session,$session_year,$exam_type,$sub_type,$sub_offerd_id){
		  if($sub_type =="Modular"){
          $extrajoin="inner join cbcs_modular_paper_details c on b.admn_no=c.admn_no and a.subject_id in (c.$exam_type)";
		  $extraClouse="and (case when c.course_id='comm' && c.branch_id='comm' then a.sub_map_id='$sub_offerd_id' else '1=1' end)";
        }
        $sql = "select count(b.id) as total_stu,sum(IF(b.grade=null OR b.grade='',1,0)) as gradingStatus
               ,sum(IF(b.grade like '%A%',1,0)) as 	agrade
               ,sum(IF(b.grade like '%B%',1,0)) as 	bgrade
               ,sum(IF(b.grade like '%C%',1,0)) as 	cgrade
               ,sum(IF(b.grade like '%D%',1,0)) as 	dgrade
               ,sum(IF(b.grade like '%F%',1,0)) as 	fgrade
			   ,sum(IF(b.grade like '%I%',1,0)) as 	igrade
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
				$extrajoin
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code' $extraClouse";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query(); die();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
	    function getIGrade($sub_code,$session,$session_year,$exam_type,$sub_type){
        if($sub_type=="Modular"){
          $extrajoin="inner join cbcs_modular_paper_details c on b.admn_no=c.admn_no and a.subject_id in (c.$exam_type)";
        }
        $sql = "select count(b.id) as total_stu,sum(IF(b.grade='I',1,0)) as igrade
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
                $extrajoin
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code'";
          $query = $this->db->query($sql);
      //    echo  $this->db->last_query(); die();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
      public function gradeStatics($sub_code,$session,$session_year ,$exam_type,$sub_type,$sub_offerd_id){
		  //echo $exam_type;
		  if($sub_type =="Modular"){
          $extrajoin="inner join cbcs_modular_paper_details c on b.admn_no=c.admn_no and a.subject_id in (c.$exam_type)";
		  $extraClouse="and (case when c.course_id='comm' && c.branch_id='comm' then a.sub_map_id='$sub_offerd_id' else '1=1' end)";
        }
        $sql = "select count(b.id) as total_stu,sum(IF(b.grade=null OR b.grade='',1,0)) as gradingStatus
                ,sum(IF(b.grade='A+',1,0)) as apgrade
                ,sum(IF(b.grade='A',1,0)) as  agrade
                ,sum(IF(b.grade='B+',1,0)) as bpgrade
                ,sum(IF(b.grade='B',1,0)) as 	bgrade
                ,sum(IF(b.grade='C+',1,0)) as cpgrade
                ,sum(IF(b.grade='C',1,0)) as 	cgrade
                ,sum(IF(b.grade='D',1,0)) as 	dgrade
                ,sum(IF(b.grade='F',1,0)) as 	fgrade
				,sum(IF(b.grade='I',1,0)) as 	igrade
                from cbcs_marks_master a inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
				$extrajoin
                where a.`session`='$session' and a.session_year='$session_year' and a.subject_id='$sub_code' $extraClouse";
          $query = $this->db->query($sql);
        //  echo  $this->db->last_query(); die();
          if ($this->db->affected_rows() > 0) {

              return $query->result();
          } else {
              return false;
          }
      }
      public function get_subject_list($emp_id,$dept_id,$session,$session_year){
    /* 18-11-19    $sql="
      (
  SELECT c.id AS cc_id,a.course_id,a.branch_id , a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
  acc.exam_type as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
  WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code and aa.status=1 and aa.sub_offered_id !='0' ) as submit_cnt,
  count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
  ,(select sum(x.coordinator) from cbcs_subject_offered_desc x where x.sub_id=a.sub_code and x.sub_offered_id=a.id) as cnt_mrks_send_ToC,
  concat('c',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
  FROM cbcs_subject_offered a
  INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
  INNER JOIN user_details ud ON b.emp_no=ud.id
  INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
  LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.instructor_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` and concat('c',a.id)=c.sub_offered_id
  WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
  GROUP BY a.id) UNION (

  SELECT c.id AS cc_id,a.course_id,a.branch_id , a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
  acc.exam_type as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
  WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code  and aa.status=1 and aa.sub_offered_id !='0' ) as submit_cnt,

  count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
  ,(select sum(x.coordinator) from old_subject_offered_desc x where x.sub_id=a.sub_code and x.sub_offered_id=a.id) as cnt_mrks_send_ToC,
  concat('o',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'Monsoon' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
  FROM old_subject_offered a
  INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
  INNER JOIN user_details ud ON b.emp_no=ud.id
  INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
  LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.instructor_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` and concat('o',a.id)=c.sub_offered_id
  WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
  GROUP BY a.id)
      ";
// change on 24-09-19 for moduler changes
/*      $sql="
    (
SELECT c.id AS cc_id, a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
(select if(x.before_mid=c.sub_code,'before_mid','after_mid') from cbcs_modular_paper_details x where x.before_mid in(c.sub_code) or x.after_mid in(c.sub_code)
and x.session_year=c.session_year and x.`session`=c.`session` limit 1) as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
WHERE aa.session_year='2019-2020' AND aa.`session`='Monsoon' AND aa.sub_code=a.sub_code and aa.status=1) as submit_cnt,
count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
,concat('c',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.instructor_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session`
WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
GROUP BY a.sub_code) UNION (

SELECT c.id AS cc_id, a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
(select if(x.before_mid=c.sub_code,'before_mid','after_mid') from cbcs_modular_paper_details x where x.before_mid in(c.sub_code) or x.after_mid in(c.sub_code)
and x.session_year=c.session_year and x.`session`=c.`session` limit 1) as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code and aa.status=1) as submit_cnt,

count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
,concat('o',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'Monsoon' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.instructor_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session`
WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
GROUP BY a.sub_code)
    ";*/


     /*  11-12-2020 for eso and grouing  $sql="
      (
  SELECT c.dean_ac_status,c.id AS cc_id,a.course_id,a.branch_id , a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
  acc.exam_type as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
  WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code and aa.status=1 and (case when a.sub_type='Modular' then concat('c',a.id)=aa.sub_offered_id else 1=1 end) ) as submit_cnt,
  count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
  ,(
select ((SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section))  ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM cbcs_subject_offered_desc x
inner join cbcs_subject_offered xx on x.sub_offered_id=xx.id
INNER JOIN cbcs_stu_course csc on csc.sub_offered_id=xx.id
WHERE x.sub_id=a.sub_code and xx.session_year='$session_year' and xx.`session`='$session' AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END))
+
(SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section))  ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM old_subject_offered_desc x
inner join old_subject_offered xx on x.sub_offered_id=xx.id
INNER JOIN old_stu_course csc on csc.sub_offered_id=xx.id
WHERE x.sub_id=a.sub_code and xx.session_year='$session_year' and xx.`session`='$session' AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END))



)) as cnt_mrks_send_ToC,
  concat('c',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
  FROM cbcs_subject_offered a
  INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
  INNER JOIN user_details ud ON b.emp_no=ud.id
  INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
  inner join cbcs_stu_course osc on a.id=osc.sub_offered_id
  LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.coordinator_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` and concat('c',a.id)=c.sub_offered_id and c.`status` !=2
  WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
  GROUP BY a.id) UNION (

  SELECT c.dean_ac_status,c.id AS cc_id,a.course_id,a.branch_id , a.sub_code,c.marks_master_id,a.id as sub_offered_ids,a.sub_type,
  acc.exam_type as ex_type,(select count(aa.id) from cbcs_marks_send_to_coordinator aa
  WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code  and aa.status=1 and (case when a.sub_type='Modular' then concat('c',a.id)=aa.sub_offered_id else 1=1 end) ) as submit_cnt,

  count(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) as cnt_offer_in_dept
  ,(
select ((SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section))  ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM cbcs_subject_offered_desc x
inner join cbcs_subject_offered xx on x.sub_offered_id=xx.id
INNER JOIN cbcs_stu_course csc on csc.sub_offered_id=xx.id
WHERE x.sub_id=a.sub_code and xx.session_year='$session_year' and xx.`session`='$session' AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END))
+
(SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section))  ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM old_subject_offered_desc x
inner join old_subject_offered xx on x.sub_offered_id=xx.id
INNER JOIN old_stu_course csc on csc.sub_offered_id=xx.id
WHERE x.sub_id=a.sub_code and xx.session_year='$session_year' and xx.`session`='$session' AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END))



)) as cnt_mrks_send_ToC,
  concat('o',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
  FROM old_subject_offered a
  INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
  INNER JOIN user_details ud ON b.emp_no=ud.id
  INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to and b.sub_offered_id=acc.sub_offered_id
  inner join old_stu_course osc on a.id=osc.sub_offered_id
  LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.coordinator_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` and concat('o',a.id)=c.sub_offered_id and c.`status` !=2
  WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
  GROUP BY a.id)
      "; */
//Made change in line no. 1047 on 12 May 2021. To solve issue og G. Budi for Perform grading with old subject

	  $sql="(
SELECT c.dean_ac_status,c.id AS cc_id,a.course_id,a.branch_id, a.sub_code,c.marks_master_id,a.id AS sub_offered_ids,a.sub_type, acc.exam_type AS ex_type,(
SELECT COUNT(aa.id)
FROM cbcs_marks_send_to_coordinator aa
WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code AND aa.status=1 AND aa.dean_ac_status <>2
AND (CASE WHEN a.sub_type='Modular' 
AND NOT EXISTS (
SELECT *
FROM cbcs_optional_mapping zz
WHERE zz.sub_code=a.sub_code AND zz.sub_offered_id= CONCAT('c',a.id))
THEN if(((a.sub_code='ESI101' or  a.sub_code='MCI103') AND a.session='Winter' AND a.session_year='2020-2021'),1=1,CONCAT('c',a.id)=aa.sub_offered_id) 
WHEN EXISTS (
SELECT *
FROM cbcs_optional_mapping zz
WHERE zz.sub_code=a.sub_code AND zz.sub_offered_id= CONCAT('c',a.id)) then a.sub_code=aa.sub_code

ELSE 1=1 END)) AS submit_cnt, COUNT(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) AS cnt_offer_in_dept,
(SELECT (
(case when EXISTS (select * from cbcs_optional_mapping zz where zz.sub_code=a.sub_code and zz.sub_offered_id=concat('c',a.id)) then
(
select count(*) from (select com.sub_code,com.sub_offered_id,com.sub_category,com.course_id,com.branch_id,comd.map_id,comd.emp_no,comd.section from cbcs_optional_mapping com
inner join cbcs_optional_mapping_desc comd on com.id=comd.map_id
group by comd.sub_id,
(case when com.sub_category like 'eso%' then comd.emp_no else comd.map_id end)
) x
left JOIN cbcs_subject_offered xx on x.sub_code=xx.sub_code and x.sub_offered_id != concat('c',xx.id) and xx.session_year='$session_year' and xx.`session`='$session' AND x.course_id=xx.course_id AND x.branch_id=xx.branch_id
left JOIN old_subject_offered oso on x.sub_code=oso.sub_code and x.sub_offered_id != concat('o',oso.id) and oso.session_year='$session_year' and oso.`session`='$session' AND x.course_id=oso.course_id AND x.branch_id=oso.branch_id
where x.sub_code=a.sub_code  /*and (case when x.sub_category like 'ESO%' then 1=1 else concat('c','118724') end)*/
)
else(
SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section)) ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0,
COUNT(DISTINCT x.sub_offered_id)) END)
FROM cbcs_subject_offered_desc x
INNER JOIN cbcs_subject_offered xx ON x.sub_offered_id=xx.id
INNER JOIN cbcs_stu_course csc ON csc.sub_offered_id=xx.id
inner join users u on u.id=csc.admn_no and u.`status`='A'
WHERE x.sub_id=a.sub_code AND xx.session_year='$session_year' AND xx.`session`='$session'
AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END)) + (
SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section)) ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0,
COUNT(DISTINCT x.sub_offered_id)) END)
FROM old_subject_offered_desc x
INNER JOIN old_subject_offered xx ON x.sub_offered_id=xx.id
INNER JOIN old_stu_course csc ON csc.sub_offered_id=xx.id
inner join users u on u.id=csc.admn_no and u.`status`='A'
WHERE x.sub_id=a.sub_code AND xx.session_year='$session_year' AND xx.`session`='$session'
AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END)) end))) AS cnt_mrks_send_ToC,
CONCAT('c',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM cbcs_subject_offered a
INNER JOIN cbcs_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to AND b.sub_offered_id=acc.sub_offered_id
INNER JOIN cbcs_stu_course osc ON a.id=osc.sub_offered_id
LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.coordinator_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` AND CONCAT('c',a.id)=c.sub_offered_id AND c.`status` !=2
WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
GROUP BY a.id)
UNION (
SELECT c.dean_ac_status,c.id AS cc_id,a.course_id,a.branch_id, a.sub_code,c.marks_master_id,a.id AS sub_offered_ids,a.sub_type, acc.exam_type AS ex_type,(
SELECT COUNT(aa.id)
FROM cbcs_marks_send_to_coordinator aa
WHERE aa.session_year='$session_year' AND aa.`session`='$session' AND aa.sub_code=a.sub_code AND aa.status=1 AND (CASE WHEN a.sub_type='Modular' 
AND NOT EXISTS (
SELECT *
FROM cbcs_optional_mapping zz
WHERE zz.sub_code=a.sub_code AND zz.sub_offered_id= CONCAT('c',a.id))

THEN if(((a.sub_code='ESI101') AND a.session='Winter' AND a.session_year='2020-2021'),1=1,CONCAT('c',a.id)=aa.sub_offered_id)

WHEN EXISTS (
SELECT *
FROM cbcs_optional_mapping zz
WHERE zz.sub_code=a.sub_code AND zz.sub_offered_id= CONCAT('c',a.id)) THEN a.sub_code=aa.sub_code

ELSE 1=1 END)) AS submit_cnt, COUNT(DISTINCT(CONCAT_WS('/',a.dept_id,a.branch_id,a.course_id,a.semester))) AS cnt_offer_in_dept,(
SELECT (

(case when EXISTS (select * from cbcs_optional_mapping zz where zz.sub_code=a.sub_code and zz.sub_offered_id=concat('o',a.id))
then
(
select count(*) from (select com.sub_code,com.sub_offered_id,com.sub_category,com.course_id,com.branch_id,comd.map_id,comd.emp_no,comd.section from cbcs_optional_mapping com
inner join cbcs_optional_mapping_desc comd on com.id=comd.map_id
group by comd.sub_id,
(case when com.sub_category like 'eso%' then comd.emp_no else comd.map_id end)
) x
left JOIN cbcs_subject_offered xx on x.sub_code=xx.sub_code and x.sub_offered_id != concat('c',xx.id) and xx.session_year='$session_year' and xx.`session`='$session' AND x.course_id=xx.course_id AND x.branch_id=xx.branch_id
left JOIN old_subject_offered oso on x.sub_code=oso.sub_code and x.sub_offered_id != concat('o',oso.id) and oso.session_year='$session_year' and oso.`session`='$session' AND x.course_id=oso.course_id AND x.branch_id=oso.branch_id
where x.sub_code=a.sub_code /*and (case when x.sub_category like 'ESO%' then 1=1 else concat('c','118724') end)*/
)
else
(
SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section)) ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM cbcs_subject_offered_desc x
INNER JOIN cbcs_subject_offered xx ON x.sub_offered_id=xx.id
INNER JOIN cbcs_stu_course csc ON csc.sub_offered_id=xx.id
inner join users u on u.id=csc.admn_no and u.`status`='A'
WHERE x.sub_id=a.sub_code AND xx.session_year='$session_year' AND xx.`session`='Monsoon' AND (CASE WHEN a.sub_type='Modular' THEN x.sub_offered_id=a.id ELSE 1=1 END)) + (
SELECT (CASE WHEN a.course_id='comm' THEN (COUNT(DISTINCT x.section)) ELSE IF(COUNT(DISTINCT x.sub_offered_id) IS NULL,0, COUNT(DISTINCT x.sub_offered_id)) END)
FROM old_subject_offered_desc x
INNER JOIN old_subject_offered xx ON x.sub_offered_id=xx.id
INNER JOIN old_stu_course csc ON csc.sub_offered_id=xx.id
inner join users u on u.id=csc.admn_no and u.`status`='A'
WHERE x.sub_id=a.sub_code AND xx.session_year='$session_year' AND xx.`session`='$session' AND (CASE WHEN a.sub_type='Modular'
THEN x.sub_offered_id=a.id ELSE 1=1 END))end))) AS cnt_mrks_send_ToC, CONCAT('o',a.id) AS sub_offerd_id,a.sub_name,b.emp_no,'$session' AS session,'$session_year' AS session_year, GROUP_CONCAT(DISTINCT(CONCAT_WS(',', CONCAT_WS(' / ',a.dept_id,a.branch_id,a.course_id,a.semester)))) AS offered_to, GROUP_CONCAT(CONCAT_WS(' - ', CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name,'-', b.emp_no))) AS name, IF(acc.`status`,(CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name, '-',b.emp_no)), 'Not Assigned') AS course_coordinator
FROM old_subject_offered a
INNER JOIN old_subject_offered_desc b ON a.id=b.sub_offered_id
INNER JOIN user_details ud ON b.emp_no=ud.id
INNER JOIN cbcs_assign_course_coordinator acc ON b.emp_no=acc.co_emp_id AND a.sub_code=acc.sub_code AND a.dept_id=acc.offered_to AND b.sub_offered_id=acc.sub_offered_id
INNER JOIN old_stu_course osc ON a.id=osc.sub_offered_id
LEFT JOIN cbcs_marks_send_to_coordinator c ON a.sub_code=c.sub_code AND b.emp_no=c.coordinator_emp_id AND a.session_year=c.session_year AND a.`session`=c.`session` AND CONCAT('o',a.id)=c.sub_offered_id AND c.`status` !=2
WHERE a.session_year='$session_year' AND a.`session`='$session' AND acc.co_emp_id='$emp_id'
GROUP BY a.id)";

          $query = $this->db->query($sql);
          if ($this->db->affected_rows() > 0) {
//           echo  $this->db->last_query();
              return $query->result();
          } else {
              return false;
          }
      }

      function getDownloadDataDeptWise($sub_code,$session,$session_year,$exam_type,$offered_id,$sub_type,$course_id,$branch_id){
        $offered_id=substr($offered_id,1);
        if($sub_type=='Modular'  && $course_id=='comm' && $branch_id=='comm'){
          $extraJoincbcs="inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id";
          $extraJoinold="inner join old_subject_offered_desc b on a.id=b.sub_offered_id";
          $extraColumn=",b.section";
          $groupby="";
          $extraClouse="";
		  $extrawhere="and b.sub_offered_id='$offered_id'";
		  $modjoin="inner join cbcs_modular_paper_details c on a.sub_code in (c.$exam_type) and c.section=b.section";
        //  $extraClouse="and a.id='$offered_id'";
        }else{
          $groupby="group by sub_offered_id";
          $extraClouse="";
		  $extrawhere="";
        }
//note use GROUP by sub_offered_id for getting report for all branches
        $sql="(select concat('c',a.id) as sub_offered_id $extraColumn,a.sub_code,a.course_id,a.branch_id,a.dept_id, a.sub_name,a.`session`,a.session_year
from cbcs_subject_offered a
$extraJoincbcs
$modjoin
where  a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extrawhere  $extraClouse $groupby)
UNION
(select concat('o',a.id) as sub_offered_id $extraColumn,a.sub_code,a.course_id,a.branch_id,a.dept_id,a.sub_name,a.`session`,a.session_year
from old_subject_offered a
$extraJoinold
$modjoin
where  a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extrawhere  $extraClouse $groupby)";
              $query = $this->db->query($sql);
              //  echo  $this->db->last_query(); die();
              if ($this->db->affected_rows() > 0) {

                  return $query->result();
              } else {
                  return false;
              }
      }

      function getDownloadData($sub_code,$session,$session_year,$exam_type,$offered_id,$sub_type){
        $offered_id=substr($offered_id,1);
        if($sub_type=='Modular'){
          $extraJoincbcs="inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id";
          $extraJoinold="inner join old_subject_offered_desc b on a.id=b.sub_offered_id";
          $extraColumn=",b.section";
          $groupby="group by a.sub_code";
          $extraClouse="and a.id='$offered_id'";
        }else{
          $groupby="group by a.sub_code"; //"group by sub_offered_id"
          $extraClouse="";
        }
//note use GROUP by sub_offered_id for getting report for all branches
        $sql="(select concat('c',a.id) as sub_offered_id $extraColumn,a.sub_code,a.course_id,a.branch_id,a.dept_id, a.sub_name,a.`session`,a.session_year
from cbcs_subject_offered a
$extraJoincbcs
where  a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse $groupby)
UNION All
(select concat('o',a.id) as sub_offered_id $extraColumn,a.sub_code,a.course_id,a.branch_id,a.dept_id,a.sub_name,a.`session`,a.session_year
from old_subject_offered a
$extraJoinold
where  a.sub_code='$sub_code' and a.`session`='$session' and a.session_year='$session_year' $extraClouse $groupby)";
              $query = $this->db->query($sql);
              //  echo  $this->db->last_query(); die();
              if ($this->db->affected_rows() > 0) {

                  return $query->result();
              } else {
                  return false;
              }
      }

      function updateMarksStatus($sub_code,$session,$session_year){
        $sql="update cbcs_marks_master set status='Y' where session='$session' and session_year='$session_year' and subject_id='$sub_code'";
        $query = $this->db->query($sql);
        if($this->db->affected_rows() != 1){
        // /  echo  $this->db->last_query(); die();
            return true;

        } else {
          return false;
        }
      }
      function getSubjectInfoforDownloadDeptWise($sub_code,$subject_offer_id,$session,$session_year,$sub_type,$section,$examtype,$course_id,$branch_id){
       $empid=$this->session->userdata("id");
       // echo $sub_type; exit;

		$subject_offer_id= substr($subject_offer_id,1);
        if($sub_type=="Modular"){
        //  $innerJoin="inner join cbcs_modular_paper_details e on a.admn_no=e.admn_no and d.section=e.section and a.subject_code = e.$examtype";
          //and d.emp_no='$empid'
          $groupby="group by a.subject_code";


        }
		 if($sub_type=="Modular" && $course_id=='comm' && $branch_id=='comm'){
			 $extraClouse="and e.section='$section'";
              $innerJoin="inner join cbcs_modular_paper_details e on a.admn_no=e.admn_no and d.section=e.section and a.subject_code = e.$examtype";
			   $extraCo="and z.dept='$section'";

            }
        $sql="
        select p.* from (select a.subject_code as sub_code,a.subject_name as sub_name ,a.course AS course_name,a.branch AS branch_name,a.`session`,a.session_year,f.semester,d.section,count(a.admn_no) as noofstu,z.updated_at from cbcs_stu_course a
        inner join cbcs_subject_offered f on a.sub_offered_id=f.id
        inner join cbcs_subject_offered_desc d on a.sub_offered_id=d.sub_offered_id and d.coordinator='1'
         inner join cbcs_marks_send_to_coordinator z on a.subject_code=z.sub_code and a.sub_offered_id=SUBSTRING(z.sub_offered_id,2) and a.session_year=z.session_year and a.`session`=z.`session` and z.`status`=1 $extraCo
        $innerJoin
        WHERE a.subject_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.sub_offered_id='$subject_offer_id' $extraClouse $groupby

        union

        select a.subject_code as sub_code,a.subject_name as sub_name ,a.course AS course_name,a.branch AS branch_name,a.`session`,a.session_year,f.semester,d.section,count(a.admn_no) as noofstu,z.updated_at from old_stu_course a
        inner join old_subject_offered f on a.sub_offered_id=f.id
        inner join old_subject_offered_desc d on a.sub_offered_id=d.sub_offered_id and d.coordinator='1'
       inner join cbcs_marks_send_to_coordinator z on a.subject_code=z.sub_code and a.sub_offered_id=SUBSTRING(z.sub_offered_id,2) and a.session_year=z.session_year and a.`session`=z.`session` and z.`status`=1 $extraCo
        $innerJoin
        WHERE a.subject_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.sub_offered_id='$subject_offer_id' $extraClouse $groupby) p where p.noofstu !=0 order by p.noofstu

";
        // comment on 1-10-19
        /*$sql="select a.sub_name,a.sub_code,a.semester,b.name AS course_name,c.name AS branch_name,a.`session`,a.session_year,count(a.id) as noofstu from cbcs_subject_offered a
              inner join cbcs_courses b on a.course_id=b.id
              inner join cbcs_branches c on a.branch_id=c.id
              inner join cbcs_stu_course d on a.id=d.sub_offered_id
              WHERE a.sub_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.id='$subject_offer_id'";*/
              $query = $this->db->query($sql);
            //  echo  $this->db->last_query(); die();
        //    echo  $query->num_rows();
              if ($this->db->affected_rows() > 0) {
         //    echo  $this->db->last_query(); die();
                  return $query->result();
              } else {
                  return false;
              }
      }

      function getSubjectInfoforDownload($sub_code,$subject_offer_id,$session,$session_year,$sub_type,$section,$examtype){
       $empid=$this->session->userdata("id");
        $subject_offer_id= substr($subject_offer_id,1);
        if($sub_type=="Modular"){
          $innerJoin="inner join cbcs_modular_paper_details e on a.admn_no=e.admn_no and a.subject_code = e.$examtype";
          $extraClouse="and d.section='$section'";//and d.emp_no='$empid'
          $groupby="group by a.subject_code";
        }
      /*   $sql="
        select p.* from (select a.subject_code as sub_code,a.subject_name as sub_name ,b.name AS course_name,c.name AS branch_name,a.`session`,a.session_year,f.semester,d.section,count(a.admn_no) as noofstu from cbcs_stu_course a
         inner join cbcs_courses b on a.course=b.id
        inner join cbcs_branches c on a.branch=c.id
        inner join cbcs_subject_offered f on a.sub_offered_id=f.id
        inner join cbcs_subject_offered_desc d on a.sub_offered_id=d.sub_offered_id
        $innerJoin
        WHERE a.subject_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.sub_offered_id='$subject_offer_id' $extraClouse $groupby

        union

        select a.subject_code as sub_code,a.subject_name as sub_name ,b.name AS course_name,c.name AS branch_name,a.`session`,a.session_year,f.semester,d.section,count(a.admn_no) as noofstu from old_stu_course a
         inner join cbcs_courses b on a.course=b.id
        inner join cbcs_branches c on a.branch=c.id
        inner join old_subject_offered f on a.sub_offered_id=f.id
        inner join old_subject_offered_desc d on a.sub_offered_id=d.sub_offered_id
        $innerJoin
        WHERE a.subject_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.sub_offered_id='$subject_offer_id' $extraClouse $groupby) p where p.noofstu !=0 order by p.noofstu

";
        // comment on 1-10-19
        /*$sql="select a.sub_name,a.sub_code,a.semester,b.name AS course_name,c.name AS branch_name,a.`session`,a.session_year,count(a.id) as noofstu from cbcs_subject_offered a
              inner join cbcs_courses b on a.course_id=b.id
              inner join cbcs_branches c on a.branch_id=c.id
              inner join cbcs_stu_course d on a.id=d.sub_offered_id
              WHERE a.sub_code='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.id='$subject_offer_id'";*/

                $sql="
                select p.* from(select a.*,count(a.admn_no) as noofstu from cbcs_stu_course a
				$innerJoin
                where a.subject_code='$sub_code' and a.session_year='$session_year' and a.`session`='$session'
                union
                select a.*,count(a.admn_no) as noofstu from old_stu_course a
				$innerJoin
				where a.subject_code='$sub_code' and a.session_year='$session_year' and a.`session`='$session') p where p.noofstu !=0 order by p.noofstu
                ";


              $query = $this->db->query($sql);
          //     echo  $this->db->last_query(); die();
        //    echo  $query->num_rows();
              if ($this->db->affected_rows() > 0) {
              //  echo  $this->db->last_query(); die();
                  return $query->result();
              } else {
                  return false;
              }
      }
      function sendToAC($sub_code,$session,$session_year,$sub_offerd_id,$course_id,$branch_id,$sub_type){
		  if($course_id=='comm' && $branch_id=='comm'){
          $extraClouse="AND sub_offered_id='$sub_offerd_id'";
        }
        $sql="update cbcs_marks_send_to_coordinator set dean_ac_status='1' where sub_code='$sub_code' and session='$session' and session_year ='$session_year' and status='1' $extraClouse ";
              $query = $this->db->query($sql);
              if($this->db->affected_rows() != 1){
              // /  echo  $this->db->last_query(); die();
                  return true;

              } else {
                return false;
              }
      }
      function getallStudentMarksfordownload($sub_code,$subject_offer_id,$session,$session_year,$sub_type,$section){
            if($sub_type=="Modular"){
              $extrajoin="inner join cbcs_modular_paper_details d on b.admn_no =d.admn_no and a.`session`=d.session and a.session_year=d.session_year";
              $extraClouse="and d.section='$section' group by d.admn_no ";
            }
              $sql="select a.subject_id,b.admn_no,b.total,b.grade,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as stu_name from cbcs_marks_master a
                    inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
                    inner join user_details c on b.admn_no=c.id
                    $extrajoin
                    WHERE a.subject_id='$sub_code' AND a.`session`='$session' AND a.session_year='$session_year' and a.sub_map_id='$subject_offer_id' $extraClouse";
              $query = $this->db->query($sql);
              if ($this->db->affected_rows() > 0) {
              //  echo  $this->db->last_query(); die();
                  return $query->result();
              } else {
                  return false;
              }
      }
      function marksSubmitOpenRequest($emp_id,$session_year,$session){
              $sql = "select p.*,q.dean_ac_status from (select c.dept_id,c.course_id,c.branch_id,c.semester,b.pk,b.category,concat_ws(' ',d.first_name,d.middle_name,d.last_name) as req_by, a.* from cbcs_marks_submission_reopen_req a
                inner join cbcs_marks_dist_child b on a.component_id=b.pk
                inner join cbcs_marks_dist c on a.sub_code=c.sub_code and b.id=c.id
                inner join user_details d on a.req_emp_by=d.id
                where a.`session`='$session' and a.session_year='$session_year' and a.co_emp_id='$emp_id' order by a.id desc) p
                Left join cbcs_marks_send_to_coordinator q on p.sub_code=q.sub_code and p.sub_offerd_id=q.sub_offered_id
                and p.session=q.`session` and p.session_year=q.session_year group by p.component_id,p.id";
          $query = $this->db->query($sql);
        //    echo $this->db->last_query(); die();
          if ($query->num_rows() > 0)
              return $query->result();
          else
              return 0;
      }
      function getSubjectPlanforview($sessionyear,$session,$sub_code){
      $sql = "SELECT b.category,b.pk,b.wtg
      FROM cbcs_marks_dist a
      INNER JOIN cbcs_marks_dist_child b ON a.id=b.id
      WHERE a.session_year='$sessionyear' AND a.`session`='$session' AND a.sub_code='$sub_code' group by b.category order by b.category asc";
        $query = $this->db->query($sql);
        //  echo $this->db->last_query(); //die();
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return 0;
      }

      function get_submitted_marks_list($session,$session_year,$sub_code,$sub_type,$examtype,$course_id,$branch_id){
		     if($sub_type=='Modular' && $course_id=='comm' && $branch_id=='comm'){
          $extrajoin="INNER JOIN cbcs_modular_paper_details f on x.subject_id in (f.$examtype) and x.admn_no=f.admn_no and x.`session`=f.`session` and x.session_year=f.session_year";
        }else{
		$extrajoin="";
		}

        $sql = "select * from (SELECT a.*,d.subject_id, GROUP_CONCAT(CONCAT_WS(',',b.category_name)) AS category_name, GROUP_CONCAT(b.marks) AS marks,e.total,e.grade, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS name
FROM cbcs_marks_upload a
INNER JOIN cbcs_marks_upload_description b ON a.id=b.marks_id
INNER JOIN user_details c ON a.admn_no=c.id
INNER JOIN cbcs_marks_master d on a.subject_code=d.subject_id and a.session_year=d.session_year and a.session=d.session
INNER JOIN cbcs_marks_subject_description e on d.id=e.marks_master_id and a.admn_no=e.admn_no

WHERE a.session_year='$session_year' AND a.session='$session' AND a.subject_code='$sub_code'
GROUP BY a.admn_no)x
$extrajoin
";
	//echo $sql;exit;
          $query = $this->db->query($sql);
        //    echo $this->db->last_query(); die();
          if ($query->num_rows() > 0)
              return $query->result();
          else
              return 0;;
          $query = $this->db->query($sql);
        //    echo $this->db->last_query(); die();
          if ($query->num_rows() > 0)
              return $query->result();
          else
              return 0;
      }

      function get_submitted_marks_list_dept_wise($session,$session_year,$sub_code,$subject_offer_id,$exam_type,$sub_type,$section,$course_id,$branch_id){
		//	echo $sub_type;
		if($sub_type=='Modular' && $course_id=='comm' && $branch_id=='comm'){
          $extrajoin="INNER JOIN cbcs_modular_paper_details f on d.subject_id in (f.$exam_type) and e.admn_no=f.admn_no and d.`session`=f.`session` and d.session_year=f.session_year";
		  $extraClouse="and f.section='$section'";
        }

	  $sql = "SELECT a.*, GROUP_CONCAT(CONCAT_WS(',',b.category_name)) AS category_name, GROUP_CONCAT(b.marks) AS marks,e.total,e.grade, CONCAT_WS(' ',c.first_name,c.middle_name,c.last_name) AS name
FROM cbcs_marks_upload a
INNER JOIN cbcs_marks_upload_description b ON a.id=b.marks_id
INNER JOIN user_details c ON a.admn_no=c.id
INNER JOIN cbcs_marks_master d on a.subject_code=d.subject_id and a.session_year=d.session_year and a.session=d.session
INNER JOIN cbcs_marks_subject_description e on d.id=e.marks_master_id and a.admn_no=e.admn_no
$extrajoin
WHERE a.session_year='$session_year' AND a.session='$session' AND a.subject_code='$sub_code' and a.sub_offered_id='$subject_offer_id' and d.sub_map_id='$subject_offer_id' $extraClouse
GROUP BY a.admn_no";
          $query = $this->db->query($sql);
           // echo $this->db->last_query(); die();
          if ($query->num_rows() > 0)
              return $query->result();
          else
              return 0;
      }


      function getInstructor($session_year,$session,$sub_code){
        $sql = "select a.sub_code,b.coordinator,b.emp_no,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as emp_name from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id and a.sub_code=b.sub_id
inner join user_details c on b.emp_no=c.id
where a.session_year='$session_year' and a.`session`='$session' and a.sub_code='$sub_code' and b.coordinator=1
union
select a.sub_code,b.coordinator,b.emp_no,concat_ws(' ',c.salutation,c.first_name,c.middle_name,c.last_name) as emp_name from old_subject_offered a
inner join old_subject_offered_desc b on a.id=b.sub_offered_id and a.sub_code=b.sub_id
inner join user_details c on b.emp_no=c.id
where a.session_year='$session_year' and a.`session`='$session' and a.sub_code='$sub_code' and b.coordinator=1";
          $query = $this->db->query($sql);
          //  echo $this->db->last_query(); die();
          if ($query->num_rows() > 0)
              return $query->result();
          else
              return 0;
      }



// @author:rituraj  @dsc: sending  data to foil & freeze

	   function send_to_foil_old($sub_code,$session,$session_year,$dept=null,$course=null,$branch=null,$sem=null,$admn_no=null,$param=null,$start=null,$eachset=null){
	     $this->load->model('attendance/exam_attendance_model');
		try{
			$this->db->trans_begin();
			$returntmsg='';
          if($sub_code<>null)$txt=" a.subject_code='$sub_code' and "; else $txt="" ;
		  if($dept<>null)$append1="  and  b.dept_id='$dept' "; else $append1="" ;
		  if($course<>null)$append2="  and  b.course_id='$course' "; else $append2="" ;
		    if($branch<>null )  $append3="  and  b.branch_id='$branch' "; else $append3="" ;  //hack code for mech+te to met
		  //if($branch<>null &&   $branch<>'mech+te')  $append3="  and  b.branch_id='$branch' "; else $append3="" ;  //hack code for mech+te to met
		  //if($branch<>null &&   $branch=='mech+te')  $append3="  and  (b.branch_id='mech+te'  or b.branch_id='met' )   "; else $append3="" ;//hack code for mech+te to met
		  if($sem<>null  )$append5="  and  rg.semester='".($dept=='comm'? ($session=='Monsoon'?1:2) : $sem )."' "; else $append5="" ;
		   if($sem<>null && $dept=='comm' && $sem<>'all'){
			  $section_append="   join stu_section_data ssd on  ssd.admn_no=v.admn_no and ssd.session_year=v.session_year and  ssd.section='$sem'";
			  $section_select =" ,ssd.section  ";
		  }
		 else{
				$section_append="" ;$section_select="";
		    }


			if($dept<>'comm')
				$append6="  and  rg.course_id='$course'   and  rg.branch_id= '$branch'  ";
			    else
			    $append6="" ;


		  if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;
        if ($admn_no == null) {
          if($param==null)
             $rep=" limit ".$start." ,".$eachset."";
          else
           $rep='';
        }
        else
        $rep='';


        $sql="SELECT v.*,COALESCE((gp.points*v.credit_hours),0) AS cr_pts , rg.semester AS  curr_sem  $section_select    FROM(
SELECT f.*,g.id AS sub_des_id,g.marks_master_id,g.total,g.grade FROM
(

(
SELECT a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(CASE WHEN b.course_id='honour' THEN 'b.tech' ELSE b.course_id END) AS course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year
FROM cbcs_stu_course a
INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
LEFT JOIN cbcs_marks_master f ON CONCAT('c',a.sub_offered_id)=f.sub_map_id AND a.subject_code=f.subject_id AND a.session_year=f.session_year AND a.`session`=f.`session`
WHERE $txt  a.`session`='$session' and a.session_year='$session_year'  AND b.dept_id='comm' AND b.course_id='comm' AND b.branch_id='comm'  $append4)

union

 (
  SELECT a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(case when b.course_id='honour' then 'b.tech' else b.course_id end ) as  course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year

from cbcs_stu_course a
inner join cbcs_subject_offered b on a.sub_offered_id=b.id
left join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`

where $txt a.`session`='$session' and a.session_year='$session_year'  $append1 $append2 $append3 $append4 )

 union

(SELECT a.id,a.form_id,a.admn_no, CONCAT('o',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(case when b.course_id='honour' then 'b.tech' else b.course_id end ) as  course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year
 from old_stu_course a
inner join old_subject_offered b on a.sub_offered_id=b.id

 left join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`

where $txt a.`session`='$session' and a.session_year='$session_year' $append1 $append2 $append3 $append4 )
)f
 join cbcs_marks_subject_description g on f.m_m_id=g.marks_master_id and f.admn_no=g.admn_no and g.grade is not null /*and g.grade<>'I'*/ order by f.subject_code,g.grade limit 10000000

)v
JOIN   reg_regular_form rg ON rg.form_id=v.form_id $append5 $append6
$section_append

 left  JOIN  grade_points gp ON gp.grade=v.grade

 group by  v.admn_no, v.subject_code
  ORDER BY v.dept_id,v.course_id,v.branch_id,curr_sem ,v.admn_no $rep
 ";





// echo $sql; die();

        $query = $this->db->query($sql);

		//echo $query->num_rows();
		if($param==null){

        if ($query->num_rows() > 0) {
		$affected=null;	$arr22=null;$k=0;$arr21=null;$arr25=null;$arr23=null;$arr26=null;$admn_uniqure_array=null;
   // echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();




	      foreach($query->result_array() as $row1){
			    $defaulterStatus=null;
			    $defaulterStatus=$this->exam_attendance_model->GetdefaulterStatus($row1['admn_no'], $row1['subject_code'],$row1['sub_offerd_id'],$session_year,$session);  // defaulter
				$admn_uniqure_array[]= $row1['admn_no'];
                $arr22[$k]['session_yr'] = $session_year;
                $arr22[$k]['session'] = $session;
                $arr22[$k]['dept'] = $dept;
                $arr22[$k]['course'] = $course;
                $arr22[$k]['branch'] = $branch;
                $arr22[$k]['semester'] = $row1['curr_sem'];
				if($dept=='comm')
				$arr22[$k]['branch'] = $row1['section'];
                $arr22[$k]['admn_no'] = $row1['admn_no'];
                $arr22[$k]['type'] = 'R';
                $arr22[$k]['exam_type'] = 'R';
				$arr22[$k]['hstatus'] =  ($row1['course_id']=='honour'?'Y':'N');
				$arr22[$k]['tot_cr_hr'] =  null;
                $arr22[$k]['tot_cr_pts'] =  null;
                $arr22[$k]['core_tot_cr_hr'] =  null;
                $arr22[$k]['core_tot_cr_pts'] =  null;
                $arr22[$k]['ctotcrpts'] =  null;
                $arr22[$k]['core_ctotcrpts'] =  null;
                $arr22[$k]['ctotcrhr'] =  null;
                $arr22[$k]['core_ctotcrhr'] =  null;
                $arr22[$k]['gpa'] =  null;
                $arr22[$k]['core_gpa'] =  null;
                $arr22[$k]['cgpa'] =  null;
                $arr22[$k]['core_cgpa'] =  null;
                $arr22[$k]['status'] =  null;
                $arr22[$k]['core_status'] = null;



				//  checking whether data preseent from before
				$sqlfirst=" select admn_no,id from final_semwise_marks_foil a where a.admn_no='".$row1['admn_no']."' and  a.session_yr='".$session_year."' and  a.session='".$session."' and a.semester='".$row1['curr_sem']."'   and a.course='".$course."' and a.branch='".( $dept=='comm'?$row1['section']:$branch)."' and a.dept='".$dept."'  ";
				 //echo $sqlfirst; die();

				 $queryfirst = $this->db->query($sqlfirst);
			//   echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
				if ($queryfirst->num_rows()>0){
					 //	  echo $this->db->last_query(); die();
				$queryfirstdata=$queryfirst->result_array();
				$arr21[$k]['cr_hr'] = $row1['credit_hours'];
				$arr21[$k]['total'] = $row1['total'];
				$arr21[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr21[$k]['cr_pts'] = $row1['cr_pts'];
				$arr21[$k]['foil_id'] =$queryfirstdata[0]['id'];
				$arr21[$k]['admn_no'] =  $queryfirstdata[0]['admn_no']  ;
				$arr21[$k]['sub_code'] = $row1['subject_code'];
				$arr21[$k]['current_exam'] = null;
				$arr21[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);
                 $sqlfirst1=null;
				$sqlfirst1=" select a.* from final_semwise_marks_foil_desc a where  a.foil_id='".$queryfirstdata[0]['id']."'  and   a.admn_no='".$queryfirstdata[0]['admn_no']."'  and a.sub_code= '".$row1['subject_code']."'   and  a.grade<>'I' ";
                //echo  $sqlfirst1;
			   $queryfirst2 =null;

				$queryfirst2 = $this->db->query($sqlfirst1);
				 //print_r($queryfirst1->result()); die();
				  //  echo $this->db->last_query();  die();
 //echo $queryfirst2->num_rows(); die();
				 if ($queryfirst2->num_rows() == 0  ) {
                  if(!$this->db->insert('final_semwise_marks_foil_desc',  $arr21[$k]))
				   $returntmsg .= $this->db->_error_message() . ",";
                   $affected[] = $this->db->affected_rows();
				 //  echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
				  }
				  else
				  {
                      $data_to_update= array('cr_hr' => $row1['credit_hours'],'total' => $row1['total'],'grade'=> ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']),'cr_pts' => $row1['cr_pts'],	'remark2' => ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null));

                  //print_r($queryfirst2->result_array()); die();

					$queryfirst1=$queryfirst2->result_array();

				    if(($queryfirst1[0]['cr_hr'] !=  $row1['credit_hours'])|| ( $queryfirst1[0]['total'] !=  $row1['total']) || ( $queryfirst1[0]['grade'] !=  $row1['grade'])||($queryfirst1[0]['cr_pts'] !=  $row1['cr_pts'])) {
                         //echo 'ee'. $queryfirst1[0]['total'].'#'.$row1['total'];	die();
                     //if(
					 if(!$this->db->update('final_semwise_marks_foil_desc' ,$data_to_update,array('foil_id' =>$queryfirst1[0]['foil_id'],'sub_code'=> $row1['subject_code'] ))
					 ){
					//	 echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';						die();
						             $returntmsg .= $this->db->_error_message() . ",";
									 $affected[] = $this->db->affected_rows();
									// echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';
								   }


								   }
				  }


				 // freeze handling

				 	//  checking whether data preseent from before
				$sqlfirst_freeze="select * from final_semwise_marks_foil_freezed a where a.admn_no='".$row1['admn_no']."' and  a.session_yr='".$session_year."' and  a.session='".$session."' and a.semester='".$row1['curr_sem']."'   and a.course='".$course."' and a.branch='".( $dept=='comm'?$row1['section']:$branch)."' and a.dept='".$dept."'  ";

				 //echo $sqlfirst; die();

				 $queryfirst_freeze = $this->db->query($sqlfirst_freeze);
				 $queryfirstdata_freeze=$queryfirst_freeze->result_array();

				if ($queryfirst_freeze->num_rows()>0){
                    if($queryfirstdata_freeze[0]['published_on']=='') {

					 //	  echo $this->db->last_query(); die();

				$arr26[$k]['cr_hr'] = $row1['credit_hours'];
				$arr26[$k]['total'] = $row1['total'];
				$arr26[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr26[$k]['cr_pts'] = $row1['cr_pts'];
				$arr26[$k]['foil_id'] =$queryfirstdata_freeze[0]['id'];
				$arr26[$k]['old_foil_id'] =$queryfirstdata_freeze[0]['old_id'];
				$arr26[$k]['admn_no'] =  $queryfirstdata_freeze[0]['admn_no']  ;
				$arr26[$k]['sub_code'] = $row1['subject_code'];
				$arr26[$k]['current_exam'] = null;
				$arr26[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);

				$sqlfirst1_freeze=" select * from final_semwise_marks_foil_desc_freezed a where  a.foil_id='".$queryfirstdata_freeze[0]['id']."'  and   a.admn_no='".$queryfirstdata_freeze[0]['admn_no']."'  and a.sub_code= '".$row1['subject_code']."'   and  a.grade<>'I' ";

				$queryfirst2_freeze = $this->db->query($sqlfirst1_freeze);

				 if ($queryfirst2_freeze->num_rows() == 0) {
                  if(!$this->db->insert('final_semwise_marks_foil_desc_freezed',  $arr26[$k]))
					        $returntmsg .= $this->db->_error_message() . ",";
                               $affected[] = $this->db->affected_rows();
							//   echo $this->db->last_query(). $this->db->affected_rows().'<br/>';

							   // update time of publish on each subject arrival

							   /*$this->db->select('id'); $this->db->from('final_semwise_marks_foil_freezed');$this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on !='=> '' ) );
							   $query_update = $this->db->get();
                               if ( $query_update->num_rows() > 0 ){
					               $this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on !='=> '' ));
                                   if(!$this->db->update('final_semwise_marks_foil_freezed', array('published_on'=>date("Y-m-d")  ,'actual_published_on'=>date("Y-m-d H:i:s")))){
							        // echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
						             $returntmsg .= $this->db->_error_message() . ",";
								   }
                                      $affected[] = $this->db->affected_rows();
				                 }                */
					           // end

				  }

				  else
				  { // update in  case  grade not released
					 $this->db->select('id'); $this->db->from('final_semwise_marks_foil_freezed');
					 $this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on'=> '' ) );
					  $query_update = $this->db->get();
                      if ( $query_update->num_rows() > 0 ){
                          $data_to_update_freeze= array('cr_hr' => $row1['credit_hours'],'total' => $row1['total'],'grade'=> ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']),'cr_pts' => $row1['cr_pts']);
					  $queryfirst1_freeze=$queryfirst2_freeze->result_array();

				      if (($queryfirst1_freeze[0]['cr_hr'] !=  $row1['credit_hours'])|| ( $queryfirst1_freeze[0]['total'] !=  $row1['total'] )|| ( $queryfirst1_freeze[0]['grade'] !=  $row1['grade'])||($queryfirst1_freeze[0]['cr_pts'] !=  $row1['cr_pts'])) {
                     //    echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
                        if(!$this->db->update('final_semwise_marks_foil_desc' ,$data_to_update_freeze,array('foil_id' =>$queryfirst1_freeze[0]['foil_id'],'sub_code'=> $row1['subject_code'] ))){//	 echo$this->db->last_query().$this->db->affected_rows().'<br/>';
						             $returntmsg .= $this->db->_error_message() . ",";
									 $affected[] = $this->db->affected_rows();
                                    // echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';
								   }

								   }
						}

				  }


					}

					}

				 // end of handlimng freeze
			  }
				else{
				//	echo $this->db->last_query(); die();
                if(!$this->db->insert("final_semwise_marks_foil", $arr22[$k]))
			   $returntmsg .= $this->db->_error_message() . ",";
                $affected[] = $this->db->affected_rows();
			//	echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
					  //echo $this->db->last_query(); die();
				$arr25[$k]['session_yr'] = $session_year;
                $arr25[$k]['session'] = $session;
                $arr25[$k]['dept'] = $dept;
                $arr25[$k]['course'] = $course;
                $arr25[$k]['branch'] = $branch;
                $arr25[$k]['semester'] = $row1['curr_sem'];
					if($dept=='comm')
				$arr25[$k]['branch'] = $row1['section'];
                $arr25[$k]['admn_no'] = $row1['admn_no'];
                $arr25[$k]['type'] = 'R';
                $arr25[$k]['exam_type'] = 'R';
				$arr25[$k]['hstatus'] =  ($row1['course_id']=='honour'?'Y':'N');
				$arr25[$k]['tot_cr_hr'] =  null;
                $arr25[$k]['tot_cr_pts'] =  null;
                $arr25[$k]['core_tot_cr_hr'] =  null;
                $arr25[$k]['core_tot_cr_pts'] =  null;
                $arr25[$k]['ctotcrpts'] =  null;
                $arr25[$k]['core_ctotcrpts'] =  null;
                $arr25[$k]['ctotcrhr'] =  null;
                $arr25[$k]['core_ctotcrhr'] =  null;
                $arr25[$k]['gpa'] =  null;
                $arr25[$k]['core_gpa'] =  null;
                $arr25[$k]['cgpa'] =  null;
                $arr25[$k]['core_cgpa'] =  null;
                $arr25[$k]['status'] =  null;
                $arr25[$k]['core_status'] = null;
				$arr25[$k]['published_on'] = null;
                $arr25[$k]['actual_published_on'] = null;
                $arr25[$k]['result_dec_id'] = null;


                $arr25[$k]['old_id']=$curr22 = $this->db->insert_id();
				if(!$this->db->insert("final_semwise_marks_foil_freezed", $arr25[$k]))
			      $returntmsg .= $this->db->_error_message() . ",";
                  $affected[] = $this->db->affected_rows();
				//  echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
				$curr23_frz = $this->db->insert_id();

				$arr21[$k]['cr_hr'] = $row1['credit_hours'];
				$arr21[$k]['total'] = $row1['total'];
				$arr21[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr21[$k]['cr_pts'] = $row1['cr_pts'];
				$arr21[$k]['foil_id'] =$curr22;
				$arr21[$k]['admn_no'] = $row1['admn_no']  ;
				$arr21[$k]['sub_code'] = $row1['subject_code'];
				$arr21[$k]['current_exam'] = null;
				$arr21[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);
               if(! $this->db->insert('final_semwise_marks_foil_desc',  $arr21[$k]))
				         $returntmsg .= $this->db->_error_message() . ",";
                $affected[] = $this->db->affected_rows();
				//echo $this->db->last_query(). $this->db->affected_rows().'<br/>';

				$arr23[$k]['cr_hr'] = $row1['credit_hours'];
				$arr23[$k]['total'] = $row1['total'];
				$arr23[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr23[$k]['cr_pts'] = $row1['cr_pts'];
				$arr23[$k]['foil_id'] =$curr23_frz;
				$arr23[$k]['old_foil_id'] =$curr22;
				$arr23[$k]['admn_no'] = $row1['admn_no']  ;
				$arr23[$k]['sub_code'] = $row1['subject_code'];
				$arr23[$k]['current_exam'] = null;
				$arr23[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);


				if(!$this->db->insert('final_semwise_marks_foil_desc_freezed',  $arr23[$k]))
					      $returntmsg .= $this->db->_error_message() . ",";
                        $affected[] = $this->db->affected_rows();
						//echo $this->db->last_query(). $this->db->affected_rows().'<br/>';

				}


			$k++;
		}
		       if (in_array(0, $affected) || ($this->db->trans_status() === FALSE)) {
                    //if($this->db->trans_status()!= FALSE ) {
                    $this->db->trans_rollback();
                    $returntmsg = "failed";
                } else {
                    $returntmsg = "success";
                    $this->db->trans_commit();
                }

//			 echo '<pre>';	print_r($affected);echo '</pre>';   echo $returntmsg ;  die();

			return  ($returntmsg== "failed"?0:($k).'_'.(count(array_unique($admn_uniqure_array))) );
        } else {
            return 0;
        }
     }// end of param
	 else
	     return $query->num_rows();



	 } catch (Exception $e) { //echo 'tt'. $e->getMessage(); die();
        //   throw new Exception(0);
           // return $e->getMessage() == null ? 'Internal error ocuured' : $e->getMessage();
		 //return  0;
	    //echo  $e->getMessage() == null ? 'Internal error ocuured' : 'error:'.$e->getMessage();

		 throw new Exception($e->getMessage() == null ? 'Internal error ocuured' : 'error:'.$e->getMessage());
        }


      }



function send_to_foil($sub_code,$session,$session_year,$dept=null,$course=null,$branch=null,$sem=null,$admn_no=null,$param=null,$start=null,$eachset=null,$tailor_process=null){
	  $this->load->model('result_declaration/result_declaration_config');
		$get_reg_info=null;
	//	$course=null;
		//$branch=null;
		//$dept=null;
		/*if($admn_no !=null || $admn_no !=""){
			$admn_no_value=$admn_no;
		if($course==null &&  $branch==null){
	    $get_reg_info= $this->get_regstration_info($session,$session_yr,$row1['admn_no']);
		$dept1=( $dept==null &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->dept_id):$dept);
        $course1=($course==null  &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->course_id): $course);
        $branch1=($branch==null &&   $get_reg_info<>null?( strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->branch_id): $branch);
		}
		}*/

      /* if($admn_no !=null || $admn_no !=""){
        $data= $this->getAdmnNoData($admn_no,$session_year,$session);
        $dept=$data[0]->dept_id;
        $course=$data[0]->course_id;
        $branch=$data[0]->branch_id;
       }*/
    //    echo $dept; exit;


       $this->load->model('attendance/exam_attendance_model');
		try{
			$this->db->trans_begin();
			$returntmsg='';
          if($sub_code<>null)$txt=" a.subject_code='$sub_code' and "; else $txt="" ;
          if($sub_code==null){

		  /* if($dept<>null && $course<>'jrf')
		      $append1="  and  b.dept_id='$dept' "; else $append1="" ;
			  */  ///commneted for working of eso/oe
		  if($course<>null)
		  //$append2="  and  b.course_id='$course' "; else $append2="" ;/oe/eso not working
		  $append2="  and  a.course='$course' "; else $append2="" ;
		  if($branch<>null && ($session_year<='2018-2019'?$course<>'jrf':1) ) 
		  //$append3="  and  b.branch_id='$branch' "; else $append3="" ; /oe/eso not working
		  $append3="  and  a.branch='$branch' "; else $append3="" ;
		  //if($branch<>null &&   $branch<>'mech+te')  $append3="  and  b.branch_id='$branch' "; else $append3="" ;  //hack code for mech+te to met
		  //if($branch<>null &&   $branch=='mech+te')  $append3="  and  (b.branch_id='mech+te'  or b.branch_id='met' )   "; else $append3="" ;//hack code for mech+te to met
		  if($sem<>null  )$append5="  and  rg.semester='".($dept=='comm'? ($session=='Monsoon'?1:2) : $sem )."' "; else $append5="" ;

		  if($sem<>null && $dept=='comm' && $sem<>'all'){
			  $section_append="   join stu_section_data ssd on  ssd.admn_no=v.admn_no and ssd.session_year=v.session_year and  ssd.section='$sem'";
			  $section_select =" ,ssd.section  ";
		  }
		 else{
				$section_append="" ;$section_select="";
		    }


			if(  $admn_no==null && $dept<>'comm' && $course<>'minor')
				$append6="  and  rg.course_id='".$course."'   and  rg.branch_id= '".$branch."'  ";
			    else
			    $append6="" ;


		  if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;
        if ($admn_no == null && $sub_code == null) {
          if($param==null)
             $rep=" limit ".$start." ,".$eachset."";
          else
           $rep='';
        }
        else
        $rep='';

	
	if($tailor_process=='thesis'){
			$thesis_process_admn_list=array();
		  $thesis_process_admn_listobj=$this->get_thesis_taking_admission_no($session,$session_year,$branch);
		 foreach ($thesis_process_admn_listobj as $row) {
            $thesis_process_admn_list[] = $row->admn_no1;
        }
		  $thesis_process_admn_list_str = "'" . implode("','", $thesis_process_admn_list) . "'";
		  // echo $this->db->last_query();
		//   echo '<pre>'; print_r( $thesis_process_admn_list);echo '</pre>';
		 // echo $thesis_process_admn_list_str; die();
		  $thesis_process_str= " and  upper(rg.admn_no) in( ".$thesis_process_admn_list_str.")";
		 }
	
$union="(
SELECT a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(CASE WHEN b.course_id='honour' THEN 'b.tech' ELSE b.course_id END) AS course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year,a.session
FROM cbcs_stu_course a
INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
LEFT JOIN cbcs_marks_master f ON CONCAT('c',a.sub_offered_id)=f.sub_map_id AND a.subject_code=f.subject_id AND a.session_year=f.session_year AND a.`session`=f.`session`
WHERE $txt  a.`session`='$session' and a.session_year='$session_year'  AND b.dept_id='comm' AND b.course_id='comm' AND b.branch_id='comm'  $append4)  union";


}

        $sql="SELECT /*v.*,*/    v.id,v.form_id,v.admn_no, v.sub_offered_id, v.subject_code,v.semester,v.course_id,v.branch_id,v.dept_id,(case when  v.grade ='S' then 0 else  v.credit_hours end) as credit_hours,v.m_m_id,v.session_year,v.session,
		 v.sub_des_id,v.marks_master_id,v.total,v.grade,
		 COALESCE((gp.points*v.credit_hours),0) AS cr_pts , rg.semester AS  curr_sem  $section_select    FROM(
SELECT f.*,g.id AS sub_des_id,g.marks_master_id,g.total,g.grade FROM
(


      $union



 (
  SELECT a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(case when b.course_id='honour' then 'b.tech' else b.course_id end ) as  course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year,a.session

from cbcs_stu_course a
inner join cbcs_subject_offered b on a.sub_offered_id=b.id
left join cbcs_marks_master f on concat('c',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`

where $txt a.`session`='$session' and a.session_year='$session_year'  $append1 $append2 $append3 $append4 )

 union

(SELECT a.id,a.form_id,a.admn_no, CONCAT('o',a.sub_offered_id) AS sub_offered_id, a.subject_code,b.semester,(case when b.course_id='honour' then 'b.tech' else b.course_id end ) as  course_id,b.branch_id,b.dept_id,b.credit_hours,f.id AS m_m_id,a.session_year,a.session
 from old_stu_course a
inner join old_subject_offered b on a.sub_offered_id=b.id

 left join cbcs_marks_master f on concat('o',a.sub_offered_id)=f.sub_map_id and a.subject_code=f.subject_id and a.session_year=f.session_year and a.`session`=f.`session`

where $txt a.`session`='$session' and a.session_year='$session_year' $append1 $append2 $append3 $append4 )
)f
 join cbcs_marks_subject_description g on f.m_m_id=g.marks_master_id and f.admn_no=g.admn_no and g.grade is not null /*and g.grade<>'I'*/ order by f.subject_code,g.grade limit 10000000

)v
JOIN   reg_regular_form rg ON rg.form_id=v.form_id  and rg.hod_status='1' and  rg.acad_status='1' $append5 /*and  rg.session_year=v.session_year and rg.`session`=v.session*/ $append6
$section_append   /*and  rg.admn_no='16je001895'*/
 $thesis_process_str 
 left  JOIN  grade_points gp ON gp.grade=v.grade

 group by  v.admn_no, v.subject_code
  ORDER BY v.dept_id,v.course_id,v.branch_id,curr_sem ,v.admn_no $rep
 ";





 //echo $sql; die();

        $query = $this->db->query($sql);
 //echo  $this->db->last_query();  	  		die();
		//echo $query->num_rows();
		if($param==null){

        if ($query->num_rows() > 0) {
		$affected=null;	$arr22=null;$subctr=0;$k=0;$arr21=null;$arr25=null;$arr23=null;$arr26=null;$admn_uniqure_array=null;$admn_updatable_uniqure_array=null;$admn_ogpa_only_after_result_uniqure_array=null;
		$admn_unique_new_added_array=null;
        //echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();

      //  echo  $this->db->last_query(); die();
	      foreach($query->result_array() as $row1){
			    $defaulterStatus=null;
			    $defaulterStatus=$this->exam_attendance_model->GetdefaulterStatus($row1['admn_no'], $row1['subject_code'],$row1['sub_offered_id'],$session_year,$session);  // defaulter

					 // echo $dept.'#'.$course; die();


				if($course==null &&  $branch==null){
					$get_reg_info=null;
					 $get_section=null;
	               $get_reg_info= $this->get_regstration_info($session,$session_year,$row1['admn_no']);
			       //echo"Q <br>". $this->db->last_query(). $this->db->affected_rows().'<br/>'; die();
				    //echo $get_reg_info->section; die();
				   //if($get_reg_info->section<>null ){
					      if($get_reg_info->section<>null  and  $get_reg_info->section<>''  and   $get_reg_info->section<>'0'){
                       $get_reg_info=null;
					    $get_section=null;
					      $get_section= $this->get_section_cbcs($session,$session_year,$row1['admn_no']);
					//echo'<br>'. $this->db->last_query();
						//  print_r($get_section); die();
					      $get_reg_info->section = $get_section->sub_category_cbcs_offered;
						  $get_reg_info->course_aggr_id='comm' ;

				   }
				}
				  //  echo  $this->db->last_query();
				   // print_r($get_reg_info);

				$admn_uniqure_array[]= $row1['admn_no'];
                $arr22[$k]['session_yr'] = $session_year;
                $arr22[$k]['session'] = $session;
               /*$dept=  $dept1=*/$arr22[$k]['dept'] =  ( $dept==null &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->dept_id):$dept);
               /*$course= */$arr22[$k]['course'] = ($course==null  &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->course_id): $course);
                /*$branch= */$arr22[$k]['branch'] = ($branch==null &&   $get_reg_info<>null?( strstr($get_reg_info->course_aggr_id ,'comm') ? $get_reg_info->section:$get_reg_info->branch_id): $branch);
                $arr22[$k]['semester'] = $row1['curr_sem'];
			//	if($dept=='comm' ||  ($get_reg_info<>null &&  strstr($get_reg_info->course_aggr_id ,'comm') ) )
				//$arr22[$k]['branch'] = ($row1['section']==null?$get_reg_info->section :$row1['section']);


                $arr22[$k]['admn_no'] = $row1['admn_no'];
                $arr22[$k]['type'] = 'R';
                $arr22[$k]['exam_type'] = 'R';
				$arr22[$k]['hstatus'] =  ($row1['course_id']=='honour'?'Y':'N');
				$arr22[$k]['tot_cr_hr'] =  null;
                $arr22[$k]['tot_cr_pts'] =  null;
                $arr22[$k]['core_tot_cr_hr'] =  null;
                $arr22[$k]['core_tot_cr_pts'] =  null;
                $arr22[$k]['ctotcrpts'] =  null;
                $arr22[$k]['core_ctotcrpts'] =  null;
                $arr22[$k]['ctotcrhr'] =  null;
                $arr22[$k]['core_ctotcrhr'] =  null;
                $arr22[$k]['gpa'] =  null;
                $arr22[$k]['core_gpa'] =  null;
                $arr22[$k]['cgpa'] =  null;
                $arr22[$k]['core_cgpa'] =  null;
                $arr22[$k]['status'] =  null;
                $arr22[$k]['core_status'] = null;

 //echo '<pre>';print_r($arr22); echo '</pre>';die();

				//  checking whether data preseent from before
				$sqlfirst=" select admn_no,id from final_semwise_marks_foil a where a.admn_no='".$row1['admn_no']."' and  a.session_yr='".$session_year."' and
          a.session='".$session."' and a.semester='".$row1['curr_sem']."'  ".($sub_code==null? " and a.course='".$course."' and a.branch='".( $dept=='comm'?$row1['section']:$branch)."'
          and a.dept='".$dept."'  ":"" )." "  ;
				 //echo $sqlfirst; die();

				 $queryfirst = $this->db->query($sqlfirst);
			//  echo"Q <br>". $this->db->last_query(). $this->db->affected_rows().'<br/>'; die();

				if ($queryfirst->num_rows()>0){
					 //	  echo $this->db->last_query(); die();
				$queryfirstdata=$queryfirst->result_array();
				$arr21[$k]['cr_hr'] = $row1['credit_hours'];
				$arr21[$k]['total'] = $row1['total'];
				$arr21[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr21[$k]['cr_pts'] = $row1['cr_pts'];
				$arr21[$k]['foil_id'] =$queryfirstdata[0]['id'];
				$arr21[$k]['admn_no'] =  $queryfirstdata[0]['admn_no']  ;
				$arr21[$k]['sub_code'] = $row1['subject_code'];
				$arr21[$k]['current_exam'] = ($session=='Summer'?'Y':null);
				$arr21[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);
                 $sqlfirst1=null;
				$sqlfirst1=" select a.* from final_semwise_marks_foil_desc a where  a.foil_id='".$queryfirstdata[0]['id']."'  and   a.admn_no='".$queryfirstdata[0]['admn_no']."'  and a.sub_code= '".$row1['subject_code']."'   and  a.grade<>'I' ";
              //  echo  $sqlfirst1; die();
			   $queryfirst2 =null;


				$queryfirst2 = $this->db->query($sqlfirst1);
				//echo $this->db->last_query();  die();

				 //print_r($queryfirst1->result());
				   //echo $this->db->last_query();  die();
                  //echo $queryfirst2->num_rows(); die();
				 if ($queryfirst2->num_rows() == 0  ) {
					 $queryfirst11=$queryfirst2->result_array();
					//  if($queryfirst11[0]['grade']=='I' && $arr21[$k]['grade'] =='I'){}
					 // else{
                  if(!$this->db->insert('final_semwise_marks_foil_desc',  $arr21[$k]))
				   $returntmsg .= $this->db->_error_message() . ",";
                   $affected[] = $this->db->affected_rows();
				 //  echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
				    $k++; //addedd
					 // }
				  }
				  else
				  {
                      $data_to_update= array('cr_hr' => $row1['credit_hours'],'total' => $row1['total'],'grade'=> ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']),'cr_pts' => $row1['cr_pts'],	'remark2' => ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null));

                  //print_r($queryfirst2->result_array()); die();

					$queryfirst1=$queryfirst2->result_array();

				    if(($queryfirst1[0]['cr_hr'] !=  $row1['credit_hours'])|| ( $queryfirst1[0]['total'] !=  $row1['total']) || ( $queryfirst1[0]['grade'] !=  $row1['grade'])||($queryfirst1[0]['cr_pts'] !=  $row1['cr_pts'])) {
                         //echo 'ee'. $queryfirst1[0]['total'].'#'.$row1['total'];	die();
                     //if(
					 if(!$this->db->update('final_semwise_marks_foil_desc' ,$data_to_update,array('foil_id' =>$queryfirst1[0]['foil_id'],'sub_code'=> $row1['subject_code'] ))
					 ){
						// echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';						die();
						             $returntmsg .= $this->db->_error_message() . ",";
									 $affected[] = $this->db->affected_rows();
									// echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';
								   }

	               $subctr++; // updated
			//	   $admn_updatable_uniqure_array[]= $row1['admn_no'];      // in case updated value  available
					}

				  }


				 // freeze handling

				 	//  checking whether data preseent from before
				$sqlfirst_freeze="select * from final_semwise_marks_foil_freezed a where a.admn_no='".$row1['admn_no']."' and  a.session_yr='".$session_year."' and  a.session='".$session."' and a.semester='".$row1['curr_sem']."'
        ".(  $sub_code==null?  " and a.course='".$course."' and a.branch='".( $dept=='comm'?$row1['section']:$branch)."' and a.dept='".$dept."'  ":"" )."  order by a.actual_published_on desc limit 1 "    ;

				 //echo $sqlfirst_freeze; die();

				 $queryfirst_freeze = $this->db->query($sqlfirst_freeze);
				 $queryfirstdata_freeze=$queryfirst_freeze->result_array();

				if ($queryfirst_freeze->num_rows()>0){
                    if($queryfirstdata_freeze[0]['published_on']=='' || Result_declaration_config::$result_dec_override   ) {

					 //	  echo $this->db->last_query(); die();

				$arr26[$k]['cr_hr'] = $row1['credit_hours'];
				$arr26[$k]['total'] = $row1['total'];
				$arr26[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr26[$k]['cr_pts'] = $row1['cr_pts'];
				$arr26[$k]['foil_id'] =$queryfirstdata_freeze[0]['id'];
				$arr26[$k]['old_foil_id'] =$queryfirstdata_freeze[0]['old_id'];
				$arr26[$k]['admn_no'] =  $queryfirstdata_freeze[0]['admn_no']  ;
				$arr26[$k]['sub_code'] = $row1['subject_code'];
				$arr26[$k]['current_exam'] = ($session=='Summer'?'Y':null);
				$arr26[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);

				$sqlfirst1_freeze=" select * from final_semwise_marks_foil_desc_freezed a where  a.foil_id='".$queryfirstdata_freeze[0]['id']."'  and   a.admn_no='".$queryfirstdata_freeze[0]['admn_no']."'  and a.sub_code= '".$row1['subject_code']."'   and  a.grade<>'I' ";

				$queryfirst2_freeze = $this->db->query($sqlfirst1_freeze);
                 //echo $this->db->last_query(); die();
				 if ($queryfirst2_freeze->num_rows() == 0) {
					  $queryfirst1_freeze11=$queryfirst2_freeze->result_array();
					// if($queryfirst1_freeze11[0]['grade']=='I' && $arr26[$k]['grade'] =='I'){}
					//  else {
                       if(!$this->db->insert('final_semwise_marks_foil_desc_freezed',  $arr26[$k]))
					        $returntmsg .= $this->db->_error_message() . ",";
                            $affected[] = $this->db->affected_rows();
						   $admn_updatable_uniqure_array[]= strtolower($row1['admn_no']);      // in case updated value  available
							//   echo $this->db->last_query(). $this->db->affected_rows().'<br/>';

							   // update time of publish on each subject arrival

							   /*$this->db->select('id'); $this->db->from('final_semwise_marks_foil_freezed');$this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on !='=> '' ) );
							   $query_update = $this->db->get();
                               if ( $query_update->num_rows() > 0 ){
					               $this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on !='=> '' ));
                                   if(!$this->db->update('final_semwise_marks_foil_freezed', array('published_on'=>date("Y-m-d")  ,'actual_published_on'=>date("Y-m-d H:i:s")))){
							        // echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
						             $returntmsg .= $this->db->_error_message() . ",";
								   }
                                      $affected[] = $this->db->affected_rows();
				                 }                */
					           // end
				   //  }

				  }

				  else
				  { // update in  case  grade not released
					 $this->db->select('id'); $this->db->from('final_semwise_marks_foil_freezed');
					 $this->db->where(array('id' =>$queryfirstdata_freeze[0]['id'],'published_on  is null'=> null  ),false );
					  $query_update = $this->db->get();
                      if ( $query_update->num_rows() > 0  || Result_declaration_config::$result_dec_override   ){
                          $data_to_update_freeze= array('cr_hr' => $row1['credit_hours'],'total' => $row1['total'],'grade'=> ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']),'cr_pts' => $row1['cr_pts']);
					      $queryfirst1_freeze=$queryfirst2_freeze->result_array();
                        //echo $this->db->last_query(); die();
				      if (($queryfirst1_freeze[0]['cr_hr'] !=  $row1['credit_hours'])|| ( $queryfirst1_freeze[0]['total'] !=  $row1['total'] )|| ( $queryfirst1_freeze[0]['grade'] !=  $row1['grade'])||($queryfirst1_freeze[0]['cr_pts'] !=  $row1['cr_pts'])) {
                         //echo '<pre>';  print_r($data_to_update_freeze);  echo '</pre>'; 		die();
                        if(!$this->db->update('final_semwise_marks_foil_desc_freezed' ,$data_to_update_freeze,array('foil_id' =>$queryfirst1_freeze[0]['foil_id'],'sub_code'=> $row1['subject_code'] ))){	 //echo $this->db->last_query().$this->db->affected_rows().'<br/>'; die();
						             $returntmsg .= $this->db->_error_message() . ",";
									 $affected[] = $this->db->affected_rows();
                                    // echo $this->db->last_query(); echo $this->db->affected_rows().'<br/>';
								   }
								     $admn_updatable_uniqure_array[]=  strtolower($row1['admn_no']);      // in case updated value  available

								   }
				}


				  }


					}
					else{ // in case grade released
					 $admn_updatable_after_result_uniqure_array[]= strtolower($row1['admn_no']);
                     // code is  just indicative not do anything in case grade already released from before
/*
				   $sqlfirst1_freeze=" select * from final_semwise_marks_foil_desc_freezed a where  a.foil_id='".$queryfirstdata_freeze[0]['id']."'  and   a.admn_no='".$queryfirstdata_freeze[0]['admn_no']."'  and a.sub_code= '".$row1['subject_code']."'   and  a.grade<>'I' ";
				      $queryfirst2_freeze = $this->db->query($sqlfirst1_freeze);
					  $queryfirst1_freeze=$queryfirst2_freeze->result_array();
                       //echo $this->db->last_query(); die();
				      if (($queryfirst1_freeze[0]['cr_hr'] !=  $row1['credit_hours'])|| ( $queryfirst1_freeze[0]['total'] !=  $row1['total'] )|| ( $queryfirst1_freeze[0]['grade'] !=  $row1['grade'])||($queryfirst1_freeze[0]['cr_pts'] !=  $row1['cr_pts'])) {
						//   echo $row1['admn_no']; die();   //    echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
						 $admn_updatable_after_result_uniqure_array[]= $row1['admn_no'];      // in case updated value  available
                       }

                      // code is  just indicative not do anything in case grade already released from before
				*/


                     }


				}// end of handlimng freeze
			  } //end of data present from before

			  else{  // start of data not present from before
				//	echo $this->db->last_query(); die();

				//print_r($arr22[$k]);die();
                if(!$this->db->insert("final_semwise_marks_foil", $arr22[$k]))
			   $returntmsg .= $this->db->_error_message() . ",";
                $affected[] = $this->db->affected_rows();
			//	echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
					  //echo $this->db->last_query(); die();
				$arr25[$k]['session_yr'] = $session_year;
                $arr25[$k]['session'] = $session;
                $arr25[$k]['dept'] =  ( $dept==null &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->dept_id):$dept);
                $arr25[$k]['course'] = ($course==null  &&   $get_reg_info<>null?(  strstr($get_reg_info->course_aggr_id ,'comm') ? 'comm':$get_reg_info->course_id): $course);
                $arr25[$k]['branch'] = ($branch==null &&   $get_reg_info<>null?( strstr($get_reg_info->course_aggr_id ,'comm') ? $get_reg_info->section:$get_reg_info->branch_id): $branch);
                $arr25[$k]['semester'] = $row1['curr_sem'];
				//if($dept=='comm' ||  ($get_reg_info<>null &&  strstr($get_reg_info->course_aggr_id ,'comm') ) )
				//$arr25[$k]['branch'] =  ($row1['section']==null?$get_reg_info->section :$row1['section']);
                $arr25[$k]['admn_no'] = $row1['admn_no'];
				$admn_unique_new_added_array[]= strtolower($row1['admn_no']);
                $arr25[$k]['type'] = 'R';
                $arr25[$k]['exam_type'] = 'R';
				$arr25[$k]['hstatus'] =  ($row1['course_id']=='honour'?'Y':'N');
				$arr25[$k]['tot_cr_hr'] =  null;
                $arr25[$k]['tot_cr_pts'] =  null;
                $arr25[$k]['core_tot_cr_hr'] =  null;
                $arr25[$k]['core_tot_cr_pts'] =  null;
                $arr25[$k]['ctotcrpts'] =  null;
                $arr25[$k]['core_ctotcrpts'] =  null;
                $arr25[$k]['ctotcrhr'] =  null;
                $arr25[$k]['core_ctotcrhr'] =  null;
                $arr25[$k]['gpa'] =  null;
                $arr25[$k]['core_gpa'] =  null;
                $arr25[$k]['cgpa'] =  null;
                $arr25[$k]['core_cgpa'] =  null;
                $arr25[$k]['status'] =  null;
                $arr25[$k]['core_status'] = null;
				$arr25[$k]['published_on'] = null;
                $arr25[$k]['actual_published_on'] = null;
                $arr25[$k]['result_dec_id'] = null;


                $arr25[$k]['old_id']=$curr22 = $this->db->insert_id();
				if(!$this->db->insert("final_semwise_marks_foil_freezed", $arr25[$k]))
			      $returntmsg .= $this->db->_error_message() . ",";
                  $affected[] = $this->db->affected_rows();
				//  echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
				$curr23_frz = $this->db->insert_id();

				$arr21[$k]['cr_hr'] = $row1['credit_hours'];
				$arr21[$k]['total'] = $row1['total'];
				$arr21[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr21[$k]['cr_pts'] = $row1['cr_pts'];
				$arr21[$k]['foil_id'] =$curr22;
				$arr21[$k]['admn_no'] = $row1['admn_no']  ;
				$arr21[$k]['sub_code'] = $row1['subject_code'];
				$arr21[$k]['current_exam'] = ($session=='Summer'?'Y':null);
				$arr21[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);
               if(! $this->db->insert('final_semwise_marks_foil_desc',  $arr21[$k]))
				         $returntmsg .= $this->db->_error_message() . ",";
                $affected[] = $this->db->affected_rows();
			//	echo $this->db->last_query(). $this->db->affected_rows().'<br/>';

				$arr23[$k]['cr_hr'] = $row1['credit_hours'];
				$arr23[$k]['total'] = $row1['total'];
				$arr23[$k]['grade'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'F':$row1['grade']);
				$arr23[$k]['cr_pts'] = $row1['cr_pts'];
				$arr23[$k]['foil_id'] =$curr23_frz;
				$arr23[$k]['old_foil_id'] =$curr22;
				$arr23[$k]['admn_no'] = $row1['admn_no']  ;
				$arr23[$k]['sub_code'] = $row1['subject_code'];
				$arr23[$k]['current_exam'] = ($session=='Summer'?'Y':null);
				$arr23[$k]['remark2'] = ($defaulterStatus=='y' && $defaulterStatus!='1'?'y':null);


				if(!$this->db->insert('final_semwise_marks_foil_desc_freezed',  $arr23[$k]))
					      $returntmsg .= $this->db->_error_message() . ",";
                        $affected[] = $this->db->affected_rows();
						//echo $this->db->last_query(). $this->db->affected_rows().'<br/>';
                  $k++;
				}  // start of data not present from before
       //     echo '<br><pre>';	print_r($affected);echo '</pre>';   echo $returntmsg ;
		     }// end of master loop
		 //die();
		 if (in_array(0, $affected) || ($this->db->trans_status() === FALSE)) {
                    //if($this->db->trans_status()!= FALSE ) {
                    $this->db->trans_rollback();
                    $returntmsg = "failed";
                } else {
                    $returntmsg = "success";
                    $this->db->trans_commit();
                }

		 // echo '<pre>';	print_r($affected);echo '</pre>';   echo $returntmsg ;
		 // echo '<pre>';	print_r(array_unique($admn_uniqure_array));echo '</pre>';   echo $returntmsg ;  die();
		 // echo 'added_sub:'.$k.'#updated_sub'.$subctr.'#subcode:'.$sub_code.'#admn_no:'.$admn_no; print_r($admn_uniqure_array);print_r($admn_updatable_uniqure_array);print_r($admn_unique_new_added_array); die();


			//return  ($returntmsg== "failed"?0: (    $sub_code<>null?array_unique($admn_uniqure_array):   (  $sub_code==null && $admn_no<>null ?(($k).'_'.count(array_unique($admn_unique_new_added_array)).'_'.($subctr).'_'.count(array_unique($admn_updatable_uniqure_array))  )  :   (($k).'_'.(count(array_unique($admn_unique_new_added_array))).'_'.($subctr).'_'.count(array_unique($admn_updatable_uniqure_array))  )  )   ));

   $data=null;
           if($returntmsg== "failed")
			    return 0;
		   else{
			   if( $sub_code<>null)
			       return array_unique($admn_uniqure_array);
			   else
			   {
				   $data['admn_unique_new_added_array']=array_unique($admn_unique_new_added_array);
				   $data['admn_updatable_uniqure_array']=array_unique($admn_updatable_uniqure_array);
				   $data['admn_updatable_after_result_uniqure_array']=array_unique($admn_updatable_after_result_uniqure_array);
				   $data['admn_ogpa_only_after_result_uniqure_array']=array_unique($admn_ogpa_only_after_result_uniqure_array);

				   $data['admn_list_both']= (array_unique($admn_updatable_uniqure_array)<>null?array_merge( $data['admn_unique_new_added_array'],$data['admn_updatable_uniqure_array']):$data['admn_unique_new_added_array']);
				   $data['sub_ctr_addded']=$k;
				   $data['sub_ctr_updated']=$subctr;
				   return $data;

				//  echo'<pre>'; print_r($data); echo'</pre>'; die();
			   }

		   }




        } else {
            return 0;
        }
     }// end of param
	 else
	     return $query->num_rows();



	 } catch (Exception $e) { //echo 'tt'. $e->getMessage(); die();
        //   throw new Exception(0);
           // return $e->getMessage() == null ? 'Internal error ocuured' : $e->getMessage();
		 //return  0;
	    //echo  $e->getMessage() == null ? 'Internal error ocuured' : 'error:'.$e->getMessage();

		 throw new Exception($e->getMessage() == null ? 'Internal error ocuured' : 'error:'.$e->getMessage());
        }


      }


	  /* function update_freeze($session,$session_year,$dept,$course,$branch){
			  $this->load->model('attendance/exam_attendance_model');
			  // echo $session.','.$session_year.','.$dept.','.$course.','.$branch; die();


		try{
			$returntmsg='';

				//  checking whether data preseent from before
				$sqlfirst_freeze="select admn_no,id ,old_id from final_semwise_marks_foil_freezed a where   a.session_yr='".$session_year."' and  a.session='".$session."'  and
				 a.course='".$course."' and a.branch='".$branch."' and a.dept='".$dept."' ";
				 //echo $sqlfirst; die();
				 $queryfirst_freeze = $this->db->query($sqlfirst_freeze);
				if ($queryfirst_freeze->num_rows()>0){
					 //	  echo $this->db->last_query(); die();
				$queryfirstdata_freeze=$queryfirst_freeze->result_array();
				$k=0;
				foreach($queryfirstdata_freeze as $row1){

				         $this->db->where(array('id' =>$row1[$k]['id'],'published_on !='=> '' ));
                         if(!$this->db->update('final_semwise_marks_foil_freezed', array('published_on'=>date("Y-m-d")  ,'actual_published_on'=>date("Y-m-d H:i:s"))))
							   //echo $this->db->last_query(); die();
						    $returntmsg .= $this->db->_error_message() . ",";
                            $affected[] = $this->db->affected_rows();
								 // end of handlimng freeze
                  $k++;
				}

		       if (in_array(0, $affected) || ($this->db->trans_status() === FALSE)) {
                    //if($this->db->trans_status()!= FALSE ) {
                    $this->db->trans_rollback();
                    $returntmsg = "failed";
                } else {
                    $returntmsg = "success";
                    $this->db->trans_commit();
                }

		     	return  ($returntmsg== "failed"?0:($k-1));

			}
		    else
                return 0;
				} catch (Exception $e) {
            return $e->getMessage() == null ? 'Internal error ocuured' : $e->getMessage();
				}
      }
*/


function update_freeze($session,$session_year,$dept,$course,$branch,$sem,$sec,$admn_no_single=null,$mode=null){
	// echo 'mod'.$mode;die();
	       $this->load->model('attendance/exam_attendance_model');
		   $this->load->model('result_declaration/result_declaration_config');
		   $cid=$course;
		   //  echo $session.','.$session_year.','.$dept.','.$course.','.$branch.'.'.$sem.','.$sec; die();
		    try{
				$wrong_release_list=null;$wrong_release_list_arr=null;
			   // $this->db->trans_begin();
					$this->db->trans_start();
			     $returntmsg='';
               //  $addsec=  ($dept=='comm'? "  and  a.branch = '$sec' " : " " );

                    $addcourse=($course=='jrf' || $course=='comm'?"":"  and a.semester='".$sem."'  ");

				// $hons_only=" and a.hstatus='Y' ";
				if($admn_no_single<>null)
				$admn_no_single_txt= "  and a.admn_no='$admn_no_single'  ";


				 //  checking whether data preseent from before
				  $sqlfirst_freeze="select a.* from(
				  select admn_no,id ,old_id ,semester from final_semwise_marks_foil_freezed a where   /*a.published_on is null and*/   a.session_yr='".$session_year."' and  a.session='".$session."'  and
				  a.course='".$course."' and a.branch='".($dept=='comm'?$sec:$branch)."' and a.dept='".$dept."' $addcourse     $hons_only   $admn_no_single_txt
                    order by a.admn_no,a.semester,a.actual_published_on desc limit 10000000	)a group by a.admn_no,a.semester			  "  ;
				  		//echo	 $sqlfirst_freeze; die();

				  $queryfirst_freeze = $this->db->query($sqlfirst_freeze);
				   //echo $this->db->last_query();die();
				  if ($queryfirst_freeze->num_rows()>0){
				      $queryfirstdata_freeze=$queryfirst_freeze->result_array();
				      //echo $this->db->last_query(); echo '<pre>';print_r($queryfirstdata_freeze);echo '</pre>'; die();

					   if(( strtoupper($cid)==strtoupper('b.tech') ||  strtoupper($cid)== strtoupper('dualdegree') ||  strtoupper($cid)== strtoupper('int.m.sc')||  strtoupper($cid)== strtoupper('int.msc.tech')||  strtoupper($cid)== strtoupper('int.m.tech')  )  && $sem>4){


					// getting studentds whose redeclaration gone wrong for monsoon/winter 18-19 thus ogpa will be calaculated  based on based_on_publish_date,however students not falls on this category will be  based on actual published_on
					  /* $wrong_release_list=$this->get_summer_release_issue_admn_list();
					     foreach($wrong_release_list as $ll){
							 $wrong_release_list_arr[]=$ll->admn_no;
						 }*/

						  $wrong_release_list_arr=Result_declaration_config::$hons_wrong_redec_list;

					// echo '#wrong_release_list_arr:';echo'<pre>'; print_r($wrong_release_list_arr);echo'</pre>'.'<br/>';die();
					   }
					   else{
						   $wrong_release_list_arr=null;
					   }




				      $k=0;$t=0;$alt=0;$nupd=0;$not_dec=0;$hrc=0;		$crr_time= date("Y-m-d")." 10:22:42";/* date("Y-m-d H:i:s")*/;  
					  $curr_dt=/*"2020-08-20" */ date("Y-m-d");
					  $hons_release_case=$online=$alternate=$not_updated=$affected=null;
            //echo $this->db->last_query(); echo '<pre>';print_r($queryfirst_freeze->result_array() );echo '</pre>'; die();
				      foreach($queryfirst_freeze->result_array() as $row1){
                         if( ($cid=='minor'?$this->check_eligibility_for_grading($session_year,$session,$row1['admn_no'],'minor'):$this->check_eligibility_for_grading($session_year,$session,$row1['admn_no'])) ){

					//	   echo $this->db->last_query(); die();
	 // to be onn later
					           if(Result_declaration_config::$grade_published_on){
							 $this->db->where(array('id' =>$row1['id'],'published_on  is null '=> null  ),false );
                             if(!$this->db->update('final_semwise_marks_foil_freezed', array('published_on'=> $curr_dt ,'actual_published_on'=>$crr_time)))
								 $returntmsg .= $this->db->_error_message() . ",";
							   }
                           //   $affected[] = $this->db->affected_rows();
							//   echo $this->db->last_query(); die();
						   //  if($this->db->affected_rows()>0){						 // to be onn later

						    $p=$q=$r=$s=null;

							/*if(!in_array($row1['admn_no'],$wrong_release_list_arr)) {echo 'testingmode';   echo '#wrong_release_list_arr:';echo'<pre>'; print_r($wrong_release_list_arr);echo'</pre>'.'<br/>';die();}*/

							//ogpa/gpa update
							$p=$affected[]= $this->get_foil_sgpa($course,$session_year,$session,$row1['admn_no'],$row1['old_id']);							 // update sgpa of foil
							// $returntmsg .= $this->db->_error_message() . ","; echo '<br/><br/>'. $this->db->last_query();
							$q=$affected[]= $this->upadte_freeze_gpa_based_on_foil($course,$session_year,$session,$row1['admn_no'],$row1['id']);            // update  freeze sgpa based on  foil update
							 $returntmsg .= $this->db->_error_message() . "," ;  //echo '<br/><br/>'.  $this->db->last_query(); //die();

							if(!in_array(strtoupper($row1['admn_no']),$wrong_release_list_arr)){
								$r=$affected[]= $this->get_freeze_cgpa($course,$session_year,$session,$row1['admn_no'],$row1['id'],null,null,($session=='Summer'?null: ($dept=='comm'?$row1['semester']:$sem) ));
								// update cgpa of freeze based on actual_publish_date
							//	$returntmsg .= $this->db->_error_message() . ","; echo '<br/><br/>'. $this->db->last_query();
								}
							else{
								$r=$affected[]= $this->get_freeze_cgpa($course,$session_year,$session,$row1['admn_no'],$row1['id'],$param='based_on_publish_date',null,($session=='Summer'?null: ($dept=='comm'?$row1['semester']:$sem) ));//update cgpa of freeze based on publish_date
								if($this->db->affected_rows()>0){
								$returntmsg .= $this->db->_error_message() . ",";//echo '<br/><br/>'. $this->db->last_query();
								$hons_release_case[]=$row1['admn_no'];
									$hrc++;
								}
							   }



							$s=$affected[]= $this->upadte_foil_cgpa_based_on_freeze($course,$session_year,$session,$row1['admn_no'],$row1['old_id'],$row1['id']);                    //update cgpa of foil
							$returntmsg .= $this->db->_error_message() . ",";//echo '<br/><br/>'. $this->db->last_query();




						  // } //die();
							$k++;

								if(($p=='1')&&($q=='1')&&($r=='1')&&($s=='1')){
									$online[]=$row1['admn_no'];
									$t++;
								}



								elseif(($p=='0')&&($q=='0')&&($r=='1')&&($s=='1')){
									$alternate[]=$row1['admn_no'];
									$alt++;
								}
								else{
									$not_updated[]=$row1['admn_no'];
									$nupd++;

								}



                   /* echo '<br/>#online:'.$t.'#online_updated_admn_no:'; echo'<pre>';print_r($online);echo'</pre>'.'<br/>';
					echo '#alternative:'.$alt.'#alternative_updated_admn_no:'; echo'<pre>'; print_r($alternate);echo'</pre>'.'<br/>';
					echo '#hons_release_case:'.$hrc.'#hons_release_case:'; echo'<pre>'; print_r($hons_release_case);echo'</pre>'.'<br/>';								*/
                 //     echo '#'.$row1['admn_no'].'<br/>';echo $p.'#'.$q.'#'.$r.'#'.$s; echo '<br/>';

						 }
						 else{
							 $not_dec_list[]=$row1['admn_no'];
							 $not_dec++;

						 }


				      }//die();
                   //    print_r($affected)                  ; die();

				/*  if (in_array(0, $affected) || ($this->db->trans_status() === FALSE)) {
                    //if($this->db->trans_status()!= FALSE ) {
                    $this->db->trans_rollback();
                    $returntmsg = "failed";
                } else {
                    $returntmsg = "success";
                    $this->db->trans_commit();
                }
				*/

			 $this->db->trans_complete();
		   if($mode==null){

			if($this->db->trans_status()===FALSE){
					 echo 0;
				}
				else
				{
					echo'<br/>#dept:'.$dept.'#course:'.$course.'#branch:'.$branch.'#semester:'.$sem.($sec<>null?'#section:'.$sec:"") ;
					echo '<br/>#online:'.$t.'#online_updated_admn_no:'; echo'<pre>';print_r($online);echo'</pre>'.'<br/>';
					echo '#alternative:'.$alt.'#alternative_updated_admn_no:'; echo'<pre>'; print_r($alternate);echo'</pre>'.'<br/>';
					echo '#hons_release_case:'.$hrc.'#hons_release_case:'; echo'<pre>'; print_r($hons_release_case);echo'</pre>'.'<br/>';

					echo '#not_updated:'.$nupd.'#not_updated_admn_no:';echo'<pre>'; print_r($not_updated);echo'</pre>'.'<br/>';
					echo '#not_declared:'.$not_dec.'#not_declared_admn_no:';echo'<pre>'; print_r($not_dec_list);echo'</pre>'.'<br/>';
					die();

				}
		   }
		   else{
				 if($this->db->trans_status()===FALSE) return 0;
				 else  return ( $t==0?$alt:$t );
		      }
		  }
		  else
             return 0;
		} catch (Exception $e) {
          return $e->getMessage() == null ? 'Internal error ocuured' : $e->getMessage();
		}
       }


  function get_summer_release_issue_admn_list($admn_no=null){
	  if($admn_no<>null)
		  $admn_str= " and f.admn_no='".$admn_no."' ";
	  else
		  $admn_str= " ";
		  $sql="
    select  f.dept,f.course,f.branch,   upper(f.admn_no)as  admn_no,f.semester,f.session_yr,f.session,  f.published_on, f.actual_published_on ,
         g.dept as dept1 ,f.course as crs1,f.branch as branch1 ,g.admn_no as admn_no1 ,g.semester as sem1 , g.session_yr as yr1,g.session as sess1,
 f.published_on as pub_date1,g.actual_published_on as date1 from final_semwise_marks_foil_freezed  f join final_semwise_marks_foil_freezed g on
  f.admn_no=g.admn_no and f.semester=g.semester  and  f.session_yr=g.session_yr  /* and    f.published_on=g.published_on */
  and
  f.session<>g.session   and
  f.actual_published_on<>g.actual_published_on   and
  f.actual_published_on like '2020%' and  f.session_yr<>'2019-2020' and f.semester>4   group by f.admn_no,f.semester, f.actual_published_on
   having   f.actual_published_on >date1 $admn_str
   order by f.dept,f.course,f.branch,f.semester,f.session_yr,f.session,f.admn_no ";
		    $query = $this->db->query($sql);

			  //  echo $sql;die();
		 //echo $query->num_rows();
		   //echo '<br>'. $this->db->last_query();
               if ($query->num_rows() > 0) {

              //  echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return ($admn_no<>null?1:$query->result());
	          }
              else  return 0;
	   }

	   function marks_monitor($session,$session_year,$dept=null,$course=null,$branch=null,$admn_no=null){
		  if($dept<>null)$append1="  and  a.dept='$dept' "; else $append1="" ;
		  if($dept<>null)$append0="  and  a.dept_id='$dept' "; else $append0="" ;
		  if($course<>null)$append2="  and  a.course='$course' "; else $append2="" ;
		  if($branch<>null)$append3="  and  a.branch='$branch' "; else $append3="" ;
		   if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;


		   $sql="
			SELECT c.std_dept   , cd.name,   u.dept, c.course,c.branch,cb.name AS br_name   ,c.subject_code,  c.sub_name,c.sub_type,u.published_on,c.resrc  FROM
				(SELECT v.course,v.branch,v.subject_code,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc
				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=? $append0 $append2 $append3 $append4  )
				 UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?   $append0 $append2 $append3 $append4 ))v GROUP BY v.course,v.branch ,v.subject_code)c

				left JOIN(
				SELECT a.*,f.sub_code FROM
               (SELECT a.*  FROM final_semwise_marks_foil_freezed a  WHERE a.`session`=?  and a.session_yr=? $append1 $append2 $append3 $append4) a
                 JOIN final_semwise_marks_foil_desc_freezed f ON  f.foil_id=a.id
                 GROUP BY a.dept,a.course,a.branch,f.sub_code )u

				 ON u.course=c.course  AND  u.branch=c.branch and  u.sub_code=c.subject_code
				 LEFT JOIN cbcs_departments cd ON cd.id=c.std_dept
                 LEFT JOIN cbcs_branches cb ON cb.id=c.branch

				 order by u.dept,u.course,u.branch,c.subject_code ";

			   $query = $this->db->query($sql,array($session,$session_year,$session,$session_year,$session,$session_year));

			  //  echo $sql;die();
		 //echo $query->num_rows();
               if ($query->num_rows() > 0) {
             //   echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->result();
	          }
       else  return 0;
			   }


/*

	   function get_release_info($session,$session_year,$dept=null,$course=null,$branch=null,$sem=null,$sec=null,$admn_no=null){
		  if($dept<>null)$append1="  and  a.dept='$dept' "; else $append1="" ;
		  if($dept<>null)$append0="  and  b.dept_id='$dept' "; else $append0="" ;
		  if($course<>null)$append2="  and  a.course='$course' "; else $append2="" ;
		  if($branch<>null)$append3= "  and  a.branch='".$branch."' "; else $append3="" ;
		  if($branch<>null)$append3_1= "  and  a.branch='".($dept=='comm'? $sec:$branch)."' "; else $append3_1="" ;

		     if($sem<>null  )$append5="  and  a.semester='".($dept=='comm'? ($session=='Monsoon'?1:2) : $sem )."' "; else $append5="" ;
		  if($sem<>null && $dept=='comm' && $sem<>'all'){
			  $section_select =" ,ssd.section  ";
		  }
		  else{
				$section_select="";
		    }
		  if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;


		   $sql="
		     	SELECT c.std_dept   , cd.name,   u.dept, c.course,c.branch,cb.name AS br_name   ,c.subject_code,  c.sub_name,c.sub_type, date_format(u.published_on,'%d-%m-%Y') as  published_on,c.resrc,c.semester,c.section,(case when  u.course IS NOT null then u.tot_stu_ctr ELSE   'N/A' END ) AS tot_stu_ctr, (case when  u.course IS NOT null then u.formal_stu_ctr ELSE   'N/A' END ) AS formal_stu_ctr
				FROM
				(SELECT v.course,v.branch,v.subject_code,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc,v.section,v.semester

				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type,d.section,b.semester
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  cbcs_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?  AND  a.course<>'honour' $append0 $append2 $append3 $append4  )
				 UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type,d.section,b.semester
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  old_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=? AND  a.course<>'honour'   $append0 $append2 $append3 $append4 ))v GROUP BY v.course,v.branch,(case when v.course='comm' then v.section else v.semester  END))c

				left JOIN
				(SELECT a.* , COUNT( (case when a.actual_published_on IS NOT NULL  then a.admn_no END)) AS tot_stu_ctr, COUNT( a.admn_no ) as formal_stu_ctr
				  FROM  final_semwise_marks_foil_freezed a WHERE  a.`session`=?  and a.session_yr=? $append1 $append2 $append3_1 $append4

				 GROUP BY a.dept,a.course,a.branch,(case when a.course<>'jrf' then  a.semester else 1=1 end) )u
				 ON u.course=c.course  	AND    ( case when c.course<>'comm' then c.branch  else c.section END )=u.branch  AND c.semester=u.semester
				 LEFT JOIN cbcs_departments cd ON cd.id=c.std_dept
                 LEFT JOIN cbcs_branches cb ON cb.id=c.branch

				 order by u.dept,u.course,u.branch ";

			   $query = $this->db->query($sql,array($session,$session_year,$session,$session_year,$session,$session_year));


//			    echo $sql;die();
		 //echo $query->num_rows();
               if ($query->num_rows() > 0) {
              //echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->result();
	          }
       else  return 0;
			   }
			   */



	   function get_release_info($session,$session_year,$dept=null,$course=null,$branch=null,$sem=null,$sec=null,$admn_no=null){
		  if($dept<>null)$append1="  and  a.dept='$dept' "; else $append1="" ;
		  if($dept<>null)$append0="  and  b.dept_id='$dept' "; else $append0="" ;
		  if($course<>null)$append2="  and  a.course='$course' "; else $append2="" ;
		  if($branch<>null&&$dept<>'comm' )$append3= "  and  a.branch='".$branch."' "; else $append3="" ;
		  if($branch<>null&&$dept=='comm'&& $sec<>null  )$append3_1= "  and  a.branch='".($dept=='comm'? $sec:$branch)."' "; else $append3_1="" ;

		     if($sem<>null  )$append5="  and  a.semester='".($dept=='comm'? ($session=='Monsoon'?1:2) : $sem )."' "; else $append5="" ;
		  if($sem<>null && $dept=='comm' && $sem<>'all'){
			  $section_select =" ,ssd.section  ";
		  }
		  else{
				$section_select="";
		    }
		  if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;


		   $sql="
		     	SELECT c.std_dept   , cd.name,   u.dept, c.course,c.branch,cb.name AS br_name   ,c.subject_code,  c.sub_name,c.sub_type, date_format(u.published_on,'%d-%m-%Y') as  published_on,c.resrc,/*c.semester*/   (case when u.semester is not null then  u.semester else c.semester end ) as semester ,c.section,(case when  u.course IS NOT null then u.tot_stu_ctr ELSE   'N/A' END ) AS tot_stu_ctr, (case when  u.course IS NOT null then  c.src_admn_ctr ELSE   'N/A' END )/* c.src_admn_ctr*/ AS registered_stu_ctr,(case when  u.course IS NOT null then u.formal_stu_ctr ELSE   'N/A' END ) AS formal_stu_ctr
				FROM
				(SELECT v.course,v.branch, v.branch_id,v.subject_code, v.sub_category,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc,v.section,v.semester,group_concat( distinct v.subject_code) as  sub_list, group_concat(distinct v.admn_no),count(distinct v.admn_no) as src_admn_ctr

				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.branch_id, b.dept_id,b.sub_name,b.sub_type,d.section,b.semester, a.sub_category_cbcs_offered,b.sub_category
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  cbcs_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?  AND  a.course<>'honour'  and a.sub_category<>'online'  $append0 $append2 $append3 $append4  )
				 UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.branch_id,b.dept_id,b.sub_name,b.sub_type,d.section,b.semester,  a.sub_category_cbcs_offered,b.sub_category
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  old_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=? AND  a.course<>'honour'   $append0 $append2 $append3 $append4 ))v
				where(
       ((v.sub_category_cbcs_offered  LIKE 'ESO%'  and  v.branch=v.branch_id) or ( v.sub_category_cbcs_offered  LIKE 'OE%'   and  v.branch=v.branch_id) )
        or
		 ((v.sub_category_cbcs_offered NOT LIKE 'ESO%' or v.sub_category_cbcs_offered is  null   or v.sub_category_cbcs_offered ='' )  AND (v.sub_category_cbcs_offered NOT LIKE 'OE%' or			 v.sub_category_cbcs_offered is  null   or v.sub_category_cbcs_offered =''))
      )

				GROUP BY v.course,v.branch, (case when v.course='jrf' then 1=1 else (case when v.course='comm' then v.section else v.semester  END) end)
				)c

				left JOIN
				(SELECT a.* , COUNT( (case when a.actual_published_on IS NOT NULL  then a.admn_no END)) AS tot_stu_ctr, COUNT( a.admn_no ) as formal_stu_ctr
				  FROM

				  	(
				  select a.* from
				  ( select a.*  from final_semwise_marks_foil_freezed a WHERE  a.`session`=?  and a.session_yr=? $append1 $append2 $append3_1 $append4
				   order by a.admn_no,a.semester,a.actual_published_on desc limit 100000000

				  )a
				   group by a.admn_no,a.semester    order by  a.semester,a.actual_published_on desc limit 1000000000)a

				 GROUP BY a.dept,a.course,a.branch,(case when a.course<>'jrf' then  a.semester else 1=1 end) )u
				 ON u.course=c.course  	AND    ( case when c.course<>'comm' then c.branch  else c.section END )=u.branch
				 /*AND c.semester=u.semester*/

				  AND   (case when c.course='jrf' then 1=1 else( (  CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)=(CASE WHEN u.course='comm' THEN u.branch ELSE u.semester END)) end)
				 LEFT JOIN cbcs_departments cd ON cd.id=c.std_dept
                 LEFT JOIN cbcs_branches cb ON cb.id=c.branch

				   /* group by u.dept,u.course,u.branch,u.semester */
 				  group by c.course,c.branch,(CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)


			/*	  order by u.dept,u.course,u.branch,u.semester */
        		 order by  c.course,c.branch,(CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)
				 ";
 //echo $sql;die();
			   $query = $this->db->query($sql,array($session,$session_year,$session,$session_year,$session,$session_year));
/*
SELECT b.dept_id, a.course,a.branch,  c.semester,  (case when a.course='comm' then  ssd.section end ) FROM old_stu_course a
INNER JOIN user_details b ON b.id=a.admn_no

left JOIN stu_section_data ssd ON ssd.admn_no=a.admn_no and ssd.session_year=a.session_year

left JOIN old_subject_offered c ON c.id=a.sub_offered_id
WHERE a.session_year='2019-2020' AND a.`session`='Monsoon'-- AND a.course<>'comm'

GROUP BY a.course,a.branch,c.semester, (case when a.course='comm' then  ssd.section end )
UNION
SELECT b.dept_id, a.course,a.branch,c.semester,ssd.section FROM cbcs_stu_course a
INNER JOIN user_details b ON b.id=a.admn_no

left  JOIN stu_section_data ssd ON ssd.admn_no=a.admn_no and ssd.session_year=a.session_year

left JOIN cbcs_subject_offered c ON c.id=a.sub_offered_id
WHERE a.session_year='2019-2020' AND a.`session`='Monsoon' -- AND a.course<>'comm'
GROUP BY  a.course,a.branch,c.semester,ssd.section

*/


//			    echo $sql;die();
		 //echo $query->num_rows();
               if ($query->num_rows() > 0) {
            // echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->result();
	          }
       else  return 0;
			   }





	   function get_eso_info($session,$session_year,$course=null,$branch=null,$sem=null,$sec=null,$admn_no=null){
		  //if($dept<>null)$append1="  and  a.dept='$dept' "; else $append1="" ;
		  //if($dept<>null)$append0="  and  b.dept_id='$dept' "; else $append0="" ;
		  if($course<>null)$append2="  and  a.course='$course' "; else $append2="" ;
		  if($branch<>null&&$dept<>'comm' )$append3= "  and  a.branch='".$branch."' "; else $append3="" ;
		  if($branch<>null&&$dept=='comm'&& $sec<>null  )$append3_1= "  and  a.branch='".($dept=='comm'? $sec:$branch)."' "; else $append3_1="" ;

		     if($sem<>null  )$append5="  and  a.semester='".($dept=='comm'? ($session=='Monsoon'?1:2) : $sem )."' "; else $append5="" ;
		  if($sem<>null && $dept=='comm' && $sem<>'all'){
			  $section_select =" ,ssd.section  ";
		  }
		  else{
				$section_select="";
		    }
		  if($admn_no<>null)$append4="  and  a.admn_no='$admn_no' "; else $append4="" ;


		   $sql="
		     	SELECT c.std_dept   , cd.name,   u.dept, c.course,c.branch,cb.name AS br_name   ,c.subject_code,  c.sub_name,c.sub_type, date_format(u.published_on,'%d-%m-%Y') as  published_on,c.resrc,/*c.semester*/   (case when u.semester is not null then  u.semester else c.semester end ) as semester ,c.section,(case when  u.course IS NOT null then u.tot_stu_ctr ELSE   'N/A' END ) AS tot_stu_ctr, (case when  u.course IS NOT null then u.formal_stu_ctr ELSE   'N/A' END ) AS formal_stu_ctr
				FROM
				(SELECT v.course,v.branch, v.branch_id,v.subject_code, v.sub_category,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc,v.section,v.semester,group_concat( distinct v.subject_code) as  sub_list, group_concat(distinct v.admn_no)

				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.branch_id, b.dept_id,b.sub_name,b.sub_type,d.section,b.semester, a.sub_category_cbcs_offered,b.sub_category
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  cbcs_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?  AND  a.course<>'honour'  and a.sub_category<>'online'   $append2 $append3 $append4  )
				 UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.branch_id,b.dept_id,b.sub_name,b.sub_type,d.section,b.semester,  a.sub_category_cbcs_offered,b.sub_category
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				INNER JOIN  old_subject_offered_desc d ON  d.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=? AND  a.course<>'honour'   $append0 $append2 $append3 $append4
				and  (  a.branch<>b.branch_id  and(   a.sub_category_cbcs_offered  like  'ESO%' or a.sub_category_cbcs_offered  like  'OE%' )  )
				))v


				GROUP BY v.course,v.branch, (case when v.course='jrf' then 1=1 else (case when v.course='comm' then v.section else v.semester  END) end)
				)c

				left JOIN
				(SELECT a.* , COUNT( (case when a.actual_published_on IS NOT NULL  then a.admn_no END)) AS tot_stu_ctr, COUNT( a.admn_no ) as formal_stu_ctr
				  FROM

				  	(
				  select a.* from
				  ( select a.*  from final_semwise_marks_foil_freezed a WHERE  a.`session`=?  and a.session_yr=?  $append2 $append3_1 $append4
				   order by a.admn_no,a.semester,a.actual_published_on desc limit 100000000

				  )a
				   group by a.admn_no,a.semester)a

				 GROUP BY a.dept,a.course,a.branch,(case when a.course<>'jrf' then  a.semester else 1=1 end) )u
				 ON u.course=c.course  	AND    ( case when c.course<>'comm' then c.branch  else c.section END )=u.branch
				 /*AND c.semester=u.semester*/

				  AND   (case when c.course='jrf' then 1=1 else( (  CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)=(CASE WHEN u.course='comm' THEN u.branch ELSE u.semester END)) end)
				 LEFT JOIN cbcs_departments cd ON cd.id=c.std_dept
                 LEFT JOIN cbcs_branches cb ON cb.id=c.branch

				   /* group by u.dept,u.course,u.branch,u.semester */
 				  group by c.course,c.branch,(CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)
				   having  u.dept is null

			/*	  order by u.dept,u.course,u.branch,u.semester */
        		 order by  c.course,c.branch,(CASE WHEN c.course='comm' THEN c.section ELSE c.semester END)
				 ";
 //echo $sql;die();
			   $query = $this->db->query($sql,array($session,$session_year,$session,$session_year,$session,$session_year));
/*
SELECT b.dept_id, a.course,a.branch,  c.semester,  (case when a.course='comm' then  ssd.section end ) FROM old_stu_course a
INNER JOIN user_details b ON b.id=a.admn_no

left JOIN stu_section_data ssd ON ssd.admn_no=a.admn_no and ssd.session_year=a.session_year

left JOIN old_subject_offered c ON c.id=a.sub_offered_id
WHERE a.session_year='2019-2020' AND a.`session`='Monsoon'-- AND a.course<>'comm'

GROUP BY a.course,a.branch,c.semester, (case when a.course='comm' then  ssd.section end )
UNION
SELECT b.dept_id, a.course,a.branch,c.semester,ssd.section FROM cbcs_stu_course a
INNER JOIN user_details b ON b.id=a.admn_no

left  JOIN stu_section_data ssd ON ssd.admn_no=a.admn_no and ssd.session_year=a.session_year

left JOIN cbcs_subject_offered c ON c.id=a.sub_offered_id
WHERE a.session_year='2019-2020' AND a.`session`='Monsoon' -- AND a.course<>'comm'
GROUP BY  a.course,a.branch,c.semester,ssd.section

*/


//			    echo $sql;die();
		 //echo $query->num_rows();
               if ($query->num_rows() > 0) {
         //    echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->result();
	          }
       else  return 0;
			   }



	   function   get_regstration_info($session,$session_yr,$admn_no){
	     $sql=" select rg.*,u.dept_id from  reg_regular_form rg  join  user_details u  on u.id=rg.admn_no
		          and  rg.session_year=? and rg.session=? and  rg.admn_no=?  and  rg.hod_status='1' and rg.acad_status='1'   ";
			   $query = $this->db->query($sql,array($session_yr,$session,$admn_no));

               if ($query->num_rows() > 0) {
               //echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->row();
	          }
       else  return 0;

	}






	   function   get_section_cbcs($session,$session_yr,$admn_no){
	     $sql=" select rg.sub_category_cbcs_offered from  cbcs_stu_course rg
		           where rg.session_year=? and rg.session=? and  rg.admn_no=? limit 1 ";
			   $query = $this->db->query($sql,array($session_yr,$session,$admn_no));
               if ($query->num_rows() > 0) {
            //   echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
				return $query->row();
	          }
       else  return 0;

	}


	   function  get_foil_sgpa_old($course_id,$session_yr,$session,$admn_no,$id=null){
		  // echo $admn_no;exit;
       if(!empty($id))  $byid=" and  y.id='$id' ";

 if(strtolower($course_id)<>'jrf' && strtolower($course_id)<>'minor'){
		  $sql="


  update final_semwise_marks_foil y join

 /* select  y.id, B.*  from   final_semwise_marks_foil y join */
(
select   sum( if(def.def_status='y',1,0)) as def_status ,group_concat(if(def.def_status='y',def.sub_code,null)),  y.id,  y.core_tot_cr_pts, y.core_tot_cr_hr,   y.tot_cr_pts  ,y.tot_cr_hr, y.hstatus,   B.*  from   final_semwise_marks_foil y join



(
SELECT x.sum_core_crpts,x.sum_core_cr_hr,x.sum_core_gpa, x.sum_crpts, x.sum_crdthr, x.sum_gpa, x.session_yr,x.session, ( case when x.status>0 then 'FAIL'else 'PASS' end) as status ,
 ( case when x.core_status>0 then 'FAIL'else 'PASS' end)  as core_status,

x.type,x.exam_type,x.semester,x.foil_id,x.dept,x.course,x.branch,x.admn_no
FROM (

SELECT

         SUM( if(  (g.grade='I' or g.grade='F') , 0, g.cr_pts)) AS sum_crpts   ,
       SUM(g.cr_hr) AS sum_crdthr,
   	 FORMAT(( SUM( if(  (g.grade='I' or g.grade='F') , 0, g.cr_pts)) / SUM(g.cr_hr)),5) AS sum_gpa,

		SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'   or g.course_id is null) and   (g.grade<>'I'  and g.grade<>'F')  ), g.cr_pts, 0)) AS sum_core_crpts,
		SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null)), g.cr_hr, 0)) AS sum_core_cr_hr,
		FORMAT((SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null) and   (g.grade<>'I'  and g.grade<>'F')  ), g.cr_pts, 0))/ SUM(IF ((g.sub_code <>''
		AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null)), g.cr_hr, 0))),5) AS sum_core_gpa,


		 SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'   or g.course_id is null)   and  (g.grade='I' or g.grade='F') ),1, 0)) AS core_status,

		  SUM(IF( ( g.grade='I' or g.grade='F' ),1, 0)) AS status, g.admn_no,
		  	 g.session_yr,
			 g.session,
    		    g.`type`,
           g.exam_type,

				g.foil_id,g.dept,g.course,g.branch,
				 g.semester,
			 g.tot_cr_pts,
			 g.tot_cr_hr,
			 g.gpa,
			  g.core_tot_cr_pts,
			  g.core_tot_cr_hr,
 			  g.core_gpa	,  g.course_id


	 from(

			 select p.*
		 from (
			 select
			  fd.admn_no,    fd.sub_code,		 fd.cr_pts,fd.cr_hr, fd.grade,
			 a.session_yr,
			 a.session,
			 a.semester,
			 a.tot_cr_pts,
			 a.tot_cr_hr,
			 a.gpa,
			  a.core_tot_cr_pts,
			  a.core_tot_cr_hr,
 			  a.core_gpa,
           a.`type`,
           a.exam_type,

				a.id AS foil_id,a.dept,a.course,a.branch,o.course_id



FROM  (select a.* from  final_semwise_marks_foil a  where
  UPPER(a.course)<>'MINOR' and  a.course<>'jrf' AND (a.semester!= '0' AND a.semester!='-1') and a.session_yr=? and a.`session`=?  and
 a.admn_no =?)a

JOIN final_semwise_marks_foil_desc fd ON fd.foil_id=a.id
left join old_stu_course  oso on oso.admn_no=a.admn_no and  oso.subject_code=fd.sub_code and  oso.session_year=a.session_yr and  oso.`session`=a.`session`


 left join old_subject_offered o on o.id=oso.sub_offered_id order by fd.foil_id,fd.sub_code,fd.cr_pts desc limit 100000)p
 group by   p.foil_id,p.sub_code)g


GROUP BY  g.foil_id) x

) B


on B.foil_id=y.id  and     ( (case when  y.tot_cr_pts is null then 'NA' else y.tot_cr_pts  end ) <>B.sum_crpts
                            or
									(case when  y.core_tot_cr_pts is null then 'NA' else y.core_tot_cr_pts  end ) <>B.sum_core_crpts
									or
									(case when  y.core_tot_cr_hr is null then 'NA' else y.core_tot_cr_hr  end ) <>B.sum_core_cr_hr
									or
									(case when  y.tot_cr_hr is null then 'NA' else y.tot_cr_hr  end ) <>B.sum_crdthr

									)
 left join cbcs_absent_table_defaulter def   on  def.admn_no=B.admn_no  group by def.admn_no  )B

 on B.foil_id=y.id  $byid

set y.tot_cr_hr=B.sum_crdthr,
    y.tot_cr_pts=B.sum_crpts,
    y.core_tot_cr_hr=B.sum_core_cr_hr,
    y.core_tot_cr_pts=B.sum_core_crpts,
    y.gpa=B.sum_gpa,
    y.core_gpa=B.sum_core_gpa,
    y.status=B.status,
    y.core_status=B.core_status";

 }
 else if(strtolower($course_id)=='jrf' && strtolower($course_id)<>'minor'){
	 $sql="
 update final_semwise_marks_foil y join

 (
SELECT x.sum_core_crpts,x.sum_core_cr_hr,x.sum_core_gpa, x.sum_crpts, x.sum_crdthr, x.sum_gpa, x.session_yr,x.session, ( case when x.status>0 then 'FAIL'else 'PASS' end) as status ,
 ( case when x.core_status>0 then 'FAIL'else 'PASS' end)  as core_status,

x.type,x.exam_type,x.semester,x.foil_id,x.dept,x.course,x.branch,x.admn_no
FROM (

SELECT


       SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_pts)) AS sum_crpts,

       SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_hr)) AS sum_crdthr,

		 format( ( SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_pts))/   SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_hr))  )  ,5) AS sum_gpa,

	     SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_pts)) AS sum_core_crpts,
        SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_hr)) AS sum_core_cr_hr,

   	 format( ( SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_pts))/   SUM(       IF( ( fd.grade='I' or fd.grade='F' ), 0,fd.cr_hr))  )  ,5) as   sum_core_gpa,

		  SUM(IF( ( fd.grade='I' or fd.grade='F' ),1, 0))  AS core_status,

		  SUM(IF( ( fd.grade='I' or fd.grade='F' ),1, 0)) AS status, fd.admn_no,

			 a.session_yr,
			 a.session,
			 a.semester,
			 a.tot_cr_pts,
			 a.tot_cr_hr,
			 a.gpa,
			  a.core_tot_cr_pts,
			  a.core_tot_cr_hr,
 			  a.core_gpa,
           a.`type`,
           a.exam_type,
				a.id AS foil_id,a.dept,a.course,a.branch

FROM final_semwise_marks_foil a
JOIN final_semwise_marks_foil_desc fd ON fd.foil_id=a.id
WHERE a.admn_no=? AND UPPER(a.course)<>'MINOR' AND (a.semester!= '0' AND a.semester!='-1') and a.session_yr=? and a.`session`=?
 and  a.course='jrf'

GROUP BY  fd.foil_id) x

) B

on B.foil_id=y.id  $byid

set y.tot_cr_hr=B.sum_crdthr,
    y.tot_cr_pts=B.sum_crpts,
    y.core_tot_cr_hr=B.sum_core_cr_hr,
    y.core_tot_cr_pts=B.sum_core_crpts,
    y.gpa=B.sum_gpa,
    y.core_gpa=B.sum_core_gpa,
    y.status=B.status,
    y.core_status=B.core_status
     ";

 }






   $query = $this->db->query($sql,array($session_yr,$session,$admn_no));
  //  echo $this->db->last_query(); die();
     return $this->db->affected_rows();
	  }

      function get_online_paper_offered($session_yr,$session,$param=null){
		     // getting online papers whose credits & credit hr not be included(treating  like  assexx with grade S  however they have actual grades)
			 if($param==null || $param==''){
			   $str=" x.session_year=? and  x.`session`=? and	 ";
			   $sec_array=array($session_yr,$session);
			 }
		    else
			{
			   $str=" x.session_year<=? and	 ";
			   $sec_array=array($session_yr);

			}

		   $sql1=" select  distinct(x.sub_code) as code from cbcs_subject_offered x where $str x.sub_category='online'";
		   $query1 = $this->db->query($sql1,$sec_array);
		    if ($query1->num_rows() > 0)
		   return $query1->result();
	      else
			  return 0;
	  }




  function  get_freeze_cgpa_old($course_id,$session_yr,$session,$admn_no,$foil_id){

       // if( strstr($admn_no ,'dr')==FALSE  && strstr($admn_no ,'dp')==FALSE){
  if(strtolower($course_id)<>'jrf' && strtolower($course_id)<>'minor'){



	   $sql="
     update  final_semwise_marks_foil_freezed y1 join

(
SELECT    SUM(  if(  (z.grade='I' or z.grade='F') , 0, z.cr_pts)  ) AS ctotcrpts, SUM(z.cr_hr) AS ctotcrhr, FORMAT(( SUM(  if(  (z.grade='I' or z.grade='F') , 0, z.cr_pts)  )/
SUM(z.cr_hr)),5) AS cgpa,
SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)     and   (z.grade<>'I'  and z.grade<>'F'  ) ), z.cr_pts, 0)) AS core_ctotcrpts,
SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)), z.cr_hr, 0)) AS core_ctotcrhr,
 FORMAT((SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)   and   (z.grade<>'I'  and z.grade<>'F'  )   ), z.cr_pts, 0))/ SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)), z.cr_hr, 0))),5) AS core_cgpa,
z.admn_no,z.semester,z.foil_id




FROM
(
SELECT z1.* from
(
SELECT v.*
FROM (
SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade, fd.cr_pts, fd.cr_hr,  fd.mis_sub_id,
y.admn_no , y.ctotcrpts as stored_ctotcrpts  ,y.ctotcrhr as stored_ctotcrhr ,y.core_ctotcrpts  as stored_core_ctotcrpts ,y.core_ctotcrhr  as  stored_core_ctotcrhr ,
 IF(ac.alternate_subject_code IS NOT NULL,ac.old_subject_code, IF(acl.alternate_subject_code IS NOT NULL,acl.old_subject_code,fd.sub_code)) AS newsub,
 if(o.course_id IS NULL ,if(cs.id IS NOT NULL, 'honour',NULL ),o.course_id) AS course_id,cs.id


 FROM ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id as foil_id,a.`status`,
a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts , if( rg.semester<>a.semester, a.semester, null)  as sem ,
rg.semester as reg_sem ,  a.published_on, a.actual_published_on
FROM final_semwise_marks_foil_freezed AS a
join  reg_regular_form rg  on rg.admn_no=a.admn_no and  rg.hod_status='1' and rg.acad_status='1' and rg.session_year=? and rg.`session`=?

WHERE  a.admn_no=? AND  UPPER(a.course)<>'MINOR' AND
(a.semester!= '0' AND a.semester!='-1') and a.course<>'jrf'
ORDER BY a.admn_no,a.semester,a.actual_published_on desc

LIMIT 100000000)x    GROUP BY x.admn_no,IFNULL(x.sem, x.session_yr)    /*having  x.semester<= x.reg_sem*/   order by  x.admn_no,x.semester,x.actual_published_on desc limit 100000000) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.foil_id AND

fd.admn_no=y.admn_no LEFT JOIN alternate_course ac ON ac.session_year=y.session_yr AND ac.`session`=y.session AND
ac.admn_no=y.admn_no AND ac.alternate_subject_code=fd.sub_code

LEFT JOIN alternate_course_all acl ON acl.session_year=y.session_yr AND acl.`session`=y.session AND
acl.alternate_subject_code=fd.sub_code left join old_subject_offered o on o.sub_code=fd.sub_code and
o.session_year=y.session_yr and o.`session`=y.session and o.dept_id=y.dept and (case when o.course_id='honour'
then 'honour' else y.course end)=o.course_id and o.branch_id=y.branch

LEFT JOIN  subjects s ON s.id = fd.mis_sub_id AND y.session_yr<'2019-2020'
LEFT JOIN  course_structure cs ON cs.id= fd.mis_sub_id AND cs.aggr_id LIKE '%honour%' AND  cs.semester=y.semester
AND y.session_yr<'2019-2020'

ORDER BY  y.admn_no, newsub,  fd.cr_pts desc,y.session_yr DESC limit 10000000 )v
GROUP BY v.admn_no, v.newsub  ORDER BY v.admn_no, v.session_yr,v.dept,v.course,v.branch,v.semester,v.newsub   limit 10000000 ) z1
 GROUP BY z1.admn_no,z1.sub_code)z

GROUP BY
z.admn_no/*,z.semester*/       )B1
on    B1.admn_no=y1.admn_no   and  y1.session_yr=? and y1.`session`=? and   UPPER(y1.course)<>'MINOR' AND
(y1.semester!= '0' AND y1.semester!='-1') and y1.course<>'jrf'   and  y1.id=?
set y1.ctotcrhr=B1.ctotcrhr,
	y1.ctotcrpts =B1.ctotcrpts,
	y1.core_ctotcrhr=B1.core_ctotcrhr,
	y1.core_ctotcrpts= B1.core_ctotcrpts,
    y1.cgpa=B1.cgpa,
    y1.core_cgpa=B1.core_cgpa";


  }
	else if(strtolower($course_id)=='jrf' && strtolower($course_id)<>'minor'){

		$sql="  update final_semwise_marks_foil_freezed y join
 (
SELECT SUM( if(( z.grade='S' or  z.grade='I' or z.grade='F' ), 0,   z.cr_pts) )AS ctotcrpts,
SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ) , 0, z.cr_hr) )AS ctotcrhr,
FORMAT((SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ), 0,   z.cr_pts) )/SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ) , 0, z.cr_hr)) ),5) AS cgpa,
SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ), 0,   z.cr_pts) ) AS core_ctotcrpts,
SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ) , 0, z.cr_hr) ) AS core_ctotcrhr,
FORMAT((SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ), 0,   z.cr_pts) )/SUM( if(( z.grade='S' or z.grade='I' or z.grade='F' ) , 0, z.cr_hr)) ),5)  AS core_cgpa,

z.admn_no


FROM
(
SELECT z1.* from
(
SELECT v.*
FROM (
SELECT y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade, fd.cr_pts, fd.cr_hr,  fd.mis_sub_id,
y.admn_no
,IF(ac.alternate_subject_code IS NOT NULL,ac.old_subject_code, IF(acl.alternate_subject_code IS NOT NULL,acl.old_subject_code,fd.sub_code)) AS newsub



 FROM ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id,a.`status`,
	a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts
	FROM final_semwise_marks_foil_freezed AS a
	join  reg_regular_form rg  on rg.admn_no=a.admn_no and  rg.hod_status='1' and rg.acad_status='1' and rg.session_year=? and rg.`session`=?

	WHERE a.admn_no=? AND  UPPER(a.course)<>'MINOR'  and  lower(a.course)='jrf'
	 ORDER BY a.admn_no,a.session_yr a.actual_published_on DESC


		 LIMIT 100000000)x  GROUP BY x.admn_no,x.session_yr,x.session) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.id AND

			fd.admn_no=y.admn_no

			LEFT JOIN alternate_course ac ON ac.session_year=y.session_yr AND ac.`session`=y.session AND
			 ac.admn_no=y.admn_no AND ac.alternate_subject_code=fd.sub_code

				 LEFT JOIN alternate_course_all acl ON acl.session_year=y.session_yr AND acl.`session`=y.session AND
					 acl.alternate_subject_code=fd.sub_code




 						ORDER BY  y.admn_no, newsub, fd.cr_pts desc, y.session_yr DESC LIMIT 100000000 )v
						 GROUP BY v.admn_no, v.newsub  ORDER BY v.admn_no, v.session_yr,v.dept,v.course,v.branch,v.semester,v.newsub LIMIT 100000000 ) z1
						  GROUP BY z1.admn_no,z1.sub_code)z

							 GROUP BY
							z.admn_no)B
on B.admn_no=y.admn_no   and  y.session_yr=? and y.`session`=? and   UPPER(y.course)<>'MINOR' AND lower( y.course)='jrf'    and  y.id=?
set y.ctotcrhr=B.ctotcrhr,
	y.ctotcrpts =B.ctotcrpts,
	y.core_ctotcrhr=B.core_ctotcrhr,
	y.core_ctotcrpts= B.core_ctotcrpts,
   y.cgpa=B.cgpa,
   y.core_cgpa=B.core_cgpa

							";

	 }
	  $query = $this->db->query($sql,array($session_yr,$session,$admn_no,$session_yr,$session,$foil_id));

	// echo $this->db->last_query(); die();
     return $this->db->affected_rows();
	  }

 function get_thesis_paper_offered($session_yr,$session,$param=null){
		     // getting thesis_paper whose credits & credit hr not be included(treating  like  assex with grade 4S/3Sx/2SXX....  however they have actual grades)
			 if($param==null || $param==''){
			   $str=" x.session_year=? and  x.`session`=? and	 ";
			   $sec_array=array($session_yr,$session);
			 }
		     else
			 {
			   $str=" x.session_year<=? and	 ";
			   $sec_array=array($session_yr);
			}

		   $sql1=" select  distinct(x.sub_code) as code from cbcs_subject_offered x where $str  lower(x.sub_name)=lower('Thesis') and lower(x.sub_type)=lower('Audit') and x.sub_code like '%99' ";
		   $query1 = $this->db->query($sql1,$sec_array);
		    if ($query1->num_rows() > 0)
		   return $query1->result();
	      else
			  return 0;
	  }
	  
	  
	  
	 function  get_foil_sgpa($course_id,$session_yr,$session,$admn_no,$id=null){
		   $onlinepaper=$this->get_online_paper_offered($session_yr,$session);
		   //echo $this->db->last_query();die();


		   $ctr=0;
		   if( isset($onlinepaper)&&  count($onlinepaper)>0 )
		     foreach($onlinepaper as $rr){
		     $ctr++;
			 $online_paper_list.=  "'".$rr->code."'".($ctr==count($onlinepaper)?"":",")  ;
		   }
		   else
		   $online_paper_list="";


	//	 echo $online_paper_list.'*'. $online_str1.'*'. $online_str2; die();

		  // echo $admn_no;exit;
       if(!empty($id))  $byid=" and  y.id='$id' ";

 if(strtolower($course_id)<>'jrf' /*&& strtolower($course_id)<>'minor'*/){
	 if (strtolower($course_id)=='minor')
		$minor_txt=" UPPER(a.course)='MINOR' ";
	 else
		 $minor_txt=" UPPER(a.course)<>'MINOR' ";


	  if($online_paper_list<>"")
	          if (substr_count($online_paper_list, ',') > 0) {
				  $online_str1= " or  g.sub_code in(".$online_paper_list.") ";
				  $online_str2= " and g.sub_code not in(".$online_paper_list.") ";
				  $online_str3= " if( g.sub_code in(".$online_paper_list.") , 0,  g.cr_hr) ";
			   }
			   else{
				   $online_str1= " or g.sub_code =".$online_paper_list." ";
				   $online_str2= " and  g.sub_code <> ".$online_paper_list." ";
				   $online_str3= "  if(  (g.sub_code =".$online_paper_list." ) , 0,  g.cr_hr)      ";
			   }
			else
			 {
        		 $online_str1="";
		         $online_str2="";
				 $online_str3= "  g.cr_hr ";
			 }
		  $sql="


  update final_semwise_marks_foil y join

 /* select  y.id, B.*  from   final_semwise_marks_foil y join */
(
select   sum( if(def.def_status='y',1,0)) as def_status ,group_concat(if(def.def_status='y',def.sub_code,null)),  y.id,  y.core_tot_cr_pts, y.core_tot_cr_hr,   y.tot_cr_pts  ,y.tot_cr_hr, y.hstatus,   B.*  from   final_semwise_marks_foil y join



(
SELECT x.sum_core_crpts,x.sum_core_cr_hr,x.sum_core_gpa, x.sum_crpts, x.sum_crdthr, x.sum_gpa, x.session_yr,x.session, ( case when x.status>0 then 'FAIL'else 'PASS' end) as status ,
 ( case when x.core_status>0 then 'FAIL'else 'PASS' end)  as core_status,

x.type,x.exam_type,x.semester,x.foil_id,x.dept,x.course,x.branch,x.admn_no
FROM (

SELECT

         SUM( if(  (g.grade='I' or g.grade='F'  $online_str1 ) , 0, g.cr_pts)) AS sum_crpts   ,
      /* SUM( if(  ($online_str3 ) , 0,  g.cr_hr)  )*/    SUM( $online_str3 ) AS sum_crdthr,
   	 FORMAT(( SUM( if(  (g.grade='I' or g.grade='F'  $online_str1) , 0, g.cr_pts)) / SUM( $online_str3 )),5) AS sum_gpa,

		SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'   or g.course_id is null) and   (g.grade<>'I'  and g.grade<>'F'   $online_str2)  ), g.cr_pts, 0)) AS sum_core_crpts,
		SUM(IF ((g.sub_code <>''   $online_str2 AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null)), g.cr_hr, 0)) AS sum_core_cr_hr,
		FORMAT((SUM(IF ((g.sub_code <>'' AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null) and   (g.grade<>'I'  and g.grade<>'F'
		  $online_str2)  ), g.cr_pts, 0))/ SUM(IF ((g.sub_code <>''  $online_str2
		AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'    or g.course_id is null)), g.cr_hr, 0))),5) AS sum_core_gpa,


		 SUM(IF ((g.sub_code <>'' $online_str2 AND LOWER(g.course)<>'jrf' AND (g.course_id<> 'honour'   or g.course_id is null)   and  (g.grade='I' or g.grade='F' ) ),1, 0)) AS core_status,

		  SUM(IF( ( (g.grade='I' or g.grade='F')    $online_str2 ),1, 0)) AS status, g.admn_no,
		  	 g.session_yr,
			 g.session,
    		    g.`type`,
           g.exam_type,

				g.foil_id,g.dept,g.course,g.branch,
				 g.semester,
			 g.tot_cr_pts,
			 g.tot_cr_hr,
			 g.gpa,
			  g.core_tot_cr_pts,
			  g.core_tot_cr_hr,
 			  g.core_gpa	,  g.course_id


	 from(

			 select p.*
		 from (
			 select
			  fd.admn_no,    fd.sub_code,		 fd.cr_pts,fd.cr_hr, fd.grade,
			 a.session_yr,
			 a.session,
			 a.semester,
			 a.tot_cr_pts,
			 a.tot_cr_hr,
			 a.gpa,
			  a.core_tot_cr_pts,
			  a.core_tot_cr_hr,
 			  a.core_gpa,
           a.`type`,
           a.exam_type,

				a.id AS foil_id,a.dept,a.course,a.branch,o.course_id



FROM  (select a.* from  final_semwise_marks_foil a  where
  $minor_txt and  a.course<>'jrf' AND (a.semester!= '0' AND a.semester!='-1') and a.session_yr=? and a.`session`=?  and
 a.admn_no =?)a

JOIN final_semwise_marks_foil_desc fd ON fd.foil_id=a.id
left join old_stu_course  oso on oso.admn_no=a.admn_no and  oso.subject_code=fd.sub_code and  oso.session_year=a.session_yr and  oso.`session`=a.`session`


 left join old_subject_offered o on o.id=oso.sub_offered_id order by fd.foil_id,fd.sub_code,fd.cr_pts desc limit 100000)p
 group by   p.foil_id,p.sub_code)g


GROUP BY  g.foil_id) x

) B


on B.foil_id=y.id  /*and     ( (case when  y.tot_cr_pts is null then 'NA' else y.tot_cr_pts  end ) <>B.sum_crpts
                            or
									(case when  y.core_tot_cr_pts is null then 'NA' else y.core_tot_cr_pts  end ) <>B.sum_core_crpts
									or
									(case when  y.core_tot_cr_hr is null then 'NA' else y.core_tot_cr_hr  end ) <>B.sum_core_cr_hr
									or
									(case when  y.tot_cr_hr is null then 'NA' else y.tot_cr_hr  end ) <>B.sum_crdthr

									)*/
 left join cbcs_absent_table_defaulter def   on  def.admn_no=B.admn_no  group by def.admn_no  )B

 on B.foil_id=y.id  $byid

set y.tot_cr_hr=B.sum_crdthr,
    y.tot_cr_pts=B.sum_crpts,
    y.core_tot_cr_hr=B.sum_core_cr_hr,
    y.core_tot_cr_pts=B.sum_core_crpts,
    y.gpa=B.sum_gpa,
    y.core_gpa=B.sum_core_gpa,
    y.status=B.status,
    y.core_status=B.core_status";
	  $query = $this->db->query($sql,array($session_yr,$session,$admn_no));

 }
 else if(strtolower($course_id)=='jrf' && strtolower($course_id)<>'minor'){

   $thesispaper=$this->get_thesis_paper_offered($session_yr,$session);
		   //echo $this->db->last_query();die();


		   $ctr=0;
		   if( isset($thesispaper)&&  count($thesispaper)>0 )
		     foreach($thesispaper as $rr){
		     $ctr++;
			 $thesis_paper_list.=  "'".$rr->code."'".($ctr==count($thesispaper)?"":",")  ;
		    }
		    else
		     $thesis_paper_list="";


	//	 echo $online_paper_list.'*'. $online_str1.'*'. $online_str2; die();


	 if($thesis_paper_list<>"")
	          if (substr_count($thesis_paper_list, ',') > 0) {
				  $thesis_str1= " or  fd.sub_code in(".$thesis_paper_list.") ";
				  $thesis_str2= " and fd.sub_code not in(".$thesis_paper_list.") ";
				  $thesis_str3= " fd.sub_code in(".$thesis_paper_list.") ";
			   }
			   else{
				   $thesis_str1= " or fd.sub_code =".$thesis_paper_list." ";
				   $thesis_str2= " and  fd.sub_code <> ".$thesis_paper_list." ";
				   $thesis_str3= " fd.sub_code =".$thesis_paper_list." ";
			   }
			else
			 {
        		 $thesis_str1="";
		         $thesis_str2="";
			 }







	 if($online_paper_list<>"")
	          if (substr_count($online_paper_list, ',') > 0) {
				  $online_str1= " or  fd.sub_code in(".$online_paper_list.") ";
				  $online_str2= " and fd.sub_code not in(".$online_paper_list.") ";
				  $online_str3= " fd.sub_code in(".$online_paper_list.") ";
			   }
			   else{
				   $online_str1= " or fd.sub_code =".$online_paper_list." ";
				   $online_str2= " and  fd.sub_code <> ".$online_paper_list." ";
				   $online_str3= " fd.sub_code =".$online_paper_list." ";
			   }
			else
			 {
        		 $online_str1="";
		         $online_str2="";
			 }


	 $sql="
 update final_semwise_marks_foil y join

 (
SELECT x.sum_core_crpts,x.sum_core_cr_hr,x.sum_core_gpa, x.sum_crpts, x.sum_crdthr, x.sum_gpa, x.session_yr,x.session, ( case when x.status>0 then 'FAIL' else 'PASS' end) as status ,
 ( case when x.core_status>0 then 'FAIL'else 'PASS' end)  as core_status,

x.type,x.exam_type,x.semester,x.foil_id,x.dept,x.course,x.branch,x.admn_no
FROM (

SELECT


       SUM(       IF( (  (((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  ) or (a.dept='hss' and fd.grade='X'  ) ) or fd.grade='I' or fd.grade='F'  $online_str1 $thesis_str1 ), 0,fd.cr_pts)) AS sum_crpts,

       SUM(       IF( ( ((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )  $online_str1  $thesis_str1), 0,fd.cr_hr)) AS sum_crdthr,

		 format( ( SUM(       IF( ( (((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  ) or (a.dept='hss' and fd.grade='X'  ) ) or fd.grade='I' or fd.grade='F'  $online_str1   $thesis_str1), 0,fd.cr_pts))/  if( SUM(IF((((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )  $online_str1 $thesis_str1), 0,fd.cr_hr))=0 , 1 , SUM(IF((((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )   $online_str1 $thesis_str1), 0,fd.cr_hr)) )  )  ,5) AS sum_gpa,

	     SUM(       IF( ((((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  ) or (a.dept='hss' and fd.grade='X'  ) ) or fd.grade='I' or fd.grade='F'  $online_str1 $thesis_str1 ), 0,fd.cr_pts)) AS sum_core_crpts,
        SUM(       IF( ( ((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )  $online_str1 $thesis_str1 ), 0,fd.cr_hr)) AS sum_core_cr_hr,

   	 format( ( SUM(       IF( (  (((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  ) or (a.dept='hss' and fd.grade='X'  ) ) or fd.grade='I' or fd.grade='F'  $online_str1  $thesis_str1), 0,fd.cr_pts))/   if(SUM(IF((((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )  $online_str1 $thesis_str1), 0,fd.cr_hr))=0 ,1,SUM(IF(( ((fd.grade='S'  or  fd.grade='X')  and  a.dept<>'hss'  )  $online_str1 $thesis_str1), 0,fd.cr_hr)))  )  ,5) as   sum_core_gpa,

		  SUM(IF( ( (fd.grade='I' or fd.grade='F' or fd.grade='X' )  $online_str2 $thesis_str2 ),1, 0))  AS core_status,

		  SUM(IF( ( (fd.grade='I' or fd.grade='F' or fd.grade='X' )  $online_str2 $thesis_str2 ),1, 0)) AS status, fd.admn_no,

			 a.session_yr,
			 a.session,
			 a.semester,
			 a.tot_cr_pts,
			 a.tot_cr_hr,
			 a.gpa,
			  a.core_tot_cr_pts,
			  a.core_tot_cr_hr,
 			  a.core_gpa,
           a.`type`,
           a.exam_type,
				a.id AS foil_id,a.dept,a.course,a.branch

FROM final_semwise_marks_foil a
JOIN final_semwise_marks_foil_desc fd ON fd.foil_id=a.id
WHERE a.admn_no=? AND UPPER(a.course)<>'MINOR' /*AND (a.semester!= '0' AND a.semester!='-1')*/ and a.session_yr=? and a.`session`=?
 and  a.course='jrf'

GROUP BY  fd.foil_id) x

) B

on B.foil_id=y.id  $byid

set y.tot_cr_hr=B.sum_crdthr,
    y.tot_cr_pts=B.sum_crpts,
    y.core_tot_cr_hr=B.sum_core_cr_hr,
    y.core_tot_cr_pts=B.sum_core_crpts,
    y.gpa=B.sum_gpa,
    y.core_gpa=B.sum_core_gpa,
    y.status=B.status,
    y.core_status=B.core_status
     ";
  $query = $this->db->query($sql,array($admn_no,$session_yr,$session));
 }







   // echo $this->db->last_query(); die();
     return $this->db->affected_rows();
	  }


  

  

  function  get_freeze_cgpa($course_id,$session_yr,$session,$admn_no,$foil_id,$param=null,$selectqry=null,$sem=null){

  //echo 'entered_freeze_cgpa'; exit;
if((strstr( strtolower($admn_no),'14je')!=false || strstr( strtolower($admn_no),'13je')!=false || strstr( strtolower($admn_no),'12je')!=false||
 strstr( strtolower($admn_no),'11je')!=false)){
	// die();

	 $old_stu=1;
	 $old_str1= "( select y.*,fd.mis_sub_id,fd.sub_code,fd.foil_id as foil_id2,fd.grade,fd.cr_pts,fd.cr_hr,fd.remark2 from ";
	 $old_str2=" union

SELECT /*'oldsys' as rec_from,*/ a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.foil_id,a.`status`, a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,
a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts,  a.published_on, a.actual_published_on  
 , a.mis_sub_id,a.sub_code,a.foil_id2,a.grade,a.cr_pts,a.cr_hr,NULL AS remark2
FROM (

SELECT a.ysession AS  session_yr, if(a.wsms='ZS','Summer',a.wsms) as session,a.adm_no as admn_no,  if(d.course='comm','comm',d.deptmis)  as dept, d.course , d.branch, /*CAST(REVERSE(a.sem_code) AS UNSIGNED) AS semester*/ d.sem as semester,a.id AS foil_id, if(a.passfail='F' or a.passfail='U' or a.passfail is null ,'FAIL','PASS') as `status`,
 a.ctotcrpts,a.ctotcrhr, a.ctotcrpts as  core_ctotcrpts, a.ctotcrhr as core_ctotcrhr,  a.totcrhr as tot_cr_hr,a.totcrpts as tot_cr_pts, a.totcrhr as core_tot_cr_hr, a.totcrpts as core_tot_cr_pts, /*CAST(REVERSE(a.sem_code) AS UNSIGNED ) AS sem, CAST(REVERSE(a.sem_code) AS UNSIGNED ) AS  reg_sem, */ null as  published_on, null as actual_published_on,
 null as  mis_sub_id, a.subje_code as sub_code, a.id as foil_id2,a.grade,a.crdhrs as cr_hr ,  a.crpts as cr_pts ,a.sem_code

   FROM tabulation1 a
   JOIN dip_m_semcode d ON d.semcode=a.sem_code
and a.adm_no='$admn_no' AND a.sem_code NOT LIKE 'PREP%'  and a.passfail<>'F'

ORDER BY a.ysession DESC,semester DESC, a.wsms DESC,a.totcrpts DESC,a.examtype DESC limit 10000000)a GROUP BY a.sem_code,a.sub_code )y ";

 $old_str0 =" SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,y.sub_code, y.grade,

 /*(case when y.session_yr='2019-2020' and (y.session='Winter' or y.session='Summer')  then (y.cr_pts)/2 else y.cr_pts end) as cr_pts,
 (case when y.session_yr='2019-2020' and (y.session='Winter' or y.session='Summer')   then (y.cr_hr)/2 else y.cr_hr end) as cr_hr, */

(case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (y.cr_pts)/2 else y.cr_pts end) as cr_pts,
  (case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (y.cr_hr)/2 else y.cr_hr end) as cr_hr,

 y.mis_sub_id,
y.admn_no , y.ctotcrpts as stored_ctotcrpts  ,y.ctotcrhr as stored_ctotcrhr ,y.core_ctotcrpts  as stored_core_ctotcrpts ,y.core_ctotcrhr  as  stored_core_ctotcrhr ,
 IF(ac.alternate_subject_code IS NOT NULL,ac.old_subject_code, IF(acl.alternate_subject_code IS NOT NULL,acl.old_subject_code,y.sub_code)) AS newsub,
 if(o.course_id IS NULL ,if(cs.id IS NOT NULL, 'honour',NULL ),o.course_id) AS course_id,cs.id ";



 }else{
	 $old_stu=0; $old_str1='';$old_str2="";
	 $old_str0=" SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade,

 /*(case when y.session_yr='2019-2020' and (y.session='Winter' or y.session='Summer')  then (fd.cr_pts)/2 else fd.cr_pts end) as cr_pts,
 (case when y.session_yr='2019-2020' and (y.session='Winter' or y.session='Summer')   then (fd.cr_hr)/2 else fd.cr_hr end) as cr_hr, */

(case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (fd.cr_pts)/2 else fd.cr_pts end) as cr_pts,
  (case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (fd.cr_hr)/2 else fd.cr_hr end) as cr_hr,

 fd.mis_sub_id,
y.admn_no , y.ctotcrpts as stored_ctotcrpts  ,y.ctotcrhr as stored_ctotcrhr ,y.core_ctotcrpts  as stored_core_ctotcrpts ,y.core_ctotcrhr  as  stored_core_ctotcrhr ,
 IF(ac.alternate_subject_code IS NOT NULL,ac.old_subject_code, IF(acl.alternate_subject_code IS NOT NULL,acl.old_subject_code,fd.sub_code)) AS newsub,
 if(o.course_id IS NULL ,if(cs.id IS NOT NULL, 'honour',NULL ),o.course_id) AS course_id,cs.id ";
 }

	//echo $old_str2; die();

//echo $selectqry; die();


 if($selectqry=='select2'){
 $form_validation_str ="";$sem_select= "";
}
else{
 $form_validation_str =" join  reg_regular_form rg  on rg.admn_no=a.admn_no and  rg.hod_status='1' and rg.acad_status='1' and rg.session_year=? and rg.`session`=? ";
 //$sem_select= " if( rg.semester<>a.semester, a.semester, null)  as sem ,rg.semester as reg_sem , " ;
 $sem_select= "";
}
 /*if(isset($sem)){
	 $sem_str= "  and  a.semester<='$sem'  ";
 }*/


 $sem_str= "".($session=='Monsoon'?  " and  a.session_yr<='$session_yr'	and !( a.session_yr='$session_yr' and  (a.session='Winter'  or a.session='Summer') ) " :  (  $session=='Winter'?  " and  a.session_yr<='$session_yr'	and !( a.session_yr='$session_yr' and   a.session='Summer' ) " : " and a.session_yr<='$session_yr' " )  )."";

if($param=='based_on_publish_date'){
	$str_pub1=" ,a.published_on ";
	$str_pub2=" ,x.published_on ";
}
else{
	$str_pub1=" ,a.actual_published_on ";
	$str_pub2=" ,x.actual_published_on ";
}


  $onlinepaper=$this->get_online_paper_offered($session_yr,$session,'all');
 //  echo $this->db->last_query();die();
		   $ctr=0;
		   if( isset($onlinepaper)&& count($onlinepaper)>0)
		     foreach($onlinepaper as $rr){
		     $ctr++;
			 $online_paper_list.=  "'".$rr->code."'".($ctr==count($onlinepaper)?"":",")  ;
		   }
		   else
		   $online_paper_list="";




// echo'<br/>'. $online_paper_list; echo  ',cr:'.$course_id.',syr:'.$session_yr.',sess:'.$session.',admn:'.$admn_no.'foil:'.$foil_id.',based:'.$param.',select:'.$selectqry; die();


  if(strtolower($course_id)<>'jrf' /*&& strtolower($course_id)<>'minor'*/){

if (strtolower($course_id)=='minor'){
		$minor_txt=" UPPER(a.course)='MINOR' ";
		$minor_txt1= " UPPER(y1.course)='MINOR' ";
}
	 else{
		 $minor_txt=" UPPER(a.course)<>'MINOR' ";
		 	$minor_txt1= " UPPER(y1.course)<>'MINOR' ";
	 }


  // echo "online paper : ".$online_paper_list;
  if($online_paper_list<>"")
	          if (substr_count($online_paper_list, ',') > 0) {
				  $online_str1= " or  z.sub_code in(".$online_paper_list.") ";
				  $online_str2= " and z.sub_code not in(".$online_paper_list.") ";
				  $online_str3= " z.sub_code in(".$online_paper_list.") ";
			   }
			   else{
				   $online_str1= " or z.sub_code =".$online_paper_list." ";
				   $online_str2= " and  z.sub_code <> ".$online_paper_list." ";
				     $online_str3= "  if(  (z.sub_code =".$online_paper_list." ) , 0,  z.cr_hr)      ";
			   }
			else
			 {
        		 $online_str1="";
		         $online_str2="";
				 $online_str3= "  z.cr_hr ";
			 }



if($selectqry=='select' || $selectqry=='select2'){

$select_passfail="

 SUM(IF ((TRIM(z.grade='F') OR TRIM(z.grade)='I'), 0, 1)) AS overall_pass_sub_ctr,
 GROUP_CONCAT(IF ((TRIM(z.grade<>'F')  and TRIM(z.grade)<>'I'), z.sub_code, NULL)) AS overall_pass_sub_list,
SUM(IF ((TRIM(z.grade='F') OR TRIM(z.grade)='I' ), 1, 0)) AS overall_pass_status,
    group_concat(IF ((TRIM(z.grade='F') OR TRIM(z.grade)='I' ), z.sub_code, null)) AS overall_fail_subjects,

	 GROUP_CONCAT( distinct( IF ((TRIM(z.grade='F') OR TRIM(z.grade)='I'),  z.semester , NULL)) ) AS overall_fail_semester,

    SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL) AND (z.grade='I' or z.grade='F')), 1, 0)) AS overall_core_pass_status,
       group_concat(IF (((z.course_id<> 'honour' OR z.course_id IS NULL) AND (z.grade='I' or z.grade='F')), z.sub_code, null)) AS overall_core_fail_subjects,
       group_concat( distinct( IF (((z.course_id<> 'honour' OR z.course_id IS NULL) AND (z.grade='I' or z.grade='F')), z.semester, null))) AS overall_core_fail_semester,

	   GROUP_CONCAT(DISTINCT(IF (((z.course_id<> 'honour' OR z.course_id IS NULL) AND (z.grade='I' OR z.grade='F')), concat(z.sub_code,'(',z.semester , (case when z.semester in(4,5,6,7,8,9,10) then 'th' when  z.semester='1'then '1st'  when  z.semester='2' then 'nd'  when  z.semester='3'then 'rd'  end)  , ')'  ) , NULL))) AS overall_core_fail_subject_semester,

	   group_concat( distinct( IF (((z.course_id= 'honour') AND (z.grade='I' or z.grade='F')), z.semester, null))) AS overall_hons_fail_semester,
	   group_concat(IF (((z.course_id= 'honour') AND (z.grade='I' or z.grade='F')), z.sub_code, null)) AS overall_hons_fail_subjects,
	   SUM(IF (((z.course_id= 'honour') AND (z.grade='I' or z.grade='F')), 1, 0)) AS overall_hons_pass_status,
	     GROUP_CONCAT(IF (((z.course_id= 'honour') AND (z.grade='I' OR z.grade='F')),concat(z.sub_code,'(',z.semester , (case when z.semester in(4,5,6,7,8,9,10) then 'th' when  z.semester='1'then '1st'  when  z.semester='2' then 'nd'  when  z.semester='3'then 'rd'  end)  , ')'  ), NULL)) AS overall_hons_fail_subjects_semester,




	";
}
else
	$select_passfail='';

$select="SELECT   $select_passfail

          SUM(  if(  (z.grade='I' or z.grade='F'  $online_str1 ) , 0, z.cr_pts)  ) AS ctotcrpts,

		  SUM(/*if(  ($online_str3 ) , 0,  z.cr_hr)*/   $online_str3) AS ctotcrhr,
          FORMAT(( SUM(  if(  (z.grade='I' or z.grade='F'  $online_str1 ) , 0, z.cr_pts)  )/SUM(/*if(  ($online_str3 ) , 0,  z.cr_hr)*/  $online_str3 ) ),5) AS cgpa,

SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)     and   (z.grade<>'I'  and z.grade<>'F'    $online_str2  ) ), z.cr_pts, 0)) AS core_ctotcrpts,
SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)  $online_str2  ), z.cr_hr, 0)) AS core_ctotcrhr,
 FORMAT((SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL)   and   (z.grade<>'I'  and z.grade<>'F'  $online_str2   )   ), z.cr_pts, 0))/ SUM(IF (((z.course_id<> 'honour' OR z.course_id IS NULL) $online_str2  ), z.cr_hr, 0))),5) AS core_cgpa,
z.admn_no,z.semester,z.foil_id




FROM
(
SELECT z1.* from
(
SELECT v.*
FROM (


$old_str0


 FROM

  $old_str1

  ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id as foil_id,a.`status`,
a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts , $sem_select    a.published_on, a.actual_published_on
FROM final_semwise_marks_foil_freezed AS a

$form_validation_str


WHERE  a.admn_no=? AND   $minor_txt AND
(a.semester!= '0' AND a.semester!='-1') and a.course<>'jrf' $sem_str
ORDER BY a.admn_no,a.semester $str_pub1   desc


LIMIT 100000000)x

  /*GROUP BY x.admn_no,IFNULL(x.sem, x.session_yr)*/

GROUP BY x.admn_no, x.semester  ,IF( /*x.semester<= x.reg_sem and*/ x.session_yr>='2019-2020' ,x.session_yr,null) ,  IF( /*x.semester<= x.reg_sem and*/ x.session_yr>='2019-2020' ,x.session,null)

   order by  x.admn_no,x.semester  $str_pub2  desc limit 100000000) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.foil_id AND

fd.admn_no=y.admn_no
$old_str2

LEFT JOIN alternate_course ac ON /*ac.session_year=y.session_yr AND ac.`session`=y.session AND*/
ac.admn_no=y.admn_no AND ac.alternate_subject_code=".($old_stu==1?"y" :"fd").".sub_code

LEFT JOIN alternate_course_all acl ON /*acl.session_year=y.session_yr AND acl.`session`=y.session AND */
acl.alternate_subject_code=".($old_stu==1?"y" :"fd").".sub_code
/*
left join old_subject_offered o on o.sub_code=".($old_stu==1?"y" :"fd").".sub_code and
o.session_year=y.session_yr and o.`session`=y.session and o.dept_id=y.dept and (case when o.course_id='honour'
then 'honour' else y.course end)=o.course_id and o.branch_id=y.branch
*/



left join old_stu_course os  on os.admn_no=y.admn_no and os.session_year=y.session_yr and os.`session`=y.session and os.subject_code=".($old_stu==1?"y" :"fd").".sub_code
/*and os.course=y.course and os.branch=y.branch*/

left join old_subject_offered o on o.id=os.sub_offered_id

LEFT JOIN  subjects s ON s.id = ".($old_stu==1?"y" :"fd").".mis_sub_id AND y.session_yr<'2019-2020'
LEFT JOIN  course_structure cs ON cs.id= ".($old_stu==1?"y" :"fd").".mis_sub_id AND cs.aggr_id LIKE '%honour%' AND  cs.semester=y.semester
AND y.session_yr<'2019-2020'

ORDER BY  y.admn_no, newsub,   (case when ".($old_stu==1?"y" :"fd").".remark2 ='Y' then null else ".($old_stu==1?"y" :"fd").".cr_pts END )    desc,y.session_yr DESC limit 10000000 )v
GROUP BY v.admn_no, v.newsub  ORDER BY v.admn_no, v.session_yr,v.dept,v.course,v.branch,v.semester,v.newsub   limit 10000000 ) z1
 GROUP BY z1.admn_no,z1.sub_code)z

GROUP BY
z.admn_no/*,z.semester*/       ";

//if($selectqry=='select'){


	//$sql=($selectqry=='select'?$select:"
	$sql=(($selectqry=='select' || $selectqry=='select2')?$select:"
     update  final_semwise_marks_foil_freezed y1 join

     ($select)B1
on    B1.admn_no=y1.admn_no   and  y1.session_yr=? and y1.`session`=? and   $minor_txt1 AND
(y1.semester!= '0' AND y1.semester!='-1') and y1.course<>'jrf'   and  y1.id=?
set y1.ctotcrhr=B1.ctotcrhr,
	y1.ctotcrpts =B1.ctotcrpts,
	y1.core_ctotcrhr=B1.core_ctotcrhr,
	y1.core_ctotcrpts= B1.core_ctotcrpts,
    y1.cgpa=B1.cgpa,
    y1.core_cgpa=B1.core_cgpa");

//echo $sql;die();
  }
	else if(strtolower($course_id)=='jrf' && strtolower($course_id)<>'minor'){
if($online_paper_list<>"")
        if (substr_count($online_paper_list, ',') > 0) {
				  $online_str1= " or  z.sub_code in(".$online_paper_list.") ";
				  $online_str2= " and z.sub_code not in(".$online_paper_list.") ";
				  $online_str3= " z.sub_code in(".$online_paper_list.") ";
			   }
			   else{
				   $online_str1= " or z.sub_code =".$online_paper_list." ";
				   $online_str2= " and  z.sub_code <> ".$online_paper_list." ";
				   $online_str3= " z.sub_code =".$online_paper_list." ";
			   }
			else
			 {
        		 $online_str1="";
		         $online_str2="";
			 }

$thesispaper=$this->get_thesis_paper_offered($session_yr,$session,'all');
 //  echo $this->db->last_query();die();
		   $ctr=0;
		   if( isset($thesispaper)&& count($thesispaper)>0)
		     foreach($thesispaper as $rr){
		     $ctr++;
			 $thesis_paper_list.=  "'".$rr->code."'".($ctr==count($thesispaper)?"":",")  ;
		   }
		   else
		   $thesis_paper_list="";
if($thesis_paper_list<>"")
        if (substr_count($thesis_paper_list, ',') > 0) {
				  $thesis_str1= " or  z.sub_code in(".$thesis_paper_list.") ";
				  $thesis_str2= " and z.sub_code not in(".$thesis_paper_list.") ";
				  $thesis_str3= " z.sub_code in(".$thesis_paper_list.") ";
			   }
			   else{
				   $thesis_str1= " or z.sub_code =".$thesis_paper_list." ";
				   $thesis_str2= " and  z.sub_code <> ".$thesis_paper_list." ";
				   $thesis_str3= " z.sub_code =".$thesis_paper_list." ";
			   }
			else
			 {
        		 $thesis_str1="";
		         $thesis_str2="";
			 }


if($selectqry=='select' || $selectqry=='select2'){

$select_passfail="

     SUM(IF ((TRIM(z.grade)='F' or z.grade='X' OR TRIM(z.grade)='I'), 0, 1)) AS overall_pass_sub_ctr,
     GROUP_CONCAT(IF ((TRIM(z.grade)<>'F' and z.grade<>'X' and TRIM(z.grade)<>'I'), z.sub_code, NULL)) AS overall_pass_sub_list,
     SUM(IF ((TRIM(z.grade)='F' or z.grade='X' OR TRIM(z.grade)='I' ), 1, 0)) AS overall_pass_status,
     group_concat(IF ((TRIM(z.grade)='F' or  z.grade='X' OR TRIM(z.grade)='I' ), z.sub_code, null)) AS overall_fail_subjects,
	    GROUP_CONCAT( distinct( IF ((TRIM(z.grade)='F' OR TRIM(z.grade)='I'),  concat(z.session_yr,'/',z.session) , NULL)) ) AS overall_fail_semester,
	";
}
else
	$select_passfail='';

$select="SELECT   $select_passfail


 SUM( if(( (((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) or (z.dept='hss' and z.grade='X'  ) ) or  z.grade='I' or z.grade='F'   $online_str1 $thesis_str1  ), 0,   z.cr_pts) )AS ctotcrpts,
SUM( if(( ((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) /* or z.grade='I' or z.grade='F' */  $online_str1  $thesis_str1 ) , 0, z.cr_hr) )AS ctotcrhr,
FORMAT((SUM( if(( (((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) or (z.dept='hss' and z.grade='X'  ) ) or z.grade='I' or z.grade='F'  $online_str1 $thesis_str1 ), 0,   z.cr_pts) )/SUM( if((((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) /* or z.grade='I' or z.grade='F' */  $online_str1 $thesis_str1 ) , 0, z.cr_hr)) ),5) AS cgpa,
SUM( if(( (((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) or (z.dept='hss' and z.grade='X'  ) ) or z.grade='I' or z.grade='F'  $online_str1 $thesis_str1 ), 0,   z.cr_pts) ) AS core_ctotcrpts,
SUM( if(( ((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) /* or z.grade='I' or z.grade='F' */ $online_str1 $thesis_str1) , 0, z.cr_hr) ) AS core_ctotcrhr,
FORMAT((SUM( if(( (((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) or (z.dept='hss' and z.grade='X'  ) ) or z.grade='I' or z.grade='F'  $online_str1 $thesis_str1), 0,   z.cr_pts) )/SUM( if(( ((z.grade='S'  or  z.grade='X')  and  z.dept<>'hss'  ) /* or z.grade='I' or z.grade='F' */  $online_str1 $thesis_str1 ) , 0, z.cr_hr)) ),5)  AS core_cgpa,

z.admn_no


FROM
(
SELECT z1.* from
(
SELECT v.*
FROM (
SELECT y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade,
/*fd.cr_pts,
 fd.cr_hr,*/
  (case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (fd.cr_pts)/2 else fd.cr_pts end) as cr_pts,
  (case when ((y.session_yr='2019-2020' and  (y.session='Winter' or y.session='Summer')) or  (y.session_yr='2020-2021' and  (y.session='Monsoon' or  y.session='Winter' /* or  y.session='Summer' */)) ) then (fd.cr_hr)/2 else fd.cr_hr end) as cr_hr,
 fd.mis_sub_id,
y.admn_no
,IF(ac.alternate_subject_code IS NOT NULL,ac.old_subject_code, IF(acl.alternate_subject_code IS NOT NULL,acl.old_subject_code,fd.sub_code)) AS newsub



 FROM ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id,a.`status`,
	a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts
	FROM final_semwise_marks_foil_freezed AS a

	$form_validation_str

	WHERE a.admn_no=? AND  UPPER(a.course)<>'MINOR'  and  lower(a.course)='jrf'

     $sem_str
	 ORDER BY a.admn_no,a.session_yr ,a.actual_published_on DESC


		 LIMIT 100000000)x  GROUP BY x.admn_no,x.session_yr,x.session) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.id AND

			fd.admn_no=y.admn_no

			LEFT JOIN alternate_course ac ON /*ac.session_year=y.session_yr AND ac.`session`=y.session AND*/
			 ac.admn_no=y.admn_no AND ac.alternate_subject_code=fd.sub_code

				 LEFT JOIN alternate_course_all acl ON /*acl.session_year=y.session_yr AND acl.`session`=y.session AND*/
					 acl.alternate_subject_code=fd.sub_code




 						ORDER BY  y.admn_no, newsub,  (case when fd.remark2 ='Y' then null else fd.cr_pts end) desc, y.session_yr DESC LIMIT 100000000 )v
						 GROUP BY v.admn_no, v.newsub  ORDER BY v.admn_no, v.session_yr,v.dept,v.course,v.branch,v.semester,v.newsub LIMIT 100000000 ) z1
						  GROUP BY z1.admn_no,z1.sub_code)z

							 GROUP BY
							z.admn_no";



//$sql=($selectqry=='select'?$select:"
$sql=(($selectqry=='select' || $selectqry=='select2')?$select:"
    update ignore final_semwise_marks_foil_freezed y join
      ($select) B
    on B.admn_no=y.admn_no   and  y.session_yr=? and y.`session`=? and   UPPER(y.course)<>'MINOR' AND lower( y.course)='jrf'    and  y.id=?
    set
	y.ctotcrhr=B.ctotcrhr,
	y.ctotcrpts =B.ctotcrpts,
	y.core_ctotcrhr=B.core_ctotcrhr,
	y.core_ctotcrpts= B.core_ctotcrpts,
    y.cgpa=B.cgpa,
    y.core_cgpa=B.core_cgpa");
// echo  'jrf-----------------';


	 }
	 // $query = $this->db->query($sql, (($selectqry=='select')?
	  //array($session_yr,$session,$admn_no):array($session_yr,$session,$admn_no,$session_yr,$session,$foil_id)));

	    $query = $this->db->query($sql, (($selectqry=='select' || $selectqry=='select2')?
	    ( ($selectqry=='select2'? array($admn_no):array($session_yr,$session,$admn_no)) ) :array($session_yr,$session,$admn_no,$session_yr,$session,$foil_id)));


	//echo $this->db->last_query(); die();


     //return ($selectqry=='select'?$query->row(): $this->db->affected_rows());
	  return (($selectqry=='select'|| $selectqry=='select2')?$query->row(): $this->db->affected_rows());
	  }




/*
function  upadte_foil_cgpa_based_on_freeze($course_id,$session_yr,$session,$admn_no,$old_id,$id){

	   $sql=" update  final_semwise_marks_foil y1  JOIN final_semwise_marks_foil_freezed B ON B.old_id=y1.id
and  y1.admn_no =?  and  y1.session_yr=? and y1.`session`=? and   UPPER(y1.course)<>'MINOR' AND (y1.semester!= '0' AND y1.semester!='-1')
  and  y1.id=? and B.id=?


set y1.ctotcrhr=B.ctotcrhr,
	y1.ctotcrpts =B.ctotcrpts,
	y1.core_ctotcrhr=B.core_ctotcrhr,
	y1.core_ctotcrpts= B.core_ctotcrpts,
    y1.cgpa=B.cgpa,
    y1.core_cgpa=B.core_cgpa";

    $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$old_id,$id));

    // echo $this->db->last_query(); die();
       return $this->db->affected_rows();
}
*/



function  upadte_foil_cgpa_based_on_freeze($course_id,$session_yr,$session,$admn_no,$old_id,$id){
if (strtolower($course_id)=='minor')
		$minor_txt=" UPPER(y1.course)='MINOR' ";
	 else
		 $minor_txt=" UPPER(y1.course)<>'MINOR' ";


	   $sql=" update  final_semwise_marks_foil y1  JOIN final_semwise_marks_foil_freezed B ON B.old_id=y1.id
and  y1.admn_no =?  and  y1.session_yr=? and y1.`session`=? and   $minor_txt AND (y1.semester!= '0' AND y1.semester!='-1')
  and  y1.id=? and B.id=?


set y1.ctotcrhr=B.ctotcrhr,
	y1.ctotcrpts =B.ctotcrpts,
	y1.core_ctotcrhr=B.core_ctotcrhr,
	y1.core_ctotcrpts= B.core_ctotcrpts,
    y1.cgpa=B.cgpa,
    y1.core_cgpa=B.core_cgpa";

    $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$old_id,$id));

    // echo $this->db->last_query(); die();
       return $this->db->affected_rows();
}

/*
function  upadte_freeze_gpa_based_on_foil($course_id,$session_yr,$session,$admn_no,$id){

	   $sql=" update  final_semwise_marks_foil_freezed y1  JOIN final_semwise_marks_foil B ON B.id=y1.old_id
       and  y1.admn_no =?  and  y1.session_yr=? and y1.`session`=? and   UPPER(y1.course)<>'MINOR' AND (y1.semester!= '0' AND y1.semester!='-1')
  and  y1.id=?


set y1.tot_cr_hr=B.tot_cr_hr,
	y1.tot_cr_pts =B.tot_cr_pts,
	y1.core_tot_cr_hr=B.core_tot_cr_hr,
	y1.core_tot_cr_pts= B.core_tot_cr_pts,
    y1.gpa=B.gpa,
    y1.core_gpa=B.core_gpa";

    $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$id));

    // echo $this->db->last_query(); die();
       return $this->db->affected_rows();
}
*/

function  upadte_freeze_gpa_based_on_foil($course_id,$session_yr,$session,$admn_no,$id){
//echo  $course_id; die();

 if (strtolower($course_id)=='minor')
		$minor_txt=" UPPER(y1.course)='MINOR' ";
	 else
		 $minor_txt=" UPPER(y1.course)<>'MINOR' ";


	   $sql=" update  final_semwise_marks_foil_freezed y1  JOIN final_semwise_marks_foil B ON B.id=y1.old_id
       and  y1.admn_no =?  and  y1.session_yr=? and y1.`session`=? and   $minor_txt  AND (y1.semester!= '0' AND y1.semester!='-1')
       and  y1.id=?


set y1.tot_cr_hr=B.tot_cr_hr,
	y1.tot_cr_pts =B.tot_cr_pts,
	y1.core_tot_cr_hr=B.core_tot_cr_hr,
	y1.core_tot_cr_pts= B.core_tot_cr_pts,
    y1.gpa=B.gpa,
    y1.core_gpa=B.core_gpa,
	y1.status=B.status,
	y1.core_status=B.core_status
	";

    $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$id));

     //echo $this->db->last_query(); die();
       return $this->db->affected_rows();
}


function   check_eligibility_for_grading($session_yr,$session,$admn_no,$crs_type=null){

if($crs_type=='minor'){
	$minor=" AND a.course='minor' ";
	$minor1=" AND z.course='minor' ";
}
else{

	$minor=" AND a.course<>'minor' ";
	$minor1=" AND z.course<>'minor' ";
}

	$sql="SELECT x.admn_no,x.form_id,x.session_year,x.session,x.subject_code, COUNT(x.subject_code) AS reg_paper_cnt,
(
SELECT COUNT(*)
FROM (
SELECT z.*
FROM final_semwise_marks_foil_freezed z
INNER JOIN final_semwise_marks_foil_desc_freezed y ON z.id=y.foil_id AND z.admn_no=y.admn_no
WHERE z.admn_no=? AND z.session_yr=? AND z.`session`=? $minor1  AND z.course<>'prep'
GROUP BY y.sub_code
ORDER BY z.semester,z.actual_published_on DESC) f) AS freez_paper_count

FROM (

SELECT  * FROM cbcs_stu_course a WHERE a.admn_no=? AND a.session_year=? AND a.`session`=?  $minor  AND a.course<>'prep'
UNION
SELECT * FROM old_stu_course a WHERE a.admn_no=? AND a.session_year=? AND a.`session`=? $minor AND a.course<>'prep' ) x

";

	 $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$admn_no,$session_yr,$session,$admn_no,$session_yr,$session));
	 // echo $this->db->last_query(); die();

	 	return ($query->row()->reg_paper_cnt==$query->row()->freez_paper_count?1:0);
}


function check_stu_subject_registration($session_yr,$session,$admn_no,$crs_type=null){

if($crs_type=='minor'){
	$minor=" AND a.course='minor' ";

}
else{

	$minor=" AND a.course<>'minor' ";

}
$sql="select COUNT(x.subject_code) AS paper_count	FROM (

SELECT  * FROM cbcs_stu_course a WHERE a.admn_no=? AND a.session_year=? AND a.`session`=?  $minor  AND a.course<>'prep'
UNION
SELECT * FROM old_stu_course a WHERE a.admn_no=? AND a.session_year=? AND a.`session`=? $minor AND a.course<>'prep' ) x";
 $query = $this->db->query($sql,array($admn_no,$session_yr,$session,$admn_no,$session_yr,$session));
return ($query->row()->paper_count);

}


function check_missing_paper_in_foil_cbcs_based($admn_no,$sessyr='2019-2020',$session,$crs_type=null){
$session_yr=$sessyr;
if($crs_type=='minor'){
	$minor=" AND a.course='minor' ";
	$minor1=" AND z.course='minor' ";
}
else{

	$minor=" AND a.course<>'minor' ";
	$minor1=" AND z.course<>'minor' ";
}



  if($sessyr=='2019-2020'){
	$strwhere=" a.session_year='$sessyr' ";
	$strwhere2=" z.session_yr='$sessyr' ";
  }
  else{
	$strwhere=" a.session_year>='2019-2020' ";
	$strwhere2=" z.session_yr>='2019-2020' ";
  }
$sem_str1= "".($session=='Monsoon'?  " and  a.session_year<='$session_yr'	and !( a.session_year='$session_yr' and  (a.session='Winter'  or a.session='Summer') ) " :  (  $session=='Winter'?  " and  a.session_year<='$session_yr'	and !( a.session_year='$session_yr' and   a.session='Summer' ) " : " and a.session_year<='$session_yr' " )  )."";


$sem_str2= "".($session=='Monsoon'?  " and  z.session_yr<='$session_yr'	and !( z.session_yr='$session_yr' and  (z.session='Winter'  or z.session='Summer') ) " :  (  $session=='Winter'?  " and  z.session_yr<='$session_yr'	and !( z.session_yr='$session_yr' and   z.session='Summer' ) " : " and z.session_yr<='$session_yr' " )  )."";



	$sql="

select x.subject_code,p.sub_code
,group_concat( distinct x.session_year) as ly_in_session_yr ,group_concat(x.session) as ly_in_session ,group_concat( distinct x.semester) as ly_in_semester

, group_concat(distinct concat( (  x.semester),'/',x.subject_code,'*') ) as paper_sem_list

from
(
SELECT a.*,cso.semester
FROM cbcs_stu_course a
join  cbcs_subject_offered cso on cso.id=a.sub_offered_id
WHERE $strwhere   $sem_str1/*AND a.`session`='Monsoon' */ $minor AND a.course<>'prep' AND a.admn_no=?


UNION
SELECT a.*,oso.semester
FROM old_stu_course a
join  old_subject_offered oso on oso.id=a.sub_offered_id
WHERE $strwhere   $sem_str1/*AND a.`session`='Monsoon' */ $minor AND a.course<>'prep' AND a.admn_no=?

) x


left join




(SELECT z.*
FROM (
SELECT z.*,y.sub_code
FROM final_semwise_marks_foil_freezed z
INNER JOIN final_semwise_marks_foil_desc_freezed y ON z.id=y.foil_id AND z.admn_no=y.admn_no AND z.admn_no=?
WHERE   $strwhere2 $sem_str2 /*AND z.`session`='Monsoon' */$minor1 AND z.course<>'prep'
ORDER BY z.session_yr,z.session,z.actual_published_on DESC
LIMIT 100000)z
GROUP BY z.admn_no, z.session_yr,z.session ,z.sub_code) p on  x.admn_no=p.admn_no and x.subject_code=p.sub_code  /*and x.session_year=p.session_yr  and x.session=p.session*/
 group by   x.session_year,x.session, x.subject_code
having p.sub_code is null
	";

	// echo  $sql; die();
	 $query = $this->db->query($sql,array($admn_no,$admn_no,$admn_no));
	        //    echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
	  if ($query->num_rows() > 0) {
				return $query->result();
	          }
       else  return 0;


}





function get_cbcs_marks_status($session,$sessyr,$dept=null,$course=null,$branch=null,$sem=null){


if(!empty($dept))
{
	$dept_str=" and a.dept_id=? ";
	$secure_array=array($sessyr,$session,$dept,$sessyr,$session,$dept);
}else
{
    $dept_str="";
	$secure_array=array($sessyr,$session,$sessyr,$session);
}



	$sql="

(select y.* from

(


select x.*,c.`status`,c.id as marks_id from(
select concat('o',a.id)as sid,a.id,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_type,a.sub_code,a.sub_name,
 group_concat(concat ( concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name),'[',   d.emp_no,']') ) as ft,md.exam_type
from
(select a.* from  old_subject_offered a  where a.session_year=? and a.`session`=? $dept_str  )a
left JOIN old_subject_offered_desc d on d.sub_offered_id=a.id
left join  user_details u on u.id=d.emp_no 
left JOIN cbcs_modular_paper_main md ON a.sub_code=md.sub_code AND a.session_year=md.session_year and a.`session`=md.`session` and a.course_id=md.course_id and a.branch_id=md.branch_id
group by a.id,a.sub_code,(case when a.sub_type='Modular' then md.id ELSE 1=1 end )  order by a.sub_code )x
left join cbcs_marks_master c on c.sub_map_id=concat('o',x.id)  and c.subject_id=x.sub_code
 /*where c.id is null*/
 )y
 order by  y.dept_id,y.course_id,y.branch_id,y.semester,y.sub_code)

union all

(
select y.* from

(
select x.*,c.`status`,c.id as marks_id from(
select concat('c',a.id)as sid,a.id,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_type,a.sub_code,a.sub_name,
 group_concat(concat ( concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name),'[',   d.emp_no,']') ) as ft,md.exam_type
from
(select a.* from  cbcs_subject_offered a  where a.session_year=? and a.`session`=?   $dept_str)  a
left JOIN cbcs_subject_offered_desc d on d.sub_offered_id=a.id
left join  user_details u on u.id=d.emp_no 
left JOIN cbcs_modular_paper_main md ON a.sub_code=md.sub_code AND a.session_year=md.session_year and a.`session`=md.`session` and a.course_id=md.course_id and a.branch_id=md.branch_id
group by a.id, a.sub_code,(case when a.sub_type='Modular' then md.id ELSE 1=1 end )  order by a.sub_code )x
left join cbcs_marks_master c on c.sub_map_id=concat('c',x.id)
 /* where c.id is null*/
 )y
 order by  y.dept_id,y.course_id,y.branch_id,y.semester,y.sub_code)
order by
dept_id,course_id,branch_id,semester,sub_code



	";

	// echo  $sql; die();
	 $query = $this->db->query($sql,$secure_array);
	     //      echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
	  if ($query->num_rows() > 0) {
				return $query->result();
	          }
       else  return 0;


}

function  get_thesis_taking_admission_no($session,$sessionyr,$branch){
	
	$sql="
SELECT p.admn_no1 from

(
SELECT cOUNT(g.subject_code), upper(g.admn_no) as admn_no1,group_concat(g.subject_code) AS  slist,group_concat(g.subject_code)
,g.branch,g.subject_code from
(SELECT * FROM old_stu_course os  WHERE os.session_year=? AND os.`session`=? AND os.course='jrf' and os.branch='$branch'

union
SELECT * FROM cbcs_stu_course cs  WHERE cs.session_year=? AND cs.`session`=? AND cs.course='jrf'
and cs.branch='$branch'
)g
GROUP BY g.branch,g.admn_no
)p   WHERE
p.slist LIKE  '%599%' ";

$secure_array=array($sessionyr,$session, $sessionyr,$session );

$query = $this->db->query($sql,$secure_array);
	        //   echo  $this->db->last_query();  	  echo '<pre>';print_r($query->result()); echo '</pre>';		die();
	  if ($query->num_rows() > 0) {
				return $query->result();
	          }
       else  return 0;

}











}

?>
