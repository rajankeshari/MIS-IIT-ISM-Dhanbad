<?php
/* tabulation process
 * Copyright (c) ISM dhanbad *
 * @category   phpExcel
 * @package    exam_tabulation
 * @copyright  Copyright (c) 2014 - 2015 Ism dhanbad
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##0.1##, #26/11/15#
 * @Author     Ritu raj<rituraj00@rediffmail.com>
 */

class Exam_tabulation_model extends CI_Model {

    private static $db;

    function __construct() {
        // Call the Model constructor
        parent::__construct();
        self::$db = &get_instance()->db;

    }
    function getStu_basic_dat($admn_no){
       $sql="select a.form_id,a.admn_no,concat_ws(' ',d.first_name,d.middle_name,d.last_name) as stu_name,a.semester,a.course_id,a.branch_id,a.section,e.name as dept,b.name as course,c.name as branch from reg_regular_form a
             inner join cbcs_courses b on a.course_id=b.id
             inner join cbcs_branches c on a.branch_id=c.id
             inner join user_details d on a.admn_no=d.id
             inner join cbcs_departments e on d.dept_id=e.id
             where a.admn_no='$admn_no' order by a.form_id desc limit 1";
             $query = $this->db->query($sql);
           //  echo $this->db->last_query(); die();
             if ($query->num_rows() > 0)
                 return $query->result();
             else
                 return false;
     }
	     function getIndiviualTbuData($admn_no,$course_id){
      if($course_id=="jrf"){
        $sql="SELECT GROUP_CONCAT(f.sub_code) AS subject, GROUP_CONCAT(CONCAT_WS('-',f.sub_code,f.cr_hr,f.total,f.grade,f.cr_pts,f.stored_ctotcrpts
        ,f.stored_ctotcrhr,f.stored_core_ctotcrpts,f.stored_core_ctotcrhr,f.gpa,f.core_gpa,f.cgpa,f.core_cgpa,f.core_status
        )) AS sub_details,GROUP_CONCAT((CASE WHEN f.grade='F' then f.sub_code end) ) as fail_subs,f.*
        FROM (
        SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade, fd.cr_pts, fd.cr_hr, fd.mis_sub_id,fd.total,
        y.admn_no, y.ctotcrpts AS stored_ctotcrpts,y.ctotcrhr AS stored_ctotcrhr,y.core_ctotcrpts AS stored_core_ctotcrpts,y.core_ctotcrhr AS stored_core_ctotcrhr
        , y.actual_published_on,y.gpa,y.core_gpa,y.cgpa,y.core_cgpa,y.core_status
        FROM (
        SELECT x.*
        FROM(
        SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade, fd.cr_pts, fd.cr_hr, fd.mis_sub_id,
        y.admn_no,y.ctotcrpts,y.ctotcrhr, y.core_ctotcrpts,y.core_ctotcrhr, y.ctotcrpts AS stored_ctotcrpts,y.ctotcrhr AS stored_ctotcrhr,y.core_ctotcrpts AS stored_core_ctotcrpts,y.core_ctotcrhr AS stored_core_ctotcrhr
        ,y.gpa,y.core_gpa,y.cgpa,y.core_cgpa,y.core_status,y.actual_published_on
         FROM ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id AS foil_id,a.`status`,
        	a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts,a.gpa,a.core_gpa,a.cgpa,a.core_cgpa,a.core_status,a.actual_published_on
        	FROM final_semwise_marks_foil_freezed AS a
        	join  reg_regular_form rg  on rg.admn_no=a.admn_no and  rg.hod_status='1' and rg.acad_status='1' and rg.session_year='2019-2020' and rg.`session`='Monsoon'
        	WHERE a.admn_no='$admn_no' AND  UPPER(a.course)<>'MINOR'  and  lower(a.course)='jrf'
           ORDER BY a.admn_no,a.session_yr desc, a.actual_published_on DESC
        	LIMIT 100000000)x  GROUP BY x.admn_no,x.session_yr,x.session) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.foil_id AND
        	fd.admn_no=y.admn_no  )x
        GROUP BY x.admn_no, IFNULL(x.semester, x.session_yr) /*having  x.semester<= x.reg_sem*/
        ORDER BY x.admn_no,x.semester,x.actual_published_on DESC
        LIMIT 100000000) y
        JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.foil_id AND
        fd.admn_no=y.admn_no) f
        GROUP BY f.semester
        ";
      }else{
      $sql="SELECT GROUP_CONCAT(f.sub_code) AS subject, GROUP_CONCAT(CONCAT_WS('-',f.sub_code,f.cr_hr,f.total,f.grade,f.cr_pts,f.stored_ctotcrpts
,f.stored_ctotcrhr,f.stored_core_ctotcrpts,f.stored_core_ctotcrhr,f.gpa,f.core_gpa,f.cgpa,f.core_cgpa,f.core_status
)) AS sub_details,GROUP_CONCAT((CASE WHEN f.grade='F' then f.sub_code end) ) as fail_subs,f.*
FROM (
SELECT y.foil_id, y.session_yr,y.session,y.dept,y.course,y.branch,y.semester,fd.sub_code, fd.grade, fd.cr_pts, fd.cr_hr,  fd.mis_sub_id,fd.total,
y.admn_no , y.ctotcrpts as stored_ctotcrpts  ,y.ctotcrhr as stored_ctotcrhr ,y.core_ctotcrpts  as stored_core_ctotcrpts ,y.core_ctotcrhr  as  stored_core_ctotcrhr
, y.actual_published_on,y.gpa,y.core_gpa,y.cgpa,y.core_cgpa,y.core_status
FROM ( SELECT x.* FROM ( SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id as foil_id,a.`status`,
a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts , if( rg.semester<>a.semester, a.semester, null)  as sem ,
rg.semester as reg_sem ,  a.published_on, a.actual_published_on,a.gpa,a.core_gpa,a.cgpa,a.core_cgpa,a.core_status
FROM final_semwise_marks_foil_freezed AS a
join  reg_regular_form rg  on rg.admn_no=a.admn_no and  rg.hod_status='1' and rg.acad_status='1' and rg.session_year='2019-2020' and rg.`session`='Monsoon'
WHERE  a.admn_no='$admn_no' AND  UPPER(a.course)<>'MINOR' AND
(a.semester!= '0' AND a.semester!='-1') and a.course<>'jrf'
ORDER BY a.admn_no,a.semester,a.actual_published_on desc
LIMIT 100000000)x    GROUP BY x.admn_no,IFNULL(x.sem, x.session_yr)    /*having  x.semester<= x.reg_sem*/
order by  x.admn_no,x.semester,x.actual_published_on desc limit 100000000) y JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.foil_id AND
fd.admn_no=y.admn_no) f
GROUP BY f.semester

