<?php

/**
 * Attendance sheet generation for exam 
 * Copyright (c) ISM dhanbad * 
 * @category   PHPExcel
 * @package    exam_attendance
 * @copyright  Copyright (c) 2014 - 2015 Ism dhanbad
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##0.1##, #6/11/15#
 * @Author     Ritu raj<rituraj00@rediffmail.com>
 */
class Exam_attd_model extends CI_Model {

    function __construct() {
        // Call the Model constructor
        parent::__construct();
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
            //  echo $this->db->last_query();  die(); 
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
                $admn_no = preg_replace('/\s+/', '', $admn_no);
        if ($this->input->post('session') <> 'Summer' &&  !($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ) ) {
             if ($admn_no != null) {
                   if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf1.admn_no in(" . $admn_no . ") ";
                 $secure_array = array($this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory');
            } else {
                $replacer1 = " and hf1.admn_no=? ";
                  $secure_array = array($this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'),  5, 6, $admn_no,$branch, $sem, 'Theory', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $admn_no,$branch, $sem, 'Theory');
            }
            
        } else {
            $replacer1 = "";
              $secure_array = array($this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory');
        }
            $sql = " select x.* from( 
    select IF((z.honours='1' AND z.honour_hod_status='Y') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id 
    from
  (select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,d.id as sub_id,e.name,e.subject_id,A.honours,A.honour_hod_status,A.honours_agg_id from
  (select hf1.admn_no, hf1.honours,hf1.honour_hod_status,hf1.honours_agg_id, rgf.form_id from  hm_form hf1 
  inner join  reg_regular_form rgf on rgf.admn_no=hf1.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?          
  and hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=?  " . $replacer1 . ")A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no 
  INNER JOIN course_structure d ON d.aggr_id=A.honours_agg_id  and  d.semester=?  
  INNER JOIN subjects e ON e.id=d.id and e.`type`=? and  ( e.elective ='0' or  e. elective  is null)
  union

  SELECT B.admn_no, CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name) AS stu_name,d.id AS sub_id,e.name,e.subject_id,B.honours,B.honour_hod_status,B.honours_agg_id
FROM (
SELECT hf1.admn_no, hf1.honours,hf1.honour_hod_status,hf1.honours_agg_id,rgf.form_id
FROM hm_form hf1
inner join  reg_regular_form rgf on rgf.admn_no=hf1.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   
 and hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=? " . $replacer1 . ")B
INNER JOIN stu_academic ON stu_academic.admn_no=B.admn_no AND stu_academic.branch_id=?
INNER JOIN user_details ud ON ud.id=B.admn_no
INNER JOIN course_structure d ON d.aggr_id=B.honours_agg_id AND d.semester=?
INNER JOIN subjects e ON e.id=d.id AND e.`type`=? 
INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=B.form_id  and opt.sub_id=d.id
   )z 
  group by z.admn_no ORDER BY z.admn_no )x
 ";

          
            $query = $this->db->query($sql, $secure_array);
            // echo '<pre>'; print_r($query->result()); echo '</pre>'; die();
            // echo $this->db->last_query(); die();
			
			// commented as rule of hons changed  for 17-18 session  chaged on 21-7-17
          /*  $Hrow[] = (object) array();
            $h = 0;
            foreach ($query->result() as $row) {
                if ($sem % 2 <> 0)
                    $check_status = $this->exam_attd_model->check_hon_pass_fail($row->admn_no, $sem);
                //  echo $admn_no. "status" . $check_status->count_status. $check_status->incstr; die();
                $check_before_fifth_sem_status = $this->exam_attd_model->check_hm_eligibilty($row->admn_no);

                if ($check_status->count_status >= 1 || $check_before_fifth_sem_status->count_status >= 1) {
                    //return $check_status->incstr;
                } else {

                    $Hrow[$h]->both_status_string = $row->both_status_string;
                    $Hrow[$h]->admn_no = $row->admn_no;
                    $Hrow[$h]->st_name = $row->st_name;
                    $Hrow[$h]->subject = $row->subject;
                    $Hrow[$h]->sub_name = $row->sub_name;
                    $Hrow[$h]->sub_id = $row->sub_id;

                    $h++;
                }
            }
            return ($Hrow == null ? '0' : $Hrow);*/    if ($query->num_rows() > 0)  return $query->result(); else  return 0;
        } else {
               if ($admn_no != null) {
                   if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf1.admn_no in(" . $admn_no . ") ";
               $secure_array = array('1', 'Y', $this->input->post('dept'), /* $sem */ 5, 6, $branch, $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2');
            } else {
                $replacer1 = " and hf1.admn_no=? ";
                  $secure_array = array('1', 'Y', $this->input->post('dept'), /* $sem */ 5, 6, $admn_no, $branch, $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2');
            }
            
        } else {
            $replacer1 = "";
               $secure_array = array('1', 'Y', $this->input->post('dept'), /* $sem */ 5, 6, $branch, $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2');
        }
            $sql = "  
    select IF((z.hod_status='1' AND z.acad_status='1') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id
    from(
  select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name,c.sub_id,e.name,e.subject_id,x.hod_status,x.acad_status
  from
  (select hf1.admn_no from  hm_form hf1  where hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=? " . $replacer1 . ")A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no 
     inner join reg_summer_form x on x.admn_no=A.admn_no
INNER JOIN reg_summer_subject c ON c.form_id=x.form_id
INNER JOIN course_structure d ON d.id=c.sub_id  and  d.semester=?  and d.aggr_id like 'honour%'
INNER JOIN subjects e ON e.id=d.id
 and x.session=? and  x.session_year=? AND x.hod_status<> ? AND x.acad_status<> ? )z
 group by z.admn_no
ORDER BY z.admn_no";

            $secure_array = array('1', 'Y', $this->input->post('dept'), /* $sem */ 5, 6, $branch, $sem, $this->input->post('session'), $this->input->post('session_year'), '2', '2');
            $query = $this->db->query($sql, $secure_array);
            //   echo $this->db->last_query(); die();
            if ($query->num_rows() > 0)
                return $query->result();
            else {
                return 0;
            }
        }
    }
  function getStudentIncomingMinor($branch, $sem,$admn_no=null, $sess_yr = null, $sess = null, $dept = null) {
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        
        //$branch_id_first[0],  $sem_first[1],$this->input->post('option_sel'),$this->input->post('admn_no')
		//$bid,$sem,$syear,$sess,$did
        
        
        if ($this->input->post('session') <> 'Summer'  &&  !($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ) ) {
              if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
                  $secure_array = array('1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch,($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2', $sem, 'Theory','1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch,($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2', $sem, 'Theory' );
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                  $secure_array = array('1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch, $admn_no,($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2',$sem, 'Theory','1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch, $admn_no,($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2',$sem, 'Theory');
            }
        } else {
            $replacer1 = "";
             $secure_array = array('1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch, ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2',$sem, 'Theory','1', '1', 'Y', 5, ($dept == null ? $this->input->post('dept') : $dept), $branch, ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2',$sem, 'Theory');
        }
            
            $sql = "select x.* from(
          select IF((z.minor='1' AND z.minor_hod_status='Y') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id ,z.dept_name,z.dept_id,  z.branch_id , z.semester,z.own_branch,z.course_id
            from
                 ((select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester
				  ,d.id as sub_id,e.name,e.subject_id,A.minor_agg_id,A.minor,A.minor_hod_status,st.branch_id as own_branch,st.course_id
                 from 
                ( select hf2.minor_hod_status,hf2.minor,hm_minor_details.minor_agg_id, hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=?  
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                    ".$replacer1.")A 
					inner join  reg_regular_form rgf on rgf.admn_no=A.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>? 	
					 inner join user_details ud on ud.id=A.admn_no                                             
                     INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id  and  d.semester=?  
                     INNER JOIN subjects e ON e.id=d.id and e.`type`=?  and  ( e.elective ='0' or  e. elective  is null)    
				 
					 inner join stu_academic st on st.admn_no=A.admn_no
					 left join departments dpt on dpt.id =A.dept_id)
					 union
					 (select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester
				  ,d.id as sub_id,e.name,e.subject_id,A.minor_agg_id,A.minor,A.minor_hod_status,st.branch_id as own_branch,st.course_id
                 from 
                ( select hf2.minor_hod_status,hf2.minor,hm_minor_details.minor_agg_id, hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=? 
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                    ".$replacer1.")A 
					inner join  reg_regular_form rgf on rgf.admn_no=A.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>? 	
					 inner join user_details ud on ud.id=A.admn_no                                             
                     INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id  and  d.semester=?  
                     INNER JOIN subjects e ON e.id=d.id and e.`type`=? 
					 INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=rgf.form_id   and opt.sub_id=d.id
				 
					 inner join stu_academic st on st.admn_no=A.admn_no
					 left join departments dpt on dpt.id =A.dept_id)
                    
		             )z                 
                      
                   group by z.admn_no   order by z.admn_no 		
)x
  ORDER BY x.admn_no                    				   
             ";


            //$secure_array=array('1','1','Y',$this->input->post('session_year'),5,6,$this->input->post('dept'),$branch,$sem,'Theory');

          
            $query = $this->db->query($sql, $secure_array);
            //  echo $this->db->last_query(); die();
            	// commented as rule of hons changed  for 17-18 session  chaged on 21-7-17
			/*$Hrow[] = (object) array();
            $h = 0;
            foreach ($query->result() as $row) {
                if ($sem % 2 <> 0)
                    $check_status = $this->exam_attd_model->check_minor_pass_fail($row->admn_no, $sem, 'MINOR');
                //  echo $admn_no. "status" . $check_status->count_status. $check_status->incstr; die();
                $check_before_fifth_sem_status = $this->exam_attd_model->check_hm_eligibilty($row->admn_no);

                if ($check_status->count_status >= 1 || $check_before_fifth_sem_status->count_status >= 1) {
                    //return $check_status->incstr;
                } else {

                    $Hrow[$h]->both_status_string = $row->both_status_string;
                    $Hrow[$h]->admn_no = $row->admn_no;
                    $Hrow[$h]->st_name = $row->st_name;
                    $Hrow[$h]->subject = $row->subject;
                    $Hrow[$h]->sub_name = $row->sub_name;
                    $Hrow[$h]->sub_id = $row->sub_id;
                    $Hrow[$h]->dept_id = $row->dept_id;
                    $Hrow[$h]->dept_name = $row->dept_name;
                    $Hrow[$h]->branch_id = $row->branch_id;
                    $Hrow[$h]->semester = $row->semester;
                    $Hrow[$h]->own_branch = $row->own_branch;
                    $Hrow[$h]->course_id = $row->course_id;

                    $h++;
                }
            }

            return ($Hrow == null ? '0' : $Hrow);*/   if ($query->num_rows() > 0)return $query->result();   else   return 0;

            /*  $query = $this->db->query($sql, $secure_array);


              // echo $this->db->last_query(); die();

              if ($query->num_rows() > 0)
              return $query->result();
              else
              return 0; */
        } else {
                 if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
                 $secure_array = array('1', '1', 'Y'/* ,$this->input->post('session_year') */, 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), '2', '2');
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                  $secure_array = array('1', '1', 'Y'/* ,$this->input->post('session_year') */, 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $admn_no, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), '2', '2');
            }
        } else {
            $replacer1 = "";
         $secure_array = array('1', '1', 'Y'/* ,$this->input->post('session_year') */, 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), '2', '2');
        }
            
