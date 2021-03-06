<?php

class Offer_subject_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    // Course Master by @bhijeet start

      function get_cm_dept(){
        $sql="select * from cbcs_set_cm_dept a where a.`status`='1' order by a.id desc";
        $query = $this->db->query($sql);
      //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->last_row()->dept;
        else
            return FALSE;
      }


    // Course Master by @bhijeet end

//     function get_dcb_list($id)
//     {

//       $sql = "SELECT a.dept_id,b.course_id,b.branch_id,c.name AS dname,
// d.name AS cname,e.name AS bname
// FROM dept_course a
// INNER JOIN course_branch b ON b.course_branch_id=a.course_branch_id
// INNER JOIN cbcs_departments c ON c.id=a.dept_id
// INNER JOIN cbcs_courses d ON d.id=b.course_id
// INNER JOIN cbcs_branches e ON e.id=b.branch_id
// WHERE a.dept_id=? AND c.status=1 AND d.status=1 AND e.status=1
// GROUP BY c.id,d.id,e.id";


//         $query = $this->db->query($sql,array($id));


//         if ($this->db->affected_rows() > 0) {
//             return $query->result();
//         } else {
//             return false;
//         }
//     }




    // copy offer course of Previous session start


  function get_reg_regular_cnt($session_year,$session,$course_id,$branch_id,$semester){
    $sqlreg="select * from reg_regular_form a where a.session_year='$session_year' and a.`session`='$session' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.semester='$semester'";

    $queryreg = $this->db->query($sqlreg);
    $numRows=$queryreg->num_rows();
    return $numRows;
  }

    function InsertSubjectDiscription($insertCourseDescription){
      $selectValue=array(
        "sub_offered_id"=>$insertCourseDescription['sub_offered_id'],
        "emp_no"=>$insertCourseDescription['emp_no'],
        "coordinator"=>$insertCourseDescription['coordinator'],
        "sub_id"=>$insertCourseDescription['sub_id'],
        "section"=>$insertCourseDescription['section'],
      );
      $this->db->select('*');
      $this->db->from('cbcs_subject_offered_desc');
      $this->db->where($selectValue);
      $cnt = $this->db->get()->num_rows();
      if($cnt==0){
      $this->db->insert('cbcs_subject_offered_desc', $insertCourseDescription);
      if($this->db->affected_rows() > 0){
        return true;
      }else{
        return false;
      }
      }else{
      return false;
      }
    }

    function getSubjectDiscription($id){
      $sql="select * from cbcs_subject_offered_desc a where a.sub_offered_id='$id'";
            $query = $this->db->query($sql);
            if ($this->db->affected_rows() > 0) {
          //   echo  $this->db->last_query();
                return $query->result();
            } else {
                return false;
            }
    }

    function insertCourseOffered($insertCourse){
      $selectValue=array(
        "session"=>$insertCourse['session'],
        "session_year"=>$insertCourse['session_year'],
        "sub_code"=>$insertCourse['sub_code'],
        "course_id"=>$insertCourse['course_id'],
        "branch_id"=>$insertCourse['branch_id'],
        "semester"=>$insertCourse['semester'],
      );
      $this->db->select('*');
      $this->db->from('cbcs_subject_offered');
      $this->db->where($selectValue);
      $cnt = $this->db->get()->num_rows();
      if($cnt==0){
      $this->db->insert('cbcs_subject_offered', $insertCourse);
      if($this->db->affected_rows() > 0){
        return $this->db->insert_id();
      }else{
        return false;
      }
      }else{
      return false;
      }
    }

    function copyofferedCourse($session_year,$session,$dept_id,$course_id,$branch_id,$semester,$credit_policy_id){
      $session_yrData=explode("-",$session_year);
      $s1=$session_yrData[0]-1;
      $s2=$session_yrData[1]-1;
      $prevSession_year=$s1."-".$s2;

      $sql="select * from cbcs_subject_offered a where a.session_year='$prevSession_year' and a.`session`='$session'
            and a.semester='$semester' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.dept_id='$dept_id'";
            $query = $this->db->query($sql);
            if ($this->db->affected_rows() > 0) {
          //   echo  $this->db->last_query();
                return $query->result();
            } else {
                return false;
            }

    }


    function checkTemp($session_year,$session,$dept_id,$course_id,$branch_id,$semester){
      $session_yrData=explode("-",$session_year);
      $s1=$session_yrData[0];
      $s2=$session_yrData[1];
      $sqlreg="select * from reg_regular_form a where a.session_year='' and a.`session`='' and a.course_id='' and a.branch_id='' and a.semester=''";
    //  $sqlreg="select * from reg_regular_form a where a.session_year='$session_year' and a.`session`='$session' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.semester='$semester'";

      $queryreg = $this->db->query($sqlreg);
      $numRows=$queryreg->num_rows();
      if($numRows==0){
      $sql="select * from cbcs_credit_points_policy a where a.course_id='$course_id' order by id desc limit 1";
      $query = $this->db->query($sql);
      if ($this->db->affected_rows() > 0) {
       //echo  $this->db->last_query();
        $result=$query->result();
        $wef=$result[0]->wef;
        $credit_policy_id=$result[0]->id;
        $session_yrWef=explode("-",$wef);
        $wef1=$session_yrWef[0];
        if($wef1==$s1){
          return false;
        }else{
          return $credit_policy_id;
        }
        } else {
          return false;
      }
    }else{
      return false;
    }
    }

    function get_current_offered_details($session_year,$session,$dept_id,$course_id,$branch_id,$semester){
      $session_yrData=explode("-",$session_year);
      $s1=$session_yrData[0]-1;
      $s2=$session_yrData[1]-1;
      $prevSession_year=$s1."-".$s2;

    $sql="(select a.id,a.session_year,a.`session`,a.dept_id,a.course_id,a.branch_id,a.sub_code from cbcs_subject_offered a
          inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
          where a.dept_id='$dept_id' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.semester='$semester' and a.session_year='$session_year' and a.`session`='$session'
          group by b.sub_offered_id)";
          $query = $this->db->query($sql);

        //  $numRows=$this->db->last_query(); die();
          $numRows=$query->num_rows();

        if($numRows > 0)
        {

        }else{
          $sqlprev="(select a.id,a.session_year,a.`session`,a.dept_id,a.course_id,a.branch_id,a.sub_code from cbcs_subject_offered a
                inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
                where a.dept_id='$dept_id' and a.course_id='$course_id' and a.branch_id='$branch_id' and a.semester='$semester' and a.session_year='$prevSession_year' and a.`session`='$session'
                group by b.sub_offered_id)";
                $queryprev = $this->db->query($sqlprev);
                $numRowss=$queryprev->num_rows();
                if($numRowss > 0){
                  return  "1";
                }else{
                  return "0";
                }
        }

      }







      // copy offer course of Previous session end