";
}
            $query = $this->db->query($sql);
        #  echo $this->db->last_query(); die();
            if ($query->num_rows() > 0)
                return $query->result_array();
            else
                return false;
    }


    function getCourseOfferedByDept($id) {
        $q = $this->db->get_where($this->sem_subject, array('form_id' => $id));
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getCourseByDept() {
        if ($this->input->post('exm_type') == "other" || $this->input->post('exm_type') == "spl") {
            $and = "  and (b.course_id!='honour' and b.course_id!='minor') ";
        } else {
            $and = "";
        }


        if ($this->input->post('dept') != "comm") {
            $sql = "select concat(x.course_id,'(',x.branch_id,')') as sheet_name ,x.course_id,cs_courses.duration from(
                      select  a.dept_id,upper(b.course_id) as course_id,b.branch_id  from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? and b.course_id!='capsule' " . $and . "  and b.course_id!=?
                        group by b.course_id,b.branch_id)x
                         left join cs_courses on cs_courses.id=x.course_id";
            $secure_array = array($this->input->post('dept'), 'comm');
            $query = $this->db->query($sql, $secure_array);
            // echo $this->db->last_query();  die();
            if ($query->num_rows() > 0)
                return $query->result();
            else {
                return 0;
            }
        } else {
            //   echo 'section_id'. $this->input->post('section_name'); die();
            $sql = "select concat(x.course_id,'(','" . $this->input->post('section_name') . "',')') as sheet_name ,x.course_id,cs_courses.duration from(
                      select  a.dept_id,upper(b.course_id) as course_id,b.branch_id  from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and b.course_id!='capsule' " . $and . "   and b.course_id=?
                        group by b.course_id,b.branch_id)x
                         left join cs_courses on cs_courses.id=x.course_id";
            $secure_array = array('comm');
            $query = $this->db->query($sql, $secure_array);
            // echo $this->db->last_query();
            if ($query->num_rows() > 0)
                return $query->result();
            else {
                return 0;
            }
        }
        //return array()
    }

  function getStudentHonours($branch, $sem, $admn_no = null) {

      if ($this->input->post('session') == 'Summer' /*|| ($this->input->post('session') == 'Winter' && $this->input->post('exm_type') == 'spl' )*/){
        if ($admn_no != null) {
            $replacer1 = "hf1.admn_no=?  and ";
            $secure_array = array($admn_no, '1', 'Y', $this->input->post('dept'),  5, $branch,$sem,$this->input->post('session_year'),$this->input->post('session'),'2','2');
        } else {
            $replacer1 = "";
            $secure_array = array('1', 'Y', $this->input->post('dept'),  5, $branch,$sem,$this->input->post('session_year'),$this->input->post('session'),'2','2');
        }
        $sql = "
  select x.* from
  (select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name,A.honours_agg_id from
  (select hf1.admn_no, hf1.honours_agg_id from  hm_form hf1  where " . $replacer1 . "  hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=?  )A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no
  group by A.admn_no order by A.admn_no) x
  INNER JOIN course_structure d ON d.aggr_id=x.honours_agg_id AND d.semester=?
  inner join  reg_summer_form rgf on rgf.admn_no=x.admn_no and rgf.session_year=?  and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no
  ";

      }

      else{



          $table=( $this->input->post('exm_type') == 'regular' ?' reg_regular_form ' : ' reg_other_form ');
          $sem_list_str=( $this->input->post('exm_type') == 'regular' ? ' rgf.semester=? ' : ' rgf.semester like ? ' );

        if ($admn_no != null) {
            $replacer1 = "hf1.admn_no=?  and ";
            $secure_array = array($admn_no, '1', 'Y', $this->input->post('dept'),  5, $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
        } else {
            $replacer1 = "";
            $secure_array = array('1', 'Y', $this->input->post('dept'),  5, $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
        }
        $sql = "
  select x.* from
  (select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name from
  (select hf1.admn_no from  hm_form hf1  where " . $replacer1 . "  hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=?  )A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no
  group by A.admn_no order by A.admn_no) x
  inner join  $table rgf on rgf.admn_no=x.admn_no and rgf.session_year=? and $sem_list_str and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no
  ";
      }




        $query = $this->db->query($sql, $secure_array);
               //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }


	function getRejectedStudentHonours($branch, $sem, $admn_no = null) {

      if ($this->input->post('session') == 'Summer' /*|| ($this->input->post('session') == 'Winter' && $this->input->post('exm_type') == 'spl' )*/){
        if ($admn_no != null) {
            $replacer1 = "hf1.admn_no=?  and ";
            $secure_array = array($admn_no, '1', 'Y', $this->input->post('dept'),  5, $branch,$sem,$this->input->post('session_year'),$this->input->post('session'),'2','2');
        } else {
            $replacer1 = "";
            $secure_array = array('1', 'Y', $this->input->post('dept'),  5, $branch,$sem,$this->input->post('session_year'),$this->input->post('session'),'2','2');
        }
        $sql = "
  select x.* from
  (select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name,A.honours_agg_id from
  (select hf1.admn_no, hf1.honours_agg_id from  hm_form hf1  where " . $replacer1 . "  hf1.honours=? and hf1.honour_hod_status<>? and  hf1.dept_id=?  and  hf1.semester>=?  )A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no
  group by A.admn_no order by A.admn_no) x
  INNER JOIN course_structure d ON d.aggr_id=x.honours_agg_id AND d.semester=?
  inner join  reg_summer_form rgf on rgf.admn_no=x.admn_no and rgf.session_year=?  and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no
  ";

      }

      else{



          $table=( $this->input->post('exm_type') == 'regular' ?' reg_regular_form ' : ' reg_other_form ');
          $sem_list_str=( $this->input->post('exm_type') == 'regular' ? ' rgf.semester=? ' : ' rgf.semester like ? ' );

        if ($admn_no != null) {
            $replacer1 = "hf1.admn_no=?  and ";
            $secure_array = array($admn_no, '1', 'Y', $this->input->post('dept'),  5, $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
        } else {
            $replacer1 = "";
            $secure_array = array('1', 'Y', $this->input->post('dept'),  5, $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
        }
        $sql = "
  select x.* from
  (select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name from
  (select hf1.admn_no from  hm_form hf1  where " . $replacer1 . "  hf1.honours=? and hf1.honour_hod_status<>? and  hf1.dept_id=?  and  hf1.semester>=?  )A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no
  group by A.admn_no order by A.admn_no) x
  inner join  $table rgf on rgf.admn_no=x.admn_no and rgf.session_year=? and $sem_list_str and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no
  ";
      }




        $query = $this->db->query($sql, $secure_array);
               //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }



    function getStudentIncomingMinor($branch, $sem, $admn_no = null) {




        $admn_no = preg_replace('/\s+/', '', $admn_no);
      if($this->input->post('session')<>'Summer'  &&  !($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ))  {
                $table=( $this->input->post('exm_type') == 'regular' ?' reg_regular_form ' : ' reg_other_form ');
                $sem_list_str=( $this->input->post('exm_type') == 'regular' ? ' rgf.semester=? ' : ' rgf.semester like ? ' );

        if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
                $secure_array = array('1', '1', 'Y',  5, $this->input->post('dept'), $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                $secure_array = array($admn_no, '1', '1', 'Y',  5, $this->input->post('dept'), $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
            }
        } else {
            $replacer1 = "";
            $secure_array = array('1', '1', 'Y', 5,$this->input->post('dept'), $branch,$this->input->post('session_year'),( $this->input->post('exm_type') == 'regular' ?$sem: '%'.$sem.'%'),$this->input->post('session'),'2','2');
        }
        $sql = "
                 select x.* from
                 (select null as  both_status_string,
                       null AS both_status_string_old , b.name AS br_name,dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,A.dept_id,  A.branch_id , A.semester
                 from
                ( select hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id
                         " . $replacer1 . "  and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?    and hf2.semester>=?
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?
                    )A

                       inner join user_details ud on ud.id=A.admn_no
                       left join departments dpt on dpt.id =A.dept_id
                       LEFT join cs_branches b on b.id=A.branch_id
                       group by  A.admn_no order by A.admn_no )x
                       inner join  $table  rgf on rgf.admn_no=x.admn_no and rgf.session_year=? and $sem_list_str and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no
             ";
     }else{

            if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
               // $secure_array = array('1', '1', 'Y', $this->input->post('session_year'), 5,6, $this->input->post('dept'), $branch);
                $secure_array=array('1','1','Y',/*$this->input->post('session_year'),*/5,$this->input->post('dept'),$branch,$sem,$this->input->post('session'),$this->input->post('session_year'),'2','2');
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                //$secure_array = array($admn_no, '1', '1', 'Y', $this->input->post('session_year'), 5,6, $this->input->post('dept'), $branch);
                 $secure_array=array($admn_no,'1','1','Y',/*$this->input->post('session_year'),*/5,$this->input->post('dept'),$branch,$sem,$this->input->post('session'),$this->input->post('session_year'),'2','2');
            }
        } else {
            $replacer1 = "";
            //$secure_array = array('1', '1', 'Y', $this->input->post('session_year'), 5,6, $this->input->post('dept'), $branch);
            $secure_array=array('1','1','Y',/*$this->input->post('session_year'),*/5,$this->input->post('dept'),$branch,$sem,$this->input->post('session'),$this->input->post('session_year'),'2','2');
        }

             $sql="  select /*IF((z.hod_status='1' AND z.acad_status='1') ,'','Pending') as */ null as  both_status_string, null AS both_status_string_old ,z.admn_no,z.stu_name as  st_name,z.dept_name,z.br_name,z.dept_id,  z.branch_id , z.semester
                 from
                  (select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester,c.sub_id,e.name,e.subject_id,x.hod_status,x.acad_status,b.name AS br_name
                 from
                ( select hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id  " . $replacer1 . "
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=?
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?
                    )A

                      inner join user_details ud on ud.id=A.admn_no
                       left join departments dpt on dpt.id =A.dept_id
                        LEFT join cs_branches b on b.id=A.branch_id
                       inner join reg_summer_form x on x.admn_no=A.admn_no
INNER JOIN reg_summer_subject c ON c.form_id=x.form_id
INNER JOIN course_structure d ON d.id=c.sub_id  and  d.semester=?  and d.aggr_id like 'minor%'
INNER JOIN subjects e ON e.id=d.id
 and x.session=? and  x.session_year=? AND x.hod_status<>? AND x.acad_status<>? )z
 group by z.admn_no
ORDER BY z.admn_no";

         }


        $query = $this->db->query($sql, $secure_array);
         //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }


	  function getStudentListCommon_cbcs($session_yr, $session, $section, $sem, $admn_no = null,$crs_struct=null) {

	  }



     function getStudentListCommon($session_yr, $session, $section, $sem, $admn_no = null,$crs_struct=null) {
		   //echo 'sem'.$section; die();

        $admn_no = preg_replace('/\s+/', '', $admn_no);

                  if( $this->input->post('session')=='Summer'||  ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )  ){
                        	if($crs_struct){
            $crs_struct_concat=" and d.aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
              if ($admn_no == null) {
            if ($section != 'all' && $section != null && $section != "") {
                $where_sec = " and section=? ";
                $secure_array = array($session_yr, $section);
            } else {
                $where_sec = "";
                $secure_array = array($session_yr);
            }
             $where3= " ";
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                if ($section != 'all' && $section != null && $section != "") {
                    // $where = " and section=?  and  admn_no in(" . $admn_no . ")";
                      $secure_array = array($session_yr, $section);
                       $where_sec= " and i.section=? ";
                       $secure_array=array($session_yr,$section);
                } else {
                   // $where = " and  admn_no in(" . $admn_no . ")";
                     $where_sec= "";
                     $secure_array=array($session_yr);
                }
                 $where3= " and  a.admn_no in(" . $admn_no . ")";
            } else {
                if ($section != 'all' && $section != null && $section != "") {
                    $where = " and section=? ";
                    $secure_array = array($session_yr, $section);
                } else {
                    $where_sec = " ";
                    $secure_array = array($session_yr);
                }
                 $where3= " and  a.admn_no ='" . $admn_no . "' ";
            }

        }
                 $where.= " and  substring(d.semester,1,1)=? ";
                 $table=" reg_summer_form ";

                 $secure_array=  array_merge($secure_array,array($sem,$this->input->post('session'),$this->input->post('session_year'),'1','1'));
            //print_r($secure_array);
          $sql=" select x.*,gp.group from
              (SELECT A.section, null as  both_status_string,null AS both_status_string_old,A.admn_no,A.stu_name as  st_name
FROM(
SELECT i.section,a.form_id, a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name
FROM ".$table."  a
inner join stu_section_data i on i.admn_no= a.admn_no  and   i.session_year=?  ".$where_sec."
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
INNER JOIN course_structure d ON d.id=c.sub_id ".$where."   ".$crs_struct_concat."
INNER JOIN subjects e ON e.id=d.id
WHERE a.session=?  and  a.session_year=? AND a.hod_status=? AND a.acad_status=?  ".$where3."
ORDER BY a.admn_no)A
GROUP BY A.admn_no
ORDER BY A.admn_no)x
left join section_group_rel gp  on gp.section=x.section and gp.session_year='".$this->input->post('session_year')."'
ORDER BY x.admn_no
";
          }

		  else if(( ($this->input->post('session')=='Winter' || $this->input->post('session')=='Monsoon') && $this->input->post('exm_type')=='other' ))
		  {

			              	if($crs_struct){
            $crs_struct_concat=" and d.aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
              if ($admn_no == null) {
             $where3= " ";
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                 $where3= " and  a.admn_no in(" . $admn_no . ")";
            } else {
                 $where3= " and  a.admn_no ='" . $admn_no . "' ";
            }

          }
                 $where.= " and  substring(d.semester,1,1)=? ";
                 $table=" reg_other_form ";

                 $secure_array=  array($sem,$this->input->post('session'),$this->input->post('session_year'),'1','1');
            //print_r($secure_array);
          $sql=" select x.*,gp.group from
              (SELECT  null as  both_status_string,null AS both_status_string_old,A.admn_no,A.stu_name as  st_name,A.section
FROM(
SELECT CASE WHEN d.semester='1_1' THEN 'A' WHEN d.semester='1_2' THEN 'E'  WHEN d.semester='2_1' THEN 'A' WHEN d.semester='2_2' THEN 'E'  END as section,a.form_id, a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name
FROM ".$table."  a
INNER JOIN user_details b ON b.id=a.admn_no
INNER JOIN reg_other_subject c ON c.form_id=a.form_id
INNER JOIN course_structure d ON d.id=c.sub_id ".$where."   ".$crs_struct_concat."
INNER JOIN subjects e ON e.id=d.id
WHERE a.session=?  and  a.session_year=? AND a.hod_status=? AND a.acad_status=?  ".$where3."
ORDER BY a.admn_no)A
GROUP BY A.admn_no having section='".$section."'
ORDER BY A.admn_no)x
left join section_group_rel gp  on gp.section=x.section and gp.session_year='".$this->input->post('session_year')."'
ORDER BY x.admn_no
";
		  }





		  else{
              //echo $admn_no; die();
              if($crs_struct){
            $crs_struct_concat=" and r.course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }

              //echo  $section.'#'.$admn_no.'#'.$sem ; die();
        if ($admn_no == null) {
            if ($section != 'all' && $section != null && $section != "") {
                $where = " and section=? ";
                $secure_array = array($session_yr, $section);
            } else {
                $where = "";
                $secure_array = array($session_yr);
            }
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                if ($section != 'all' && $section != null && $section != "") {
                    $where = " and section=?  and  admn_no in(" . $admn_no . ")";
                    $secure_array = array($session_yr, $section);
                } else {
                    $where = " and  admn_no in(" . $admn_no . ")";
                    $secure_array = array($session_yr);
                }
            } else {

                if ($section != 'all' && $section != null && $section != "") {
                    $where = " and section=? and  admn_no=? ";
                    $secure_array = array($session_yr, $section, $admn_no);
                } else {
                    $where = " and  admn_no=? ";
                    $secure_array = array($session_yr, $admn_no);
                }
            }
        }
        $sql = "select  null as  both_status_string,
                       null AS both_status_string_old ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name , A.admn_no,gp.group from
                   (select admn_no,section  from  stu_section_data where session_year=? " . $where . ")A
                      inner join user_details ud on ud.id=A.admn_no join reg_regular_form r on r.admn_no=A.admn_no and r.`session`='" . $session . "' and r.`session_year`='" . $session_yr . "'     ".$crs_struct_concat." "
                . " and  r.hod_status='1' and r.acad_status='1'  and  r.semester='$sem'     left join section_group_rel gp  on gp.section=A.section and gp.session_year='".$this->input->post('session_year')."' order by A.admn_no";


          }
        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getPREPStudentList($admn_no = null) {
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        $yr = explode('-', $this->input->post('session_year'));
        if ($admn_no == null) {
            if ($this->input->post('dept') != 'all') {
                $where2 = "";
                $secure_array = array('prep', $yr[0], $this->input->post('dept'));
            } else {
                $where2 = "";
                $secure_array = array('prep', $yr[0]);
            }
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $where2 = " and admn_no in(" . $admn_no . ") ";
                $secure_array = array('prep', $yr[0], $this->input->post('dept'));
            } else {
                $where2 = " and admn_no=? ";
                $secure_array = array('prep', $yr[0], $admn_no);
            }
        }
        $sql = "select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                  from
                   (select admn_no  from stu_academic  where auth_id=? and  enrollment_year=?  " . $where2 . ") A
                   inner join user_details ud on ud.id=A.admn_no";
        if ($this->input->post('dept') != 'all') {
            $sql.=" and dept_id=?";
        }
        $sql.=" order by A.admn_no";

        $query = $this->db->query($sql, $secure_array);

        //    echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getJRFStudentList($dept, $course_id, $branch_id, $admn_no = null,$type='R') {
		if ($this->input->post('exm_type') == 'jrf_spl')$type='S';
        $table = " reg_exam_rc_form ";
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        if ($admn_no == null) {
            $where2 = "";
            //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
            $secure_array = array($this->input->post('session'), $this->input->post('session_year'), $course_id, $branch_id, '2', '2', $dept);
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $where2 = " and admn_no in(" . $admn_no . ") ";
                //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
                $secure_array = array($this->input->post('session'), $this->input->post('session_year'), $course_id, $branch_id, '2', '2', $dept);
            } else {
                $where2 = " and admn_no=? ";
                //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$admn_no,$course_id,$branch_id,$dept);
                $secure_array = array($this->input->post('session'), $this->input->post('session_year'),  $course_id, $branch_id, '2', '2', $admn_no,$dept);
            }
        }
        /*   $sql="select  distinct(B.admn_no),concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
          from
          (select admn_no from   ".$table."  where  session=?  and session_year=?  and hod_status=? and  acad_status=?  ".$where2."
          and upper(course_id)=? and upper(branch_id)=?   )B
          inner join user_details ud on ud.id=B.admn_no   and dept_id=?
          order by B.admn_no
          ";
         */
        $sql = " select x.*,both_status_string, group_concat( (select s.subject_id from subjects s  where s.id= rexs.sub_id))  as jrf_subject_list from
		    (select B.admn_no,B.form_id, CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,IF((B.hod_status='1' AND B.acad_status='1') ,'',' Appv. Pending') as  both_status_string ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                  from
                   (select admn_no ,form_id, hod_status,acad_status from   " . $table . "  where  session=?  and session_year=?
                   and upper(course_id)=? and upper(branch_id)=? and hod_status<>? and  acad_status<>? " . $where2 . "   and type= '".$type."' )B
                   inner join user_details ud on ud.id=B.admn_no   and dept_id=?
                   )x
                  inner join reg_exam_rc_subject rexs on rexs.form_id=x.form_id group by  x.admn_no order by x.admn_no
                   ";


        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

// @ desc: as per cbcs norms  jrf  will be also  stored in regular form @ dated:20-4-20
   function get_cbcs_JRFStudentList($dept, $course_id, $branch_id, $admn_no = null,$type='R') {
    if ($this->input->post('exm_type') == 'jrf_spl')$type='S';
        $table = " reg_regular_form ";
if(  $branch_id<>null &&  $branch_id<>'JRF')
  $br_str =" and upper(branch_id)='".$branch_id."'";
else
  $br_str ="";

// echo  $branch_id; die();
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        if ($admn_no == null) {
            $where2 = "";
            //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
            $secure_array = array($this->input->post('session'), $this->input->post('session_year'), $course_id, '1', '1', $dept);
        } else {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $where2 = " and admn_no in(" . $admn_no . ") ";
                //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
                $secure_array = array($this->input->post('session'), $this->input->post('session_year'), $course_id, '1', '1', $dept);
            } else {
                $where2 = " and admn_no=? ";
                //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$admn_no,$course_id,$branch_id,$dept);
                $secure_array = array($this->input->post('session'), $this->input->post('session_year'),  $course_id,'1', '1', $admn_no,$dept);
            }
        }
           $sql="select  distinct(B.admn_no),concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
          from
          (select admn_no from   ".$table."  where  session=?  and session_year=?     ".$where2."
          and upper(course_id)=? $br_str  and hod_status=? and  acad_status=?  and   course_aggr_id like '%jrf%' )B
          inner join user_details ud on ud.id=B.admn_no   and dept_id=?
          order by B.admn_no
          ";
        
        /*$sql = " select x.*,both_status_string, group_concat( (select s.subject_id from subjects s  where s.id= rexs.sub_id))  as jrf_subject_list from
        (select B.admn_no,B.form_id, CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,IF((B.hod_status='1' AND B.acad_status='1') ,'',' Appv. Pending') as  both_status_string ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                  from
                   (select admn_no ,form_id, hod_status,acad_status from   " . $table . "  where  session=?  and session_year=?
                   and upper(course_id)=? and upper(branch_id)=? and hod_status<>? and  acad_status<>? " . $where2 . "   and type= '".$type."' )B
                   inner join user_details ud on ud.id=B.admn_no   and dept_id=?
                   )x
                  inner join reg_exam_rc_subject rexs on rexs.form_id=x.form_id group by  x.admn_no order by x.admn_no
                   ";

*/
        $query = $this->db->query($sql, $secure_array);
   //     echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

  function getStudentList_cbcs($dept, $course_id, $branch_id, $sem, $admn_no = null,$crs_struct=null) {
		// echo $dept .','. $course_id.','.$branch_id.','.$sem.','.$admn_no .'-'.$crs_struct ; die();
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        if ($this->input->post('exm_type') == "other") {
            $where = " and  semester like '%?%' and type='R'";
            $where3 = " and  semester like '%?%' and type='R'";
            $table = " reg_exam_rc_form ";
            $table2 = " reg_other_form ";
			 if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            if ($admn_no == null) {
                //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                $where2 = "";
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in (" . $admn_no . ") ";
                    //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no);
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no);
                }
            }
            $sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (
                      (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table2 . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  ".$crs_struct_concat."  " . $where3 . "   " . $where2 . "  )
                       union
                   (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  ".$crs_struct_concat."  " . $where . "   " . $where2 . "  )


                    )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no
                   ";
        } else if ($this->input->post('exm_type') == "spl") {
            $where = " and  semester like '%?%'  and type='S'";
            $where3 = " and  semester like '%?%' and type='S'";
            $table = " reg_exam_rc_form ";
            $table2 = " reg_other_form ";

			 if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            if ($admn_no == null) {
                $where2 = "";
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in(" . $admn_no . ") ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no);
                }
            }
         /*   $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  " . $where . "   " . $where2 . "   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no group by B.admn_no
                   order by B.admn_no
                   ";*/
			$sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (
                      (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?   ".$crs_struct_concat."  " . $where . "   " . $where2 . "  )
                       union
                   (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table2 . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?    ".$crs_struct_concat." " . $where3 . "   " . $where2 . "  )


                    )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no ";
        } else if ($this->input->post('exm_type') == "espl") {
             if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            $where = " and  semester like '%?%'  and ( type='E' or type='S' )";
            $table = " reg_other_form ";

            $where3 = " and  semester like '%?%'  and type='E'";
            $table2 = " reg_exam_rc_form ";

            if ($admn_no == null) {
                $where2 = "";
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem);
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in(" . $admn_no . ") ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem, $admn_no,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$admn_no);
                }
            }
            $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?   ".$crs_struct_concat."  " . $where . "   " . $where2 . "



                  union
                   SELECT admn_no,course_id,branch_id,semester,hod_status,acad_status   FROM   " . $table2 . "
                   WHERE session=? AND session_year=?  AND hod_status=?  AND acad_status=? AND UPPER(course_id)=?  AND branch_id=? ".$crs_struct_concat."
                    " . $where3 . "   " . $where2 . "

                    )B
                    on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no
                   ";
        } else if ($this->input->post('exm_type') == "regular") {
          /*  if ($this->input->post('session') == 'Summer') {
			//	if(($sem%2)==0){$sem1=$sem-1;} else {$sem1=$sem+1;}

				if($crs_struct){
            $crs_struct_concat=" and d.aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
                //$where = "and  (d.semester=? or d.semester=? ) ";
				       $where = "and  (d.semester=? ) ";
				//$where = "";
                $table = " reg_summer_form ";
                if ($admn_no == null) {
                    $where2 = "";
                    $secure_array = array($dept,  $sem, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and a.admn_no in(" . $admn_no . ") ";
                        $secure_array = array($dept,  $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id);
                    } else {
                        $where2 = " and a.admn_no=? ";
                        $secure_array = array($dept,   $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $admn_no);
                    }
                }

                $sql = "SELECT null as  both_status_string,A.admn_no, null AS both_status_string_old ,A.stu_name as  st_name
                        FROM(
                        SELECT a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name, c.sub_id,e.name,e.subject_id
                        FROM " . $table . "  a
                        INNER JOIN user_details b ON b.id=a.admn_no  and b.dept_id=?
                        INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
                        INNER JOIN course_structure d ON d.id=c.sub_id   ".$where."  ".$crs_struct_concat."
                        INNER JOIN subjects e ON e.id=d.id
                        INNER JOIN departments f ON f.id=b.dept_id
                        INNER JOIN cs_courses g ON g.id=a.course_id
                        INNER JOIN cs_branches h ON h.id=a.branch_id
                        WHERE a.session=?  and  a.session_year=? AND a.hod_status=? AND a.acad_status=?   and upper(a.course_id)=? and a.branch_id=?   " . $where2 . "
                        ORDER BY a.admn_no)A
                        GROUP BY A.admn_no
                        ORDER BY A.admn_no
                         ";
            } else*/ 
			//{
				
                $where = " and  semester=? ";
                $table = " reg_regular_form ";
   if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
                if ($admn_no == null) {
                    $where2 = "";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and admn_no in(" . $admn_no . ") ";
                        $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem);
                    } else {
                        $where2 = " and admn_no=? ";
                        $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem, $admn_no);
                    }
                }
                $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name, null as  both_status_string,
                       null AS both_status_string_old
                    from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=?   ".$crs_struct_concat." and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  " . $where . "   " . $where2 . "   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no   group by B.admn_no
                   order by B.admn_no
                   ";
           // }
        }

        $query = $this->db->query($sql, $secure_array);