            $sql = "  select IF((z.hod_status='1' AND z.acad_status='1') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id ,z.dept_name,z.hod_status,z.acad_status,z.dept_id,  z.branch_id , z.semester
                 from
                  (select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester,c.sub_id,e.name,e.subject_id,x.hod_status,x.acad_status
                 from 
                ( select hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=?  and hf2.semester<=?
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                   ".$replacer1." )A 
                      
                       inner join user_details ud on ud.id=A.admn_no                        
                       left join departments dpt on dpt.id =A.dept_id
                       inner join reg_summer_form x on x.admn_no=A.admn_no
INNER JOIN reg_summer_subject c ON c.form_id=x.form_id
INNER JOIN course_structure d ON d.id=c.sub_id  and  d.semester=?  and d.aggr_id like 'minor%'
INNER JOIN subjects e ON e.id=d.id
 and x.session=? and  x.session_year=? AND x.hod_status<>? AND x.acad_status<>? )z
 group by z.admn_no
ORDER BY z.admn_no";
           
            $query = $this->db->query($sql, $secure_array);


           //  echo $this->db->last_query(); die();

            if ($query->num_rows() > 0)
                return $query->result();
            else
                return 0;
        }
    }
    /*function getStudentIncomingMinor($branch, $sem,$admn_no=null, $sess_yr = null, $sess = null, $dept = null) {
        $admn_no = preg_replace('/\s+/', '', $admn_no);
        
        
        
        
        if ($this->input->post('session') <> 'Summer') {
              if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
                  $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, 'Theory', ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2');
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                  $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $admn_no,$sem, 'Theory', ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2');
            }
        } else {
            $replacer1 = "";
             $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, 'Theory', ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), $sem, ($sess == null ? $this->input->post('session') : $sess), '2', '2');
        }
            
            $sql = "select x.*,rgf.semester from(
          select IF((z.minor='1' AND z.minor_hod_status='Y') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id ,z.dept_name,z.dept_id,  z.branch_id , z.semester,z.own_branch,z.course_id
            from
                 (select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester
				  ,d.id as sub_id,e.name,e.subject_id,A.minor_agg_id,A.minor,A.minor_hod_status,st.branch_id as own_branch,st.course_id
                 from 
                ( select hf2.minor_hod_status,hf2.minor,hm_minor_details.minor_agg_id, hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=?  and hf2.semester<=?
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                    ".$replacer1.")A 
					 inner join user_details ud on ud.id=A.admn_no                                             
                     INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id  and  d.semester=?  
                     INNER JOIN subjects e ON e.id=d.id and e.`type`=?  and  ( e.elective ='0' or  e. elective  is null)     
					 inner join stu_academic st on st.admn_no=A.admn_no
					 left join departments dpt on dpt.id =A.dept_id
                     order by A.admn_no 
		             )z                 
                      
                   group by z.admn_no   order by z.admn_no 		
)x
inner join  reg_regular_form rgf on rgf.admn_no=x.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   ORDER BY x.admn_no                    				   
             ";


            //$secure_array=array('1','1','Y',$this->input->post('session_year'),5,6,$this->input->post('dept'),$branch,$sem,'Theory');

          
            $query = $this->db->query($sql, $secure_array);
            //  echo $this->db->last_query(); die();
            	// commented as rule of hons changed  for 17-18 session  chaged on 21-7-17
			  if ($query->num_rows() > 0)return $query->result();   else   return 0;

         
        } else {
                 if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and hf2.admn_no in(" . $admn_no . ") ";
                 $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), 1, 1);
            } else {
                $replacer1 = " and hf2.admn_no=? ";
                  $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $admn_no, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), 1, 1);
            }
        } else {
            $replacer1 = "";
         $secure_array = array('1', '1', 'Y', 5, 6, ($dept == null ? $this->input->post('dept') : $dept), $branch, $sem, ($sess == null ? $this->input->post('session') : $sess), ($sess_yr == null ? $this->input->post('session_year') : $sess_yr), 1, 1);
        }
            
            $sql = "  select IF((z.hod_status='1' AND z.acad_status='1') ,'','Pending') as  both_status_string,z.admn_no,z.stu_name as  st_name,GROUP_CONCAT(z.name) AS sub_name, GROUP_CONCAT(z.sub_id) AS subject,GROUP_CONCAT(z.subject_id) AS sub_id ,z.dept_name,z.hod_status,z.acad_status,z.dept_id,  z.branch_id , z.semester
                 from
                  (select dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as stu_name ,A.dept_id,  A.branch_id , A.semester,c.sub_id,e.name,e.subject_id,x.hod_status,x.acad_status
                 from 
                ( select hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                          and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=?  and hf2.semester>=?  and hf2.semester<=?
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                   ".$replacer1." )A 
                      
                       inner join user_details ud on ud.id=A.admn_no                        
                       left join departments dpt on dpt.id =A.dept_id
                       inner join reg_summer_form x on x.admn_no=A.admn_no
INNER JOIN reg_summer_subject c ON c.form_id=x.form_id
INNER JOIN course_structure d ON d.id=c.sub_id  and  d.semester=?  and d.aggr_id like 'minor%'
INNER JOIN subjects e ON e.id=d.id
 and x.session=? and  x.session_year=? AND x.hod_status=? AND x.acad_status=? )z
 group by z.admn_no
ORDER BY z.admn_no";
           
            $query = $this->db->query($sql, $secure_array);


           //  echo $this->db->last_query(); die();

            if ($query->num_rows() > 0)
                return $query->result();
            else
                return 0;
        }
    }
*/
    function getStudentListCommon($session_yr, $session, $section, $sem = null, $status_check = true, $admn_no = null,$crs_struct=null) {
            //echo $admn_no; die();
          $admn_no = preg_replace('/\s+/', '', $admn_no);
           //echo $admn_no; die();
              if($crs_struct){
            $crs_struct_concat=" and r.course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
          
        if (!$status_check) {
            $chk_replace = " and r.hod_status<>'2' and  r.acad_status<>'2'";
            $chk_replace1 = " and a.hod_status<>'2' and  a.acad_status<>'2'";
            //$hod_status='2';$acad_status='2';
        } else {
            $chk_replace = " and r.hod_status='1' and  r.acad_status='1' ";
            $chk_replace1= " and a.hod_status='1' and  a.acad_status='1' ";
            
            //$hod_status='1';$acad_status='1';
        }

       /* if ($section != 'all' && $section != null && $section != "") {
            $where_sec = " and i.section=? ";
            $secure_array = array($session_yr, $section);
        } else {
            $where_sec = "";
            $secure_array = array($session_yr);
        }*/

      if( $this->input->post('session')=='Summer'||  ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ) ){
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
                      $where = " and section=?  and  admn_no in(" . $admn_no . ")";
                      $secure_array = array($session_yr, $section);
                       $where_sec= " and i.section=? ";                      
                       $secure_array=array($session_yr,$section);            
                } else {
                    $where = " and  admn_no in(" . $admn_no . ")";
                    $secure_array = array($session_yr);
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
          
            $where = "and  substring(d.semester,1,1)=? ";
            $table = " reg_summer_form ";

            $secure_array = array_merge($secure_array, array($sem, $this->input->post('session'), $this->input->post('session_year')));
            //print_r($secure_array);
            $sql = "select x.*,gp.group from (SELECT A.section,IF((A.hod_status='1' AND A.acad_status='1') ,'','Pending') as  both_status_string_old,  CONCAT_WS( ' ',(IF((A.hod_status='1'),'','HOD-P') ),(IF((A.acad_status='1'),'','ACD-P') ) )AS both_status_string,A.admn_no,A.stu_name as  st_name,GROUP_CONCAT(name) AS sub_name, GROUP_CONCAT(sub_id) AS subject,GROUP_CONCAT(subject_id) AS sub_id
FROM(
SELECT i.section,a.form_id, a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name, c.sub_id,e.name,e.subject_id
FROM " . $table . "  a
inner join stu_section_data i on i.admn_no= a.admn_no  and   i.session_year=?  " . $where_sec . " 
INNER JOIN user_details b ON b.id=a.admn_no 
INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
INNER JOIN course_structure d ON d.id=c.sub_id " . $where . "
INNER JOIN subjects e ON e.id=d.id
INNER JOIN departments f ON f.id=b.dept_id
INNER JOIN cs_courses g ON g.id=a.course_id
INNER JOIN cs_branches h ON h.id=a.branch_id
WHERE a.session=?  and  a.session_year=?   " . $chk_replace1 . "    ".$where3." 
ORDER BY a.admn_no)A
GROUP BY A.admn_no
ORDER BY A.admn_no,A.subject_id )x
left join section_group_rel gp  on gp.section=x.section GROUP BY x.admn_no
ORDER BY x.admn_no,x.sub_id ";
        } else {             
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
            $sql = "select  IF((r.hod_status='1' AND r.acad_status='1') ,'','Pending') as  both_status_string_old,
		                 CONCAT_WS( ' ',(IF((r.hod_status='1'),'','HOD-P') ),(IF((r.acad_status='1'),'','ACD-P') ) )AS both_status_string ,
						 concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name , A.admn_no from 
                   (select i.admn_no,i.section  from  stu_section_data i where i.session_year=?   ".$where.")A                    
                      inner join user_details ud on ud.id=A.admn_no join reg_regular_form r on r.admn_no=A.admn_no and r.session_year= '" . $this->input->post('session_year') . "' and r.`session`='" . $session . "'  ".$crs_struct_concat."  " . $chk_replace . "    order by A.admn_no  ";
        }
        $query = $this->db->query($sql, $secure_array);
      //    echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    /* function getStudentListCommon($session_yr,$section){
      $sql="select concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name , A.admn_no
      from
      (select admn_no,section  from  stu_section_data where session_year=? and section=? )A
      inner join user_details ud on ud.id=A.admn_no

      order by A.admn_no  ";


      $secure_array=array($this->input->post('session_year'),$this->input->post('section_name'));
      $query = $this->db->query($sql, $secure_array);
      // echo $this->db->last_query(); die();

      if ($query->num_rows() > 0)
      return $query->result();
      else {
      return 0;
      }

      } */

    function getPREPStudentList($admn_no,$status_check = true) {
        $table = " reg_regular_form ";
        $yr = explode('-', $this->input->post('session_year'));
        $admn_no = preg_replace('/\s+/', '', $admn_no);


        if (!$status_check) {
            $chk_replace = " and hod_status<>? and  acad_status<>?";
            $hod_status = '2';
            $acad_status = '2';
        } else {
            $chk_replace = " and hod_status=? and  acad_status=? ";
            $hod_status = '1';
            $acad_status = '1';
        }
        if ($this->input->post('dept') != 'all') {
            $crs_brch = "   and course_id=? and branch_id=?   ";
            $crs_brch2 = "   and upper(course_id)=? and branch_id=?    ";
            $dept_rep = " and ud.dept_id=?";
            if ($admn_no != null) {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $replacer1 = "  and admn_no in(" . $admn_no . ") ";
                    $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('course'), $this->input->post('branch'), $this->input->post('session_year'), $hod_status, $acad_status, $this->input->post('course'), $this->input->post('branch'), $this->input->post('dept'));
                } else {
                    $replacer1 = " and admn_no=? ";
                    $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('course'), $this->input->post('branch'), $this->input->post('session_year'), $hod_status, $acad_status, $this->input->post('course'), $this->input->post('branch'), $admn_no, $this->input->post('dept'));
                }
            } else {
                $replacer1 = "";
                $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('course'), $this->input->post('branch'), $this->input->post('session_year'), $hod_status, $acad_status, $this->input->post('course'), $this->input->post('branch'), $this->input->post('dept'));
            }
        } else {
            $crs_brch = "  ";
            $crs_brch2 = " ";
            $dept_rep = " ";
            if ($admn_no != null) {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $replacer1 = "  and admn_no in(" . $admn_no . ") ";
                    $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $this->input->post('dept'));
                } else {
                    $replacer1 = " and admn_no=? ";
                    $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $admn_no, $this->input->post('dept'));
                }
            } else {
                $replacer1 = "";
                $secure_array = array('prep', $yr[0], '-1', '0', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $this->input->post('dept'));
            }
        }
        //  $dept_rep =($this->input->post('dept')!='all'?" and ud.dept_id=?" : "");                           


        $sql = "select dpt.name as dept_name,B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Pending') as  both_status_string_old,
			        CONCAT_WS( ' ',(IF((B.hod_status='1'),'','HOD-P') ),(IF((B.acad_status='1'),'','ACD-P') ) )AS both_status_string,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                  from
                 ( select admn_no ,course_id,branch_id from stu_academic  where auth_id=? and  enrollment_year=? and (semester=? or semester=?) " . $crs_brch . "  )A
                  inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? " . $chk_replace . "
                    " . $crs_brch2 . "   " . $replacer1 . ")B 
				   on A.admn_no=B.admn_no 
                   inner  join user_details ud on ud.id=B.admn_no  " . $dept_rep . "
                   left join departments dpt on dpt.id =ud.dept_id
                   order by B.admn_no
                   ";
        $query = $this->db->query($sql, $secure_array);
        //		 echo $this->db->last_query(); die();	   
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getJRFStudentList($dept, $course_id, $branch_id, $status_check = true,$admn_no=null,$sub_list=null) {
     $admn_no = preg_replace('/\s+/', '', $admn_no);
     $sub_list = preg_replace('/\s+/', '', $sub_list);
   
  /*if ( $sub_list!= null) {
    if (substr_count( $sub_list, ',') > 0) {
		$sub_list = "'" . implode("','", explode(',',  $sub_list)) . "'";
        $sub_replacer1 = "   and  s.subject_id  IN(" . $sub_list . ") ";
	}else 
		$sub_replacer1 = "  and  s.subject_id='" . $sub_list . "' ";                    
        
   } else 
          $sub_replacer1 = "";
    */             
        	 if ( $sub_list!= null) {
    if (substr_count( $sub_list, ',') > 0) {
		 $sub_list = "'" . implode("','", explode(',',  $sub_list)) . "'";
         $sub_replacer1 = " ,sum( (e.subject_id IN(" . $sub_list . ")) ) AS subject_list";
	}else 
		$sub_replacer1 =  " ,sum( (e.subject_id='" . $sub_list . "' ) ) AS subject_list ";                    
        
   } else 
          $sub_replacer1 = " ,'0' AS subject_list";  
	
   
        if (!$status_check) {
            $chk_replace = " and hod_status<>? and  acad_status<>?";
            $hod_status = '2';
            $acad_status = '2';
        } else {
            $chk_replace = " and hod_status=? and  acad_status=? ";
            $hod_status = '1';
            $acad_status = '1';
        }

        $table = " reg_exam_rc_form ";
         if ($admn_no != null) {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $replacer1 = "  and admn_no in(" . $admn_no . ") ";
                    $secure_array = array($this->input->post('session'), $this->input->post('session_year'),($this->input->post('exm_type') == 'jrf_spl'?'S':'R') ,$hod_status, $acad_status, strtolower($course_id), strtolower($branch_id), $dept);
                } else {
                    $replacer1 = " and admn_no=? ";
                    $secure_array = array($this->input->post('session'), $this->input->post('session_year'),($this->input->post('exm_type') == 'jrf_spl'?'S':'R') , $hod_status, $acad_status, strtolower($course_id), strtolower($branch_id),$admn_no, $dept);
                }
            } else {
                $replacer1 = "";
                 $secure_array = array($this->input->post('session'), $this->input->post('session_year'), ($this->input->post('exm_type') == 'jrf_spl'?'S':'R') ,$hod_status, $acad_status, strtolower($course_id), strtolower($branch_id), $dept);
            }
        
        //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
        // $secure_array=array($this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,'2','2',$dept);

        /* $sql="select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
          from
          (select admn_no from   ".$table."  where  session=?  and session_year=? and hod_status=? and  acad_status=?
          and upper(course_id)=? and branch_id=?  )B
          inner join user_details ud on ud.id=B.admn_no   and dept_id=?
          order by B.admn_no
          ";
         */
        $sql = "
			     select x.*, 
				 GROUP_CONCAT(rexs.sub_id) as subject,GROUP_CONCAT(e.subject_id) AS sub_id, GROUP_CONCAT(e.name) AS sub_name
				 $sub_replacer1 
				
				 from
			     (select B.admn_no,B.form_id, IF((B.hod_status='1' AND B.acad_status='1') ,'','Pending') as  both_status_string_old ,
				  CONCAT_WS( ' ',(IF((B.hod_status='1'),'','HOD-P') ),(IF((B.acad_status='1'),'','ACD-P') ) )AS both_status_string,
				 concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                  from                                   
                   (select admn_no ,form_id, hod_status,acad_status from   " . $table . "  where  session=?  and session_year=?  and type=?" . $chk_replace . "
                   and lower(course_id)=? and lower(branch_id)=?  " . $replacer1 . " )B                   
                   inner join user_details ud on ud.id=B.admn_no   and dept_id=?                                    
                   )x
                  inner join reg_exam_rc_subject rexs on rexs.form_id=x.form_id 
				  INNER JOIN subjects e ON e.id=rexs.sub_id  
				  group by  rexs.form_id order by x.admn_no
                   ";



        $query = $this->db->query($sql, $secure_array);
            // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function getexcusive_Hon_minor_SubList($dept, $branch_id, $sem, $case) {

        if ($case == 'honour')
            $hon_minor = " and  x.aggr_id like '" . $case . "_" . $branch_id . "_%' ";
        if ($case == 'minor')
            $hon_minor = " and  x.aggr_id like '" . $case . "_" . $branch_id . "_%' ";

        $where = "  x.semester=? " . $hon_minor;

        $sql = " SELECT  distinct A.subject_id, A.name	  
        FROM(
        (SELECT e.name,e.subject_id FROM 
		(select x.id from course_structure x  where    " . $where . ")a
        INNER JOIN subjects e ON e.id=a.id and e.`type`=? /*and  ( e.elective ='0' or  e. elective  is null)*/                
       /* INNER JOIN reg_regular_elective_opted opt ON opt.form_id=B.form_id AND opt.sub_id=d.id)z*/
         )
		 )A
         ORDER BY A.subject_id ";

        $secure_array = array($sem, 'Theory');
        $query = $this->db->query($sql, $secure_array);
        //  echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }

    function get_exclusive_minor_sublist($dept, $branch, $sem) {
		 if( $this->input->post('session')=='Summer'||  ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ) ){			
			   $table='reg_summer_form';
			
			
			 
			   $sql = "
SELECT DISTINCT x.subject_id, x.name
FROM
(SELECT A.form_id, A.admn_no, e.name,e.subject_id,A.minor_agg_id
FROM (
SELECT hf1.admn_no, hmd.minor_agg_id, rgf.form_id
FROM hm_form hf1
inner  join hm_minor_details hmd on hmd.form_id=hf1.form_id and hmd.offered=? and hmd.dept_id=? and hmd.course_id=? and hmd.branch_id=? 
 AND hf1.minor=? AND hf1.minor_hod_status=?  AND hf1.semester>=?
INNER JOIN $table rgf ON rgf.admn_no=hf1.admn_no AND rgf.session_year=? AND rgf.`session`=? AND rgf.hod_status<>? AND rgf.acad_status<>?
 )A

INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id AND d.semester=?
INNER JOIN subjects e ON e.id=d.id AND e.`type`=?
)x order by x.subject_id ";
   $secure_array = array('1', $dept, 'minor', $branch, '1', 'Y', '5', $this->input->post('session_year'),$this->input->post('session'), '2', '2', $sem, 'Theory',);
        $query = $this->db->query($sql, $secure_array);
                           //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
			 
		 }else{
		
		
        $sql = "
SELECT DISTINCT x.subject_id, x.name
FROM(
(SELECT A.form_id, A.admn_no, e.name,e.subject_id,A.minor_agg_id
FROM (
SELECT hf1.admn_no, hmd.minor_agg_id, rgf.form_id
FROM hm_form hf1
inner  join hm_minor_details hmd on hmd.form_id=hf1.form_id and hmd.offered=? and hmd.dept_id=? and hmd.course_id=? and hmd.branch_id=? 
 AND hf1.minor=? AND hf1.minor_hod_status=?  AND hf1.semester>=?
INNER JOIN reg_regular_form rgf ON rgf.admn_no=hf1.admn_no AND rgf.session_year=? AND rgf.semester=? AND rgf.`session`=? AND rgf.hod_status<>? AND rgf.acad_status<>?
 )A

INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id AND d.semester=?
INNER JOIN subjects e ON e.id=d.id AND e.`type`=? AND (e.elective ='0' OR e. elective IS NULL) )

union

(SELECT A.form_id, A.admn_no, e.name,e.subject_id,A.minor_agg_id
FROM (
SELECT hf1.admn_no, hmd.minor_agg_id, rgf.form_id
FROM hm_form hf1
inner  join hm_minor_details hmd on hmd.form_id=hf1.form_id and hmd.offered=? and hmd.dept_id=? and hmd.course_id=? and hmd.branch_id=? 
 AND hf1.minor=? AND hf1.minor_hod_status=?  AND hf1.semester>=?
INNER JOIN reg_regular_form rgf ON rgf.admn_no=hf1.admn_no AND rgf.session_year=? AND rgf.semester=? AND rgf.`session`=? AND rgf.hod_status<>? AND rgf.acad_status<>?
 )A

INNER JOIN course_structure d ON d.aggr_id=A.minor_agg_id AND d.semester=?
INNER JOIN subjects e ON e.id=d.id AND e.`type`=? and      e. elective  is not  null
INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=A.form_id  and opt.sub_id=d.id
 )

)x order by x.subject_id ";

        $secure_array = array('1', $dept, 'minor', $branch, '1', 'Y', '5', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', $sem, 'Theory',
		'1', $dept, 'minor', $branch, '1', 'Y', '5', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', $sem, 'Theory');
        $query = $this->db->query($sql, $secure_array);
                         //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }
	
	
	}
    function get_exclusive_hons_sublist($dept, $branch, $sem) {
			 if( $this->input->post('session')=='Summer'||  ($this->input->post('session')=='Winter' && $this->input->post('exm_type')=='spl' ) ){			
			   $table='reg_summer_form';
			      $sql = "SELECT  distinct x.subject_id, x.name	  
					   FROM(
					   
					  select A.form_id, A.admn_no, e.name,e.subject_id
    from
  (select hf1.admn_no, hf1.honours,hf1.honour_hod_status,hf1.honours_agg_id, rgf.form_id from  hm_form hf1 
  inner join  $table rgf on rgf.admn_no=hf1.admn_no and rgf.session_year=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?          
  and hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=?)A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no 
  INNER JOIN course_structure d ON d.aggr_id=A.honours_agg_id  and  d.semester=?  
  INNER JOIN subjects e ON e.id=d.id and e.`type`=? 
)x  order by x.subject_id 
";
        $secure_array = array($this->input->post('session_year'), /*$sem,*/ $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory');
        $query = $this->db->query($sql, $secure_array);
      //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
			 }else{
        $sql = "SELECT  distinct x.subject_id, x.name	  
					   FROM(
					   
					  select A.form_id, A.admn_no, e.name,e.subject_id
    from
  (select hf1.admn_no, hf1.honours,hf1.honour_hod_status,hf1.honours_agg_id, rgf.form_id from  hm_form hf1 
  inner join  reg_regular_form rgf on rgf.admn_no=hf1.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?          
  and hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=?)A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no 
  INNER JOIN course_structure d ON d.aggr_id=A.honours_agg_id  and  d.semester=?  
  INNER JOIN subjects e ON e.id=d.id and e.`type`=? and  ( e.elective ='0' or  e. elective  is null)
  union

select  B.form_id, B.admn_no, e.name,e.subject_id   
FROM (
SELECT hf1.admn_no, hf1.honours,hf1.honour_hod_status,hf1.honours_agg_id,rgf.form_id
FROM hm_form hf1
inner join  reg_regular_form rgf on rgf.admn_no=hf1.admn_no and rgf.session_year=? and rgf.semester=? and rgf.`session`=? and  rgf.hod_status<>? and rgf.acad_status<>?   
 and hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and  hf1.semester>=? and hf1.semester<=?)B
INNER JOIN stu_academic ON stu_academic.admn_no=B.admn_no AND stu_academic.branch_id=?
INNER JOIN user_details ud ON ud.id=B.admn_no
INNER JOIN course_structure d ON d.aggr_id=B.honours_agg_id AND d.semester=?
INNER JOIN subjects e ON e.id=d.id AND e.`type`=? and      e. elective  is not  null
INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=B.form_id  and opt.sub_id=d.id)x  order by x.subject_id 
";
        $secure_array = array($this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory', $this->input->post('session_year'), $sem, $this->input->post('session'), '2', '2', '1', 'Y', $this->input->post('dept'), 5, 6, $branch, $sem, 'Theory');
        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return 0;
        }
    }
	}
    function getexcusiveSubList($dept, $course_id, $branch_id, $sem, $status_check = true,$sub_list=null,$admn_no=null,$mode=null,$crs_strct=null,$count_stu_subjectwise=false) {
		//echo 'sem'.$sem;
	//echo $dept. $course_id. $branch_id. $sem. $status_check.$sub_list ;
		 //echo 'test'.$crs_strct;
		if($sub_list)
		   if (substr_count($sub_list , ',') > 0) {
			   $sub_list = "'" . implode("','", explode(',', $sub_list)) . "'";
			   $sub_str= " where A.subject_id in (".$sub_list.")";
		    }
		     else
			$sub_str= " where A.subject_id='".$sub_list."'";
		else 
			$sub_list='';
		
		if($this->input->post('session')<>'Summer')
			$type_str2="  and  ( e.elective ='0' or  e. elective  is null) ";
		else 
		      $type_str2='';
        if($mode=='multisem')
           $type_str=" and e.`type`<>'Non-Contact' ";             
        else 
            $type_str=" and e.`type`='Theory' "; 
        
        if($admn_no){
            $admn_concat="  and a.admn_no='".$admn_no."' ";
        }
        else
            $admn_concat='';
        
        if($crs_strct<>""){
            $crs_struct_concat=" and d.aggr_id='".$crs_strct."' ";
        }else{
            $crs_struct_concat="  ";
        }
		
		
		 //echo 'test'. $crs_struct_concat; die();
        
        
        if (!$status_check) {
            $chk_replace = " and a.hod_status<>? and  a.acad_status<>?";
            $hod_status = '2';
            $acad_status = '2';
        } else {
            $chk_replace = " and a.hod_status=? and  a.acad_status=? ";
            $hod_status = '1';
            $acad_status = '1';
        }

        if ($this->input->post('session') == 'Summer') {
				$order_by=' ORDER BY A.subject_id ';
			 if($count_stu_subjectwise){
					$count_stu_subjectwise_str2=' ,count(a.admn_no) as stu_belong ';
					$count_stu_subjectwise_str1=' ,A.stu_belong ';
					$order_by ='  ORDER BY A.stu_belong desc ' ;
				}
				else{
					$count_stu_subjectwise_str2='';
					$count_stu_subjectwise_str1='';
					$order_by=' ORDER BY A.subject_id ';
				}
            if ($this->input->post('dept') == 'comm') {
                if ($this->input->post('section_name') != 'all' && $this->input->post('section_name') != null && $this->input->post('section_name') != "") {
                    $where_sec = " and i.section=? ";
                    $secure_array = array($this->input->post('session_year'), $this->input->post('section_name'));
                } else {
                    $where_sec = "";
                    $secure_array = array($this->input->post('session_year'));
                }

                $where =    ($mode=='multisem'? " and  substring(d.semester,1,1) in (".$sem.")   and d.aggr_id like 'comm_comm_%'":"and  substring(d.semester,1,1)=?  and d.aggr_id like 'comm_comm_%'");
                $dept_rep1 = "";
                $dept_rep2 = "";
                $dept_rep3 = " group by e.subject_id";
                $dept_rep4 = " inner join stu_section_data i on i.admn_no= a.admn_no  and   i.session_year=?  " . $where_sec . " ";
                
                $secure_array = array_merge($secure_array,($mode=='multisem'? array($this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status):array($sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status)));
            } else {                
                $where =  ($mode=='multisem' ? " and  d.semester in (".$sem.") ":" and  d.semester=? ") ;
                $dept_rep1 = "and b.dept_id=? ";
                $dept_rep2 = "and upper(a.course_id)=? and a.branch_id=? ";
                $dept_rep3 = " group by e.subject_id ";
                $dept_rep4 = "";
                
                $secure_array =($mode=='multisem'?  array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id): array($dept, $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id));
            }
            $table = " reg_summer_form ";
            $sql = "SELECT   distinct A.subject_id,  A.name,A.form_id as sem_form_id,A.aggr_id as course_aggr_id,A.id $count_stu_subjectwise_str1
			FROM(
			SELECT a.form_id, a.admn_no, e.name,e.subject_id,d.aggr_id,e.id $count_stu_subjectwise_str2
			FROM " . $table . "  a
			   " . $dept_rep4 . "
			INNER JOIN user_details b ON b.id=a.admn_no  " . $dept_rep1 . "
			INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
			INNER JOIN course_structure d ON d.id=c.sub_id " . $where . "
			INNER JOIN subjects e ON e.id=d.id ".$type_str. " ".$type_str2."
			INNER JOIN departments f ON f.id=b.dept_id
			INNER JOIN cs_courses g ON g.id=a.course_id
			INNER JOIN cs_branches h ON h.id=a.branch_id
			WHERE a.session=?  and  a.session_year=?  " .$admn_concat. "   " . $chk_replace . " " . $dept_rep2 . "  " . $dept_rep3 . "
			ORDER BY a.admn_no)A
				 $sub_str
			$order_by
			";
        } else if ($this->input->post('exm_type') == 'regular') {
            if ($this->input->post('dept') == 'comm') {
                if ($this->input->post('section_name') != 'all' && $this->input->post('section_name') != null && $this->input->post('section_name') != "") {
                    $where_sec = " and i.section=? ";
                    $secure_array = array($this->input->post('session_year'), $this->input->post('section_name'));
                    $secure_array2 = array($this->input->post('session_year'), $this->input->post('section_name'));
                } else {
                    $where_sec = "";
                    $secure_array = array($this->input->post('session_year'));
                    $secure_array2 = array($this->input->post('session_year'));
                }
                $where = " and  substring(d.semester,1,1)=?  and  d.semester=concat_ws('_',a.semester ,a.section)    and d.aggr_id like 'comm_comm_%' ". $crs_struct_concat;
                $where2 = " ";
                $dept_rep1 = "";
                $dept_rep2 = "";
                $dept_rep3 = " group by e.subject_id";
                $dept_rep4 = " inner join stu_section_data i on i.admn_no= a.admn_no  and   i.session_year=?  " . $where_sec . " ";
                $dept_rep5 = " inner join stu_section_data i on i.admn_no= a.admn_no  and   i.session_year=?  " . $where_sec . " ";
                $secure_array = array_merge($secure_array, array( ($sem=='null'||$sem==null|| $sem==''|| $sem=='none'?($this->input->post('session')=='Monsoon'?'1':'2'):$sem), 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status));
                $secure_array = array_merge($secure_array, $secure_array2);
                $secure_array = array_merge($secure_array, array(($sem=='null'||$sem==null|| $sem==''|| $sem=='none'?($this->input->post('session')=='Monsoon'?'1':'2'):$sem), 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status));
            } else {
                $where = " and  d.semester=? ". $crs_struct_concat;
                $where2 = " and  a.semester=? ";
                $dept_rep1 = " and b.dept_id=? ";
                $dept_rep2 = " and upper(a.course_id)=? and a.branch_id=? ";
                $dept_rep3 = " group by e.subject_id ";
                $dept_rep4 = "";
                $dept_rep5 = "";
                $secure_array = array($dept, $sem, 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, $sem,
                    $dept, $sem, 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, $sem);
            }
            $table = " reg_regular_form ";
            $sql = " SELECT  distinct A.subject_id, A.name	  
					   FROM(
					   (SELECT a.form_id, a.admn_no, e.name,e.subject_id
					   FROM " . $table . "  a
					   " . $dept_rep4 . "

				INNER JOIN user_details b ON b.id=a.admn_no  " . $dept_rep1 . "
				INNER JOIN course_structure d ON d.aggr_id=a.course_aggr_id " . $where . "
				INNER JOIN subjects e ON e.id=d.id and e.`type`=? and  ( e.elective ='0' or  e. elective  is null)
				INNER JOIN departments f ON f.id=b.dept_id
				INNER JOIN cs_courses g ON g.id=a.course_id
				INNER JOIN cs_branches h ON h.id=a.branch_id
				WHERE a.session=?  and  a.session_year=?  " .$admn_concat. "  " . $chk_replace . "  " . $dept_rep2 . "  " . $where2 . " " . $dept_rep3 . "   
				)
				union
				(SELECT a.form_id, a.admn_no, e.name,e.subject_id
					   FROM " . $table . "  a
					   " . $dept_rep5 . "

				INNER JOIN user_details b ON b.id=a.admn_no  " . $dept_rep1 . "
				INNER JOIN course_structure d ON d.aggr_id=a.course_aggr_id " . $where . "
				INNER JOIN subjects e ON e.id=d.id and e.`type`=? and      e. elective  is not  null
				INNER JOIN departments f ON f.id=b.dept_id
				INNER JOIN cs_courses g ON g.id=a.course_id
				INNER JOIN cs_branches h ON h.id=a.branch_id
				INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=a.form_id  and opt.sub_id=d.id
				WHERE a.session=?  and  a.session_year=?   " .$admn_concat. " " . $chk_replace . "   " . $dept_rep2 . "  " . $where2 . " " . $dept_rep3 . " )
				ORDER BY admn_no)A
				 $sub_str
				ORDER BY A.subject_id ";
        } else if ($this->input->post('exm_type') == 'prep') {
			if($this->input->post('session')=='Monsoon'){
            $where = " and  (d.semester='-1') ";
            $where2 = " and  (a.semester='-1')";
			}
			else if($this->input->post('session')=='Winter'){
            $where = " and  ( d.semester='0') ";
            $where2 = " and  (a.semester='0')";
			}
            $dept_rep2 = " ";
            $dept_rep3 = " group by e.subject_id ";
            if ($this->input->post('dept') != 'all') {
                $secure_array = array($dept, 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status,
                    $dept, 'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status);
                $dept_rep1 = " and b.dept_id=? ";
            } else {
                $secure_array = array('Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status,
                    'Theory', $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status);
                $dept_rep1 = "  ";
            }
            $table = " reg_regular_form ";
            $sql = " SELECT  distinct A.subject_id, A.name	  
						   FROM(
						   (SELECT a.form_id, a.admn_no, e.name,e.subject_id
						   FROM " . $table . "  a
						  

					INNER JOIN user_details b ON b.id=a.admn_no  " . $dept_rep1 . "
					INNER JOIN course_structure d ON d.aggr_id=a.course_aggr_id " . $where . "
					INNER JOIN subjects e ON e.id=d.id and e.`type`=? and  ( e.elective ='0' or  e. elective  is null)
					INNER JOIN departments f ON f.id=b.dept_id
					/*INNER JOIN cs_courses g ON g.id=a.course_id
					INNER JOIN cs_branches h ON h.id=a.branch_id*/
					WHERE a.session=?  and  a.session_year=?  " .$admn_concat. " " . $chk_replace . "  " . $dept_rep2 . "  " . $where2 . " " . $dept_rep3 . "   
					)
					union
					(SELECT a.form_id, a.admn_no, e.name,e.subject_id
						   FROM " . $table . "  a					 
					INNER JOIN user_details b ON b.id=a.admn_no  " . $dept_rep1 . "
					INNER JOIN course_structure d ON d.aggr_id=a.course_aggr_id " . $where . "
					INNER JOIN subjects e ON e.id=d.id and e.`type`=? and      e. elective  is not  null
					INNER JOIN departments f ON f.id=b.dept_id
					/*INNER JOIN cs_courses g ON g.id=a.course_id
					INNER JOIN cs_branches h ON h.id=a.branch_id*/
					INNER JOIN  reg_regular_elective_opted opt ON opt.form_id=a.form_id  and opt.sub_id=d.id
					WHERE a.session=?  and  a.session_year=? " .$admn_concat. " " . $chk_replace . "   " . $dept_rep2 . "  " . $where2 . " " . $dept_rep3 . " )
					ORDER BY admn_no)A
					ORDER BY A.subject_id ";
        } else if (($this->input->post('session') == 'Winter' && ($this->input->post('exm_type') == 'spl' || $this->input->post('exm_type') == 'spl2') ) || ($this->input->post('session') == 'Monsoon' && $this->input->post('exm_type') == 'spl')|| $this->input->post('exm_type') == 'other' || $this->input->post('exm_type') == 'jrf' || $this->input->post('exm_type') == 'jrf_spl' ||    $this->input->post('exm_type') == 'spl_jrf') {
                  
				  $order_by=' ORDER BY A.subject_id ';
			 if($count_stu_subjectwise){
					$count_stu_subjectwise_str2=' ,count(a.admn_no) as stu_belong ';
					$count_stu_subjectwise_str1=' ,A.stu_belong ';
					$order_by ='  ORDER BY A.stu_belong desc ' ;
				}
				else{
					$count_stu_subjectwise_str2='';
					$count_stu_subjectwise_str1='';
					$order_by=' ORDER BY A.subject_id ';
				}
				
            if ($this->input->post('exm_type') <> 'jrf' && $this->input->post('exm_type') <> 'jrf_spl'   &&   $this->input->post('exm_type') <> 'spl_jrf') {
                $table = " reg_other_form  ";
                $table2 = " reg_other_subject	";
                $table3 = " reg_exam_rc_form  ";
                $table4 = " reg_exam_rc_subject	";
				
				
				
                if ($this->input->post('exm_type') == 'spl2')
                    $xx_rep = " and a.reason='Special' ";
                else
                    $xx_rep = "";
                $union_pred = '(';
                $union_succ = ')';
                $union_sql = " union " . $union_pred . "
							SELECT a.form_id, a.admn_no, e.name,e.subject_id $count_stu_subjectwise_str2
							FROM " . $table3 . "  a   
							INNER JOIN user_details b ON b.id=a.admn_no  and b.dept_id=?
							INNER JOIN " . $table4 . " c ON c.form_id=a.form_id 
							INNER JOIN subjects e ON e.id=c.sub_id and e.`type`='Theory' 
							LEFT JOIN course_structure d ON d.aggr_id=a.course_aggr_id  and  d.semester='".$sem."' and c.sub_id=d.id
							INNER JOIN departments f ON f.id=b.dept_id
							INNER JOIN cs_courses g ON g.id=a.course_id
							INNER JOIN cs_branches h ON h.id=a.branch_id
							WHERE a.session=?  and  a.session_year=?  " .$admn_concat. "  and upper(a.course_id)=? and a.branch_id=? and a.semester like '%?%' and  a.type=? " . $xx_rep . "  group by e.subject_id
							ORDER BY a.admn_no
							" . $union_succ . " ";
            }else {
                $table = " reg_exam_rc_form  ";
                $table2 = " reg_exam_rc_subject	";
                $table3 = "";
                $table4 = "";
                $xx_rep = "";
                $union_pred = '';
                $union_succ = '';
                $union_sql = "";
			    if($this->input->post('exm_type') == 'spl_jrf'|| $this->input->post('exm_type') == 'jrf_spl'){$course_id='JRF'; $branch_id='JRF';}
             }

            $rep_sub_type = (($this->input->post('exm_type') == 'jrf' || $this->input->post('exm_type') == 'jrf_spl'  ||  $this->input->post('exm_type') == 'spl_jrf' ) ? " (e.`type`='jrf' or e.`type`='Theory') " : " e.`type`='Theory'" );
            $sem_rep = (($this->input->post('exm_type') == 'jrf' || $this->input->post('exm_type') == 'jrf_spl'  ||  $this->input->post('exm_type') == 'spl_jrf') ? "" : " and a.semester like '%?%' " );
            $br_rep = (($this->input->post('exm_type') == 'jrf' || $this->input->post('exm_type') == 'jrf_spl'  ||  $this->input->post('exm_type') == 'spl_jrf') ? " and upper(a.branch_id)=?  " : " and a.branch_id=?  " );



            /* $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,(int)$sem,
              (($this->input->post('exm_type')=='other'||$this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl')?'R': ('S')) ,$dept,$this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,(int)$sem,
              (($this->input->post('exm_type')=='other'||$this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl')?'R': ('S'))  ); */

            if ($this->input->post('exm_type') == 'jrf' || $this->input->post('exm_type') == 'jrf_spl' ||  $this->input->post('exm_type') == 'spl_jrf') {
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $course_id, $branch_id,
                    (($this->input->post('exm_type') == 'other' || $this->input->post('exm_type') == 'jrf' /*|| $this->input->post('exm_type') == 'jrf_spl'*/ ) ? 'R' : ( 'S')));
            } else {
                $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $course_id, $branch_id, (int) $sem,
                    (($this->input->post('exm_type') == 'other' || $this->input->post('exm_type') == 'jrf' /*|| $this->input->post('exm_type') == 'jrf_spl'*/) ? 'R' : ( 'S')), $dept, $this->input->post('session'), $this->input->post('session_year'), $course_id, $branch_id, (int) $sem,
                    (($this->input->post('exm_type') == 'other' || $this->input->post('exm_type') == 'jrf' /*|| $this->input->post('exm_type') == 'jrf_spl'*/) ? 'R' : ('S')));
            }
            /* }else{		
              $table=" reg_exam_rc_form  ";
              $table2=" reg_exam_rc_subject	";
              $table3="";
              $table4="";
              $xx_rep="";
              $union_pred='';
              $union_succ='';
              $union_sql= "";
              $rep_sub_type= (($this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl')? " (e.`type`='jrf' or e.`type`='Theory') " :  " e.`type`='Theory'" );
              $sem_rep=  (($this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl') ?"": " and a.semester like '%?%' " );
              $br_rep=(($this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl') ?" and upper(a.branch_id)=?  ": " and a.branch_id=?  " );

              if($this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl'){
              $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,
              (($this->input->post('exm_type')=='other'||$this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl')?'R': ( 'S'))   );
              }
              else{
              $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,(int)$sem,
              (($this->input->post('exm_type')=='other'||$this->input->post('exm_type')=='jrf'|| $this->input->post('exm_type')=='jrf_spl')?'R': ( 'S'))   );
              }
              } */

			  $var= ($course_id<>'JRF'? "LEFT JOIN course_structure d ON d.aggr_id=a.course_aggr_id  and  d.semester='".$sem."' and c.sub_id=d.id ":"");
			
            $sql = "SELECT   distinct A.subject_id,  A.name $count_stu_subjectwise_str1
					FROM(
					" . $union_pred . "
					SELECT a.form_id, a.admn_no, e.name,e.subject_id $count_stu_subjectwise_str2
					FROM " . $table . "  a   
					INNER JOIN user_details b ON b.id=a.admn_no  and b.dept_id=?
					INNER JOIN " . $table2 . " c ON c.form_id=a.form_id 
					INNER JOIN subjects e ON e.id=c.sub_id and   " . $rep_sub_type . "
				     ". $var ." 
					INNER JOIN departments f ON f.id=b.dept_id
					INNER JOIN cs_courses g ON g.id=a.course_id
					INNER JOIN cs_branches h ON h.id=a.branch_id
					WHERE a.session=?  and  a.session_year=?  " .$admn_concat. " and upper(a.course_id)=?  " . $br_rep . "    " . $sem_rep . "  and  a.type=? " . $xx_rep . "  group by e.subject_id
					ORDER BY a.admn_no
					" . $union_succ . "
					" . $union_sql . "
					)A
					
					 $sub_str 
					 $order_by
				   ";
        }
        $query = $this->db->query($sql, $secure_array);
        //echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return 0;
    }

    function getStudentList($dept, $course_id, $branch_id, $sem, $status_check = true,$admn_no=null,$crs_struct=null,$sub_list=null) {
		
	//	echo $course_id.'#'.$branch_id.'#'.$sem.','. $status_check .','.$admn_no=null.'#'. $crs_struct. '#'.$sub_list; die();
          $admn_no = preg_replace('/\s+/', '', $admn_no);
		  $sub_list = preg_replace('/\s+/', '', $sub_list);
   
 
          
         if($crs_struct){
            $crs_struct_concat=" and course_aggr_id= '" . $crs_struct . "' ";
        }else{
            $crs_struct_concat="  ";
        }
          
          
          
        if (!$status_check) {
            $chk_replace = " and hod_status<>? and  acad_status<>?";
            $hod_status = '2';
            $acad_status = '2';
        } else {
            $chk_replace = " and hod_status=? and  acad_status=? ";
            $hod_status = '1';
            $acad_status = '1';
        }
        if ($this->input->post('exm_type') == "other" || ($this->input->post('exm_type') == "spl" ) || ($this->input->post('exm_type') == "spl2" )) {
			 if ( $sub_list!= null) {
    if (substr_count( $sub_list, ',') > 0) {
		 $sub_list = "'" . implode("','", explode(',',  $sub_list)) . "'";
         $sub_replacer1 = " ,sum( (e.subject_id  IN(" . $sub_list . ")) ) AS subject_list";
	}else 
		$sub_replacer1 =  " ,sum( (e.subject_id='" . $sub_list . "' ) ) AS subject_list ";                    
        
   } else 
          $sub_replacer1 = " ,'0' AS subject_list";
            if ($this->input->post('exm_type') == "spl2" && $this->input->post('session') == 'Winter') {
                $addLimitation = " and  reason='Special' ";
            } else {
                $addLimitation = '';
            }


            $where = " and  semester like '%?%'  and  type = '" . (($this->input->post('exm_type') == 'spl' || $this->input->post('exm_type') == 'spl2') ? 'S' : 'R') . "' ";
            $table = " reg_exam_rc_form ";
            $table2 = " reg_other_form ";                        
             $where2 = "and  semester like '%?%'";
             
             if ($admn_no == null) {
                $where3 = "";
$secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem);
            } else {
                if (substr_count($admn_no, ',') > 0) {
                    $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                    $where3 = " and admn_no in(" . $admn_no . ") ";
$secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem);
                } else {
                    $where3 = " and admn_no=? ";
$secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem, $admn_no,$this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, (int) $sem,$admn_no);
                }
            }



            

            $sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Pending') as  both_status_string ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,CONCAT_WS(' ',ud.first_name,ud.middle_name,ud.last_name) AS st_name,B.sub_id1 as subject ,B.subject_id1 as sub_id ,B.name1 as sub_name
                      ,B.subject_list            
			from
                 (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                  inner join
                  (
				  (select x1.*,GROUP_CONCAT(c.sub_id) as sub_id1,GROUP_CONCAT(e.subject_id) AS subject_id1, GROUP_CONCAT(e.name) AS name1  $sub_replacer1     
from
                  (select form_id,admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table2 . "  where  session=?  and session_year=? " . $chk_replace . "
                    and upper(course_id)=? and branch_id=?  " . $where . "  " . $addLimitation . "  " . $where3 . " ) x1
INNER JOIN reg_other_subject c ON c.form_id=x1.form_id
INNER JOIN subjects e ON e.id=c.sub_id  
group by x1.admn_no   
)
                        union(
						select x2.*,GROUP_CONCAT(c.sub_id) as sub_id1,GROUP_CONCAT(e.subject_id) AS subject_id1, GROUP_CONCAT(e.name) AS name1   $sub_replacer1 
                        from 

                    (select form_id,admn_no,course_id,branch_id ,semester,hod_status,acad_status from   " . $table . "  where  session=?  and session_year=? " . $chk_replace . "
                    and upper(course_id)=? and branch_id=?  " . $where . "  " . $addLimitation . "     " . $where3 . "
                     )x2
INNER JOIN reg_exam_rc_subject c ON c.form_id=x2.form_id
INNER JOIN subjects e ON e.id=c.sub_id  
group by x2.admn_no   ) 
                    
                     )B on A.course_id=B.course_id  and A.branch_id=B.branch_id  
                    left join user_details ud on ud.id=B.admn_no                    
					group by admn_no,semester  
                    order by B.admn_no
                   ";
        }/* else if($this->input->post('exm_type')=="spl"){

          $where= "and  semester like '%?%'  and type='S'";
          $table=" reg_exam_rc_form ";
          if(!$status_check){
          $chk_replace= " and hod_status<>? and  acad_status<>?";
          $hod_status='2';$acad_status='2';
          }else{
          $chk_replace= " and session_year=? and hod_status=? and  acad_status=? ";
          $hod_status='1';$acad_status='1';
          }


          $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),$hod_status,$acad_status,$course_id,$branch_id,(int)$sem);
          $sql="select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Pending') as  both_status_string,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name , GROUP_CONCAT(B.subject_id1) AS sub_id, GROUP_CONCAT(B.sub_id1) AS subject, GROUP_CONCAT(B.name1) AS sub_name
          from
          (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
          inner join
          (select   x.*, GROUP_CONCAT(c.sub_id) as sub_id1,GROUP_CONCAT(e.subject_id) AS subject_id1, GROUP_CONCAT(e.name) AS name1   from (select  form_id,admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   ".$table."  where  session=?  and session_year=? ".$chk_replace."
          and upper(course_id)=? and branch_id=?  ".$where." )x
          INNER JOIN reg_exam_rc_subject c ON c.form_id=x.form_id
          INNER JOIN subjects e ON e.id=c.sub_id
          group by x.admn_no   )B
          on A.course_id=B.course_id  and A.branch_id=B.branch_id

          left join user_details ud on ud.id=B.admn_no
          group by B.admn_no
          order by B.admn_no
          ";

          } */ else if ($this->input->post('exm_type') == "regular") {
            if ($this->input->post('session') == 'Summer') {
				 if ( $sub_list!= null) {
    if (substr_count( $sub_list, ',') > 0) {
		 $sub_list = "'" . implode("','", explode(',',  $sub_list)) . "'";
         $sub_replacer1 = " ,sum( (subject_id  IN(" . $sub_list . ")) ) AS subject_list";
	}else 
		$sub_replacer1 =  " ,sum( (subject_id='" . $sub_list . "' ) ) AS subject_list ";                    
        
   } else 
          $sub_replacer1 = " ,'0' AS subject_list";
                $where = "and  d.semester=? ";
                $table = " reg_summer_form ";
                   if ($admn_no == null) {
                    $where2 = "";
                      $secure_array = array($dept, /* '4' */ $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and a.admn_no in(" . $admn_no . ") ";
                          $secure_array = array($dept, /* '4' */ $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id);
                    } else {
                        $where2 = " and a.admn_no=? ";
                          $secure_array = array($dept, /* '4' */ $sem, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id,$admn_no);
                    }
                }

                
              

                $sql = "SELECT IF((A.hod_status='1' AND A.acad_status='1') ,'','Pending') as  both_status_string,A.admn_no,A.stu_name as  st_name,GROUP_CONCAT(name) AS sub_name, 
                        GROUP_CONCAT(sub_id) AS subject,GROUP_CONCAT(subject_id) AS sub_id 
						 $sub_replacer1
                        FROM(
                        SELECT a.form_id, a.admn_no, a.hod_status,a.acad_status, CONCAT_WS(' ',b.first_name,b.middle_name,b.last_name) AS stu_name, c.sub_id,e.name,e.subject_id
                        FROM " . $table . "  a
                        INNER JOIN user_details b ON b.id=a.admn_no  and b.dept_id=?
                        INNER JOIN reg_summer_subject c ON c.form_id=a.form_id
                        INNER JOIN course_structure d ON d.id=c.sub_id " . $where . "
                        INNER JOIN subjects e ON e.id=d.id
                        INNER JOIN departments f ON f.id=b.dept_id
                        INNER JOIN cs_courses g ON g.id=a.course_id
                        INNER JOIN cs_branches h ON h.id=a.branch_id
                        WHERE a.session=?  and  a.session_year=?   " . $chk_replace . "  and upper(a.course_id)=? and a.branch_id=? " . $where2 . " 
                        ORDER BY a.admn_no)A
                        GROUP BY A.admn_no
                        ORDER BY A.admn_no,A.subject_id
                        ";
            } else {
                $where = " and  semester=?  ".$crs_struct_concat." ";
                $table = " reg_regular_form ";
                
                  if ($admn_no == null) {
                    $where2 = "";
                  $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, $sem);
                } else {
                    if (substr_count($admn_no, ',') > 0) {
                        $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                        $where2 = " and admn_no in(" . $admn_no . ") ";
                  $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, $sem);
                    } else {
                        $where2 = " and admn_no=? ";
                        $secure_array = array($dept, $this->input->post('session'), $this->input->post('session_year'), $hod_status, $acad_status, $course_id, $branch_id, $sem,$admn_no);
                    }
                }

                
                

                $sql = "select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Pending') as  both_status_string_old,
			CONCAT_WS( ' ',(IF((B.hod_status='1'),'','HOD-P') ),(IF((B.acad_status='1'),'','ACD-P') ) )AS both_status_string,
			concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                        from
                       (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                        inner join
                        (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   " . $table . "  where  session=?  and session_year=? " . $chk_replace . "
                         and upper(course_id)=? and branch_id=?  " . $where . "  " . $where2 . ")B on A.course_id=B.course_id  and A.branch_id=B.branch_id  
                          left join user_details ud on ud.id=B.admn_no                    
                          order by B.admn_no
                         ";
            }
        }
        $query = $this->db->query($sql, $secure_array);
        //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
            return $query->result();
        else
            return 0;
    }

   /* function getstauts($admn_no, $sem, $session, $session_year, $ex_type, $mode = null, $sub_id = null) {

        //      echo $admn_no."#".$sem."#".$session."#".$session_year."#".$ex_type."#".$mode."#".$sub_id."#";

        if ($ex_type == 'spl')
            $ex_type = 'special';

        
        if ($mode == null)
            $mod = 'D';
        if ($mode <> 'NA') {
            $replace1 = " and  (status=? or status=? ) ";
            $replace2 = " and status=? ";
            if ($sub_id <> null) {
                $add = " and  sub_id=? ";
                $secure_array = array($admn_no, '2', '1', $sub_id, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $mod, $sub_id);
            } else {
                $add = "";
                $secure_array = array($admn_no, '2', '1', $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $mod);
            }
        } else {
            $replace1 = "  and status<>'0' ";
            $replace2 = " ";
            if ($sub_id <> null) {
                $add = " and  sub_id=? ";
                $secure_array = array($admn_no, $sub_id, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $sub_id);
            } else {
                $add = "";
                $secure_array = array($admn_no, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type));
            }
        }
        //$sql="select A.status,s.subject_id  from (select status,sub_id from  stu_exam_absent_mark where  admn_no=?  and  semester=?  and  session=? and  session_year=? and lower(ex_type)=?) A left join subjects s  on s.id=A.sub_id " ;
        $sql = " select A.status,s.subject_id,s.`type`  from ( select x.* from  (select map_id,status,sub_id  from  absent_table where  admn_no=?  " . $replace1 . "  " . $add . "  group by admn_no,sub_id)x inner join subject_mapping sm on  sm.map_id=x.map_id and sm.session_year=? and sm.session=? ) A left join subjects s  on s.id=A.sub_id "
                . " union "
                . " select A.status,s.subject_id,s.`type`  from (select status,sub_id from  stu_exam_absent_mark where  admn_no=?  and  semester=?  and  session=? and  session_year=? and lower(ex_type)=?  " . $replace2 . " " . $add . " ) A left join subjects s  on s.id=A.sub_id ";
        //} 
        $query = $this->db->query($sql, $secure_array);
        //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return false;
        }
    }*/
	
	
	function get_stu_status($admn_no, $sem, $session, $session_year, $ex_type, $status, $sub_id = null){
		//echo $admn_no."#".$sem."#".$session."#".$session_year."#".$ex_type."#".$status."#".$sub_id."#"; echo '<br/>';
		if ($ex_type == 'spl')
            $ex_type = 'special';                       
           
            if ($sub_id <> null) {
                $add = " and  sub_id=? ";
                $secure_array = array($admn_no, $sem, $session, $session_year, strtolower($ex_type), $status, $sub_id);
            } else {
                $add = "";
                $secure_array = array($admn_no, $sem, $session, $session_year, strtolower($ex_type), $status);
            }
        
        
        $sql = "
                 select A.status,s.subject_id,s.`type`  from (select status,sub_id from  stu_exam_absent_mark where  admn_no=?  and  semester=?  and  session=? and  session_year=? and lower(ex_type)=?  and status=?   " . $add . " ) A left join subjects s  on s.id=A.sub_id ";

        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return false;
        }
    
		
	}
	
	  function getstauts($admn_no, $sem, $session, $session_year, $ex_type, $mode = null, $sub_id = null) {
 //echo 'test'; die();
            //  echo $admn_no."#".$sem."#".$session."#".$session_year."#".$ex_type."#".$mode."#".$sub_id."#"; echo '<br/>';

        if ($ex_type == 'spl')
            $ex_type = 'special';

        
        if ($mode == null)
            $mod = 'D';
        if ($mode <> 'NA') {
            $replace1 = " and  status=?";
            $replace2 = " and status=? ";
            if ($sub_id <> null) {
                $add = " and  sub_id=? ";
                $secure_array = array($admn_no, '2',  $sub_id, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $mod, $sub_id);
            } else {
                $add = "";
                $secure_array = array($admn_no, '2',  $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $mod);
            }
        } else {
            $replace1 = "  and status<>'0' ";
            $replace2 = " ";
            if ($sub_id <> null) {
                $add = " and  sub_id=? ";
                $secure_array = array($admn_no, $sub_id, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type), $sub_id);
            } else {
                $add = "";
                $secure_array = array($admn_no, $session_year, $session, $admn_no, $sem, $session, $session_year, strtolower($ex_type));
            }
        }
        //$sql="select A.status,s.subject_id  from (select status,sub_id from  stu_exam_absent_mark where  admn_no=?  and  semester=?  and  session=? and  session_year=? and lower(ex_type)=?) A left join subjects s  on s.id=A.sub_id " ;
        $sql = " select A.status,s.subject_id,s.`type`  from ( select x.* from  (select map_id,status,sub_id  from  absent_table where  admn_no=?  " . $replace1 . "  " . $add . "  group by admn_no,map_id,sub_id)x inner join subject_mapping sm on  sm.map_id=x.map_id and sm.session_year=? and sm.session=? ) A left join subjects s  on s.id=A.sub_id "
                . " union "
                . " select A.status,s.subject_id,s.`type`  from (select status,sub_id from  stu_exam_absent_mark where  admn_no=?  and  semester=?  and  session=? and  session_year=? and lower(ex_type)=?  " . $replace2 . " " . $add . " ) A left join subjects s  on s.id=A.sub_id ";
        //} 
        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return false;
        }
    }
	
	
	
	
	
	
	 function cbcs_getstauts($admn_no, $sem, $session, $session_year, $ex_type,$sub_code) {
 //echo 'test'; die();
              //echo $admn_no."#".$sem."#".$session."#".$session_year."#".$ex_type."#".$mode."#".$sub_code."#"; echo '<br/>';
        $secure_array = array($admn_no,$session,$session_year,$sub_code);     
        $sql="select def_status as status,sub_code from  cbcs_absent_table_defaulter where  admn_no=?    and  session=? and  session_year=? and sub_code=?  and def_status='y'  " ;        
        $query = $this->db->query($sql, $secure_array);
       // echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->result();
        else {
            return false;
        }
    }
	
	
	
	
	

    function check_hon_pass_fail($admn_no, $sem, $hstatus = 'Y') {
        $lst = '';
        for ($i = $sem; $i >= 5; $i--) {
            $lst.=$i . ($i == 5 ? "" : ",");
        }
        //echo  $lst ; die();
        if (substr_count($lst, ',') > 0) {
            $s_replace = " and a.semester in (" . $lst . ")";
        } else
            $s_replace = "  and a.semester ='" . $lst . "' ";
        //echo  $s_replace .'<br/>' ;

        $sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC(Minor)'), NULL) SEPARATOR ', ') AS incstr
FROM (

select z.* from(
			(
			SELECT B.*
			FROM (
			SELECT a.status AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem,a.session_yr,a.`session`,a.course
			FROM final_semwise_marks_foil a
			WHERE a.admn_no=? and  a.hstatus=? " . $s_replace . "
			GROUP BY a.session_yr,a.`session`,a.semester,a.type
			ORDER BY a.session_yr desc,a.semester DESC, a.tot_cr_pts DESC)B
			GROUP BY B.sem) 
			
			)z group by z.sem )x
         
         ";



        $query = $this->db->query($sql, array($admn_no, $hstatus));
        //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
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
            return 0;
        }
    }

    function check_hm_eligibilty($admn_no, $sem = 4) {
        $lst = '';
        for ($i = $sem; $i >= 1; $i--) {
            $lst.=$i . ($i == 1 ? "" : ",");
        }
        //echo  $lst ; die();
        if (substr_count($lst, ',') > 0) {
            $s_replace = " and a.semester in (" . $lst . ")";
            $s_replace_old = " and right(a.sem_code,1) in (" . $lst . ")";
        } else {
            $s_replace = "  and a.semester ='" . $lst . "' ";
            $s_replace_old = "  and right(a.sem_code,1) ='" . $lst . "' ";
        }
        //echo  $s_replace .'<br/>' ;

        $sql = "SELECT SUM(IF ((TRIM(x.passfail='F') OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), 1, 0)) AS count_status, GROUP_CONCAT(IF((TRIM(x.passfail)='F' OR TRIM(x.passfail)='FAIL' OR TRIM(x.passfail)='fail'), CONCAT('Sem-',x.sem,':','INC'), NULL) SEPARATOR ', ') AS incstr
FROM (
	select z.* from(
			(
			SELECT B.*
			FROM (
			SELECT a.status AS passfail, a.exam_type, NULL AS sem_code, a.semester AS sem,a.session_yr 
			FROM final_semwise_marks_foil a
			WHERE a.admn_no=? and  a.course<>'MINOR' " . $s_replace . "			
			/*GROUP BY a.session_yr,a.session,a.semester,a.exam_type			*/
			ORDER BY a.session_yr desc  ,  a.semester DESC,   a.tot_cr_pts desc)B
			/*GROUP BY B.sem*/) 
			UNION (
			SELECT A.*
			FROM (
			SELECT a.passfail, a.examtype AS exam_type,a.sem_code, CAST(REVERSE(a.sem_code) AS UNSIGNED) AS sem,a.ysession as session_yr
			FROM tabulation1 a
			WHERE a.adm_no=? and a.sem_code not like 'PREP%' " . $s_replace_old . "		
			/*GROUP BY a.ysession,a.sem_code, a.examtype, a.wsms			*/
			ORDER BY a.ysession desc,sem DESC, a.wsms desc ,a.totcrpts desc ,a.examtype DESC)A
			/*GROUP BY A.sem_code*/)
			)z /*group by z.sem */
			)x
         ";



        $query = $this->db->query($sql, array($admn_no, $admn_no));
        //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
            return $query->row();
        else {
            return 0;
        }
    }
public function save_defaulter($data,$admn_no,$defaulter_type) {        
        $data1 = array(
            'session' => $data['session'] ,
            'session_yr' => $data['session_year'],
            'course' => $data['course_id'],
            'branch' =>  (strtolower($data['dept']) =='comm'? $data['section_name']:$data['branch_id'] ),
            'dept' =>  $data['dept'],
            'semester'=> $data['semester'],
            'exam_type' => $data['exm_type'],
            'admn_no'=>$admn_no,
            'defaulter_type'=>$defaulter_type ,
            'created_by'=>$this->session->userdata('id') ,
            /*'updated_by'=>$this->session->userdata('id')         */ 
         );
    
    //echo '<pre>'; print_r($data1);echo '</pre>'; die();
    
        if ($this->db->insert('exam_attd_exception', $data1)) {
            $returntmsg = "success";
            return $returntmsg;
        } else {
            $returntmsg = $this->db->_error_message();
            return $returntmsg;
        }
    }
}

?>