function saveOrUpdateCourseCordinatorModuler($data){
  $selectValue=array(
    "session"=>$data['session'],
    "session_year"=>$data['session_year'],
    "sub_code"=>$data['sub_code'],
    "sub_group"=>$data['sub_group'],
    "sub_type"=>$data['sub_type'],
    "exam_type"=>$data['exam_type'],
  );
  $marks_master=array(
    "sub_map_id"=>$data['sub_offered_id'],
    "subject_id"=>$data['sub_code'],
    "session"=>$data['session'],
    "session_year"=>$data['session_year'],
    "process_timestamp"=>date("d-m-Y H:i:s"),
    "emp_id"=>$data['co_emp_id'],
  );
  $marks_master_update=array(
  //  "sub_map_id"=>$data['sub_offered_id'],
    "subject_id"=>$data['sub_code'],
    "session"=>$data['session'],
    "session_year"=>$data['session_year']

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
                    // comment on 6-9-19
                //    $this->db->insert('cbcs_marks_master', $marks_master);
										echo "Course Coordinator Assigned Successfully";
									}
}else{
  $this->db->where($selectValue);
  $this->db->update('cbcs_assign_course_coordinator', $data);

  if($this->db->affected_rows() != 1){

                    echo"cbcs_assign_course_coordinator :".	$this->db->_error_message();

                  }else{
                    // comment on 6-9-19
                //    $this->db->where($marks_master_update);
                //    $this->db->update('cbcs_marks_master', $marks_master);
                //      echo  $this->db->last_query();die();
                    echo "Course Coordinator Updated Successfully";
                  }
}
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
function offered_subject_Moduler_List($dept_id,$session,$session_year){
  $sql = "(select a.id,a.sub_code,a.sub_name,ca.`status` from cbcs_subject_offered a
  LEFT JOIN cbcs_assign_course_coordinator ca on a.sub_code=ca.sub_code and a.`session`=ca.session and a.session_year=ca.session_year
  LEFT JOIN cbcs_dept_code x on LEFT(a.sub_code,3)=x.course_code
  where left(a.sub_code,3) in (select b.course_code from cbcs_dept_code b where b.offering_dept_id='$dept_id') and a.session_year='$session_year' and a.`session`='$session' and a.sub_type ='Modular'  group by a.sub_code)