//        echo $this->db->last_query();     die()     ;

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getStudentList($dept, $course_id, $branch_id, $sem, $admn_no = null,$crs_struct=null) {
		// echo $dept .','. $course_id.','.$branch_id.','.$sem.','.$admn_no .'-'.$crs_struct ; die();
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        if ($this->input->post('exm_type') == "other") {
            $where = " and  semester like '%?%' and type='R'";
            $where3 = " and  semester like '%?%' and type='R'";
            $table = " reg_exam_rc_form ";
            $table2 = " reg_other_form ";
			 if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            if ($admn_no == null) {
                //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                $where2 = "";
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in (" . $admn_no . ") ";
                    //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no);
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no);
                }
            }
            $sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (
                      (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table2 . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  ".$crs_struct_concat."  " . $where3 . "   " . $where2 . "  )
                       union
                   (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  ".$crs_struct_concat."  " . $where . "   " . $where2 . "  )


                    )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no
                   ";
        } else if ($this->input->post('exm_type') == "spl") {
            $where = " and  semester like '%?%'  and type='S'";
            $where3 = " and  semester like '%?%' and type='S'";
            $table = " reg_exam_rc_form ";
            $table2 = " reg_other_form ";

			 if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            if ($admn_no == null) {
                $where2 = "";
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in(" . $admn_no . ") ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no, $this->input->post('session'), $this->input->post('session_year'), '2', '2', $course_id, $branch_id, (int) $sem, $admn_no);
                }
            }
         /*   $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?  " . $where . "   " . $where2 . "   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no group by B.admn_no
                   order by B.admn_no
                   ";*/
			$sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (
                      (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?   ".$crs_struct_concat."  " . $where . "   " . $where2 . "  )
                       union
                   (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table2 . "  where  session=?  and session_year=? and hod_status<>? and  acad_status<>?
                   and upper(course_id)=? and branch_id=?    ".$crs_struct_concat." " . $where3 . "   " . $where2 . "  )


                    )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no ";
        } else if ($this->input->post('exm_type') == "espl") {
             if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
            $where = " and  semester like '%?%'  and ( type='E' or type='S' )";
            $table = " reg_other_form ";

            $where3 = " and  semester like '%?%'  and type='E'";
            $table2 = " reg_exam_rc_form ";

            if ($admn_no == null) {
                $where2 = "";
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem);
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where2 = " and admn_no in(" . $admn_no . ") ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem);
                } else {
                    $where2 = " and admn_no=? ";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem, $admn_no,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, (int) $sem,$admn_no);
                }
            }
            $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?   ".$crs_struct_concat."  " . $where . "   " . $where2 . "



                  union
                   SELECT admn_no,course_id,branch_id,semester,hod_status,acad_status   FROM   " . $table2 . "
                   WHERE session=? AND session_year=?  AND hod_status=?  AND acad_status=? AND UPPER(course_id)=?  AND branch_id=? ".$crs_struct_concat."
                    " . $where3 . "   " . $where2 . "

                    )B
                    on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no  group by B.admn_no
                   order by B.admn_no
                   ";
        } else if ($this->input->post('exm_type') == "regular") {
            if ($this->input->post('session') == 'Summer') {
				//if(($sem%2)==0){$sem1=$sem-1;} else {$sem1=$sem+1;}

				if($crs_struct){
            $crs_struct_concat=" and d.aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
                //$where = "and  (d.semester=? or d.semester=? ) ";
				       $where = "and  (d.semester=? ) ";
				//$where = "";
                $table = " reg_summer_form ";
                if ($admn_no == null) {
                    $where2 = "";
                    $secure_array = array($dept,  $sem, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and a.admn_no in(" . $admn_no . ") ";
                        $secure_array = array($dept,  $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id);
                    } else {
                        $where2 = " and a.admn_no=? ";
                        $secure_array = array($dept,   $sem,$this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $admn_no);
                    }
                }

                $sql = "SELECT null as  both_status_string,A.admn_no, null AS both_status_string_old ,A.stu_name as  st_name
                        FROM(
                        SELECT a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name, c.sub_id,e.name,e.subject_id
                        FROM " . $table . "  a
                        INNER JOIN user_details b ON b.id=a.admn_no  and b.dept_id=?
                        INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
                        INNER JOIN course_structure d ON d.id=c.sub_id   ".$where."  ".$crs_struct_concat."
                        INNER JOIN subjects e ON e.id=d.id
                        INNER JOIN departments f ON f.id=b.dept_id
                        INNER JOIN cs_courses g ON g.id=a.course_id
                        INNER JOIN cs_branches h ON h.id=a.branch_id
                        WHERE a.session=?  and  a.session_year=? AND a.hod_status=? AND a.acad_status=?   and upper(a.course_id)=? and a.branch_id=?   " . $where2 . "
                        ORDER BY a.admn_no)A
                        GROUP BY A.admn_no
                        ORDER BY A.admn_no
                         ";
            } else 
			{
				
                $where = " and  semester=? ";
                $table = " reg_regular_form ";
   if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
                if ($admn_no == null) {
                    $where2 = "";
                    $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and admn_no in(" . $admn_no . ") ";
                        $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem);
                    } else {
                        $where2 = " and admn_no=? ";
                        $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), '1', '1', $course_id, $branch_id, $sem, $admn_no);
                    }
                }
                $sql = "select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name, null as  both_status_string,
                       null AS both_status_string_old
                    from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=?   ".$crs_struct_concat." and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  " . $where . "   " . $where2 . "   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id
                   left join user_details ud on ud.id=B.admn_no   group by B.admn_no
                   order by B.admn_no
                   ";
            }
        }

        $query = $this->db->query($sql, $secure_array);
//        echo $this->db->last_query();     die()     ;

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

	 function getSubjectsByAdminNoFrom_tabulation_particular_session($dept,$branch, $course_id, $sem, $admn_no,$session) {
        $secure_array = array($admn_no,$dept, $course_id, $branch, $sem,$session);

$sql="SELECT tb.examtype, tb.subje_name, tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts AS totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts, IF((tb.theory=0 AND tb.practiocal=0 AND tb.sessional=0),'Practicle','Theory') AS type, tb.subje_ftsp as stu_status, tb.subje_code AS sub_code,tb.ltp AS LTP,tb.sessional,tb.theory,tb.practiocal AS practical,tb.grade,tb.crpts,tb.totalmarks AS total, tb.crdhrs AS credit_hours,null as  course_id
FROM tabulation1 tb
WHERE tb.adm_no=? AND tb.sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=?)
and  tb.examtype='R'
 and  tb.wsms=?
GROUP BY tb.ysession, tb.sem_code,tb.examtype, tb.wsms, tb.subje_code
ORDER BY tb.ysession DESC,tb.sem_code DESC, tb.wsms DESC,tb.examtype DESC,tb.subje_code";


        $query = $this->db->query($sql, $secure_array);
           // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {


$sql="SELECT tb.examtype, tb.subje_name, tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts AS totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts, IF((tb.theory=0 AND tb.practiocal=0 AND tb.sessional=0),'Practicle','Theory') AS type, tb.subje_ftsp as stu_status, tb.subje_code AS sub_code,tb.ltp AS LTP,tb.sessional,tb.theory,tb.practiocal AS practical,tb.grade,tb.crpts,tb.totalmarks AS total, tb.crdhrs AS credit_hours,null as  course_id
FROM alumni_tabulation1 tb
WHERE tb.adm_no=? AND tb.sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=?)
and  tb.examtype='R'
 and  tb.wsms=?
GROUP BY tb.ysession, tb.sem_code,tb.examtype, tb.wsms, tb.subje_code
ORDER BY tb.ysession DESC,tb.sem_code DESC, tb.wsms DESC,tb.examtype DESC,tb.subje_code";


        $query = $this->db->query($sql, $secure_array);
		 if ($query->num_rows() > 0)  return $query->result(); else false;
        }
    }


    function getSubjectsByAdminNoFrom_tabulation($branch, $course_id, $sem, $admn_no) {
        /* $p=  explode('-', $this->input->post('session_year'));
          $a=  substr($p[0],-2)-1;
          $b= substr($p[1],-2)-1;
         * */

        //echo $a;echo $b;
        /* $secure_array=array($admn_no,$a.$b,'S',
          ($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")),
          $this->input->post('dept'),$course_id,$branch,$sem);
          $sql=" select  tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts as totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts,  if((tb.theory=0 and tb.practiocal=0 and tb.sessional=0),'Practicle','Theory') as type,
          tb.subje_ftsp, tb.subje_code  as  sub_code ,tb.ltp as LTP,tb.sessional,tb.theory,tb.practiocal as practical,tb.grade,tb.crpts ,tb.totalmarks as total, tb.crdhrs as credit_hours from  tabulation2 tb where tb.adm_no=?  and tb.`session`=? and tb.examtype=? and tb.wsms=? and  tb.sem_code=
          (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)";
         */
        /* $secure_array=array($admn_no,$a.$b,
          ($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")),
          $this->input->post('dept'),$course_id,$branch,$sem);


          $sql=" select tb.examtype, tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts as totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts,  if((tb.theory=0 and tb.practiocal=0 and tb.sessional=0),'Practicle','Theory') as type,
          tb.subje_ftsp, tb.subje_code  as  sub_code ,tb.ltp as LTP,tb.sessional,tb.theory,tb.practiocal as practical,tb.grade,tb.crpts ,tb.totalmarks as total, tb.crdhrs as credit_hours from  tabulation2 tb where tb.adm_no=?  and tb.`session`=? and tb.wsms=? and  tb.sem_code=
          (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)  group by tb.examtype,tb.subje_code order by tb.examtype desc,tb.subje_code ";


         */
        $secure_array = array($admn_no,
            //($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")),
            //($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':'SS')),
            $this->input->post('dept'), $course_id, $branch, $sem, $admn_no,$this->input->post('dept'), $course_id, $branch, $sem,$admn_no,$this->input->post('dept'), $course_id, $branch, $sem);


        /*$sql = " select tb.examtype,  tb.subje_name,   tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts as totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts,  if((tb.theory=0 and tb.practiocal=0 and tb.sessional=0),'Practicle','Theory') as type,
							  tb.subje_ftsp, tb.subje_code  as  sub_code ,tb.ltp as LTP,tb.sessional,tb.theory,tb.practiocal as practical,tb.grade,tb.crpts ,tb.totalmarks as total, tb.crdhrs as credit_hours from  tabulation1 tb where tb.adm_no=?  and  tb.sem_code=
                  (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)
                  group by tb.ysession, tb.sem_code,tb.examtype, tb.wsms,  tb.subje_code
                   order by  tb.ysession desc,tb.sem_code desc, tb.wsms desc ,tb.examtype desc,tb.subje_code ";*/


$sql="SELECT tb.examtype, tb.subje_name, tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts AS totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts, IF((tb.theory=0 AND tb.practiocal=0 AND tb.sessional=0),'Practicle','Theory') AS type, tb.subje_ftsp as stu_status, tb.subje_code AS sub_code,tb.ltp AS LTP,tb.sessional,tb.theory,tb.practiocal AS practical,tb.grade,tb.crpts,tb.totalmarks AS total, tb.crdhrs AS credit_hours,null as  course_id
FROM tabulation1 tb
WHERE tb.adm_no=? AND tb.sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=?)
and  tb.examtype=(select max(examtype) from tabulation1 where adm_no=? and sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=?  AND d.sem=?) )
 and  tb.wsms=(
SELECT MAX(wsms)
FROM tabulation1
WHERE adm_no=? AND sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=? ))
GROUP BY tb.ysession, tb.sem_code,tb.examtype, tb.wsms, tb.subje_code
ORDER BY tb.ysession DESC,tb.sem_code DESC, tb.wsms DESC,tb.examtype DESC,tb.subje_code";


        $query = $this->db->query($sql, $secure_array);
           // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {


$sql="SELECT tb.examtype, tb.subje_name, tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts AS totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts, IF((tb.theory=0 AND tb.practiocal=0 AND tb.sessional=0),'Practicle','Theory') AS type, tb.subje_ftsp as stu_status, tb.subje_code AS sub_code,tb.ltp AS LTP,tb.sessional,tb.theory,tb.practiocal AS practical,tb.grade,tb.crpts,tb.totalmarks AS total, tb.crdhrs AS credit_hours,null as  course_id
FROM alumni_tabulation1 tb
WHERE tb.adm_no=? AND tb.sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=?)
and  tb.examtype=(select max(examtype) from alumni_tabulation1 where adm_no=? and sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=?  AND d.sem=?) )
 and  tb.wsms=(
SELECT MAX(wsms)
FROM alumni_tabulation1
WHERE adm_no=? AND sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=? ))
GROUP BY tb.ysession, tb.sem_code,tb.examtype, tb.wsms, tb.subje_code
ORDER BY tb.ysession DESC,tb.sem_code DESC, tb.wsms DESC,tb.examtype DESC,tb.subje_code";


        $query = $this->db->query($sql, $secure_array);
		 if ($query->num_rows() > 0)  return $query->result(); else false;
        }
    }

    function getCummulativeFromFoil($dept, $branch, $data, $sem, $admn_no) {
        $secure_array = array($admn_no, $dept, $data['course_id'], $branch, $sem);
        $sql = "   select tb.*,null as totcrpts  from  final_semwise_marks_foil tb where tb.admn_no=? and tb.dept=?  and  tb.course =? and  tb.branch=? and tb.semester=?   group by tb.exam_type,tb.session,tb.semester order by tb.exam_type desc,tb.session desc limit 1 ";
        $query = $this->db->query($sql, $secure_array);
        //    echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return false;
        }
    }

    function getCummulativeFromTabulation1($branch, $course_id, $sem, $admn_no) {
        $secure_array = array($admn_no,
            /* ($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")), */
            $this->input->post('dept'), $course_id, $branch, $sem);


        $sql = "    select tb.totcrpts, tb.examtype as exam_type,  tb.ctotcrpts,tb.ctotcrhr ,tb.totcrpts from  tabulation1 tb where tb.adm_no=?   and  tb.sem_code=
                       (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)  group by tb.examtype,tb.wsms,tb.sem_code order by tb.examtype desc,tb.wsms desc limit 1 ";

        $query = $this->db->query($sql, $secure_array);
        //   echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
        }
    }

    function get_grade_point($tot) {
        $secure_array = array($tot);
        $sql = " select gp.grade  from  grade_points gp  where ? between gp.min and gp.max";
        $query = $this->db->query($sql, $secure_array);
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->grade;
        else {
            return 0;
        }
    }

    function get_grade_pt($tot) {
        $secure_array = array($tot);
        $sql = " select gp.points  from  grade_points gp  where ? between gp.min and gp.max";
        $query = $this->db->query($sql, $secure_array);
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->points;
        else {
            return 0;
        }
    }

    /* function  get_sub_map_id($session,$session_year,$type,$mis_sub_code){

      $sql="select sub_map_id  from marks_master m1 where m1.`session`=? and m1.session_year=? and m1.`type`=? and m1.subject_id=?) ";

      $query = $this->db->query($sql,array($admn_no,$session,$session_year,$type,$mis_sub_code));

      //    echo $this->db->last_query(); die();

      if ($this->db->affected_rows() > 0) {
      return $query->row();
      } else {
      return FALSE;
      }
      } */

    // check  whether  student  be  the  case  of other
    function student_belongs_to_MIS($dept, $sess_yr, $session, $branch_id, $crs_id, $sem, $admn_no, $status, $type = null) {
        if ($status == 'NA') {
            $select = $this->db->select('admn_no')->where(
                                    array('session_yr' => $sess_yr, 'session' => $session, 'dept' => $dept,
                                        'course' => $crs_id, 'branch' => $branch_id, 'semester' => $sem, 'admn_no' => $admn_no, 'type' => $type))
                            ->order_by('exam_type', 'desc')->limit('1')->get('final_semwise_marks_foil');
        } else {
            $select = $this->db->select('admn_no')->where(
                                    array('session_yr' => $sess_yr, 'session' => $session, 'dept' => $dept,
                                        'course' => $crs_id, 'branch' => $branch_id, 'semester' => $sem, 'admn_no' => $admn_no, 'type' => $type, 'status' => $status))
                            ->order_by('exam_type', 'desc')->limit('1')->get('final_semwise_marks_foil');
        }

        // echo $this->db->last_query();
        if ($select->num_rows())
            return true;
        else
            return false;
    }

    //get row of other student given exam in monsoon
    function get_desc_student_belongs_to_MIS($dept, $sess_yr, $session, $branch_id, $crs_id, $sem, $admn_no, $type = null) {
        $select = $this->db->select('ctotcrpts,ctotcrhr')->where(
                                array('session_yr' => $sess_yr, 'session' => $session, 'dept' => $dept,
                                    'course' => $crs_id, 'branch' => $branch_id, 'semester' => $sem, 'admn_no' => $admn_no, 'type' => $type))
                        ->order_by('exam_type', 'desc')->limit('1')->get('final_semwise_marks_foil');
        //  echo $this->db->last_query(); die();
        return $select->result();
    }

    // end
    // check  whether  student  be  the  case  of reapaeator  from tabulation1(old backup table)
    function student_belongs_to_repeater($dept, $sess_yr, $session, $branch_id, $crs_id, $sem, $admn_no) {
        $secure_array = array($admn_no, 'R', $this->input->post('dept'), $crs_id, $branch_id, $sem);


        $sql = "    select  tb.totcrpts, tb.examtype,  tb.ctotcrpts,tb.ctotcrhr from  tabulation1 tb where tb.adm_no=?   and  tb.examtype=? and  tb.sem_code=
                       (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)
                        group by tb.examtype,tb.wsms,tb.sem_code order by tb.examtype desc,tb.wsms desc limit 1 ";

        $query = $this->db->query($sql, $secure_array);
            //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return false;
        }
    }


    function student_belongs_to_repeater_MIS($dept, $sess_yr, $session, $branch_id, $crs_id, $sem, $admn_no) {
        $secure_array = array('1','1',$admn_no, $crs_id, $branch_id, $sem,$session);
        $sql = " select count(rgf.admn_no) as ctr from reg_regular_form rgf where rgf.hod_status=? and   rgf.acad_status=? and rgf.admn_no=? and rgf.course_id=? and  rgf.branch_id=? and rgf.semester=? and rgf.session=? ";
         $query = $this->db->query($sql, $secure_array);
         //  echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return false;
        }
    }

    function getSubjectsByAdminNo_Spl($branch_id, $sem, $admn_no, $type = null) {
        //  echo  $this->input->post('dept'); die();
    $secure_array = array($admn_no,$this->input->post('session'),$this->input->post('session_year'), $type, (strtoupper($this->input->post('dept'))=='COMM'?'%'.$this->input->post('dept').'%' : $this->input->post('dept')), $sem);

    if(strtoupper($this->input->post('dept'))=='COMM')  $exahange=" e.aggr_id like  ? " ; else $exahange=' e.dept_id =? ';
    $sql = " select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
    (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
    (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and  b.session_year=? and b.type=?  and  b.status='Y' ) A
     left join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id where $exahange and e.semester=?
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc ";
        $query = $this->db->query($sql, $secure_array);
        // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getSubjectsByAdminNo_Other($dept, $crs, $branch_id, $sem, $admn_no, $sess_yr, $sess,$exm_type, $type,$curr_sessyr=null,$curr_sess=null) {
        $secure_array = array($dept, $crs, $branch_id, $sem, $admn_no, $sess_yr, $sess, $type);

        $getgradtype = $this->get_grading_styleByAdminNo( $admn_no );
         //echo  $getgradtype->grading_type; die();
		// echo $curr_sessyr.'<br/>';
		 //echo ($curr_sessyr >= Exam_tabulation_config::$start_project_discard_session_yr?1:0).'<br/>';
  //echo  (in_array(strtolower($admn_no),Exam_tabulation_config::$start_project_discard_exception) && in_array($sem,Exam_tabulation_config::$start_project_discard_sem_exception) ?1:0);
		// die();
	     $table=  in_array(strtolower($admn_no),Exam_tabulation_config::$start_project_discard_exception) && in_array($sem,Exam_tabulation_config::$start_project_discard_sem_exception)  ? 'subjects_old' :($curr_sessyr >= Exam_tabulation_config::$start_project_discard_session_yr  ? ($getgradtype->grading_type=='A'?'subjects_old':'subjects') :'subjects_old' )  ;


  /*  $sql = " select null as stu_status, null as sub_map_id, grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.*  from
(select C.* from (select B.*,d.sequence as seq from
(select A.*,c.id as sub_id,c.name,c.credit_hours ,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
(select b.dept,b.course  as course_id,b.branch as branch_id ,b.semester, null as  stu_status,a.theory,a.sessional,a.total,a.grade,b.tot_cr_pts,b.tot_cr_hr,a.mis_sub_id as subject_id,b.`session`,b.session_yr,a.mis_sub_id  from final_semwise_marks_foil_desc as a
 inner join final_semwise_marks_foil  as b on  b.id=a.foil_id AND a.admn_no=b.admn_no  and b.dept=?  and b.course=? and b.branch=? and b.semester=? and  b.admn_no=? and b.session_yr=? and b.session=? and b.type=? )A
 inner join subjects as c on A.mis_sub_id=c.id ) B inner join course_structure as d on B.mis_sub_id=d.id ) C
 group by C.sub_code order by C.semester,C.seq asc )grp
 left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc ";
*/


       $sql = "
SELECT NULL AS stu_status,/* NULL AS*/ sub_map_id, grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
FROM
(
SELECT C.*
FROM (
SELECT B.*, (case when   d.aggr_id like 'honour%' then 'honour'  when   d.aggr_id like 'minor%' then 'minor' else B.crs_id end) as course_id
FROM
(
SELECT A.*,c.id AS sub_id,c.name,c.credit_hours,c.`type`,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
FROM
(
SELECT mm.sub_map_id,  a.sub_code AS ssub_id,b.dept,b.course AS crs_id,b.branch AS branch_id,b.semester, NULL AS stu_status,a.theory,a.sessional,a.total,a.grade,b.tot_cr_pts,b.tot_cr_hr,a.mis_sub_id AS subject_id,b.`session`,b.session_yr,a.mis_sub_id
FROM final_semwise_marks_foil_desc AS a
INNER JOIN final_semwise_marks_foil AS b ON b.id=a.foil_id AND a.admn_no=b.admn_no AND b.dept=? AND b.course=? AND b.branch=? AND b.semester=? AND b.admn_no=? AND b.session_yr=? AND b.session=? AND b.type=?
  /*inner*/ left join marks_master mm  on mm.session_year=b.session_yr and mm.`session`=b.session and /*a.mis_sub_id=mm.subject_id*/    (CASE WHEN (a.mis_sub_id <>'') THEN a.mis_sub_id=mm.subject_id  ELSE 1=1 END) and mm.`type`=b.type and mm.status='Y'
)A
left JOIN $table AS c ON  char(A.ssub_id)=char(c.subject_id)  /*and   A.subject_id=c.id */

 and
        (case
                when (A.mis_sub_id <>'') then   A.subject_id=c.id
                ELSE 1=1
        end)


group by A.ssub_id) B

/*INNER JOIN course_structure AS d ON B.mis_sub_id=d.id*/
/*GROUP BY C.sub_code*/
left JOIN course_structure AS d ON
(CASE WHEN (B.mis_sub_id <>'') THEN B.mis_sub_id=d.id ELSE 1=1 END)
) C
GROUP BY C.sub_code

ORDER BY C.semester ASC)grp
LEFT JOIN grade_points ON grade_points.grade=trim(grp.grade)
ORDER BY grp.semester,grp.ssub_id/*, grp.seq*/ ASC";

        $query = $this->db->query($sql, $secure_array);
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    /*function getSubjectsByAdminNo_With_without_hons($branch_id, $sem, $admn_no, $hons,$sess=true) {
        $secure_array = array($admn_no, $this->input->post('session_year'), 'R', $this->input->post('dept'), $sem);


        if ($hons == 'N')
            $replace = "and e.course_id!='minor' and  e.course_id!='honour'";
        else
            $replace = "and e.course_id!='minor'";
		if($sess == true){
			$replace2=' and b.session_year=?  ';
		}else{
			$replace2=' ';
			 $secure_array = array($admn_no, 'R', $this->input->post('dept'), $sem);
		}

		if ((strpos($admn_no, 'je') !== false || strpos($admn_no, 'JE') !== false) && ($sem =='1' || $sem=='2' ) ) {
				 $secure_array = array($admn_no, 'R', 'comm', $sem);
		}


        $sql = "select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.*from
   (
   select D.* from(
   select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.marks_master_id,a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? ".$replace2." and b.type=? and  b.status='Y' and b.`session`<>'Summer' ) A
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id   " . $replace . " where e.dept_id=? and e.semester=? order by e.session_year desc  limit 10000 ) D
     group by D.sub_code

	 order by D.semester,D.seq asc )grp
     left join grade_points on grade_points.grade=trim(grp.grade)  order by grp.semester,grp.seq asc
     ";
        $query = $this->db->query($sql, $secure_array);
       //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }*/   /*dated 14sept-17 as  repeator case (particular Subject are not repaeting for the next exam like 'Gandhian study')*/

	function getSubjectsByAdminNo_With_without_hons($branch_id, $sem, $admn_no, $hons,$sess=true) {



        if ($hons == 'N'){
            $replace = "and e.course_id!='minor' and  e.course_id!='honour' ";
               if($sess == true){
			$replace2=' and b.session_year=?  ';
                          $secure_array = array($admn_no, $this->input->post('session_year'), 'R', $this->input->post('dept'), $sem,$admn_no, $this->input->post('session_year'), 'R', $this->input->post('dept'), $sem);
		}else{
			$replace2=' ';
			 $secure_array = array($admn_no, 'R', $this->input->post('dept'), $sem,$admn_no, 'R', $this->input->post('dept'), $sem);
		}

		if ((strpos($admn_no, 'je') !== false || strpos($admn_no, 'JE') !== false) && ($sem =='1' || $sem=='2' ) ) {
			$add_comm= " inner join stu_section_data ssd on ssd.section=e.section and ssd.session_year=e.session_year and ssd.admn_no =C.admn_no " ;
				 $secure_array = array($admn_no, 'R', 'comm', $sem,$admn_no, 'R', 'comm', $sem);


		}


        }
        else{
            $replace = "and e.course_id!='minor' ";

             if($sess == true){
			$replace2=' and b.session_year=?  ';
                          $secure_array = array($admn_no, $this->input->post('session_year'), 'R', $this->input->post('dept'), $sem);
		}else{
			$replace2=' ';
			 $secure_array = array($admn_no, 'R', $this->input->post('dept'), $sem);
		}

		if ((strpos($admn_no, 'je') !== false || strpos($admn_no, 'JE') !== false) && ($sem =='1' || $sem=='2' ) ) {
			$add_comm= " inner join stu_section_data ssd on ssd.section=e.section and ssd.session_year=e.session_year and ssd.admn_no =C.admn_no  "		;
				 $secure_array = array($admn_no, 'R', 'comm', $sem);
		}

        }





       if ($hons == 'N'){
		 if ((strpos($admn_no, 'je') !== false || strpos($admn_no, 'JE') !== false) && ($sem =='1' || $sem=='2' ) ) {
		  $add_comm2= "  inner join stu_section_data ssd on ssd.section=e.section and ssd.session_year=e.session_year and ssd.admn_no ='".$admn_no."' ";
		 }
           $pqr=" having D.sub_map_id=( SELECT max(b.sub_map_id)
FROM marks_subject_description AS a
INNER JOIN marks_master AS b ON a.marks_master_id=b.id
INNER JOIN subject_mapping AS e ON b.sub_map_id = e.map_id  " . $replace . "
 ".$add_comm2."
 WHERE a.admn_no=? ".$replace2."  AND b.type=? AND b.status='Y' AND b.`session`<>'Summer' and e.dept_id=?  and e.semester=?) ";
        }
        else{

            $pqr='';
        }

      $sql = "select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
   (
   select D.* from(
   select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.marks_master_id,a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id,a.admn_no
	from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? ".$replace2." and b.type=? and  b.status='Y' and b.`session`<>'Summer' ) A
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id   " . $replace . "  " . $add_comm . "
	 where e.dept_id=? and e.semester=? order by e.session_year desc  /*, C.marks_master_id*/ limit 10000 ) D
     group by D.sub_code
      ".$pqr."
     order by D.semester,D.seq asc )grp
     left join grade_points on grade_points.grade=trim(grp.grade)  order by grp.semester,grp.seq asc
     ";
        $query = $this->db->query($sql, $secure_array);
      //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            //return 0;
			       if ($hons == 'N'){
		 if ((strpos($admn_no, 'je') !== false || strpos($admn_no, 'JE') !== false) && ($sem =='1' || $sem=='2' ) ) {
		  $add_comm2= "  inner join stu_section_data ssd on ssd.section=e.section and ssd.session_year=e.session_year and ssd.admn_no ='".$admn_no."' ";
		 }
           $pqr=" having D.sub_map_id=( SELECT max(b.sub_map_id)
FROM alumni_marks_subject_description AS a
INNER JOIN marks_master AS b ON a.marks_master_id=b.id
INNER JOIN subject_mapping AS e ON b.sub_map_id = e.map_id  " . $replace . "
 ".$add_comm2."
 WHERE a.admn_no=? ".$replace2."  AND b.type=? AND b.status='Y' AND b.`session`<>'Summer' and e.dept_id=?  and e.semester=?) ";
        }
        else{

            $pqr='';
        }

      $sql = "select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
   (
   select D.* from(
   select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.marks_master_id,a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id,a.admn_no
	from alumni_marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? ".$replace2." and b.type=? and  b.status='Y' and b.`session`<>'Summer' ) A
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id   " . $replace . "  " . $add_comm . "
	 where e.dept_id=? and e.semester=? order by e.session_year desc  /*, C.marks_master_id*/ limit 10000 ) D
     group by D.sub_code
      ".$pqr."
     order by D.semester,D.seq asc )grp
     left join grade_points on grade_points.grade=trim(grp.grade)  order by grp.semester,grp.seq asc
     ";
        $query = $this->db->query($sql, $secure_array);
      //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
		else return 0;

        }

    }


    /* function get_exhustive_course_structure($sess_yr, $sess,$exm_type,$sem,){
         $secure_array = array( $sess_yr, $sess, ($exm_type == 'jrf_spl'?'JS':'J') );
         $sql="select distinct a.aggr_id from subject_mapping a
where a.session_year='2016-2017' and a.`session`='Winter'
and a.dept_id='ee' and a.semester='4'  and a.course_id='m.tech' and a.branch_id='peed'";
         $query = $this->db->query($sql, $secure_array); echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return 0;
     }*/

    function getSubjectsByAdminNo($dept, $crs, $branch_id, $sem, $admn_no, $sess_yr, $sess, $exm_type,$type =null) {
		$getgradtype = $this->get_grading_styleByAdminNo( $admn_no );

	   $table=  in_array(strtolower($admn_no),Exam_tabulation_config::$start_project_discard_exception)  && in_array($sem,Exam_tabulation_config::$start_project_discard_sem_exception)? 'subjects_old' :( $sess_yr>=Exam_tabulation_config::$start_project_discard_session_yr? ($getgradtype->grading_type=='A'?'subjects_old':'subjects') :'subjects_old' )  ;

       /* if ($type == 'O' || $type == 'S') {
            $secure_array = array($admn_no,$sess, $sess_yr, $type, $dept, $sem);


            $sql = "
   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
   (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and b.session_year=? and b.type=?  and  b.status='Y' ) A
     left join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id where e.dept_id=? and e.semester=?
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=trim(grp.grade)  order by grp.semester,grp.seq asc
     ";
        }
     if (strtoupper($branch_id) == 'PREP') {
         $exchnage= ($dept=='all'&& strtoupper($crs)=='PREP'? " e.dept_id=? " :  " e.course_id=? " );

            $secure_array = array($admn_no, ($sess=='Summer' || ($sess=='Winter' && $exm_type=='spl' )?((($sem%2)<>0?'Monsoon':'Winter')):$sess),$sess_yr, 'R', ($dept=='all'&& strtoupper($crs)=='PREP'?$crs:$dept), -1);


            $sql = "
    select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.*from
    (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and b.session_year=?  and b.type=? and  b.status='Y' ) A
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C
     inner join subject_mapping as e on C.sub_map_id = e.map_id where ".$exchnage." and e.semester=?
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     ";
        }


		else*/
  if (strtoupper($branch_id) != 'JRF') {
     //   echo  $sem.'sem'; die();
     $secure_array = array($admn_no, ($sess=='Summer' || ($sess=='Winter' && $exm_type=='spl' )?((($sem%2)<>0?'Monsoon':'Winter')):$sess),$sess_yr, ($type==null?'R':$type), ($dept=='all'&& strtoupper($branch_id)=='PREP'?'prep':$dept), ($dept=='all'&& strtoupper($branch_id)=='PREP'?$sem:$sem));
     if($dept=='all'&& strtoupper($branch_id)=='PREP')$exchange=" e.course_id=? ";
	else  {
	       if($dept<>'comm') $exchange=" e.dept_id=? ";
		   else $exchange=" e.dept_id=? and e.section='".$this->input->post('section_name') ."' ";
		   }

     $sql = "   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
                (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
               (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
                (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
                 inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and b.session_year=?  and b.type=? and  b.status='Y' ) A
                 inner join $table as c on A.subject_id=c.id and  c.credit_hours<>0) B inner join course_structure as d on B.subject_id=d.id ) C
                 inner join subject_mapping as e on C.sub_map_id = e.map_id where  ". $exchange."  and  e.semester=?
                 group by C.sub_code order by e.semester,C.seq asc )grp
                 left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     ";
    }else {
    $secure_array = array($admn_no, $sess_yr, $sess, ($exm_type == 'jrf_spl'?'JS':'J') );

     $sql = "   SELECT grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
                FROM (
               SELECT C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group`
               FROM (
               SELECT B.*
               FROM (
               SELECT A.*,c.id as sub_id,c.name,c.credit_hours,c.`type` ,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
               FROM (
               SELECT a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id
               FROM marks_subject_description AS a
               INNER JOIN marks_master AS b ON a.marks_master_id=b.id
               WHERE a.admn_no=? AND b.session_year=? and   b.session=? and b.type=? and  b.status='Y') A
               INNER JOIN $table AS c ON A.subject_id=c.id and  c.credit_hours<>0) B
               ) C
               INNER JOIN subject_mapping AS e ON C.sub_map_id = e.map_id
               GROUP BY C.sub_code
               )grp
               LEFT JOIN grade_points ON grade_points.grade=grp.grade
               ORDER BY grp.semester ASC
              ";
   }
    $query = $this->db->query($sql, $secure_array); //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else
		{
			if (strtoupper($branch_id) != 'JRF') {
     //   echo  $sem.'sem'; die();
     $secure_array = array($admn_no, ($sess=='Summer' || ($sess=='Winter' && $exm_type=='spl' )?((($sem%2)<>0?'Monsoon':'Winter')):$sess),$sess_yr, ($type==null?'R':$type), ($dept=='all'&& strtoupper($branch_id)=='PREP'?'prep':$dept), ($dept=='all'&& strtoupper($branch_id)=='PREP'?$sem:$sem));
     if($dept=='all'&& strtoupper($branch_id)=='PREP')$exchange=" e.course_id=? ";
	else  {
	       if($dept<>'comm') $exchange=" e.dept_id=? ";
		   else $exchange=" e.dept_id=? and e.section='".$this->input->post('section_name') ."' ";
		   }

     $sql = "   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
                (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
               (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
                (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from alumni_marks_subject_description as a
                 inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and b.session_year=?  and b.type=? and  b.status='Y' ) A
                 inner join $table as c on A.subject_id=c.id and  c.credit_hours<>0 ) B inner join course_structure as d on B.subject_id=d.id ) C
                 inner join subject_mapping as e on C.sub_map_id = e.map_id where  ". $exchange."  and  e.semester=?
                 group by C.sub_code order by e.semester,C.seq asc )grp
                 left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     ";
    }else {
    $secure_array = array($admn_no, $sess_yr, $sess, ($exm_type == 'jrf_spl'?'JS':'J') );

     $sql = "   SELECT grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
                FROM (
               SELECT C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group`
               FROM (
               SELECT B.*
               FROM (
               SELECT A.*,c.id as sub_id,c.name,c.credit_hours,c.`type` ,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
               FROM (
               SELECT a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id
               FROM alumni_marks_subject_description AS a
               INNER JOIN marks_master AS b ON a.marks_master_id=b.id
               WHERE a.admn_no=? AND b.session_year=? and   b.session=? and b.type=? and  b.status='Y') A
               INNER JOIN $table AS c ON A.subject_id=c.id and  c.credit_hours<>0 ) B
               ) C
               INNER JOIN subject_mapping AS e ON C.sub_map_id = e.map_id
               GROUP BY C.sub_code
               )grp
               LEFT JOIN grade_points ON grade_points.grade=grp.grade
               ORDER BY grp.semester ASC
              ";
   }
    $query = $this->db->query($sql, $secure_array); //echo $this->db->last_query(); die();
			  if ($query->num_rows() > 0)    return $query->result(); else 0;

		}
    }




	 function cbcs_getSubjectsByAdminNo($dept, $crs, $branch_id, $sem, $admn_no, $sess_yr, $sess, $exm_type,$type =null) {
		
		if(strtoupper($crs)=='MINOR'){
			$minor_txt=  " AND UPPER(a.course)='MINOR' ";
		}else{
			$minor_txt=  " AND UPPER(a.course)<>'MINOR' ";
			
		}
		
		
		$getgradtype = $this->get_grading_styleByAdminNo( $admn_no );						
	   $table=  in_array(strtolower($admn_no),Exam_tabulation_config::$start_project_discard_exception)  && in_array($sem,Exam_tabulation_config::$start_project_discard_sem_exception)? 'subjects_old' :( $sess_yr>=Exam_tabulation_config::$start_project_discard_session_yr? ($getgradtype->grading_type=='A'?'subjects_old':'subjects') :'subjects_old' )  ;


  //if (strtoupper($branch_id) != 'JRF'){}
  
     //   echo  $sem.'sem'; die();
     $secure_array = array($admn_no, $sess_yr, ($sess),($type==null?'R':($exm_type == 'jrf_spl'?'R':'J')), ($dept=='all'&& strtoupper($branch_id)=='PREP'?'prep':$dept), ($dept=='all'&& strtoupper($branch_id)=='PREP'?$sem:$sem));
     if($dept=='all'&& strtoupper($branch_id)=='PREP')$exchange=" e.course_id=? ";
	else  {
	       if($dept<>'comm') $exchange=" e.dept_id=? ";
		   else $exchange=" e.dept_id=? and e.section='".$this->input->post('section_name') ."' ";
		   }

     /*$sql = "   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
                (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
               (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
                (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
                 inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session=? and b.session_year=?  and b.type=? and  b.status='Y' ) A
                 inner join $table as c on A.subject_id=c.id and  c.credit_hours<>0) B inner join course_structure as d on B.subject_id=d.id ) C
                 inner join subject_mapping as e on C.sub_map_id = e.map_id where  ". $exchange."  and  e.semester=?
                 group by C.sub_code order by e.semester,C.seq asc )grp
                 left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     ";*/

	 $sql="
SELECT y.session_yr,y.session,  y.dept,y.course, IF(cso.course_id IS NULL, oso.course_id,cso.course_id) AS course_id,
 IF(cso.semester IS NULL, oso.semester,cso.semester) AS sub_sem,
y.branch,y.semester,y.sub_code, y.grade, y.cr_pts AS totcrdthr, y.cr_hr AS credit_hours, y.mis_sub_id,y.total, y.admn_no, y.remark2 AS stu_status, (CASE WHEN cso.sub_code IS NULL THEN oso.sub_name ELSE cso.sub_name END) AS name, (CASE WHEN cso.sub_code IS NULL THEN oso.lecture ELSE cso.lecture END) AS LECTUER, CONCAT((CASE WHEN cso.sub_code IS NULL THEN oso.lecture ELSE cso.lecture END),'-', (CASE WHEN cso.sub_code IS NULL THEN oso.tutorial ELSE cso.tutorial END),'-', (CASE WHEN cso.sub_code IS NULL THEN oso.practical ELSE cso.practical END)) AS LTP, (CASE WHEN cso.sub_code IS NULL THEN oso.sub_type ELSE cso.sub_type END) AS sub_type,y.ctotcrpts,y.ctotcrhr, y.core_ctotcrpts,y.core_ctotcrhr,y.tot_cr_hr,y.tot_cr_pts, y.core_tot_cr_hr,y.core_tot_cr_pts,y.core_cgpa,y.cgpa, y.core_gpa,y.gpa, y.status,y.core_status from
(
select y.* from

(
select y.*,fd.sub_code, fd.grade , fd.cr_pts, fd.cr_hr, fd.mis_sub_id,  if(fd.grade='I',0, fd.total )  as total, fd.remark2
FROM (
SELECT x.*
FROM (
SELECT a.session_yr,a.session,a.admn_no,a.dept,a.course,a.branch,a.semester,a.id,a.`status`, a.ctotcrpts,a.ctotcrhr, a.core_ctotcrpts,a.core_ctotcrhr, a.tot_cr_hr,a.tot_cr_pts, a.core_tot_cr_hr,a.core_tot_cr_pts,a.core_cgpa,a.cgpa, a.core_gpa,a.gpa, a.core_status
FROM final_semwise_marks_foil_freezed AS a
WHERE a.admn_no=? AND a.session_yr=? AND a.`session`=? AND a.type=? $minor_txt   ".($dept<>'comm' && strtoupper($branch_id) != 'JRF' ? " AND a.dept=? AND a.semester=? " :"" )."
ORDER BY a.admn_no,a.semester,a.actual_published_on DESC
LIMIT 100000000)x
GROUP BY x.admn_no) y
JOIN final_semwise_marks_foil_desc_freezed fd ON fd.foil_id=y.id AND fd.admn_no=y.admn_no   order by fd.foil_id desc,fd.sub_code,fd.cr_pts desc limit 100000)y
group by y.sub_code)y

/*LEFT JOIN cbcs_subject_offered cso ON cso.sub_code=y.sub_code AND LOWER(y.course)= LOWER(cso.course_id) AND LOWER(y.branch)= LOWER(cso.branch_id) AND y.session_yr = cso.session_year AND y.session=cso.session
LEFT JOIN old_subject_offered oso ON oso.sub_code=y.sub_code AND LOWER(y.course)= LOWER(oso.course_id) AND LOWER(y.branch)= LOWER(oso.branch_id) AND y.session_yr = oso.session_year AND y.session=oso.session */

/*LEFT JOIN cbcs_subject_offered cso ON cso.sub_code=y.sub_code AND    (case when  lower(cso.course_id)='comm' then 'comm' else LOWER(y.course) end)  = LOWER(cso.course_id)
    AND
	 (case when  lower(cso.course_id)='comm' then 'comm' else  LOWER(y.branch) end)= LOWER(cso.branch_id) AND y.session_yr = cso.session_year AND y.session=cso.session*/

	 	 left join cbcs_stu_course  cc on cc.admn_no=y.admn_no and  cc.subject_code=y.sub_code and  cc.session_year=y.session_yr and  cc.`session`=y.`session`
         left join cbcs_subject_offered cso on cso.id=cc.sub_offered_id


/*LEFT JOIN old_subject_offered oso ON oso.sub_code=y.sub_code AND  (case when  lower(oso.course_id)='comm' then 'comm' else LOWER(y.course) end)= LOWER(oso.course_id) AND
	 (case when  lower(oso.course_id)='comm' then 'comm' else  LOWER(y.branch) end)= LOWER(oso.branch_id) AND y.session_yr = oso.session_year AND y.session=oso.session*/

	 left join old_stu_course  o on o.admn_no=y.admn_no and  o.subject_code=y.sub_code and  o.session_year=y.session_yr and  o.`session`=y.`session`
         left join old_subject_offered oso on oso.id=o.sub_offered_id

	 group by y.sub_code


 ";

  
    $query = $this->db->query($sql, $secure_array); //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();

    }















    /* function getSubMst($id){
      $sql="
      select b.subject_id, b.name,a.sub_id from  subject_mapping_des as a  inner join  subjects b on a.sub_id = b.id  where  a.map_id=? group by a.sub_id
      ";


      $query = $this->db->query($sql, array($id));
      // echo $this->db->last_query(); die();

      if ($query->num_rows() > 0)
      return $query->result_array();

      else {
      return 0;
      }
      } */

    function getSummerMaxNumber($sub,$session_yr){
       // echo $marksId;
	   // echo $this->input->post('session'); die();

		   $sql="select  highest_marks as Max from marks_master where session=(
	 select   case  when MOD( d.semester, 2 )=0 then 'Winter' else 'Monsoon' end as sess  from  course_structure d  where  d.id=?)
	  and session_year=?   and  subject_id= ?  and type=? and  status=? ";
         $q=$this->db->query($sql,array($sub,$session_yr,$sub,'R','Y'));
       //  echo $this->db->last_query();  die();
         return $q->row()->Max;

      }

	   function getSummerMaxNumber_jrf($sub,$session_yr){
       // echo $marksId;
	   // echo $this->input->post('session'); die();

		   $sql="select  highest_marks as Max from marks_master where session=(
	 select   case  when MOD( d.semester, 2 )=0 then 'Winter' else 'Monsoon' end as sess  from  course_structure d  where  d.id=?)
	  and session_year=?   and  subject_id= ?  and type=? and  status=? ";
         $q=$this->db->query($sql,array($sub,$session_yr,$sub,'j','Y'));
    //     echo $this->db->last_query();  die();
         return $q->row()->Max;

      }

    function getSummercommMaxNumber($sub,$session_yr,$sess=null){
        $q="select Z.*,(case when max(d.total) is null then '0' else max(d.total) end) as total  from (select X.map_id,c.id from (select a.map_id,a.sub_id from subject_mapping_des as a
            join subject_mapping as b on a.map_id=b.map_id
where a.sub_id=?
and a.coordinator='1' and b.session_year=? and b.`session`=?  group by b.section) X
inner join marks_master as c on c.sub_map_id=X.map_id and  c.subject_id=X.sub_id  and c.`session`<>?   and c.`type`=? and c.`status`=? and  c.session_year=?) Z
inner join marks_subject_description as d on d.marks_master_id=Z.id";
    $d=$this->db->query($q,array($sub,$session_yr,$sess,'summer','R','Y',$session_yr));

		//echo $this->db->last_query(); die();
                if($d->num_rows() > 0){
                    return $d->row()->total;
                }
                return '0';
    }


    /*function getSubMst($id) {
        $data = array();
        $sql = "
 select b.subject_id, b.name,a.sub_id from  subject_mapping_des as a  inner join  subjects b on a.sub_id = b.id  where  a.map_id=? group by a.sub_id
     ";



        $query = $this->db->query($sql, array($id));
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0) {
            $r = $query->result();
            $i = 0;
            foreach ($r as $p) {
                $data[$i]['subject_id'] = $p->subject_id;
                $data[$i]['name'] = $p->name;
                $data[$i]['max'] = "Max ( ".($this->input->post('session')=='Summer' || ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )?(  $this->input->post('dept')=='comm'?$this->getSummercommMaxNumber($p->sub_id,$this->input->post('session_year')) :$this->getSummerMaxNumber($p->sub_id,$this->input->post('session_year'))):$this->getMaxMarks($p->sub_id)) . " ) ";
                $i++;
            }
             //echo '<pre>'; print_r($data); echo '</pre>'; die();
            return $data;
        } else {
            return 0;
        }
    }
	*/
     function get_grading_styleByAdminNo($admn_no){
         $sql="select  grading_type from stu_academic  where admn_no=?";
         $query = $this->db->query($sql, $admn_no);
          if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
        }
     }


	function get_reg_crs_strct_hon($admn_no,$sem,$sessyr){
		   $sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=?";
	    $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));
		//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id);die();

		$sql2= "select * from subject_mapping x where  semester=? and x.session_year=? and session=?  and branch_id=? and course_id=?";
		$query2 = $this->db->query($sql2, array($query->row()->semester,$query->row()->session_year,$query->row()->session, $query->row()->branch_id,'honour'));
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
		  return $query2->row();

	}

	function  get_reg_crs_strct_hon_curr_yr_max($admn_no,$sem,$sessyr, $session_year,$session){
	  $sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=?";
	    $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));
		//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id);die();

		$sql2= "select * from subject_mapping x where  semester=? and x.session_year=? and session=?  and branch_id=? and course_id=?";
		$query2 = $this->db->query($sql2, array($query->row()->semester,$session_year,$session, $query->row()->branch_id,'honour'));
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
		  return $query2->row();

	}

	function get_reg_crs_strct_minor($admn_no,$sem,$sessyr){
		   $sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=? ";
	    $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));
		//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id);die();

		$sql2= "select * from subject_mapping x where  semester=? and x.session_year=? and session=?  and branch_id=? and course_id=?";
		$query2 = $this->db->query($sql2, array($query->row()->semester,$query->row()->session_year,$query->row()->session, $query->row()->branch_id,'minor'));
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
		  return $query2->row();

	}

	function  get_reg_crs_strct_minor_curr_yr_max($admn_no,$sem,$sessyr, $session_year,$session){
	 $sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=? ";
	    $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));
		//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id);die();

		$sql2= "select * from subject_mapping x where  semester=? and x.session_year=? and session=?  and branch_id=? and course_id=?";
		$query2 = $this->db->query($sql2, array($query->row()->semester,$session_year,$session, $query->row()->branch_id,'minor'));
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
		  return $query2->row();

	}



	function get_reg_crs_strct($admn_no,$sem,$sessyr){
	//	 echo $sem; die();
	    // assuming student highest marks for relative  grade will be  calculated   from current  session _year's Monsson or winter session of regular exam


		    $sql = " select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=? ";
	        $query = $this->db->query($sql, array($admn_no,$sem,$this->input->post('session_year')));
			//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id) ; echo $old_sess_yr; die();
             if(($query->row()<>null) ){
			      if($query->row()->section<>null) $addStr="  and   x.section= '".( $query->row()->section=='1'?'A':'E'  )."' " ;
		          $sql2= "select * from subject_mapping x where x.aggr_id=? and semester=? and x.session_year=? and session=?   $addStr ";
		          $query2 = $this->db->query($sql2, array($query->row()->course_aggr_id,$query->row()->semester, (($sessyr<>$this->input->post('session_year'))?$this->input->post('session_year'):$sessyr), $query->row()->session));
		          //echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
	            }
              else{
				  $arr=array('A','B','C','D');
				  if($this->input->post('section_name')<>null  && $this->input->post('section_name')<>0) {
					  $addStr="  and   x.section= '".( in_array($this->input->post('section_name'),$arr)?'A':'E'  ) /* $this->input->post('section_name')*/."' " ;
                        $crs1='comm';
						 $brnch1='comm';
						 $s= $sem;

				  }
				  else{
					  $crs1=$this->input->post('course1');
					   $brnch1=$this->input->post('branch1');
					   $s=$this->input->post('semester');

				  }

		          $sql2= "select * from subject_mapping x where  x.dept_id=? and  x.course_id=? and x.branch_id=?  and  x.semester=? and x.session_year=? and session=?   $addStr ";
		          $query2 = $this->db->query($sql2, array($this->input->post('dept'),$crs1,$brnch1,$s, $this->input->post('session_year'),($s%2==0?'Winter':'Monsoon'  )));
		//	echo $this->db->last_query(); die();

			  }



   // assuming student highest marks for relative  grade will be  calculated   from session _year when he/she has given  regular exam
	   /*$sql = " select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=? ";
	   $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));

		if(($query->row()==null)){
			$old_sess_yr=1;


    	$sql = " select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=? order by timestamp desc limit 1  ";
	    $query = $this->db->query($sql, array($admn_no,$sem));
		}

		echo $this->db->last_query();  print_r( $query->row()->course_aggr_id) ; echo $old_sess_yr;// die();

		if($query->row()->section<>null)
		        $addStr="  and   x.section= '".( $query->row()->section=='1'?'A':'E'  )."' " ;

		$sql2= "select * from subject_mapping x where x.aggr_id=? and semester=? and x.session_year=? and session=?   $addStr ";
		$query2 = $this->db->query($sql2, array($query->row()->course_aggr_id,$query->row()->semester, ($old_sess_yr==1?$query->row()->session_year:$sessyr), $query->row()->session));
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();

		// end  assuming student highest marks for relative  grade will be  calculated   from session _year when he/she has given  regular exam
	*/
		  return $query2->row();
   }


   function get_reg_crs_strct_curr_yr_max($admn_no,$sem,$sessyr, $session_year,$session,$section=null){

	   //$st_row->admn_no,$sem, $this->custom_session_yr,$curr_exam_keyword[0]->session_year,$curr_exam_keyword[0]->session
	    $sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=?  and a.session_year=? ";
	    $query = $this->db->query($sql, array($admn_no,$sem,$sessyr));

		if($query->num_rows==0){
		$sql = "select * from reg_regular_form a where a.admn_no=? and a.hod_status='1' and a.acad_status='1' and a.semester=? order by a.session_year desc limit 1 ";
	     $query = $this->db->query($sql, array($admn_no,$sem));
		}
		//echo $this->db->last_query();  print_r( $query->row()->course_aggr_id);die();
	    if($section==null) {
		$sql2= "select * from subject_mapping x where x.aggr_id=? and semester=? and x.session_year=? and session=?";
		$query2 = $this->db->query($sql2, array($query->row()->course_aggr_id,$query->row()->semester,$session_year,$session));
		}
		else{
			$sql2= "select * from subject_mapping x where x.aggr_id=? and semester=? and x.session_year=? and session=? and section=?";
		$query2 = $this->db->query($sql2, array($query->row()->course_aggr_id,$query->row()->semester,$session_year,$session,$section));
		}
		//echo $this->db->last_query();  print_r( $query2->row()->course_aggr_id);die();
		  return $query2->row();
   }


	function getSubMst($id,$sess_yr,$sess,$crs_struct=null,$grade_type='R') {
		$table=( $sess_yr>=Exam_tabulation_config::$start_project_discard_session_yr?'subjects':'subjects_old' )  ;
        $data = array();
		$add_str=" inner join marks_master mm on mm.sub_map_id=a.map_id and mm.subject_id=a.sub_id and mm.`status`='Y' ";
		if($crs_struct<>null) $str_crs_struct= " and  sm.aggr_id='".$crs_struct."'";

        $sql = " select b.subject_id, b.name,a.sub_id from
		( select smd.sub_id,sm.map_id  from subject_mapping sm inner join  subject_mapping_des smd on sm.map_id=smd.map_id and sm.session = ? and sm.session_year = ?
		and  smd.map_id=? and   smd.coordinator='1' ".$str_crs_struct."
		join  course_structure c on c.id =smd.sub_id and  c.semester=sm.semester and c.aggr_id=sm.aggr_id) as a
		inner join  $table b on a.sub_id = b.id
		".$add_str."
		group by a.sub_id
     ";



       $query = $this->db->query($sql, array($sess,$sess_yr,$id));
   //   echo $this->db->last_query(); die();

        if ($query->num_rows() > 0) {
            $r = $query->result();
            $i = 0;
            foreach ($r as $p) {
                $data[$i]['subject_id'] = $p->subject_id;
                $data[$i]['name'] = $p->name;
                if($grade_type=='R'|| $grade_type=='N')  // max concept implemneted only on relative grading only
				{


				   if(  ($this->input->post('session')<>'Summer' ) && (   $this->input->post('session_year')>=Exam_tabulation_config::$shared_relative_heighest_session_yr  )&&($this->input->post('session_year')=='2018-2019' && $this->input->post('session')<>'Monsoon' ))   {
					   if($this->is_shared($p->subject_id,$sess,$sess_yr)){

						   $shared_det=$this->get_dept_course_branch_sem_whose_max_belongto($p->subject_id,$sess,$sess_yr);
						    //echo $this->db->last_query();  die();
							//echo 'ss'.$shared_det->dept_id;die();
  					       $data[$i]['max'] = "Max* ( ".($this->input->post('session')=='Summer' || ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )?(  $this->input->post('dept')=='comm'?$this->getSummercommMaxNumber($p->sub_id,$sess_yr,$sess) :$this->getSummerMaxNumber($p->sub_id,$sess_yr)):$shared_det->maxheighest_global) . " ) From [".   ($shared_det->dept_id=='comm'?$shared_det->dept_id :  $shared_det->dept_id.'#'.$shared_det->course_id.'#'.$shared_det->branch_id).'#'.$shared_det->semester.($shared_det->section=='0'?'':'/'.$shared_det->section)." ] ";
					   }
					   else
						   $data[$i]['max'] = "Max ( ".($this->input->post('session')=='Summer' || ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )?(  $this->input->post('dept')=='comm'?$this->getSummercommMaxNumber($p->sub_id,$sess_yr,$sess) :$this->getSummerMaxNumber($p->sub_id,$sess_yr)):$this->getMaxMarks($p->sub_id,$sess,$sess_yr)) . " ) ";
				   }
				else
                  $data[$i]['max'] = "Max ( ".($this->input->post('session')=='Summer' || ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )?(  $this->input->post('dept')=='comm'?$this->getSummercommMaxNumber($p->sub_id,$sess_yr,$sess) :$this->getSummerMaxNumber($p->sub_id,$sess_yr)):$this->getMaxMarks($p->sub_id,$sess,$sess_yr)) . " ) ";
                }

				$i++;
            }
            // echo '<pre>'; print_r($data); echo '</pre>'; die();
            return $data;
        } else {
            return null;
        }
    }


 function cbcs_sub_list_semwise($session_year,$session,$dept,$course,$branch,$sem){
 //echo $this->input->post('section_name'); die();
	  if($dept<>null)$append1="  and  b.dept_id='$dept' "; else $append1="";

		  if($course<>null)$append2="  and  lower(b.course_id)='".strtolower($course)."' "; else $append2="" ;
		  if($branch<>null)$append3="  and  lower(b.branch_id)='".($dept=='comm'?comm:strtolower($branch))."' "; else $append3="" ;
		  if($sem<>null && $session<>'Summer' )$append4="  and  b.semester='$sem' "; else $append4="" ;
		   //echo  $this->input->post('section_name'); die();

                   $arr=array('A','B','C','D');
				    if($this->input->post('section_name')<>null  && $dept=='comm')
					  $addStr="  and   b.sub_group= '".( in_array($this->input->post('section_name'),$arr)?'1':'2'  ) 		  ."'";
                    else $addStr="";

		/*   $sql="
			SELECT  c.subject_code as subject_id,  c.sub_name as name  FROM
				(SELECT v.course,v.branch,v.subject_code,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc
				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?  $append2 $append3  $append4 )
				 UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND a.session_year=?   $append2 $append3 $append4))v GROUP BY v.course,v.branch ,v.subject_code)c


				 order by c.subject_code ";

				 */

				 $sql="


SELECT c.sub_code AS subject_id, c.sub_name AS name
FROM (
SELECT v.course_id,v.branch_id,v.sub_code,v.dept_id, v.sub_name,v.sub_type,v.resrc
FROM((
SELECT 'new' AS resrc, b.sub_code,b.course_id,b.branch_id,b.dept_id,b.sub_name,b.sub_type
FROM
 cbcs_subject_offered b
WHERE b.`session`=? AND b.session_year=? $append1 $append2 $append3  $append4  $addStr ) UNION (
SELECT 'old' AS resrc, b.sub_code,b.course_id,b.branch_id,b.dept_id,b.sub_name,b.sub_type
FROM old_subject_offered b
WHERE b.`session`=? AND b.session_year=?  $append1 $append2 $append3  $append4 ))v
GROUP BY v.course_id,v.branch_id,v.sub_code)c
ORDER BY c.sub_code

		 ";




			   $query = $this->db->query($sql,array($session,$session_year,$session,$session_year,$session,$session_year));
			    //echo $this->db->last_query(); die();

			  //  echo $sql;die();
		 //echo $query->num_rows();
               if ($query->num_rows() > 0) {
            $r = $query->result();
            $i = 0;
            foreach ($r as $p) {
                $data[$i]['subject_id'] = $p->subject_id;
                $data[$i]['name'] = $p->name;


				$i++;
            }
            // echo '<pre>'; print_r($data); echo '</pre>'; die();
            return $data;
        } else {
            return null;
        }

 }