union
(select a.id,a.sub_code,a.sub_name,ca.`status` from old_subject_offered  a
LEFT JOIN cbcs_assign_course_coordinator ca on a.sub_code=ca.sub_code and a.`session`=ca.session and a.session_year=ca.session_year
LEFT JOIN cbcs_dept_code x on LEFT(a.sub_code,3)=x.course_code
where left(a.sub_code,3) in (select b.course_code from cbcs_dept_code b where b.offering_dept_id='$dept_id') and a.session_year='$session_year' and a.`session`='$session' and a.sub_type ='Modular'   and LENGTH(a.sub_code)=x.course_digit group by a.sub_code)
";
    $query = $this->db->query($sql);
  //  echo  $this->db->last_query();
    if ($this->db->affected_rows() > 0) {
  //   echo  $this->db->last_query();
        return $query->result();
    } else {
        return false;
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

function get_teaching_faulty_single($subject,$session,$session_year){
  $sql = "select * from ((select a.id as sub_offered_id,a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join cbcs_stu_course c on a.id=c.sub_offered_id
#Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to
where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' and b.coordinator=1 group by b.emp_no)
union
(select a.id as sub_offered_id,a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from old_subject_offered a
inner join old_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join old_stu_course c on a.id=c.sub_offered_id
#Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code  and a.dept_id=acc.offered_to
where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' and b.coordinator=1 group by b.emp_no)) z group by z.emp_no";
    $query = $this->db->query($sql);
  #  echo  $this->db->last_query(); exit;
    if ($this->db->affected_rows() > 0) {

        return $query->result();
    } else {
        return false;
    }
}


/* 26-02-2020
function get_teaching_faulty_single($subject,$session,$session_year){
  $sql = "(select a.id as sub_offered_id,a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
#Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to
where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' and b.coordinator=1 group by b.emp_no)
union
(select a.id as sub_offered_id,a.session_year,a.`session`,a.dept_id,a.semester,a.sub_code
,a.sub_name,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name from old_subject_offered a
inner join old_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id

#Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code  and a.dept_id=acc.offered_to
where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' and b.coordinator=1 group by b.emp_no)";
    $query = $this->db->query($sql);
    if ($this->db->affected_rows() > 0) {
    //  echo  $this->db->last_query();
        return $query->result();
    } else {
        return false;
    }
} */
function get_teaching_faulty($subject,$session,$session_year,$sub_type){
  if($sub_type=='Modular'){
    $extraParam="AND exam_type=acc.exam_type AND a.id=acc.sub_offered_id";
	 $extraParam2="group by  b.section,a.branch_id,a.course_id, (case when a.course_id='prep' then b.desc_id end)";
  }
/* 10-10-19 $sql = "(select if(mpd.after_mid = a.sub_code,'after_mid','before_mid') as exam_type,mpd.after_mid,mpd.before_mid,b.section, a.id,a.session_year,a.`session`,a.dept_id,d.name as dept_name,bb.name as branch_name,cc.name as course_name,a.semester,a.sub_code,a.sub_name,a.sub_group,a.sub_type,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join cbcs_departments d on a.dept_id=d.id
inner join cbcs_branches bb on a.branch_id=bb.id
inner join cbcs_courses cc on a.course_id=cc.id
Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to $extraParam
LEFT JOIN cbcs_modular_paper_details mpd on a.`session`=mpd.`session` and a.session_year=mpd.session_year and b.section=mpd.section and a.sub_code=IF(mpd.after_mid='na',mpd.before_mid,mpd.after_mid)

where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' group by b.section,a.course_id)
union
(select if(mpd.after_mid = a.sub_code,'after_mid','before_mid') as exam_type,mpd.after_mid,mpd.before_mid,b.section,a.id,a.session_year,a.`session`,a.dept_id,d.name as dept_name,bb.name as branch_name,cc.name as course_name,a.semester,a.sub_code,a.sub_name,a.sub_group,a.sub_type,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from old_subject_offered a
inner join old_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join cbcs_departments d on a.dept_id=d.id
inner join cbcs_branches bb on a.branch_id=bb.id
inner join cbcs_courses cc on a.course_id=cc.id
Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to $extraParam
LEFT JOIN cbcs_modular_paper_details mpd on a.`session`=mpd.`session` and a.session_year=mpd.session_year and b.section=mpd.section and a.sub_code=IF(mpd.after_mid='na',mpd.before_mid,mpd.after_mid)

where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' group by b.section,a.course_id)";*/

$sql = "(select if(mpd.after_mid = a.sub_code,'after_mid','before_mid') as exam_type,mpd.after_mid,mpd.before_mid,b.section, a.id,a.session_year,a.`session`,a.dept_id,a.dept_id AS dept_name,a.branch_id AS branch_name,a.course_id AS course_name,a.semester,a.sub_code,a.sub_name,a.sub_group,a.sub_type,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join cbcs_stu_course c on a.id=c.sub_offered_id
#inner join cbcs_departments d on a.dept_id=d.id
#inner join cbcs_branches bb on a.branch_id=bb.id
#inner join cbcs_courses cc on a.course_id=cc.id
Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to AND a.session_year=acc.session_year AND a.`session`=acc.`session` $extraParam
LEFT JOIN cbcs_modular_paper_details mpd on a.`session`=mpd.`session` and a.session_year=mpd.session_year and b.section=mpd.section and a.sub_code=IF(mpd.after_mid='na',mpd.before_mid,mpd.after_mid)

where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' $extraParam2)
union
(select if(mpd.after_mid = a.sub_code,'after_mid','before_mid') as exam_type,mpd.after_mid,mpd.before_mid,b.section,a.id,a.session_year,a.`session`,a.dept_id,a.dept_id AS dept_name,a.branch_id AS branch_name,a.course_id AS course_name,a.semester,a.sub_code,a.sub_name,a.sub_group,a.sub_type,b.emp_no,concat_ws(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) as name,acc.`status` from old_subject_offered a
inner join old_subject_offered_desc b on a.id=b.sub_offered_id
inner join user_details ud on b.emp_no=ud.id
inner join old_stu_course c on a.id=c.sub_offered_id
#inner join cbcs_departments d on a.dept_id=d.id
#inner join cbcs_branches bb on a.branch_id=bb.id
#inner join cbcs_courses cc on a.course_id=cc.id
Left join cbcs_assign_course_coordinator acc on b.emp_no=acc.co_emp_id and a.sub_code=acc.sub_code and a.dept_id=acc.offered_to AND a.session_year=acc.session_year AND a.`session`=acc.`session` $extraParam
LEFT JOIN cbcs_modular_paper_details mpd on a.`session`=mpd.`session` and a.session_year=mpd.session_year and b.section=mpd.section and a.sub_code=IF(mpd.after_mid='na',mpd.before_mid,mpd.after_mid)

where a.session_year='$session_year' and a.`session`='$session'
and a.sub_code='$subject' $extraParam2)";


    $query = $this->db->query($sql);
    //echo  $this->db->last_query();
    if ($this->db->affected_rows() > 0) {
    // echo  $this->db->last_query();
        return $query->result();
    } else {
        return false;
    }
}
  public function get_subject_list($dept_id,$session,$session_year){
    $sql = "(
SELECT a.sub_code,a.sub_name,ca.`status`,a.dept_id,x.course_digit
FROM cbcs_subject_offered a
LEFT JOIN cbcs_assign_course_coordinator ca ON a.sub_code=ca.sub_code AND a.`session`=ca.session AND a.session_year=ca.session_year
LEFT JOIN cbcs_dept_code x on LEFT(a.sub_code,3)=x.course_code and (case when a.dept_id='comm' then 1=1 else x.offering_dept_id='$dept_id' end)
WHERE
LEFT(a.sub_code,2) IN (
SELECT b.course_code
FROM cbcs_dept_code b
WHERE b.offering_dept_id='$dept_id') AND a.session_year='$session_year' AND a.`session`='$session' and a.sub_code not like '%599' AND a.sub_type !='Modular'
GROUP BY a.sub_code)

UNION

(
SELECT a.sub_code,a.sub_name,ca.`status`,a.dept_id,x.course_digit
FROM old_subject_offered a
LEFT JOIN cbcs_assign_course_coordinator ca ON a.sub_code=ca.sub_code AND a.`session`=ca.session AND a.session_year=ca.session_year
LEFT JOIN cbcs_dept_code x on LEFT(a.sub_code,3)=x.course_code and (case when a.dept_id='comm' then 1=1 else x.offering_dept_id='$dept_id' end)
WHERE
LEFT(a.sub_code,3) in (
SELECT b.course_code
FROM cbcs_dept_code b
WHERE b.offering_dept_id='$dept_id') AND a.session_year='$session_year' AND a.`session`='$session' and a.sub_code not like '%599' AND a.sub_type !='Modular' and LENGTH(a.sub_code)=x.course_digit
GROUP BY a.sub_code)
";
      $query = $this->db->query($sql);
        // echo  $this->db->last_query();exit;
      if ($this->db->affected_rows() > 0) {

          return $query->result();
      } else {
          return false;
      }
  }
  /*  function get_dept_list()
    {

      $sql = "select a.* from cbcs_departments a where a.`type`='academic' and a.status=1 order by a.name;";


        $query = $this->db->query($sql);


        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }*/

public function getbranch($course_id){

    $query=$this->db->query("SELECT a.*,b.name,b.id from course_branch a INNER join cbcs_branches b on b.id=a.branch_id  where a.course_id='$course_id'");
    return $query->result_array();
}

    function get_dept_list()
    {

      $sql = "select a.* from cbcs_departments a where a.`type`='academic' and a.status=1 order by a.name;";


        $query = $this->db->query($sql);


        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
/*public function getbranch($course_id){

    $query=$this->db->query("SELECT a.*,b.name,b.id from course_branch a INNER join cbcs_branches b on b.id=a.branch_id  where a.course_id='$course_id' order BY b.name asc ");
    return $query->result_array();
}
*/

    public function get_student_list($year,$session,$course,$branch,$sem,$group){

      if($course=='comm'){
      /*  if($group=='Group1'){
          $group='1';
        }else{
            $group='2';
        }*/
        $sql = "select * from reg_regular_form a where a.session_year='$year' and a.`session`='$session' and
         a.section='$group' and a.semester='$sem';";
      }else{
        $sql = "select * from reg_regular_form a where a.session_year='$year' and a.`session`='$session' and
        a.course_id='$course' and a.branch_id='iem' and a.semester='$sem';";
      }

      $query = $this->db->query($sql);
         //echo  $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {

            return $query->result();
        } else {
            return false;
        }

    }
    public function save_opted_subject($year,$session,$course,$branch,$sem,$group,$formid,$admin_no){

      if($course=='comm'){
      $sql = "select * from cbcs_subject_offered a where a.session_year='$year' and a.`session`='$session' and a.course_id='$course' and a.branch_id='$branch'  and a.semester='$sem' and a.sub_group = '$group' union all
      select * from cbcs_subject_offered a where a.session_year='$year' and a.`session`='$session'
       and a.semester='$sem' and a.sub_group like '%comm%';";
      $query = $this->db->query($sql);
  //   echo  $this->db->last_query(); exit;
        if ($this->db->affected_rows() > 0) {
            foreach($query->result() as $result){
              $subname=$result->sub_name;
              $subcode=$result->sub_code;
              $cntrow = $this->db->query("SELECT * FROM cbcs_stu_course where form_id='$formid' and admn_no='$admin_no' and subject_code='$subcode' and sub_category='$result->sub_category' and session_year='$year' and session='$session'");
			 // echo  $this->db->last_query(); exit;
              $cnt= $cntrow->num_rows();
				//echo "cnt rows".$cnt;
			  //echo  $this->db->last_query(); exit;
              if($cnt=='0'){
              $sql = "insert into cbcs_stu_course (form_id,admn_no,subject_code,subject_name,sub_category,course,branch,session_year,session)
              values ('$formid','$admin_no','$subcode','$subname','$result->sub_category','$course','$branch','$year','$session')";
              $this->db->query($sql);
            }
            }
          //  print_r($query->result());exit;
            //  echo $this->db->last_query();die();

            return true;
        } else {
          $this->session->set_flashdata('error','Offered Subjects not found for this acadmic year.');
          redirect('/cbcs_offered_subjects/offer_subject/opted_subject/', 'refresh');
        }
      }else{
        $sql = "select * from cbcs_subject_offered a where a.session_year='$year' and a.`session`='$session' and
        a.course_id='$course' and a.branch_id='$branch' and a.semester='$sem'";
        //  $this->db->last_query();die();
        $query = $this->db->query($sql);

      #  echo"not common ".  $this->db->last_query(); exit;

          if ($this->db->affected_rows() > 0) {
            $cnt=0;
              foreach($query->result() as $result){
                $subname=$result->sub_name;
                $subcode=$result->sub_code;
                $sub_category=$result->sub_category;
                $cntrow = $this->db->query("SELECT * FROM cbcs_stu_course where form_id='$formid' and admn_no='$admin_no' and subject_code='$subcode'
                  and course='$course' and sub_category='$sub_category' and branch='$branch' and session_year='$year' and session='$session'");
                $cntrows= $cntrow->num_rows();
              //echo  $this->db->last_query();
              if($cntrows==0){
              $sql = "insert into cbcs_stu_course (form_id,admn_no,subject_code,subject_name,priority,sub_category,sub_category_cbcs_offered,course,branch,session_year,session)
              values ('$formid','$admin_no','$subcode','$subname','0','$sub_category','','$course','$branch','$year','$session')";
                $this->db->query($sql);
                $cnt=$cnt+1;
              }

              }
            //  print_r($query->result());exit;
              //  echo $this->db->last_query();die();
              return true;
          } else {
            $this->session->set_flashdata('error','Offered Subjects not found for this Acadmic Year.');
            redirect('/cbcs_offered_subjects/offer_subject/opted_subject/', 'refresh');
          }
      }

    }

function get_dname($id){
    $sql = "select a.name from cbcs_departments a where a.id=? and a.status=1";
    $query = $this->db->query($sql,array($id));
    if ($this->db->affected_rows() > 0) {
       return $query->row()->name;
    } else {
       return false;
    }
}


function get_cname($id){
    $sql = "select a.name from cbcs_courses a where a.id=? and a.status=1";
    $query = $this->db->query($sql,array($id));
    if ($this->db->affected_rows() > 0) {
       return $query->row()->name;
    } else {
       return false;
    }
}

function get_bname($id){
    $sql = "select a.name from cbcs_branches a where a.id=? and a.status=1";
    $query = $this->db->query($sql,array($id));
    if ($this->db->affected_rows() > 0) {
       return $query->row()->name;
    } else {
       return false;
    }
}
function get_session_year($id){
    $sql = "select * from mis_session_year order by id desc";
    $query = $this->db->query($sql,array($id));
    if ($this->db->affected_rows() > 0) {
       return $query->result();
    } else {
       return false;
    }
}

function get_session($id){
    $sql = "select * from mis_session";
    $query = $this->db->query($sql,array($id));
    if ($this->db->affected_rows() > 0) {
       return $query->result();
    } else {
       return false;
    }
}
function get_paper_types(){
     $sql = "select * from mis_paper_type order by id";
    $query = $this->db->query($sql);
    if ($this->db->affected_rows() > 0) {
       return $query->result();
    } else {
       return false;
    }


}

function sub_master_insert($data) {
        if ($this->db->insert('cbcs_subject_master', $data))
        return $this->db->insert_id();
            //return TRUE;
        else
            return FALSE;
    }

    function get_sub_master_lastrow($id){

        $sql = "select * from cbcs_subject_master where id=?";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
           return $query->row();
        } else {
           return false;
        }

    }
    function get_sub_master_all(){
            $sql = "select * from cbcs_subject_master order by sub_name";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }

    }

    // function insert_backup($id,$action){

    //     $sql = "insert into cbcs_subject_master_backup select * from cbcs_subject_master where id=?";
    //     $query = $this->db->query($sql,array($id));

    //     $sql = "update cbcs_subject_master_backup set action='".$action."' where id=".$id;
    //     $query = $this->db->query($sql);

    //     if($action=='modify'){
    //         $sql = "update cbcs_subject_master set action='".$action."' where id=".$id;
    //         $query = $this->db->query($sql);
    //     }

    //     if ($this->db->affected_rows() > 0) {
    //         return TRUE;
    //     } else {
    //         return false;
    //     }


    // }
    //  function delete_rowid($id) {
    //     $this->db->where('id', $id);
    //     $this->db->delete('cbcs_subject_master');
    // }

    function sub_master_update($data,$con)
    {
        $con1['id'] = $con;
         if($this->db->update('cbcs_subject_master',$data,$con1))
         {
                    return true;
         }
            return false;

    }

    function get_courses(){

        $sql = "select * from cbcs_courses";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }



    }