function cbcs_getSubMst($id,$session_year,$session,$dept,$course,$branch,$sem) {
		  $data = array();
          if($dept<>null)//$append1="  and    b.dept_id='$dept' "; else $append1="";
		   $append1="  and   lower(b.dept_id)=   (case when  lower(b.course_id)='comm'  then 'comm'  else  '$dept'  end ) ";  else $append1="";
		  if($course<>null)
			  //$append2="  and  b.course_id='$course' "; else $append2="" ;
		   $append2="   AND     lower(b.course_id)=   (case when  lower(b.course_id)='comm'  then 'comm'  else lower('$course') end ) "; else $append2="" ;


		  if($branch<>null)//$append3="  and  b.branch_id='$branch' "; else $append3="" ;
		  $append3=" AND     b.branch_id=   (case when  lower(b.course_id)='comm'  then 'comm'  else '$branch' end ) "; else $append3="" ;

		  if($sem<>null  )$append4="  and     b.semester!='$sem' "; else $append4="" ;
           //$txt_Sess= ($session=='Summer'?"":" a.`session`='$session' AND ");
		   

          $sql="
			    SELECT  c.subject_code as subject_id,  c.sub_name as name  FROM
				(SELECT v.course,v.branch,v.subject_code,v.dept_id AS std_dept, v.sub_name,v.sub_type,v.resrc
				FROM((
				SELECT  'new' AS  resrc  , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM cbcs_stu_course a
				INNER JOIN cbcs_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND  a.session_year<=?  and a.admn_no=? $append1 $append2 $append3  $append4 )
				UNION (
				SELECT 'old' AS  resrc   , a.id,a.form_id,a.admn_no, CONCAT('c',a.sub_offered_id) AS sub_offered_id, a.subject_code,a.course,a.branch,b.dept_id,b.sub_name,b.sub_type
				FROM old_stu_course a
				INNER JOIN old_subject_offered b ON a.sub_offered_id=b.id
				WHERE a.`session`=? AND  a.session_year<=?   and a.admn_no=?  $append1 $append2 $append3 $append4))v GROUP BY v.course,v.branch ,v.subject_code)c


				 order by c.subject_code ";

       $query = $this->db->query($sql, array($session,$session_year,$id,$session,$session_year,$id));
    //  echo $this->db->last_query(); die();

       if ($query->num_rows() > 0) {
            $r = $query->result();
            $i = 0;
            foreach ($r as $p) {
                $data[$i]['subject_id'] = $p->subject_id;
                $data[$i]['name'] = $p->name;


				$i++;
            }
            // echo '<pre>'; print_r($data); echo '</pre>'; die();
            return $data;
        } else {
            return null;
        }
    }








    function totalCrbyId($id,$sess,$sem=null) {
		$table=  in_array(strtolower($admn_no),Exam_tabulation_config::$start_project_discard_exception)? 'subjects_old' :( $this->input->post('session_year')>=Exam_tabulation_config::$start_project_discard_session_yr?'subjects':'subjects_old' )  ;
        /*              $sql="
          select sum(b.credit_hours) as `total_cr` from  subject_mapping_des as a  inner join  subjects b on a.sub_id = b.id  where  a.map_id=?
          ";
         */ $sql = "select sum(A.credit_hours) as total_cr from (select c.* from subject_mapping as a
        inner join course_structure as b on a.aggr_id=b.aggr_id and
        (case
               when (a.aggr_id  like 'comm_%' ) then   CONCAT_WS('_',a.semester,a.`group`)= b.semester
               ELSE  a.semester=b.semester
        end)

inner join $table as c on b.id=c.id
where a.map_id=? and a.`session`=? and a.session_year=?
group by floor(b.sequence)) A";

        $query = $this->db->query($sql, array($id, ($this->input->post('session')=='Summer' || ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' )?((($sem%2)<>0?'Monsoon':'Winter')):$this->input->post('session')), $sess));
       //  echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->total_cr;
        else {
            return null;
        }
    }

    function getOGPA($admn_no, $sem) {
        $sql = "select ogpa,passfail,examtype from resultdata where admn_no=? and RIGHT(sem_code,1)=? order by passfail desc limit 1";
        $query = $this->db->query($sql, array($admn_no, ($sem - 1)));
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getMaxMarks($id,$sess=null,$sess_yr=null) {
        $sql = "select (case when max(b.total) is null then '0' else max(b.total) end) /*a.highest_marks*/  as total from marks_master as a
join  marks_subject_description as b on a.id=b.marks_master_id
where a.subject_id=?   and a.session_year=?  and a.session=?  and a.status='Y'  and a.`type`='R' ";

        $query = $this->db->query($sql, array($id,$sess_yr,$sess));
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->total;
        else {
            return 0;
        }
    }

	 //shared subject identification
	 function is_shared($sub_code,$sess=null,$sess_yr=null) {

    $sql=" select  a.id, a.`session`,a.session_year,a.subject_id, s.subject_id as sub_code
         from
         marks_master a
			inner join  subjects s on a.subject_id=s.id
		   AND s.subject_id=?   and a.session_year=?  and a.session=?  and a.status='Y'  and a.`type`='R'";


        $query = $this->db->query($sql, array($sub_code,$sess_yr,$sess));
        // echo $this->db->last_query(); die();

        if ($query->num_rows() > 1)
            return true;
        else {
            return 0;
        }
    }

		   // getting shared heighest & local heighest and corresponding dept_course_branch_sem_whose_max_belongto
	function get_dept_course_branch_sem_whose_max_belongto($sub_code,$sess=null,$sess_yr=null){




$sql="  select y.*, sm.dept_id,sm.course_id,sm.branch_id,sm.semester,sm.section from
		     ( select  x.sub_map_id, x.`session`,x.session_year, x.sub_code,x.highest_marks as maxheighest_global,
			(case when max(b.total) is null then '0' else max(b.total) end) as maxheighest_local


			from
		   (select  a.id,a.sub_map_id, a.`session`,a.session_year, s.subject_id as sub_code,a.highest_marks
         from
         marks_master a
			inner join  subjects s on a.subject_id=s.id
		   AND s.subject_id=? AND a.session_year=? AND a.session=? AND a.status='Y' AND a.`type`='R')x

		   inner join  marks_subject_description as b on x.id=b.marks_master_id  group by b.marks_master_id)y
		   inner join   subject_mapping sm on sm.map_id=y.sub_map_id  order by 0+maxheighest_local desc limit 1 ";

	     $query = $this->db->query($sql, array($sub_code,$sess_yr,$sess));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
        }

	}















	function get_dept_whose_max_belongto_jrf($sub_code,$sess=null,$sess_yr=null){
		$sql="
select u.*,ud.dept_id from (


SELECT /*(CASE WHEN MAX(b.total) IS NULL THEN '0' ELSE MAX(b.total) END) AS total,*/ a.id,s.name,b.id as desc_id,b.total,a.highest_marks,s.subject_id,
b.admn_no
FROM marks_master AS a
JOIN marks_subject_description AS b ON a.id=b.marks_master_id
inner join  subjects s on a.subject_id=s.id
WHERE s.subject_id=? AND a.session_year=? AND a.session=? AND a.status='Y' AND a.`type`='J'  order by b.total desc limit 1

)u

join  user_details ud on ud.id=u.admn_no";
	 $query = $this->db->query($sql, array($sub_code,$sess_yr,$sess));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->dept_id;
        else {
            return 0;
        }

	}

	   function getMaxMarks_JRF($id,$sess=null,$sess_yr=null) {
        $sql = "select a.highest_marks as total from marks_master as a
where a.subject_id=?   and a.session_year=?  and a.session=?  and a.status='Y'  and a.`type`='J' ";

        $query = $this->db->query($sql, array($id,$sess_yr,$sess));
         //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->total;
        else {
            return 0;
        }
    }



function insert_batch_des_freeze($data) {
        $this->db->insert_batch('final_semwise_marks_foil_desc_freezed', $data);
    }

    function insert_batch_des($data) {
        $this->db->insert_batch('final_semwise_marks_foil_desc', $data);
    }

	function insert_batch_des_alumni($data) {
        $this->db->insert_batch('alumni_final_semwise_marks_foil_desc', $data);
    }


	function insert_batch_des1($data) {
        $this->db->insert_batch('final_semwise_marks_foil_desc_change_br', $data);
    }

    function update_des($data) {
        $this->db->update('final_semwise_marks_foil_desc', $data);
    }

	function update_final_status_spl($admn_no,$branch_id, $course_id, $sem,$f_status,$cgpa, $core_cgpa,$ccrpts, $ccrdthr, $core_ccrpts, $core_ccrdthr){
		 $returntmsg = "";

        $this->db->trans_start();
        $data['final_status'] =  $f_status;


		// echo  number_format($cgpa, 5, '.', ''); die();
		   $select = $this->db->select('*')->where(
                       array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        )
                )->get('final_semwise_marks_foil');

				 if ($select->num_rows()) {
            $row = $select->row();
			if (($row->cgpa==0 || ($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) ||($row->cgpa != floor( $cgpa * 100000) / 100000))&& ($f_status==''|| $f_status==null) ) {
              $data['cgpa'] =  $cgpa ;
			  $data['ctotcrhr']=$ccrdthr;
			  $data['ctotcrpts']=$ccrpts;
        }
		if (($row->core_cgpa==0 || ($row->core_ctotcrpts != $core_ccrpts)|| ( $row->core_ctotcrhr != $core_ccrdthr) ||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000)) && ($f_status==''|| $f_status==null) ) {
              $data['core_cgpa'] = $core_cgpa;
			  $data['core_ctotcrhr']=$core_ccrdthr;
			  $data['core_ctotcrpts']=$core_ccrpts;
        }






          //   echo $this->db->last_query();
           //  echo '<pre>';  print_r($data);  echo '</pre>'; echo  'foil_id='.$row->id 	;	die();
                  $this->db->update('final_semwise_marks_foil', $data, array('id' => $row->id));

				//  echo $this->db->last_query(); die();
        }

		 if(Exam_tabulation_config::$mask_write_validation_freeze)  {
		 $this->db->select('*');
         $this->db->where( array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        ));
    $this->db->from('final_semwise_marks_foil_freezed');
    $this->db->order_by("actual_published_on", "desc");
     $this->db->limit('1');
	 $select2  = $this->db->get();
	 if ($select2->num_rows()) {
            $row = $select2->row();
			if (( $row->cgpa==0 || ($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) ||($row->cgpa != floor( $cgpa * 100000) / 100000) )  && ($f_status==''|| $f_status==null) ) {
              $data2['cgpa'] =  $cgpa ;
			   $data2['ctotcrhr']=$ccrdthr;
			  $data2['ctotcrpts']=$ccrpts;
        }
		if (($row->core_cgpa==0 || ($row->core_ctotcrpts != $core_ccrpts)|| ( $row->core_ctotcrhr != $core_ccrdthr) ||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000) )&& ($f_status==''|| $f_status==null) ) {
              $data2['core_cgpa'] = $core_cgpa;
			   $data2['core_ctotcrhr']=$core_ccrdthr;
			  $data2['core_ctotcrpts']=$core_ccrpts;
        }

            // echo $this->db->last_query();
             //echo '<pre>';  print_r($data2);  echo '</pre>'; echo  'foil_id='.$row->id .'old_id'.$row->old_id	;	die();

				  $this->db->update('final_semwise_marks_foil_freezed', $data2, array('id' => $row->id,'old_id' => $row->old_id));
				//  echo $this->db->last_query(); die();
        }
		}



		  $this->db->trans_complete();
        if ($this->db->trans_status() != FALSE)
            $returntmsg = "success";
        else
		   $returntmsg .= "Error while Inserting/updating: " . $this->db->_error_message() . ",";

		 //echo $returntmsg; die();
            return $returntmsg;

	}



	 function save_excel_output_spl_change_br($unmatched, $admn_no, $h_status, $branch_id, $course_id, $sem, $sum_totcrdthr, $sum_totcrdpts_final, $sum_core_totcrdthr, $sum_core_totcrdpts_final, $ccrpts, $ccrdthr, $core_ccrpts, $core_ccrdthr, $gpa, $core_gpa, $cgpa, $core_cgpa, $status, $core_status, $exm_type, $repeator,$f_status=null) {
         //echo $gpa .','. $core_gpa.','. $cgpa.','. $core_cgpa; die();
        date_default_timezone_set("Asia/Calcutta");
        $returntmsg = "";
        $j = 0;
        $this->db->trans_start();
        $data = array('session_yr' => $this->input->post('session_year'),
            'session' => $this->input->post('session'),
            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
            'course' => $course_id,
            'branch' => $branch_id,
            'semester' => $sem,
            'admn_no' => $admn_no,
            'tot_cr_hr' => $sum_totcrdthr,
            'tot_cr_pts' => $sum_totcrdpts_final,
            'core_tot_cr_hr' => $sum_core_totcrdthr,
            'core_tot_cr_pts' => $sum_core_totcrdpts_final,
            'ctotcrpts' => $ccrpts,
            'ctotcrhr' => $ccrdthr,
            'core_ctotcrpts' => $core_ccrpts,
            'core_ctotcrhr' => $core_ccrdthr,
            'gpa' => $gpa,
            'core_gpa' => $core_gpa,
            'cgpa' => $cgpa,
            'core_cgpa' => $core_cgpa,
            'status' => $status,
            'core_status' => $core_status,
            'hstatus' => $h_status,
            'type' => ($this->input->post('exm_type') == 'other' ? 'O' :
                    ($this->input->post('exm_type') == 'spl' ? 'S' :
                            ( $this->input->post('exm_type') == 'jrf' ? 'J' :
                                    ($this->input->post('exm_type') == 'espl' ? 'E' :
                                            'R')
                            )
                    )
            ),
            'exam_type' => $exm_type,
            'final_status' => $f_status

        );

        if ($this->input->post('exm_tpye') == 'regular') {
            $data[] = array('repeator' => $repeator);
        }
        $select = $this->db->select('*')->where(
                       array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        )
                )->get('final_semwise_marks_foil_change_br');
      //  echo $this->db->last_query(); die();
        if ($select->num_rows()) {
            $row = $select->row();
             //echo $this->db->last_query(); die();
             //echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
            if (  (($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa !=$gpa)) || ($row->core_gpa != $core_gpa) */||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000)/* ($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/||     ($row->core_status != $core_status)|| ($row->status != $status) )|| Exam_tabulation_config::$mask_write_validation  ) {
 //echo $xx=(Boolean)(($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa !=$gpa)) || ($row->core_gpa != $core_gpa) */||(abs($row->cgpa != $cgpa)>0.0001)||(abs($row->core_cgpa != $core_cgpa)>0.0001)/* ($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/||     ($row->core_status != $core_status)|| ($row->status != $status) );
		//	echo $row->ctotcrpts .'!='. $ccrpts .') || ('. $row->ctotcrhr .'!='. $ccrdthr.') || ('.$row->gpa .'!='. $gpa.') || ('.$row->core_gpa .'!='. $core_gpa.') || ('.abs($cgpa-$row->cgpa) .'!='.(floor( $cgpa * 100000) / 100000) .'='.$row->cgpa.')||  ('.abs($core_cgpa-$row->core_cgpa) .'!='.(floor( $core_cgpa * 100000) / 100000).'='.$row->core_cgpa.') /*($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ('.$row->core_status .'!='. $core_status.')|| ('.$row->status .'!='. $status;				die();
			 // echo '<pre>';  print_r($data);  echo '</pre>'; 	//	die();
				if(Exam_tabulation_config::$mask_update_foil_master_reg)
                  $this->db->update('final_semwise_marks_foil_change_br', $data, array('id' => $row->id));
                  //echo $this->db->last_query(); die();
                  if ((($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa != $gpa) || ($row->core_gpa !=$core_gpa)*/||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000) /*($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ($row->core_status != $core_status)|| ($row->status != $status) ) || Exam_tabulation_config::$mask_write_validation   ) {
                    while ($j < count($unmatched)) {
                        $unmatched[$j++]['foil_id'] = $row->id;
                    }// echo '<pre>';print_r($unmatched); echo '</pre>';  die();
                     $this->db->delete('final_semwise_marks_foil_desc_change_br', array('foil_id' => $row->id));
                    $this->insert_batch_des1($unmatched);
                }
            }
  //  echo $row->ctotcrpts .'!='. $ccrpts .') || ('. $row->ctotcrhr .'!='. $ccrdthr.') || ('.$row->gpa .'!='. $gpa.') || ('.$row->core_gpa .'!='. $core_gpa.') || ('.$row->cgpa .'!='.number_format($cgpa, 5, '.', '') .')|| ('.$row->core_cgpa .'!='. number_format($core_cgpa, 5, '.', '').') /*($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ('.$row->core_status .'!='. $core_status.')|| ('.$row->status .'!='. $status;				die();
        } else {
              $this->db->insert('final_semwise_marks_foil_change_br', $data);
			  //echo '<pre>';print_r($data); echo '</pre>'; die();
            $j = 0;
            $last_insert_id = $this->db->insert_id();
            if ($last_insert_id) {
                while ($j < count($unmatched)) {
                    $unmatched[$j]['foil_id'] = $last_insert_id;
                    $j++;
                }   //echo '<pre>';print_r($unmatched); echo '</pre>';                                         die();
                $this->insert_batch_des1($unmatched);
            } //echo '<pre>';print_r($unmatched); echo '</pre>';    die();
        }





        $this->db->trans_complete();
        if ($this->db->trans_status() != FALSE)
            $returntmsg = "success";
        else
		   $returntmsg .= "Error while Inserting/updating: " . $this->db->_error_message() . ",";

		 //echo $returntmsg; die();
            return $returntmsg;
    }


    function save_excel_output_spl($unmatched, $admn_no, $h_status, $branch_id, $course_id, $sem, $sum_totcrdthr, $sum_totcrdpts_final, $sum_core_totcrdthr, $sum_core_totcrdpts_final, $ccrpts, $ccrdthr, $core_ccrpts, $core_ccrdthr, $gpa, $core_gpa, $cgpa, $core_cgpa, $status, $core_status, $exm_type, $repeator,$f_status=null) {
         //echo $gpa .','. $core_gpa.','. $cgpa.','. $core_cgpa; die();
		 
		 //echo  strlen($gpa)>7; die();
		 
        date_default_timezone_set("Asia/Calcutta");
        $returntmsg = "";
        $j = 0;
		$affected=null;
			$alumni=0;
        $this->db->trans_start();
        $data = array('session_yr' => $this->input->post('session_year'),
            'session' => $this->input->post('session'),
            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
            'course' => $course_id,
            'branch' => $branch_id,
            'semester' => $sem,
            'admn_no' => $admn_no,
            'tot_cr_hr' => $sum_totcrdthr,
            'tot_cr_pts' => $sum_totcrdpts_final,
            'core_tot_cr_hr' => $sum_core_totcrdthr,
            'core_tot_cr_pts' => $sum_core_totcrdpts_final,
            'ctotcrpts' => $ccrpts,
            'ctotcrhr' => $ccrdthr,
            'core_ctotcrpts' => $core_ccrpts,
            'core_ctotcrhr' => $core_ccrdthr,
            'gpa' => ($gpa==null?0:$gpa),
            'core_gpa' => ($core_gpa==null?0:$core_gpa),
            'cgpa' => ($cgpa==''?null:$cgpa),
            'core_cgpa' => ($core_cgpa==''?null:$core_cgpa),
            'status' => $status,
            'core_status' => $core_status,
            'hstatus' => $h_status,
            'type' => ($this->input->post('exm_type') == 'other' ? 'O' :
                    ($this->input->post('exm_type') == 'spl' ? 'S' :
                            ( $this->input->post('exm_type') == 'jrf' ? 'J' :
                                    ($this->input->post('exm_type') == 'espl' ? 'E' :
                                            'R')
                            )
                    )
            ),
            'exam_type' => $exm_type,
            'final_status' => $f_status

        );

        if ($this->input->post('exm_tpye') == 'regular') {
            $data[] = array('repeator' => $repeator);
        }
        $select = $this->db->select('*')->where(
                       array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        )
                )->get('final_semwise_marks_foil');
        if (!($select->num_rows()>0)){
			$alumni=1;
			 $select = $this->db->select('*')->where(
                       array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        )
                )->get('alumni_final_semwise_marks_foil');
		}


        //echo $this->db->last_query(); echo 'alumni'.$alumni;  die();
        if ($select->num_rows()) {
            $row = $select->row();
             //echo $this->db->last_query(); die();
           //echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
            if (  (($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) ||
                  ($row->tot_cr_hr != $sum_totcrdthr) ||($row->tot_cr_pts != $sum_totcrdpts_final)||
				  ($row->core_tot_cr_hr != $sum_core_totcrdthr) ||($row->core_tot_cr_pts != $sum_core_totcrdpts_final)||           
			(abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa !=$gpa)) || ($row->core_gpa != $core_gpa) */||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000)/* ($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/||     ($row->core_status != $core_status)|| ($row->status != $status) )|| Exam_tabulation_config::$mask_write_validation  ) {
 //echo $xx=(Boolean)(($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa !=$gpa)) || ($row->core_gpa != $core_gpa) */||(abs($row->cgpa != $cgpa)>0.0001)||(abs($row->core_cgpa != $core_cgpa)>0.0001)/* ($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/||     ($row->core_status != $core_status)|| ($row->status != $status) );
		//	echo $row->ctotcrpts .'!='. $ccrpts .') || ('. $row->ctotcrhr .'!='. $ccrdthr.') || ('.$row->gpa .'!='. $gpa.') || ('.$row->core_gpa .'!='. $core_gpa.') || ('.abs($cgpa-$row->cgpa) .'!='.(floor( $cgpa * 100000) / 100000) .'='.$row->cgpa.')||  ('.abs($core_cgpa-$row->core_cgpa) .'!='.(floor( $core_cgpa * 100000) / 100000).'='.$row->core_cgpa.') /*($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ('.$row->core_status .'!='. $core_status.')|| ('.$row->status .'!='. $status;				die();
			 // echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
				if(Exam_tabulation_config::$mask_update_foil_master_reg){
                  $this->db->update('final_semwise_marks_foil', $data, array('id' => $row->id));
				  $returntmsg.= $this->db->_error_message();				  
				  $affected[]=$this->db->affected_rows();
				//  echo $this->db->last_query(); 
				//   echo $returntmsg;
				  
				  if($alumni==1)
			     $this->db->update('alumni_final_semwise_marks_foil', $data, array('id' => $row->id));
				}
                  //echo $this->db->last_query(); die();
                  if ((($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || 
				   ($row->tot_cr_hr != $sum_totcrdthr) ||($row->tot_cr_pts != $sum_totcrdpts_final)||
				  ($row->core_tot_cr_hr != $sum_core_totcrdthr) ||($row->core_tot_cr_pts != $sum_core_totcrdpts_final)||    
				  (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa != $gpa) || ($row->core_gpa !=$core_gpa)*/||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000) /*($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ($row->core_status != $core_status)|| ($row->status != $status) ) || Exam_tabulation_config::$mask_write_validation   ) {
                    while ($j < count($unmatched)) {
                        $unmatched[$j++]['foil_id'] = $row->id;
                    } //echo '<pre>';print_r($unmatched); echo '</pre>';  die();
                     $this->db->delete('final_semwise_marks_foil_desc', array('foil_id' => $row->id));
					 $returntmsg.= $this->db->_error_message();
				      $affected[]=$this->db->affected_rows();
					  if($alumni==1)
				      $this->db->delete('alumni_final_semwise_marks_foil_desc', array('foil_id' => $row->id));
                       $this->insert_batch_des($unmatched);
					//   echo $this->db->last_query(); die();
					    if($alumni==1)
					    $this->insert_batch_des_alumni($unmatched);
                }
            }
  //  echo $row->ctotcrpts .'!='. $ccrpts .') || ('. $row->ctotcrhr .'!='. $ccrdthr.') || ('.$row->gpa .'!='. $gpa.') || ('.$row->core_gpa .'!='. $core_gpa.') || ('.$row->cgpa .'!='.number_format($cgpa, 5, '.', '') .')|| ('.$row->core_cgpa .'!='. number_format($core_cgpa, 5, '.', '').') /*($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/|| ('.$row->core_status .'!='. $core_status.')|| ('.$row->status .'!='. $status;				die();
        } else {
              $this->db->insert('final_semwise_marks_foil', $data);
			  //echo '<pre>';print_r($data); echo '</pre>'; die();
            $j = 0;
            $last_insert_id = $this->db->insert_id();
            if ($last_insert_id) {
                while ($j < count($unmatched)) {
                    $unmatched[$j]['foil_id'] = $last_insert_id;
                    $j++;
                }   //echo '<pre>';print_r($unmatched); echo '</pre>';                                         die();
                $this->insert_batch_des($unmatched);
            } //echo '<pre>';print_r($unmatched); echo '</pre>';    die();
        }

		// update for freeze table
		 if(Exam_tabulation_config::$mask_write_validation_freeze)  {
		$select=null;$row=null;
		 $this->db->select('*');
         $this->db->where(  array('session_yr' => $this->input->post('session_year'),
                            'session' => $this->input->post('session'),
                            'dept' => ($course_id=='PREP'? 'PREP': $this->input->post('dept')),
                            'course' => $course_id,
                            'branch' => $branch_id,
                            'semester' => $sem,
                            'admn_no' => $admn_no,
                            'type' => ($this->input->post('exm_type') == 'other' ? 'O' : ($this->input->post('exm_type') == 'spl' ? 'S' : ($this->input->post('exm_type') == 'jrf' ? 'J' : ($this->input->post('exm_type') == 'espl' ? 'E' : 'R')) ) )
                        ));
    $this->db->from('final_semwise_marks_foil_freezed');
    $this->db->order_by("actual_published_on", "desc");
    $this->db->limit('1');
	$select  = $this->db->get();
			//echo $this->db->last_query();	die();
        if ($select->num_rows()) {
            $row = $select->row();
             //echo $this->db->last_query();
            // echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
            if (($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) ||
			 ($row->tot_cr_hr != $sum_totcrdthr) ||($row->tot_cr_pts != $sum_totcrdpts_final)||
				  ($row->core_tot_cr_hr != $sum_core_totcrdthr) ||($row->core_tot_cr_pts != $sum_core_totcrdpts_final)||   
			(abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa != $gpa) || ($row->core_gpa != $core_gpa)*/ ||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000)/*  ($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/ || Exam_tabulation_config::$mask_write_validation  ) {
                //echo '<pre>';  print_r($data);  echo '</pre>'; 		die();
				if(Exam_tabulation_config::$mask_update_foil_master_reg)
                  $this->db->update('final_semwise_marks_foil_freezed', $data, array('id' => $row->id));
                  //echo $this->db->last_query(); die();
                  if (($row->ctotcrpts != $ccrpts)|| ( $row->ctotcrhr != $ccrdthr) || 
				   ($row->tot_cr_hr != $sum_totcrdthr) ||($row->tot_cr_pts != $sum_totcrdpts_final)||
				  ($row->core_tot_cr_hr != $sum_core_totcrdthr) ||($row->core_tot_cr_pts != $sum_core_totcrdpts_final)||    
				  (abs($gpa-$row->gpa)>0.0001)||(abs($core_gpa-$row->core_gpa)>0.0001)/*($row->gpa != $gpa) || ($row->core_gpa != $core_gpa)*/ ||($row->cgpa != floor( $cgpa * 100000) / 100000)||($row->core_cgpa != floor( $core_cgpa * 100000) / 100000)   /*($row->cgpa !=number_format($cgpa, 5, '.', '') )|| ($row->core_cgpa != number_format($core_cgpa, 5, '.', '')) ($row->cgpa != $cgpa)|| ($row->core_cgpa != $core_cgpa)*/  || Exam_tabulation_config::$mask_write_validation   ) {
                     //echo count($unmatched); die();
					   $j = 0;
					while ($j < count($unmatched)) {
                          $unmatched[$j]['foil_id'] = $row->id;
						  $unmatched[$j++]['old_foil_id'] = $row->old_id;
                    } //echo '<pre>';print_r($unmatched); echo '</pre>';  die();
                     if($this->db->delete('final_semwise_marks_foil_desc_freezed', array('foil_id' => $row->id))){
					 $this->insert_batch_des_freeze($unmatched);
					 }

					//echo $this->db->last_query(); die();
                }
            }
        }else{
			// assuming  record present from before  because of result  already declared, so nohting to insert
		  }

		 }


        $this->db->trans_complete();
		
		//print_r($affected); echo $returntmsg;  die();
        if ($this->db->trans_status() != FALSE)
            $returntmsg = "success";
        else
		   $returntmsg .= "Error while Inserting/updating: " . $this->db->_error_message() . ",";

		 //echo $returntmsg; die();
            return $returntmsg;
    }

    function get_semesterList_of_registration($branch, $admno, $data, $sem) {
        $sem_string = "";
        $reg_data_array = array('session_year' => $this->input->post('session_year'),
            'session' => $this->input->post('session'),
            'course_id' => $data['course_id'],
            'branch_id' => $branch,
            'semester' => $sem,
            'admn_no' => $admno
        );

        $other_data_array = array('session_year' => $this->input->post('session_year'),
            'session' => $this->input->post('session'),
            'course_id' => $data['course_id'],
            'branch_id' => $branch,
            'semester' => $sem,
            'admn_no' => $admno,
            'type' => 'O'
        );
        $spl_data_array = array('session_year' => $this->input->post('session_year'),
            'session' => $this->input->post('session'),
            'course_id' => $data['course_id'],
            'branch_id' => $branch,
            'semester' => $sem,
            'admn_no' => $admno,
            'type' => 'S'
        );
        $select_special_sem = $this->db->select('semester')->where($spl_data_array)->get('reg_exam_rc_form');

        //print_r($select_special_sem); die();
        if ($select_special_sem->num_rows()) {
            $select_special_sem = $select_special_sem->row();
            if (substr_count($select_special_sem->semester, ',') > 0)
                $sem_string = "'" . implode("','", explode(',', $select_special_sem->semester)) . "'";
            else
                $sem_string = ($sem_string == "" ? $select_special_sem->semester : ("','" . $select_special_sem->semester));
        }
        $select_other_sem = $this->db->select('semester')->where($other_data_array)->get('reg_exam_rc_form');
        if ($select_other_sem->num_rows()) {
            $select_other_sem = $select_other_sem->row();

            if (substr_count($select_other_sem->semester, ',') > 0)
                $sem_string = "'" . implode("','", explode(',', $select_other_sem->semester)) . "'";
            else
                $sem_string = ($sem_string == "" ? $select_other_sem->semester : ("','" . $select_other_sem->semester));
        }
        $select_regular_sem = $this->db->select('semester')->where($reg_data_array)->get('reg_regular_form');
        if ($select_regular_sem->num_rows()) {
            $select_regular_sem = $select_regular_sem->row();
            $sem_string = ($sem_string == "" ? $select_regular_sem->semester : ("','" . $select_regular_sem->semester));
        }

        return $sem_string;
    }


     function cumm_OGPA_status_exlude_sem($admn_no, $h_status,$sem) {

            $s_replace = "  and a.semester <>'" . $sem . "' ";
            $s_replace_old = "  and right(a.sem_code,1) <>'" . $sem . "' ";

        //echo  $s_replace .'<br/>' ;

    if ($h_status == 'Y')
            $status = "status";
        else
            $status = "core_status";

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1') " . $s_replace . "
GROUP BY a.session_yr,a.session,a.semester,a.type

ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all(
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%'  " . $s_replace_old . "
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";


        $query = $this->db->query($sql, array($admn_no, $admn_no));
         // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {
           $sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM alumni_final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1') " . $s_replace . "
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all(
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM alumni_tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%'  " . $s_replace_old . "
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";


        $query = $this->db->query($sql, array($admn_no, $admn_no));
         // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0) return $query->row();else return 0;

        }
    }

	static function cumm_OGPA_status_exlude_sem_static($admn_no, $h_status,$sem) {

          //  $s_replace = "  and a.semester <>'" . $sem . "' ";
            //$s_replace_old = "  and right(a.sem_code,1) <>'" . $sem . "' ";

        //echo  $s_replace .'<br/>' ;



    if ($h_status == 'Y')
            $status = "status";
        else
            $status = "core_status";

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1') " . $s_replace . "
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all (
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%'  " . $s_replace_old . "
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";


        //$query = $this->db->query($sql, array($admn_no, $admn_no));
		$query = self::$db->query($sql, array($admn_no, $admn_no));
         // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM alumni_final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1') " . $s_replace . "
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all (
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM alumni_tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%'  " . $s_replace_old . "
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";


        //$query = $this->db->query($sql, array($admn_no, $admn_no));
		$query = self::$db->query($sql, array($admn_no, $admn_no));
         // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)return $query->row(); else return 0;
        }
    }

	  function check_minor_pass_fail($admn_no, $sem, $type = 'MINOR') {
        $lst = '';
		  if($sem<>null){
        for ($i = $sem; $i >= 5; $i--) {
            $lst.=$i . ($i == 5 ? "" : ",");
        }
        //echo  $lst ; die();
        if (substr_count($lst, ',') > 0) {
            $s_replace = " and a.semester in (" . $lst . ")";
        } else
            $s_replace = "  and a.semester ='" . $lst . "' ";
        //echo  $s_replace .'<br/>' ;
		  }
        $sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC(Minor)'), NULL) SEPARATOR ', ') AS incstr
FROM (

select z.* from(
			(
			SELECT B.*
			FROM (
			SELECT a.status AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem,a.session_yr,a.`session`,a.course
			FROM final_semwise_marks_foil a
			WHERE a.admn_no=? and  a.course=? " . $s_replace . "
			GROUP BY a.session_yr,a.`session`,a.semester,a.type
			ORDER BY a.session_yr desc ,a.semester DESC, a.tot_cr_pts DESC)B
			GROUP BY B.sem)

			)z group by z.sem )x

         ";



        $query = $this->db->query($sql, array($admn_no, $type));
        // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {
            $sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC(Minor)'), NULL) SEPARATOR ', ') AS incstr
FROM (

select z.* from(
			(
			SELECT B.*
			FROM (
			SELECT a.status AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem,a.session_yr,a.`session`,a.course
			FROM alumni_final_semwise_marks_foil a
			WHERE a.admn_no=? and  a.course=? " . $s_replace . "
			GROUP BY a.session_yr,a.`session`,a.semester,a.type
			ORDER BY a.session_yr desc ,a.semester DESC, a.tot_cr_pts DESC)B
			GROUP BY B.sem)

			)z group by z.sem )x

         ";



        $query = $this->db->query($sql, array($admn_no, $type));
        // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)return $query->row(); else return 0;

        }
    }


    function cumm_OGPA_status($admn_no, $h_status,$sem=null) {
		   $lst = '';$lst_old = '';
		  if($sem<>null){
        for ($i = $sem; $i >= 1; $i--) {
            $lst.=$i . ($i == 1 ? "" : ",");
            $lst_old.= ($i == 10?"'X'":$i) . ($i == 1 ? "" : ",");
        }
        //echo  $lst ; die();
        if (substr_count($lst, ',') > 0) {
            $s_replace = " and a.semester in (" . $lst . ")";
            $s_replace_old = " and right(a.sem_code,1) in (" . $lst_old . ")";
        } else {
            $s_replace = "  and a.semester ='" . $lst . "' ";
            $s_replace_old = "  and right(a.sem_code,1) ='" . $lst_old . "' ";
        }
		  }

        if ($h_status == 'Y')
            $status = "status";
        else
            $status = "core_status";

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status,
       GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr,
	   GROUP_CONCAT(IF((TRIM(x.passfail)='P' OR TRIM(x.passfail)='PASS' OR TRIM(x.passfail)='pass'),  x.sem, NULL) SEPARATOR ', ') AS incstr_pass
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1')   $s_replace
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all (
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%' $s_replace_old
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";



        $query = $this->db->query($sql, array($admn_no, $admn_no));
//echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();

        else {

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM alumni_final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1')   $s_replace
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all (
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM alumni_tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%' $s_replace_old
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";
 $query = $this->db->query($sql, array($admn_no, $admn_no));
//echo $this->db->last_query(); die();
        if ($query->num_rows() > 0) return $query->row();	 else return 0;

        }
    }

	 static function cumm_OGPA_status_static($admn_no, $h_status,$sem=null) {
		   $lst = '';$lst_old = '';
		  if($sem<>null){
        for ($i = $sem; $i >= 1; $i--) {
            $lst.=$i . ($i == 1 ? "" : ",");
            $lst_old.= ($i == 10?"'X'":$i) . ($i == 1 ? "" : ",");
        }
        //echo  $lst ; die();
        if (substr_count($lst, ',') > 0) {
            $s_replace = " and a.semester in (" . $lst . ")";
            $s_replace_old = " and right(a.sem_code,1) in (" . $lst_old . ")";
        } else {
            $s_replace = "  and a.semester ='" . $lst . "' ";
            $s_replace_old = "  and right(a.sem_code,1) ='" . $lst_old . "' ";
        }
		  }

        if ($h_status == 'Y')
            $status = "status";
        else
            $status = "core_status";

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1')  $s_replace
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all (
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%' $s_replace_old
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";



//        $query = $this->db->query($sql, array($admn_no, $admn_no));
 $query = self::$db->query($sql, array($admn_no, $admn_no));
//echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {

$sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM
(
select z.* from(
(
SELECT B.*
FROM (
SELECT a." . $status . " AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem
FROM alumni_final_semwise_marks_foil a
WHERE a.admn_no=?  and  a.course<>'MINOR' AND (a.semester!= '0' and a.semester!='-1')  $s_replace
GROUP BY a.session_yr,a.session,a.semester,a.type
/*ORDER BY a.session_yr,a.semester DESC, a.tot_cr_pts DESC, a.exam_type DESC)B*/
ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
GROUP BY B.sem)
UNION all(
SELECT A.*
FROM (
SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem
FROM alumni_tabulation1 a
WHERE a.adm_no=? and a.sem_code not like 'PREP%' $s_replace_old
GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms
ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
GROUP BY A.sem_code)
order by sem,passfail desc
)z group by z.sem

)x";



//        $query = $this->db->query($sql, array($admn_no, $admn_no));
 $query = self::$db->query($sql, array($admn_no, $admn_no));
//echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)   return $query->row(); else return 0;

        }
    }

    function cummulative_OGPA_STATUSFromTabulation1($admn_no) {
        $sql = "select SUM(IF (( z.passfail='F'), 1, 0)) AS count_status from
(select A.* from
(select a.passfail, a.examtype,a.sem_code, CAST(REVERSE(a.sem_code ) AS UNSIGNED) as  latestsem from tabulation1 a where a.adm_no=?  group by
a.adm_no,
a.sem_code,
a.examtype,
a.wsms
order by  CAST(REVERSE(a.sem_code ) AS UNSIGNED) desc, a.examtype desc )A
 group by A.sem_code)z ";

        $query = $this->db->query($sql, array($admn_no));
        //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row()->count_status;
        else {
            return 0;
        }
    }

    function cummulative_OGPA_STATUSFromMIS($admn_no, $h_status) {
        if ($h_status == 'Y')
            $status = "status";
        else
            $status = "core_status";
        $sql = "
select SUM(IF (( z." . $status . "='FAIL'), 1, 0)) AS count_status from
(select A.* from
(select a." . $status . ", a.exam_type,a.course,a.branch ,a.semester from  final_semwise_marks_foil a where a.admn_no=?  group by
a.admn_no,a.course,a.branch ,a.semester,a.exam_type
order by  a.semester desc,  a.tot_cr_pts DESC,a.exam_type desc )A
group by A.course,A.branch ,A.semester)z
 ";


        $query = $this->db->query($sql, array($admn_no));
     //   echo $this->db->last_query();
      //  die();
        if ($query->num_rows() > 0)
            return $query->row()->count_status;
        else {
            return 0;
        }
    }

    function getLatestSemesterFromOldDatabase($admno) {

        $sql = "select  CAST(REVERSE(a.sem_code ) AS UNSIGNED) as  latestsem from tabulation1 a where a.adm_no=?  group by a.adm_no,a.sem_code  order by  CAST(REVERSE(a.sem_code ) AS UNSIGNED) desc  limit 1";
        $query = $this->db->query($sql, array($admno));
        if ($query->num_rows() > 0)
            return $query->row()->latestsem;
        else {
            return 0;
        }
    }
function getStudentStatusFromMIS_param($admno,$sem) {
        $sql = " select  admn_no  from final_semwise_marks_foil  where admn_no=?  and  semester=? limit 1";
        $query = $this->db->query($sql, array($admno,$sem));
         if($query->num_rows()>0) return $query->num_rows();
		 else{
			 $sql = " select  admn_no  from alumni_final_semwise_marks_foil  where admn_no=?  and  semester=? limit 1";
        $query = $this->db->query($sql, array($admno,$sem));
          return $query->num_rows();
		 }
    }
    function getStudentStatusFromOldDatabase($admno) {
        $sql = " select  passfail  from tabulation1  where adm_no=?  group by examtype,wsms,sem_code order by examtype desc,wsms desc limit 1";
        $query = $this->db->query($sql, array($admno));
        if ($query->num_rows() > 0)
            return $query->row()->passfail;
        else {
            return 0;
        }
    }

    function getStudentStatusFromMIS($admno) {
        $sql = " select  passfail  from final_semwise_marks_foil  where adm_no=?  group by exam_type,wsms,sem_code order by examtype desc,wsms desc limit 1";
        $query = $this->db->query($sql, array($admno));
        if ($query->num_rows() > 0)
            return $query->row()->passfail;
        else {
            return 0;
        }
    }

    function get_exam_type($admn_no, $session, $session_yr, $sem) {
        $sql = " select  exam_type  from final_semwise_marks_foil  where admn_no=?  and  session_yr=? and session=? and semester=? and  status=?  ";
        $query = $this->db->query($sql, array($admn_no, $session_yr, $session, $sem, 'FAIL'));
         // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row()->exam_type;
        else {
            return 0;
        }
    }

	function getModeratedmarks_CaseAggFail($dept,$session_year,$session,$branch,$course_id,$sem,$admn_no,$type,$sub){
		 $secure_array = array($dept, $course_id, $branch, $sem, $admn_no, $session_year, $session, $type,$sub);
		$sql = "
SELECT NULL AS stu_status, NULL AS sub_map_id, grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
FROM
(
SELECT C.*
FROM (
SELECT B.*
FROM
(
SELECT A.*,c.id AS sub_id,c.name,c.credit_hours,c.`type`,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
FROM
(
SELECT a.sub_code AS ssub_id,b.dept,b.course AS course_id,b.branch AS branch_id,b.semester, NULL AS stu_status,a.theory,a.sessional,a.total,a.grade,b.tot_cr_pts,b.tot_cr_hr,a.mis_sub_id AS subject_id,b.`session`,b.session_yr,a.mis_sub_id
FROM final_semwise_marks_foil_desc AS a
INNER JOIN final_semwise_marks_foil AS b ON b.id=a.foil_id AND a.admn_no=b.admn_no AND b.dept=? AND b.course=? AND b.branch=? AND b.semester=? AND b.admn_no=? AND b.session_yr=? AND b.session=? AND b.type=?
 and a.remark='mod' and a.sub_code=?)A
/*JOIN subjects AS c ON A.ssub_id=c.subject_id group by c.subject_id*/
JOIN subjects AS c ON A.mis_sub_id=c.id group by c.id
) B
INNER JOIN course_structure AS d ON B.mis_sub_id=d.id) C
GROUP BY C.sub_code
ORDER BY C.semester ASC)grp
LEFT JOIN grade_points ON grade_points.grade=trim(grp.grade)
ORDER BY grp.semester ASC";
       $query = $this->db->query($sql, $secure_array);
       //  echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
        }
	}

	 public static function  get_bocs_subjects_static($dept, $course_id, $branch, $sem, $subje_name,$crdhrs,$admn_no=null){
if($admn_no==null)	{
	$secure_array = array($sem,$dept,$subje_name,$crdhrs, $dept,$course_id, $branch, $sem,$subje_name,$crdhrs  );
	 $rep=" ";
     }else{
	$secure_array = array($sem,$dept,$subje_name,$crdhrs, $admn_no,$dept,$course_id, $branch, $sem,$subje_name,$crdhrs  );
	$rep=" adm_no=? and ";
	 }
	$sql=" select x.name,group_concat( x.subject_id  order by x.date desc )  as  change_list_ch_order ,group_concat( x.date  order by x.date desc )  as  time_change_list_ch_order,x.aggr_id,GROUP_CONCAT(x.latest) as latest, GROUP_CONCAT(x.id
ORDER BY x.date DESC) AS change_list_ch_order2 from
(
(select c.name,c.subject_id,a.date,b.aggr_id,'2' as latest,c.id from dept_course a
inner join course_structure b on b.aggr_id=a.aggr_id and (b.aggr_id not like 'honour%'  and  b.aggr_id not like 'minor%') and b.semester=?
inner join subjects c on c.id=b.id
where a.dept_id=?    and   REPLACE(trim(lower(c.name) ),' ','' ) =REPLACE( trim(lower(?)),' ','' )   and c.credit_hours=? group by c.subject_id  )
 union all
(select distinct( trim(lower(tb.subje_name ) )) as name, tb.subje_code as subject_id , null as date, null as aggr_id, '1' as latest, null as id   from tabulation1 tb where ".$rep." tb.sem_code= (
SELECT d.semcode
FROM dip_m_semcode d
WHERE d.deptmis=? AND d.course=? AND d.branch=? AND d.sem=?) and  REPLACE( trim(lower(tb.subje_name) ),' ','') = REPLACE( trim(lower(?)),' ','' ) and tb.crdhrs=? group by  tb.subje_code)
order by latest desc limit 1 )x
 group by trim(lower(x.name) ) ";
  $query = self::$db->query($sql, $secure_array);  //echo self::$db->last_query().'<br/><br/><br/>';
if ($query->num_rows() > 0)return $query->row(); else return 0;
}

function get_exception_final_sem_data($session_year=null,$session=null,$crs_id=null,$admn_no=null,$param='all'){
	if($session_year!=null){
		$where=' where session_yr=? and session=? ';	
           $secure_array = array($session_year,$session);		
	}
	
	if($crs_id!=null){
		$where.=' and course=?';
		 $secure_array[] = ($crs_id);		
	}
	if($admn_no!=null){
		$where.=' and admn_no=?';
		$secure_array[] = ($admn_no);		
	}
	//print_r($secure_array); die();
	if($param=='all'){
	$sql=" select 
	        id,
            session_yr,
            session,
            course,
            curr_sem,
            prv_sem,
            prv_session_yr,
            prv_session,
            lower(admn_no) from exception_final_sem_data $where   ";
	}
	else{
		$sql=" select 	       
            lower(admn_no)  as admn_no from exception_final_sem_data $where   ";
	}
	if($where==null)
	 $query = $this->db->query($sql);
     else
	 $query = $this->db->query($sql, $secure_array);
       //  echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
}



}

?>