//     function get_branch_bycourse($cid){

//         $query = $this->db->query("select a.* from cbcs_branches a
// inner join course_branch b on b.branch_id=a.id
// where b.course_id='".$cid."'
// order by a.name");
//         if($query->num_rows() > 0)
//             return $query->result();
//         else
//             return false;


//     }

    function get_component_list_details($cid,$bid,$sem){

//and a.course_component like 'D%' has been added later, when decided eso will not part of semester

        $sql = "select a.course_component,d.name,a.sequence,count(a.course_component)as countsub from cbcs_coursestructure_policy a inner join cbcs_curriculam_policy b on b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
inner join cbcs_credit_points_policy c on c.id=b.cbcs_credit_points_policy_id inner join cbcs_course_component d on d.id=a.course_component
inner join cbcs_credit_points_master e on e.course_id=c.course_id where a.course_id=? and e.branch_id=? and a.sem=? /*and a.course_component like 'D%'*/ group by a.course_component";
        $query = $this->db->query($sql,array($cid,$bid,$sem));
        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }


    }

    function get_component_wise_list($sy,$sess,$cid,$bid,$sem,$ftype){

//         $sql = "select a.course_component,d.name,a.sequence,a.status from cbcs_coursestructure_policy a inner join cbcs_curriculam_policy b on b.cbcs_credit_points_policy_id=b.id
// inner join cbcs_credit_points_policy c on c.id=b.cbcs_credit_points_policy_id inner join cbcs_course_component d on d.id=a.course_component
// inner join cbcs_credit_points_master e on e.course_id=c.course_id where a.course_id=? and e.branch_id=? and a.sem=? and a.course_component=?";
//         $query = $this->db->query($sql,array($cid,$bid,$sem,$ftype));
//         if ($this->db->affected_rows() > 0) {
//            return $query->result();
//         } else {
//            return false;
//         }

     /*   $sql="SELECT a.course_component,d.name,a.sequence,f.sub_code,f.sub_name,f.sub_type,a.status,f.id,GROUP_CONCAT(distinct CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ',CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '<br>') AS fname,f.sub_category
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
LEFT join cbcs_subject_offered f on f.session_year=? and f.`session`=? and f.course_id=a.course_id and f.branch_id=e.branch_id
and f.semester=a.sem and if(INSTR (f.unique_sub_pool_id,'DC/DE'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))

LEFT join cbcs_subject_offered_desc g on g.sub_offered_id=f.id
LEFT join user_details h on h.id=g.emp_no


WHERE a.course_id=? AND e.branch_id=? AND a.sem=? AND a.course_component=?
group by a.sequence,f.sub_code
order by a.sequence";*/
   /*$sql="SELECT a.course_component,d.name,a.sequence,f.sub_code,f.sub_name,f.sub_type,a.status,f.id,GROUP_CONCAT(distinct CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ',CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '<br>') AS fname,f.sub_category
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
LEFT join cbcs_subject_offered f on f.session_year=? and f.`session`=? and f.course_id=a.course_id and f.branch_id=e.branch_id
and f.semester=a.sem and if(INSTR (f.unique_sub_pool_id,'DC/DE') OR INSTR (f.unique_sub_pool_id,'DC/TU') OR  INSTR (f.unique_sub_pool_id,'DE/OE') OR  INSTR (f.unique_sub_pool_id,'HSS+MS'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))

LEFT join cbcs_subject_offered_desc g on g.sub_offered_id=f.id
LEFT join user_details h on h.id=g.emp_no


WHERE a.course_id=? AND e.branch_id=? AND a.sem=? AND a.course_component=?
group by a.sequence,f.sub_code
order by a.sequence";*/

/* commented on 09-06-2020
$sql="SELECT a.course_component,d.name,a.sequence,f.sub_code,f.sub_name,f.sub_type,a.status,f.id, GROUP_CONCAT(DISTINCT CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ', CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '
') AS fname,f.sub_category,if(i.id IS NULL AND j.id IS NULL,0,1) AS checking
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
LEFT JOIN cbcs_subject_offered f ON f.session_year=? and f.`session`=? AND f.branch_id=? AND
f.course_id=a.course_id  AND f.semester=a.sem AND if(INSTR (f.unique_sub_pool_id,'DC/DE') OR INSTR (f.unique_sub_pool_id,'DC/TU') OR INSTR (f.unique_sub_pool_id,'DE/OE') OR INSTR (f.unique_sub_pool_id,'HSS+MS'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))
LEFT JOIN cbcs_subject_offered_desc g ON g.sub_offered_id=f.id
LEFT JOIN user_details h ON h.id=g.emp_no
LEFT JOIN cbcs_stu_course i ON i.sub_offered_id=f.id
LEFT JOIN pre_stu_course j ON j.sub_offered_id=CONCAT('c',f.id)
WHERE a.course_id=? AND a.sem=? AND a.course_component=?
GROUP BY a.sequence,f.sub_code
ORDER BY a.sequence"; */




// $sql="select x.*,if(i.id IS NULL AND j.id IS NULL,0,1) AS checking from
// (SELECT a.course_component,d.name,a.sequence,f.sub_code,f.sub_name,f.id,f.sub_type,a.status,
//  GROUP_CONCAT(DISTINCT CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ',
// CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '
// ') AS fname,f.sub_category
// FROM cbcs_coursestructure_policy a
// INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
// INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
// INNER JOIN cbcs_course_component d ON d.id=a.course_component
// INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
// LEFT JOIN cbcs_subject_offered f ON f.session_year=? and f.`session`=? AND f.branch_id=? AND
// f.course_id=a.course_id /*AND f.branch_id=e.branch_id*/ AND f.semester=a.sem AND if(INSTR (f.unique_sub_pool_id,'DC/DE') OR INSTR (f.unique_sub_pool_id,'DC/TU') OR INSTR (f.unique_sub_pool_id,'DE/OE') OR INSTR (f.unique_sub_pool_id,'HSS+MS') OR INSTR (f.unique_sub_pool_id,'DC/OE'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))
// LEFT JOIN cbcs_subject_offered_desc g ON g.sub_offered_id=f.id
// LEFT JOIN user_details h ON h.id=g.emp_no
// /*LEFT JOIN cbcs_stu_course i ON i.sub_offered_id=f.id
// LEFT JOIN pre_stu_course j ON j.sub_offered_id=CONCAT('c',f.id)*/
// WHERE a.course_id=? AND a.sem=? AND a.course_component=?
// GROUP BY a.sequence,f.sub_code
// ORDER BY a.sequence) x
// LEFT JOIN cbcs_stu_course i ON i.sub_offered_id=x.id
// LEFT JOIN pre_stu_course j ON j.sub_offered_id=CONCAT('c',x.id)
// GROUP BY x.sequence,x.sub_code
// ORDER BY x.sequence";
$sql="select x.*,if(i.id IS NULL AND j.id IS NULL,0,1) AS checking from
(SELECT  if(k.sub_category IS null,a.course_component,k.sub_category) AS course_component, a.course_component AS c_comp,d.name,a.sequence,f.sub_code,f.sub_name,f.id,f.sub_type,a.status,
 GROUP_CONCAT(DISTINCT CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ',
CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '
') AS fname,f.sub_category
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
LEFT JOIN cbcs_subject_offered f ON f.session_year=? and f.`session`=? AND f.branch_id=? AND
f.course_id=a.course_id /*AND f.branch_id=e.branch_id*/ AND f.semester=a.sem AND if(INSTR (f.unique_sub_pool_id,'DC/DE') OR INSTR (f.unique_sub_pool_id,'DC/TU') OR INSTR (f.unique_sub_pool_id,'DE/OE') OR INSTR (f.unique_sub_pool_id,'HSS+MS') OR INSTR (f.unique_sub_pool_id,'DC/OE'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))
LEFT JOIN cbcs_subject_offered_desc g ON g.sub_offered_id=f.id
LEFT JOIN user_details h ON h.id=g.emp_no
/*LEFT JOIN cbcs_stu_course i ON i.sub_offered_id=f.id
LEFT JOIN pre_stu_course j ON j.sub_offered_id=CONCAT('c',f.id)*/
LEFT JOIN group_course_type k ON k.course_id=a.course_id AND k.semester=a.sem  AND k.branch_id='$bid' AND  a.course_component =k.course_component AND k.session_year='$sy' AND k.`session`='$sess' 
WHERE a.course_id=? AND a.sem=? AND a.course_component=?
GROUP BY a.sequence,f.sub_code
ORDER BY a.sequence) x
LEFT JOIN cbcs_stu_course i ON i.sub_offered_id=x.id
LEFT JOIN pre_stu_course j ON j.sub_offered_id=CONCAT('c',x.id)
GROUP BY x.sequence,x.sub_code
ORDER BY x.sequence";

/*$sql="SELECT a.course_component,d.name,a.sequence,f.sub_code,f.sub_name,f.sub_type,a.status,f.id,
CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name) AS fname,
CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END   AS uploadright
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id LEFT join cbcs_subject_offered f on f.session_year='2019-2020' and f.`session`='Monsoon' and f.course_id=a.course_id and f.branch_id=e.branch_id and f.semester=a.sem and f.sub_category=concat(a.course_component,a.sequence) LEFT join cbcs_subject_offered_desc g on g.sub_offered_id=f.id LEFT join user_details h on h.id=g.emp_no WHERE a.course_id='m.tech' AND e.branch_id='cse' AND a.sem='1' AND a.course_component='DC' group by a.sequence,f.sub_code order by a.sequence";*/
        $query = $this->db->query($sql,array($sy,$sess,$bid,$cid,$sem,$ftype));
        //echo $this->db->last_query();
         if ($this->db->affected_rows() > 0) {
            return $query->result();
         } else {
            return false;
         }


    }

    /*function get_subject_master_list($cid="",$bid=""){
        if(!empty($cid) && !empty($bid)){
            $sql = "select a.* from cbcs_subject_master a where a.course_id=? and a.branch_id=? GROUP BY a.sub_name,a.lecture,a.tutorial,a.practical order by trim(a.sub_name)";
             $query = $this->db->query($sql,array($cid,$bid));
        }else{
            $sql = "select a.* from cbcs_subject_master a GROUP BY a.sub_name,a.lecture,a.tutorial,a.practical order by trim(a.sub_name)";
             $query = $this->db->query($sql);
        }


        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }


    }*/

	function get_subject_master_list($did=""){

        if(!empty($did)){

            $sql = "select a.* from cbcs_course_master a where a.dept_id=? GROUP BY a.sub_name, a.sub_code,a.lecture,a.tutorial,a.practical order by trim(a.sub_code)";
             $query = $this->db->query($sql,array($did));
        }else{
            $sql = "select a.* from cbcs_course_master a GROUP BY a.sub_name, a.sub_code, a.lecture,a.tutorial,a.practical order by trim(a.sub_code)";
             $query = $this->db->query($sql);
        }


        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }


    }

    function get_subject_by_id($did,$sid){

        if( !empty($did)){

            $sql = "select * from cbcs_course_master where  dept_id=? and sub_code=? order by trim(sub_name)";
             $query = $this->db->query($sql,array($did,$sid));
        }

        else{
             $sql = "select * from cbcs_subject_master where sub_code=? order by trim(sub_name)";
             $query = $this->db->query($sql,array($sid));
        }


        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }

    }

    function get_subject_details($dept_id,$scode,$wef){

         if( !empty($dept_id)){

        $sql = "select a.* from cbcs_course_master a where a.dept_id=?  and a.sub_code=? and a.wef_year=? ";
        $query = $this->db->query($sql,array($dept_id,$scode,$wef));
        }else{

            $sql = "select a.* from cbcs_subject_master a where  a.wef_year=? ";
            $query = $this->db->query($sql,array($wef));

        }

        if ($this->db->affected_rows() > 0) {
           return $query->row();
        } else {
           return false;
        }


    }


    //==========Insert into subject offered table

    function insert_subject_offered($data)
    {
        if($this->db->insert('cbcs_subject_offered',$data))
            //return TRUE;
                        return $this->db->insert_id();
        else
            return FALSE;
    }



    //================Insert into subject offered desc
    function insert_batch_subject_offered_child($data)
    {
        if($this->db->insert_batch('cbcs_subject_offered_desc',$data))
            return TRUE;
        else
            return FALSE;
    }





    //Insert into subject mapping table

    function insert_subject_mapping($data)
    {
        if($this->db->insert('subject_mapping',$data))
            //return TRUE;
                        return $this->db->insert_id();
        else
            return FALSE;
    }



    //insert into subject mapping description tabl

     function insert_batch_subject_mapping_desc($data)
    {
        if($this->db->insert_batch('subject_mapping_des',$data))
            return TRUE;
        else
            return FALSE;
    }
    //==================================Delete Row of subject offered table======================================

    function insert_backup($id,$action){

        $sql = "insert into cbcs_subject_offered_backup select * from cbcs_subject_offered where id=?";
        $query = $this->db->query($sql,array($id));

        $sql = "insert into cbcs_subject_offered_desc_backup select * from cbcs_subject_offered_desc where sub_offered_id=?";
        $query = $this->db->query($sql,array($id));

        if($action=='delete'){
        $sql = "update cbcs_subject_offered_backup set action='".$action."' where id=".$id;
        $query = $this->db->query($sql);
        }

        if($action=='modify'){
            $sql = "update cbcs_subject_master set action='".$action."' where id=".$id;
            $query = $this->db->query($sql);
        }

        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return false;
        }


    }
     function delete_rowid_subject_offered_table($id) {
        $this->db->where('id', $id);
        $this->db->delete('cbcs_subject_offered');
    }

    function delete_rowid_subject_offered_desc_table($id) {
        $this->db->where('sub_offered_id', $id);
        $this->db->delete('cbcs_subject_offered_desc');
    }

    function get_subject_offered_desc_ft_details($id){

          $sql = "select a.*,b.dept_id from cbcs_subject_offered_desc a  inner join cbcs_subject_offered b on b.id=a.sub_offered_id where b.id=?";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
           return $query->result();
        } else {
           return false;
        }


    }

    function get_offered_subject_by_id($id){

          $sql = "select * from cbcs_subject_offered where id=?";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
           return $query->row();
        } else {
           return false;
        }


    }

	//==========get subject name
    function get_sub_name($sub_code){
        $sql="SELECT sub_name FROM cbcs_course_master WHERE sub_code='$sub_code'";
        $result=$this->db->query($sql);
        return $result->result();
    }


     function get_offered_sub_list($sy,$sess,$dept)
    {

      $sql = "select a.session_year,a.`session`,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_code,a.sub_name,a.lecture
,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.sub_type,a.pre_requisite,a.pre_requisite_subcode,a.sub_category,
a.sub_group,a.criteria,b.part,b.emp_no,
CASE WHEN b.coordinator='1' THEN 'Yes' ELSE 'No' END as marks_up_rt,
c.name as dname,d.name as cname,e.name as bname,
concat_ws(' ',f.first_name,f.middle_name,f.last_name) as fname,a.minstu,a.maxstu
 from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on b.sub_offered_id=a.id
inner join cbcs_departments c on c.id=a.dept_id and c.`status`='1'
left join cbcs_courses d on d.id=a.course_id and d.`status`='1'
left join cbcs_branches e on e.id=a.branch_id and e.`status`='1'
inner join user_details f on f.id=b.emp_no
where a.session_year=? and a.`session`=? and a.dept_id=?";


        $query = $this->db->query($sql,array($sy,$sess,$dept));
//echo $this->db->last_query();die();

        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }


    function get_offered_sub_list_dynamic($session_year,$session,$dept_id,$course_id,$branch_id,$semester)
   {
    //  and a.course_id='m.tech' and a.branch_id='ece' and a.semester="1"
      if($branch_id !="none"){
        $addBranch="and a.branch_id='$branch_id'";
      }
      if($semester !="none"){
        $addSem="and a.semester='$semester'";
      }
 $sql = "select a.session_year,a.`session`,a.dept_id,a.course_id,a.branch_id,a.semester,a.sub_code,a.sub_name,a.lecture
,a.tutorial,a.practical,a.credit_hours,a.contact_hours,a.sub_type,a.pre_requisite,a.pre_requisite_subcode,a.sub_category,
a.sub_group,a.criteria,b.part,b.emp_no,
CASE WHEN b.coordinator='1' THEN 'Yes' ELSE 'No' END as marks_up_rt,
c.name as dname,d.name as cname,e.name as bname,
concat_ws(' ',f.first_name,f.middle_name,f.last_name) as fname,a.minstu,a.maxstu
from cbcs_subject_offered a
inner join cbcs_subject_offered_desc b on b.sub_offered_id=a.id
inner join cbcs_departments c on c.id=a.dept_id and c.`status`='1'
left join cbcs_courses d on d.id=a.course_id and d.`status`='1'
left join cbcs_branches e on e.id=a.branch_id and e.`status`='1'
inner join user_details f on f.id=b.emp_no
where a.session_year=? and a.`session`=? and a.dept_id=? and a.course_id= ? $addBranch $addSem";


       $query = $this->db->query($sql,array($session_year,$session,$dept_id,$course_id));
#echo $this->db->last_query();die();

       if ($this->db->affected_rows() > 0) {
           return $query->result();
       } else {
           return false;
       }
   }


    //==================================================================================================================

function getEmpNamesByDept($dept = '')
  {
    if($dept == '')
      return FALSE;
    else
    {
     /* $query=$this->db->select('users.id, salutation, first_name, middle_name, last_name, dept_id')
                ->from('user_details')
                ->join('users','users.id = user_details.id')
                //->join('user_auth_type','user_auth_type.id = users.id')
                ->where('dept_id',$dept)
                ->where('auth_id','emp')
                //->where('user_auth_type.auth.id','fa')
                ->order_by('first_name','ASC')
                ->get();*/
/*      $sql="SELECT `users`.`id`, `salutation`, `first_name`, `middle_name`, `last_name`, `dept_id`,user_auth_types.auth_id
FROM (`user_details`)
JOIN `users` ON `users`.`id` = `user_details`.`id`
join user_auth_types on user_auth_types.id=user_details.id
WHERE user_details.`dept_id` = '$dept' AND users.`auth_id` = 'emp' and (user_auth_types.auth_id='fa' or user_auth_types.auth_id='hod')
group by users.id
ORDER BY `first_name` ASC";*/


$sql="SELECT `users`.`id`, `salutation`, `first_name`, `middle_name`, `last_name`, `dept_id`
FROM (`user_details`)
JOIN `users` ON `users`.`id` = `user_details`.`id`
join emp_basic_details on users.id=emp_basic_details.emp_no
WHERE user_details.`dept_id` = '$dept' AND users.`auth_id` = 'emp' and ( emp_basic_details.auth_id='ft' or emp_basic_details.designation='spo') and users.`status`='A'
group by users.id
ORDER BY `first_name` ASC";


$query=$this->db->query($sql);
      return $query->result();
    }
  }

  function offered_course_compoment_list($dept,$syear,$sess,$course,$branch,$sem){
    $sql="SELECT a.course_component,d.name,a.course_id,e.branch_id,a.sem,a.sequence,f.sub_code,f.sub_name,f.sub_type,a.status,f.id,
    (select count(*) from cbcs_coursestructure_policy x
    LEFT join cbcs_credit_points_policy y on x.cbcs_curriculam_policy_id=y.id
    where x.course_id=a.course_id and x.sem=a.sem and x.course_component=d.id
    ) as count_comp,
(SELECT count(DISTINCT(aa.sub_category))
FROM cbcs_subject_offered aa
JOIN cbcs_coursestructure_policy bb ON bb.course_id=aa.course_id AND bb.sem=aa.semester AND (CONCAT(bb.course_component,bb.sequence)=aa.sub_category OR CONCAT(bb.course_component,bb.sequence)=aa.unique_sub_pool_id)
JOIN cbcs_curriculam_policy cc ON cc.id=bb.cbcs_curriculam_policy_id
JOIN cbcs_credit_points_policy dd ON dd.id=cc.cbcs_credit_points_policy_id
JOIN cbcs_course_component ee ON ee.id=bb.course_component AND ee.course_id=aa.course_id
LEFT JOIN cbcs_departments ff ON ff.id=aa.dept_id
LEFT JOIN cbcs_courses gg ON gg.id=aa.course_id
LEFT JOIN cbcs_branches hh ON hh.id=aa.branch_id
WHERE aa.dept_id='$dept' AND aa.course_id=a.course_id AND aa.branch_id=e.branch_id AND aa.semester=a.sem AND aa.`session`='$sess' AND aa.session_year='$syear' AND bb.course_component=a.course_component
ORDER BY aa.id) as created_component

     #, GROUP_CONCAT(DISTINCT CONCAT_WS(' ',h.salutation,h.first_name,h.middle_name,h.last_name),' / ', CASE WHEN g.coordinator='1' THEN 'Yes' WHEN g.coordinator='0' THEN 'No' END SEPARATOR '<br>') AS fname,f.sub_category
FROM cbcs_coursestructure_policy a
INNER JOIN cbcs_curriculam_policy b ON b.cbcs_credit_points_policy_id=a.cbcs_curriculam_policy_id
INNER JOIN cbcs_credit_points_policy c ON c.id=b.cbcs_credit_points_policy_id
INNER JOIN cbcs_course_component d ON d.id=a.course_component
INNER JOIN cbcs_credit_points_master e ON e.course_id=c.course_id
LEFT JOIN cbcs_subject_offered f ON f.session_year='$syear' AND f.`session`='$sess' AND f.course_id=a.course_id AND f.branch_id=e.branch_id AND f.semester=a.sem
#AND IF(INSTR (f.unique_sub_pool_id,'DC/DE'),f.unique_sub_pool_id= CONCAT(a.course_component,a.sequence),f.sub_category= CONCAT(a.course_component,a.sequence))
/*f.sub_category=concat(a.course_component,a.sequence)*/
LEFT JOIN cbcs_subject_offered_desc g ON g.sub_offered_id=f.id
LEFT JOIN user_details h ON h.id=g.emp_no
WHERE a.course_id in (SELECT DISTINCT course_branch.course_id #,id,name,duration
FROM
cbcs_courses
INNER JOIN course_branch ON course_branch.course_id = cbcs_courses.id
INNER JOIN dept_course ON
dept_course.course_branch_id = course_branch.course_branch_id
WHERE dept_course.dept_id = '$dept' AND cbcs_courses.`status`=1) and a.course_id='$course' and e.branch_id='$branch' and a.sem='$sem' /*AND a.course_component !='ESO'*/
 AND a.course_component not in ('ESO')
GROUP BY a.course_component ,a.course_id,e.branch_id#a.sequence,f.sub_code
ORDER BY a.course_component,a.sequence,a.course_id,e.branch_id";
    $query=$this->db->query($sql);
      return $query->result();
  }

  function check_duplicate_subject($sy,$sess,$dept,$course,$branch,$sem,$f_type,$sub_code){
    $f_type=$f_type.'%';
    $sql="SELECT a.*
FROM cbcs_subject_offered a
WHERE a.session_year='$sy' AND a.`session`='$sess' AND a.dept_id='$dept' AND a.course_id='$course' AND a.branch_id='$branch' AND a.semester='$sem' AND a.sub_code='$sub_code' AND a.sub_category LIKE '$f_type'";
 $query=$this->db->query($sql);
  return $query->num_rows();
  }

  function expected_course_component($sub_c,$dept,$course,$branch,$sem,$syear,$sess){
    $sql="SELECT if(replace(MAX(X.sub_category),'$sub_c','') is NULL ,1,replace(MAX(X.sub_category),'$sub_c','')+1) AS new_num
FROM (SELECT a.sub_category
FROM cbcs_subject_offered a
WHERE a.dept_id='$dept' AND a.course_id='$course' AND a.branch_id='$branch' AND a.sub_category LIKE '$sub_c%'  AND a.semester='5' AND a.session_year='$syear' AND a.session='$sess'
UNION
SELECT concat(b.course_component,b.sequence)
FROM cbcs_coursestructure_policy b
WHERE b.course_id='$course' AND b.sem<='$sem' AND b.course_component='$sub_c') X
";
    $query=$this->db->query($sql);
    return $query->result();
  }

  function get_dpgc_convener($dept){
    $sql="SELECT DISTINCT a.id
FROM user_auth_types a
INNER JOIN user_details b ON a.id=b.id
WHERE b.dept_id='$dept' AND a.auth_id='dpgc'";
    $query=$this->db->query($sql);
    return $query->result();
  }


}

?>
