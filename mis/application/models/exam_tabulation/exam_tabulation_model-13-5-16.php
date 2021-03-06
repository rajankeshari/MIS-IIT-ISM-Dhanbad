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

class Exam_tabulation_model
extends CI_Model
{
	
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
        function getCourseOfferedByDept($id){
            $q=$this->db->get_where($this->sem_subject,array('form_id'=>$id));
            if($q->num_rows() > 0){
                return true;
            }else{
                return false;
            }
        }
        function getCourseByDept(){
                if($this->input->post('exm_type')=="other" || $this->input->post('exm_type')=="spl"){
                  $and= "  and (b.course_id!='honour' and b.course_id!='minor') ";                  
              }else {
                  $and= "";                  
              }
            
            
           if($this->input->post('dept')!="comm"){
               
               
            
            
             $sql="select concat(x.course_id,'(',x.branch_id,')') as sheet_name ,x.course_id,cs_courses.duration from(
                      select  a.dept_id,upper(b.course_id) as course_id,b.branch_id  from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? and b.course_id!='capsule' ".$and."  and b.course_id!=?
                        group by b.course_id,b.branch_id)x
                         left join cs_courses on cs_courses.id=x.course_id";
             $secure_array=array($this->input->post('dept'),'comm');            
             $query = $this->db->query($sql, $secure_array);
            // echo $this->db->last_query();  die(); 
          if ($query->num_rows() > 0)
              return $query->result();
           else {
             return 0;
          }
        }else{
            
          //   echo 'section_id'. $this->input->post('section_name'); die();
               $sql="select concat(x.course_id,'(','".$this->input->post('section_name')."',')') as sheet_name ,x.course_id,cs_courses.duration from(
                      select  a.dept_id,upper(b.course_id) as course_id,b.branch_id  from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and b.course_id!='capsule' ".$and."   and b.course_id=?
                        group by b.course_id,b.branch_id)x
                         left join cs_courses on cs_courses.id=x.course_id";
             $secure_array=array('comm');            
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
      
      function getStudentHonours($branch,$sem,$admn_no=null){
        if( $admn_no !=null){
          $replacer1="hf1.admn_no=?  and ";
          $secure_array=array($admn_no,'1','Y',$this->input->post('dept'),$this->input->post('session_year'),5,$branch);             
        }
        else{
            $replacer1="";
            $secure_array=array('1','Y',$this->input->post('dept'),$this->input->post('session_year'),5,$branch);             
        }
         $sql="  
  select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name from
  (select hf1.admn_no from  hm_form hf1  where ".$replacer1."  hf1.honours=? and hf1.honour_hod_status=? and  hf1.dept_id=?  and session_year=? and  hf1.semester>=?)A
  inner join stu_academic on stu_academic.admn_no=A.admn_no and  stu_academic.branch_id=?
  inner join user_details ud on ud.id=A.admn_no 
  order by A.admn_no "; 
            
            $query = $this->db->query($sql, $secure_array);
   //        echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }
        
      function getStudentIncomingMinor($branch,$sem,$admn_no=null){
            $admn_no=preg_replace('/\s+/', '',$admn_no);
         if( $admn_no !=null){
             if(substr_count($admn_no, ',')>0){                               
                $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                $replacer1="  and hf2.admn_no in(".$admn_no.") ";                                         
                $secure_array=array('1','1','Y',$this->input->post('session_year'),5,$this->input->post('dept'),$branch);    
             }else{  
                $replacer1=" and hf2.admn_no=? ";
                $secure_array=array($admn_no,'1','1','Y',$this->input->post('session_year'),5,$this->input->post('dept'),$branch);    
             } 
           }
           else{
            $replacer1="";
           $secure_array=array('1','1','Y',$this->input->post('session_year'),5,$this->input->post('dept'),$branch);    
        }
          $sql="
                 select null as  both_status_string,
                       null AS both_status_string_old , b.name AS br_name,dpt.name as dept_name , A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,A.dept_id,  A.branch_id , A.semester
                 from 
                ( select hf2.semester ,hf2.admn_no,hf2.dept_id,hm_minor_details.dept_id as from_dept,branch_id from hm_form hf2  
                    inner join hm_minor_details on hm_minor_details.form_id=hf2.form_id 
                         ".$replacer1."  and hm_minor_details.offered=? and hf2.minor=? and hf2.minor_hod_status=? and hf2.session_year=? and hf2.semester=? 
								  and hm_minor_details.dept_id=?  and hm_minor_details.branch_id=?  
                    )A 
                      
                       inner join user_details ud on ud.id=A.admn_no                        
                       left join departments dpt on dpt.id =A.dept_id                       
                       LEFT join cs_branches b on b.id=A.branch_id
                       order by A.admn_no 		  
             ";
          
           
                     
            $query = $this->db->query($sql, $secure_array);
         // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
            }         
            
       function getStudentListCommon($session_yr,$session,$section,$admn_no=null){    
             $admn_no=preg_replace('/\s+/', '',$admn_no);
         if($admn_no==null){   
             if($section!='all' && $section!=null && $section!=""){
                $where= " and section=? ";
                 $secure_array=array($session_yr,$section);            
             }else{
               $where= "";
               $secure_array=array($session_yr);            
           }
          }
          else {
                 if(substr_count($admn_no, ',')>0){
                      $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                      if($section!='all' && $section!=null && $section!=""){
                        $where= " and section=?  and  admn_no in(".$admn_no.")";                        
                          $secure_array=array($session_yr,$section);            
                      }else{
                          $where=" and  admn_no in(".$admn_no.")";
                          $secure_array=array($session_yr);            
                        }
                    
                 }else{                  
                     if($section!='all' && $section!=null && $section!=""){
                        $where= " and section=? and  admn_no=? ";
                          $secure_array=array($session_yr,$section,$admn_no);            
                      }else{
                          $where= " and  admn_no=? ";
                          $secure_array=array($session_yr,$admn_no);            
                        }
                    }                   
               }
           $sql="select  null as  both_status_string,
                       null AS both_status_string_old ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name , A.admn_no from 
                   (select admn_no,section  from  stu_section_data where session_year=? ".$where.")A                    
                      inner join user_details ud on ud.id=A.admn_no join reg_regular_form r on r.admn_no=A.admn_no and r.`session`='".$session."' and r.hod_status='1' and r.acad_status='1' order by A.admn_no  ";                              
            $query = $this->db->query($sql, $secure_array);
      //     echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }
      
          function getPREPStudentList($admn_no=null){        
                $admn_no=preg_replace('/\s+/', '',$admn_no);
                  $yr=explode('-',$this->input->post('session_year'));                                                        
                    if($admn_no==null){
                        if($this->input->post('dept')!='all'){
                              $where2="";
                            $secure_array=array('prep',$yr[0],$this->input->post('dept'));                        
                        }else{
                            $where2="";
                            $secure_array=array('prep',$yr[0]);
                        }
                      }
                     else {
                         if(substr_count($admn_no, ',')>0){
                            $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                            $where2=" and admn_no in(".$admn_no.") ";                     
                            $secure_array=array('prep',$yr[0],$this->input->post('dept'));                        
                        }else{                  
                             $where2=" and admn_no=? "; 
                              $secure_array=array('prep',$yr[0],$admn_no);
                         }                   
                     } 
                      $sql="select A.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                  from                                   
                   (select admn_no  from stu_academic  where auth_id=? and  enrollment_year=?  ".$where2.") A
                   inner join user_details ud on ud.id=A.admn_no";
                  if($this->input->post('dept')!='all'){
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
      
      
      function getJRFStudentList($dept,$course_id,$branch_id,$admn_no=null){          
                  $table=" reg_exam_rc_form ";
                    $admn_no=preg_replace('/\s+/', '',$admn_no);
                   if($admn_no==null){
                    $where2="";   
                   //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
                   $secure_array=array($this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,'2','2',$dept);
                   }
                   else {
                     if(substr_count($admn_no, ',')>0){
                      $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                     $where2=" and admn_no in(".$admn_no.") ";                     
                     //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$dept);
                     $secure_array=array($this->input->post('session'),$this->input->post('session_year'),$course_id,$branch_id,'2','2',$dept);
                     }else{                  
                     $where2=" and admn_no=? ";
                     //$secure_array=array($this->input->post('session'),$this->input->post('session_year'),'1','1',$admn_no,$course_id,$branch_id,$dept);
                     $secure_array=array($this->input->post('session'),$this->input->post('session_year'),$admn_no,$course_id,$branch_id,'2','2',$dept);
                    }
                   
                  } 
         /*   $sql="select  distinct(B.admn_no),concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                  from                                   
                   (select admn_no from   ".$table."  where  session=?  and session_year=?  and hod_status=? and  acad_status=?  ".$where2."
                   and upper(course_id)=? and upper(branch_id)=?  )B                   
                   inner join user_details ud on ud.id=B.admn_no   and dept_id=?                                    
                   order by B.admn_no
                   ";            
           */    
             $sql=" select x.*,both_status_string, group_concat( (select s.subject_id from subjects s  where s.id= rexs.sub_id))  as jrf_subject_list from
		    (select B.admn_no,B.form_id, CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,IF((B.hod_status='1' AND B.acad_status='1') ,'',' Appv. Pending') as  both_status_string ,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                  from                                   
                   (select admn_no ,form_id, hod_status,acad_status from   ".$table."  where  session=?  and session_year=? 
                   and upper(course_id)=? and upper(branch_id)=? and hod_status<>? and  acad_status<>? ".$where2." )B                   
                   inner join user_details ud on ud.id=B.admn_no   and dept_id=?                                    
                   )x
                  inner join reg_exam_rc_subject rexs on rexs.form_id=x.form_id group by  rexs.form_id order by x.admn_no
                   ";         
            
            
             $query = $this->db->query($sql, $secure_array);
            // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }
      
      
        function getStudentList($dept,$course_id,$branch_id,$sem,$admn_no=null){
              $admn_no=preg_replace('/\s+/', '',$admn_no);
              if($this->input->post('exm_type')=="other"){
                  $where= "and  semester like '%?%' and type='R'";
                  $where3= "and  semester like '%?%'";
                  $table=" reg_exam_rc_form ";
                  $table2=" reg_other_form ";
                  if($admn_no==null){
                  //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                  $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'2','2',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'2','2',$course_id,$branch_id,(int)$sem);
                  $where2="";
                  }else{
                  if(substr_count($admn_no, ',')>0){
                       $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                       $where2=" and admn_no in (".$admn_no.") ";                    
                     //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);
                     $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$this->input->post('session'),$this->input->post('session_year'),'2','2',$course_id,$branch_id,(int)$sem);
                  }else{                      
                  $where2=" and admn_no=? ";
                  //$secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no);
                  $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no,$this->input->post('session'),$this->input->post('session_year'),'2','2',$course_id,$branch_id,(int)$sem,$admn_no);
                  }
                  
                }
                  $sql="select B.admn_no,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name 
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (
                      (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   ".$table2."  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  ".$where3."   ".$where2."  ) 
                       union
                   (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   ".$table."  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  ".$where."   ".$where2."  ) 
                       
                   
                    )B on A.course_id=B.course_id  and A.branch_id=B.branch_id  
                   left join user_details ud on ud.id=B.admn_no                    
                   order by B.admn_no
                   ";            
             }else if($this->input->post('exm_type')=="spl"){
                  $where= "and  semester like '%?%'  and type='S'";
                  $table=" reg_exam_rc_form ";
                  if($admn_no==null){
                      $where2="";
                  $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);             
                  }else {
                     if(substr_count($admn_no, ',')>0){
                      $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                     $where2=" and admn_no in(".$admn_no.") ";                     
                     $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem);                             
                     }else{                  
                  $where2=" and admn_no=? ";
                  $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,(int)$sem,$admn_no);                             
                    }
                   
                  }
                    $sql="select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name ,IF((B.hod_status='1' AND B.acad_status='1') ,'','Appv. Pending') as  both_status_string,CONCAT_WS( ',',(IF((B.hod_status='1'),'HOD-A','HOD-P') ),(IF((B.acad_status='1'),'ACD-A','ACD-P') ) )AS both_status_string_old 
                   from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status  from   ".$table."  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  ".$where."   ".$where2."   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id  
                   left join user_details ud on ud.id=B.admn_no                    
                   order by B.admn_no
                   ";            
              }else if($this->input->post('exm_type')=="regular"){
                  $where= "and  semester=? ";
                  $table=" reg_regular_form ";
                  if($admn_no==null){
                      $where2="";
                  $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$sem);
                  }
                  else{
                    if(substr_count($admn_no, ',')>0){                               
                     $admn_no="'". implode("','", explode(',', $admn_no)) ."'";
                     $where2=" and admn_no in(".$admn_no.") ";                                         
                     $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$sem);   
                     }else{                  
                      $where2=" and admn_no=? ";
                      $secure_array=array($dept,$this->input->post('session'),$this->input->post('session_year'),'1','1',$course_id,$branch_id,$sem,$admn_no);   
                    }  
                   
                   
                  }
                    $sql="select B.admn_no,concat_ws(' ',ud.first_name,ud.middle_name,ud.last_name) as st_name, null as  both_status_string,
                       null AS both_status_string_old 
                    from
                  (select course_id,branch_id from dept_course a  join course_branch b  on a.course_branch_id=b.course_branch_id  and a.dept_id=? group by b.course_id,b.branch_id)A
                   inner join
                  (select admn_no,course_id,branch_id ,semester,hod_status,acad_status from   ".$table."  where  session=?  and session_year=? and hod_status=? and  acad_status=?
                   and upper(course_id)=? and branch_id=?  ".$where."   ".$where2."   )B on A.course_id=B.course_id  and A.branch_id=B.branch_id  
                   left join user_details ud on ud.id=B.admn_no                    
                   order by B.admn_no
                   ";            
              }
          
                        
             $query = $this->db->query($sql, $secure_array);
            //echo $this->db->last_query();     die()     ;

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }
      
      function getSubjectsByAdminNoFrom_tabulation($branch, $course_id,$sem,$admn_no){
         /* $p=  explode('-', $this->input->post('session_year'));
          $a=  substr($p[0],-2)-1;
          $b= substr($p[1],-2)-1;
          * */
          
          //echo $a;echo $b;
            /*$secure_array=array($admn_no,$a.$b,'S',
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
             $secure_array=array($admn_no,
                               ($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")),
                                $this->input->post('dept'),$course_id,$branch,$sem);                               
            
            
             $sql=" select tb.examtype,  tb.subje_name,   tb.gpa, tb.ogpa,tb.totcrhr,tb.totcrpts as totalcreditpoint, tb.ctotcrhr, tb.ctotcrpts,  if((tb.theory=0 and tb.practiocal=0 and tb.sessional=0),'Practicle','Theory') as type,
							  tb.subje_ftsp, tb.subje_code  as  sub_code ,tb.ltp as LTP,tb.sessional,tb.theory,tb.practiocal as practical,tb.grade,tb.crpts ,tb.totalmarks as total, tb.crdhrs as credit_hours from  tabulation1 tb where tb.adm_no=?  and tb.wsms=? and  tb.sem_code=  
                  (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)  group by tb.examtype,tb.subje_code order by tb.examtype desc,tb.subje_code ";
           
            

           
          $query = $this->db->query($sql, $secure_array);
     // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
          
      }
      
       function getCummulativeFromFoil($dept,$branch, $data,$sem,$admn_no){
               $secure_array=array($admn_no, $dept,$data['course_id'],$branch,$sem);                                           
               $sql="   select *  from  final_semwise_marks_foil tb where tb.admn_no=? and tb.dept=?  and  tb.course =? and  tb.branch=? and tb.semester=?   group by tb.exam_type,tb.session,tb.semester order by tb.exam_type desc,tb.session desc limit 1 ";                                
              $query = $this->db->query($sql, $secure_array);
           //    echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
           return $query->row();
        else {
            return false;
        }
      }   
      
          function getCummulativeFromTabulation1($branch, $course_id,$sem,$admn_no){
           $secure_array=array($admn_no,
                               /*($this->input->post('session')=='Monsoon'?'MS':($this->input->post('session')=='Winter'?'WS':($this->input->post('session')=='Summer')?'SS':"")),*/
                                $this->input->post('dept'),$course_id,$branch,$sem);                               
            
            
             $sql="    select tb.totcrpts, tb.examtype,  tb.ctotcrpts,tb.ctotcrhr  from  tabulation1 tb where tb.adm_no=?   and  tb.sem_code=  
                       (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)  group by tb.examtype,tb.wsms,tb.sem_code order by tb.examtype desc,tb.wsms desc limit 1 ";           
                     
          $query = $this->db->query($sql, $secure_array);
//          echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->row();
        else {
            return 0;
        }
          
      }
      
      
       function get_grade_point($tot){
           $secure_array=array($tot);
           $sql=" select gp.grade  from  grade_points gp  where ? between gp.min and gp.max";
               $query = $this->db->query($sql, $secure_array);
      // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->row()->grade;
        else {
            return 0;
        }
      
       }
       
       function get_grade_pt($tot){
           $secure_array=array($tot);
           $sql=" select gp.points  from  grade_points gp  where ? between gp.min and gp.max";
               $query = $this->db->query($sql, $secure_array);
      // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->row()->points;
        else {
            return 0;
        }
      
       }
        /*function  get_sub_map_id($session,$session_year,$type,$mis_sub_code){
        
     $sql="select sub_map_id  from marks_master m1 where m1.`session`=? and m1.session_year=? and m1.`type`=? and m1.subject_id=?) ";
  
       $query = $this->db->query($sql,array($admn_no,$session,$session_year,$type,$mis_sub_code));
        
     //    echo $this->db->last_query(); die();
             
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return FALSE;
        }   
    }*/
    
       
    // check  whether  student  be  the  case  of other 
       function student_belongs_to_other($dept,$sess_yr,$session,$branch_id,$crs_id,$sem,$admn_no,$type=null){
            $select = $this->db->select('admn_no')->where(
                                                        array('session_yr' => $sess_yr, 'session' => $session, 'dept' => $dept, 
                                                              'course' => $crs_id, 'branch' => $branch_id, 'semester' => $sem, 'admn_no' => $admn_no, 'type' => $type,'status'=>'FAIL'))
                                                      ->order_by('exam_type', 'desc')->limit('1')->get('final_semwise_marks_foil');
   //  echo $this->db->last_query(); die();
             if ($select->num_rows())
                   return true;
               else
                 return false;
           } 
      // check  whether  student  be  the  case  of reapaeator 
       function student_belongs_to_repeater($dept,$sess_yr,$session,$branch_id,$crs_id,$sem,$admn_no){
        $secure_array=array($admn_no,'R',$this->input->post('dept'),$crs_id,$branch_id,$sem);                               
            
            
             $sql="    select  tb.totcrpts, tb.examtype,  tb.ctotcrpts,tb.ctotcrhr from  tabulation1 tb where tb.adm_no=?   and  tb.examtype=? and  tb.sem_code=  
                       (select d.semcode from dip_m_semcode d  where d.deptmis=? and d.course=? and  d.branch=? and d.sem=?)  
                        group by tb.examtype,tb.wsms,tb.sem_code order by tb.examtype desc,tb.wsms desc limit 1 ";           
                     
          $query = $this->db->query($sql, $secure_array);
      //    echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->row();
        else {
            return false;
        }
           }     
      function getSubjectsByAdminNo_Spl($branch_id,$sem,$admn_no,$type=null){                 
             $secure_array=array($admn_no,$this->input->post('session_year'),$type,$this->input->post('dept'),$sem);                                    
           
     $sql=" 
     select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
    (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
    (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session_year=? and b.type=?  and  b.status='Y' ) A 
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C 
     inner join subject_mapping as e on C.sub_map_id = e.map_id where e.dept_id=? and e.semester=? 
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     "; 
            $query = $this->db->query($sql, $secure_array);
     // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }
      
    function getSubjectsByAdminNo_Other($dept,$crs,$branch_id,$sem,$admn_no,$sess_yr,$sess,$type){                 
             $secure_array=array($dept,$crs,$branch_id,$sem,$admn_no,$sess_yr,$sess,$type);                                               
  /*   $sql=" 
     select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
    (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
    (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session_year=? and b.type=?  and  a.grade='F')A 
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C 
     inner join subject_mapping as e on C.sub_map_id = e.map_id where e.dept_id=? and e.semester=? 
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     
     "; 
   * */
   
$sql=" select null as stu_status, null as sub_map_id, grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.*  from
(select C.* from (select B.*,d.sequence as seq from
(select A.*,c.id as sub_id,c.name,c.credit_hours ,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from    
(select b.dept,b.course  as course_id,b.branch as branch_id ,b.semester, null as  stu_status,a.theory,a.sessional,a.total,a.grade,b.tot_cr_pts,b.tot_cr_hr,a.mis_sub_id as subject_id,b.`session`,b.session_yr,a.mis_sub_id  from final_semwise_marks_foil_desc as a
 inner join final_semwise_marks_foil  as b on  b.id=a.foil_id AND a.admn_no=b.admn_no  and b.dept=?  and b.course=? and b.branch=? and b.semester=? and  b.admn_no=? and b.session_yr=? and b.session=? and b.type=? )A         
 inner join subjects as c on A.mis_sub_id=c.id ) B inner join course_structure as d on B.mis_sub_id=d.id ) C 
 group by C.sub_code order by C.semester,C.seq asc )grp
 left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc ";

/*( SELECT a.admn_no, a.id, a.tot_cr_hr AS core_crdthr,a.tot_cr_pts AS core_tot
              FROM final_semwise_marks_foil a
                WHERE a.session_yr=? AND a.`session`=? AND a.dept=? AND a.course=? AND a.branch=? AND a.semester=? AND a.`type`=?  " . $where_add2." )G
INNER JOIN final_semwise_marks_foil_desc b ON G.id=b.foil_id AND G.admn_no=b.admn_no 
group by b.foil_id  order by b.admn_no ";
*/
     
            $query = $this->db->query($sql, $secure_array);
  //   echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
      }  
      

      
           
       
      
       function getSubjectsByAdminNo($branch_id,$sem,$admn_no,$type=null){
           
           if($type=='O' || $type=='S'){
               $secure_array=array($admn_no,$this->input->post('session_year'),$type,$this->input->post('dept'),$sem);                               
      
           
           $sql=" 
   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.* from
   (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session_year=? and b.type=?  and  b.status='Y' ) A 
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C 
     inner join subject_mapping as e on C.sub_map_id = e.map_id where e.dept_id=? and e.semester=? 
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     "; 
           }
           
           
           else  if(strtoupper($branch_id)!='JRF')
           {
               $secure_array=array($admn_no,$this->input->post('session_year'),'R',$this->input->post('dept'),$sem);                               
      
           
           $sql=" 
   select grade_points.points ,  (grp.credit_hours*grade_points.points)  as totcrdthr ,grp.*from
   (select C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group` from (select B.*,d.sequence as seq from
   (select A.*,c.id as sub_id,c.name,c.credit_hours,c.`type`,c.subject_id as sub_code , concat(c.lecture,'-',c.tutorial,'-',c.practical) as LTP from
    (select a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id from marks_subject_description as a
     inner join marks_master as b on a.marks_master_id=b.id where a.admn_no=? and b.session_year=?  and b.type=? and  b.status='Y' ) A 
     inner join subjects as c on A.subject_id=c.id ) B inner join course_structure as d on B.subject_id=d.id ) C 
     inner join subject_mapping as e on C.sub_map_id = e.map_id where e.dept_id=? and e.semester=? 
     group by C.sub_code order by e.semester,C.seq asc )grp
     left join grade_points on grade_points.grade=grp.grade  order by grp.semester,grp.seq asc
     ";
           }
           
          /* else{
                $secure_array=array($admn_no,$this->input->post('session_year'),'J');                                
                 $sql=" 
 SELECT grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
FROM (
SELECT C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group`
FROM (
SELECT B.*
FROM (
SELECT A.*,c.name,c.credit_hours,c.`type` ,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
FROM (
SELECT a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id
FROM marks_subject_description AS a
INNER JOIN marks_master AS b ON a.marks_master_id=b.id
WHERE a.admn_no=? AND b.session_year=? and b.type=? and  b.status='Y') A
INNER JOIN subjects AS c ON A.subject_id=c.id ) B
) C
INNER JOIN subject_mapping AS e ON C.sub_map_id = e.map_id

GROUP BY C.sub_code
)grp
LEFT JOIN grade_points ON grade_points.grade=grp.grade
ORDER BY grp.semester ASC

     ";  
           }   */
              else{
                $secure_array=array($admn_no,$this->input->post('session_year'),$this->input->post('session'),'J');                                
                 $sql=" 
 SELECT grade_points.points, (grp.credit_hours*grade_points.points) AS totcrdthr,grp.*
FROM (
SELECT C.*,e.dept_id,e.aggr_id,e.course_id,e.branch_id,e.semester,e.`group`
FROM (
SELECT B.*
FROM (
SELECT A.*,c.name,c.credit_hours,c.`type` ,c.subject_id AS sub_code, CONCAT(c.lecture,'-',c.tutorial,'-',c.practical) AS LTP
FROM (
SELECT a.stu_status,a.theory,a.sessional,a.practical,a.total,a.grade,b.subject_id,b.`session`,b.session_year,b.sub_map_id
FROM marks_subject_description AS a
INNER JOIN marks_master AS b ON a.marks_master_id=b.id
WHERE a.admn_no=? AND b.session_year=? and   b.session=? and b.type=? and  b.status='Y') A
INNER JOIN subjects AS c ON A.subject_id=c.id ) B
) C
INNER JOIN subject_mapping AS e ON C.sub_map_id = e.map_id

GROUP BY C.sub_code
)grp
LEFT JOIN grade_points ON grade_points.grade=grp.grade
ORDER BY grp.semester ASC

     ";  
           }   
          $query = $this->db->query($sql, $secure_array);
   //   echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->result();
        else {
            return 0;
        }
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
      }*/
	    function getSubMst($id){
			$data=array();
               $sql=" 
 select b.subject_id, b.name,a.sub_id from  subject_mapping_des as a  inner join  subjects b on a.sub_id = b.id  where  a.map_id=? group by a.sub_id
     ";
           
           
          $query = $this->db->query($sql, array($id));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0){
         $r= $query->result();
	 $i=0;
			foreach($r as $p){
				$data[$i]['subject_id'] = $p->subject_id;
				$data[$i]['name'] = $p->name;
				$data[$i]['max'] = "Max ( ".$this->getMaxMarks($p->sub_id)." ) ";
			$i++;}
			//print_r($data); die();
			return $data;
        }else {
            return 0;
        }
      }
      
      function totalCrbyId($id){
 /*              $sql=" 
 select sum(b.credit_hours) as `total_cr` from  subject_mapping_des as a  inner join  subjects b on a.sub_id = b.id  where  a.map_id=?
     ";
   */        $sql="select sum(A.credit_hours) as total_cr from (select c.* from subject_mapping as a 
inner join subject_mapping_des as d on a.map_id =d.map_id
inner join course_structure as b on d.sub_id=b.id and a.semester=b.semester
inner join subjects as c on b.id=c.id
where a.map_id=? and a.`session`=? and a.session_year=?
group by floor(b.sequence)) A";
           
          $query = $this->db->query($sql, array($id,$this->input->post('session'),$this->input->post('session_year')));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
           return $query->row()->total_cr;
        else {
            return 0;
        }
      }
        
     
      function getOGPA($admn_no,$sem){
          $sql="select ogpa,passfail,examtype from resultdata where admn_no=? and RIGHT(sem_code,1)=? order by passfail desc limit 1";
         $query = $this->db->query($sql, array($admn_no,($sem-1)));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
          return $query->result();
        else {
            return 0;
        }
      }
        
		
	function getMaxMarks($id){
		$sql = "select (case when max(b.total) is null then '0' else max(b.total) end) as total from marks_master as a 
join  marks_subject_description as b on a.id=b.marks_master_id
where a.subject_id=?";

 $query = $this->db->query($sql, array($id));
       // echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
          return $query->row()->total;
        else {
            return 0;
        }

	}
        
        function save_excel_output($unmatched,$admn_no,$branch_id,$course_id,$sem,$sum_totcrdthr,$sum_totcrdpts_final,$gpa,$cgpa,$status){
        date_default_timezone_set("Asia/Calcutta");
        $returntmsg = "";    
         $this->db->trans_start(); 
        $data= array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'tot_cr_hr'    => $sum_totcrdthr,
                      'tot_cr_pts'   =>   $sum_totcrdpts_final,
                      'gpa'   =>$gpa,
                      'cgpa'  =>$cgpa,
                      'status' => $status,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='spl'?'S':($this->input->post('exm_type')=='jrf'?'J':'R')))
            );
        
      
        
        $select = $this->db->select('gpa,id')->where(
                array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='spl'?'S':($this->input->post('exm_type')=='jrf'?'J':'R')))
                     )
                )->get('final_semwise_marks_foil');
            
       //  echo $this->db->last_query(); die();
                 
        
          if ($select->num_rows()){
               $row = $select->row();
          //     print_r($row);die();
             
           /* $row = $select->row();   
             
            if($row->gpa!=$gpa){                                  
            $this->db->where(   
                       array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='jrf'?'J':'R'))
                      )
                   );
               $this->db->update('final_semwise_marks_foil', $data);
               
               $this->db->where(   
                       array('foil_id'=>$row->id,	              
                             'admn_no' =>$admn_no                      
                            )
                   );
                   $k=0;                                
                   while($k<count($unmatched)){
                    $unmatched[$k]['foil_id']=$row->id;
                   // $unmatched[$k]['admn_no']=$admn_no;
                    $this->update_des($unmatched[$k]);
                    $k++;
                   }                
            
              
          }else{
                $this->db->insert('final_semwise_marks_foil', $data);
                $j=0;
                $last_insert_id= $this->db->insert_id();
                if($last_insert_id){
                   while($j<count($unmatched)){
                    $unmatched[$j]['foil_id']=$last_insert_id;
                    $j++;
                   }                
                 
                   $this->insert_batch_des($unmatched);
                } 
                
          }  
           */ 
        } else { 
            
               $this->db->insert('final_semwise_marks_foil', $data);
                $j=0;
                $last_insert_id= $this->db->insert_id();
                if($last_insert_id){
                    $temp =array();
                   while($j<count($unmatched)){
                    $unmatched[$j]['foil_id']=$last_insert_id;
                    
                    
                    $j++;
                   }                
               //  echo '<pre>';
            //print_r($unmatched); echo '</pre>'; 
            
                   $this->insert_batch_des($unmatched);
                } 
           }
        
      
          $this->db->trans_complete();
           
          
            if ($this->db->trans_status() != FALSE) {
                $returntmsg = "success";
               
            }
            else{
                $returntmsg .= "Error while Inserting/updating: ".$this->db->_error_message() . ",";
            }
         
         return $returntmsg;
             
        }
        
          function insert_batch_des($data){
               $this->db->insert_batch('final_semwise_marks_foil_desc', $data);                    
               }
               function update_des($data){
               $this->db->update('final_semwise_marks_foil_desc', $data);                    
               }
                
    
               
          function save_excel_output_spl($unmatched,$admn_no,$h_status,$branch_id,$course_id,$sem,$sum_totcrdthr,$sum_totcrdpts_final,$sum_core_totcrdthr,$sum_core_totcrdpts_final,$ccrpts,$ccrdthr,$core_ccrpts,$core_ccrdthr,$gpa,$core_gpa,$cgpa,$core_cgpa,$status,$core_status,$exm_type,$repeator){
          date_default_timezone_set("Asia/Calcutta");
          $returntmsg = "";    
           $this->db->trans_start(); 
           $data= array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'tot_cr_hr'    => $sum_totcrdthr,
                      'tot_cr_pts'   =>   $sum_totcrdpts_final,
                      'core_tot_cr_hr'    => $sum_core_totcrdthr,
                      'core_tot_cr_pts'   =>   $sum_core_totcrdpts_final,
                      'ctotcrpts' =>$ccrpts,
                      'ctotcrhr'=>$ccrdthr,
                      'core_ctotcrpts' =>$core_ccrpts,
                      'core_ctotcrhr'=>$core_ccrdthr,
                      'gpa'   =>$gpa,
                      'core_gpa'   =>$core_gpa,
                      'cgpa'  =>$cgpa,
                      'core_cgpa'  =>$core_cgpa,
                      'status' => $status,
                      'core_status' => $core_status,
                      'hstatus'=>$h_status,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='spl'?'S':($this->input->post('exm_type')=='jrf'?'J':'R'))),
                      'exam_type'  => $exm_type,
            );
           if($this->input->post('exm_tpye')=='regular'){
               $data[]=array('repeator'=>$repeator);
           }
      
        
        $select = $this->db->select('gpa,id')->where(
                array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='spl'?'S':($this->input->post('exm_type')=='jrf'?'J':'R')))
                     )
                )->get('final_semwise_marks_foil');
            
       //  echo $this->db->last_query(); die();
                 
        
          if ($select->num_rows()){
               $row = $select->row();
          //     print_r($row);die();
             
           /* $row = $select->row();   
             
            if($row->gpa!=$gpa){                                  
            $this->db->where(   
                       array('session_yr'=>$this->input->post('session_year'),
	              'session'   => $this->input->post('session'),                      
                      'dept'=>  $this->input->post('dept'),
                      'course'=>$course_id,
                      'branch'=>$branch_id,                      
                      'semester' =>$sem,
                      'admn_no' =>$admn_no,
                      'type' =>  ($this->input->post('exm_type')=='other'?'O':($this->input->post('exm_type')=='jrf'?'J':'R'))
                      )
                   );
               $this->db->update('final_semwise_marks_foil', $data);
               
               $this->db->where(   
                       array('foil_id'=>$row->id,	              
                             'admn_no' =>$admn_no                      
                            )
                   );
                   $k=0;                                
                   while($k<count($unmatched)){
                    $unmatched[$k]['foil_id']=$row->id;
                   // $unmatched[$k]['admn_no']=$admn_no;
                    $this->update_des($unmatched[$k]);
                    $k++;
                   }                
            
              
          }else{
                $this->db->insert('final_semwise_marks_foil', $data);
                $j=0;
                $last_insert_id= $this->db->insert_id();
                if($last_insert_id){
                   while($j<count($unmatched)){
                    $unmatched[$j]['foil_id']=$last_insert_id;
                    $j++;
                   }                
                 
                   $this->insert_batch_des($unmatched);
                } 
                
          }  
           */ 
        } else { 
               
                $this->db->insert('final_semwise_marks_foil', $data);
                /* echo '<pre>';
                 print_r($data); 
                 echo '</pre>'; */
                $j=0;
                $last_insert_id= $this->db->insert_id();
                if($last_insert_id){
                    $temp =array();
                   while($j<count($unmatched)){
                    $unmatched[$j]['foil_id']=$last_insert_id;
                    
                    
                    $j++;
                   }                
              /*   echo '<pre>';
                 print_r($unmatched); 
                 echo '</pre>'; 
            */
                  $this->insert_batch_des($unmatched);
                } 
           }
        
      
          $this->db->trans_complete();
           
          
            if ($this->db->trans_status() != FALSE) {
                $returntmsg = "success";
               
            }
            else{
                $returntmsg .= "Error while Inserting/updating: ".$this->db->_error_message() . ",";
            }
         
         return $returntmsg;
             
        }        
                
        
        
        function  get_semesterList_of_registration($branch,$admno,$data,$sem){
           $sem_string="";
           $reg_data_array=array(     'session_year'=>$this->input->post('session_year'),
	                          'session'   => $this->input->post('session'),                                                        
                                  'course_id'=>$data['course_id'],
                                  'branch_id'=>$branch,                      
                                  'semester' =>$sem,
                                  'admn_no' =>$admno
                                  );       
            
            $other_data_array=array(     'session_year'=>$this->input->post('session_year'),
	                          'session'   => $this->input->post('session'),                                                        
                                  'course_id'=>$data['course_id'],
                                  'branch_id'=>$branch,                      
                                  'semester' =>$sem,
                                  'admn_no' =>$admno,
                                  'type'=>'O'
                                  );     
            $spl_data_array=array(     'session_year'=>$this->input->post('session_year'),
	                          'session'   => $this->input->post('session'),                                                        
                                  'course_id'=>$data['course_id'],
                                  'branch_id'=>$branch,                      
                                  'semester' =>$sem,
                                  'admn_no' =>$admno,
                                  'type'=>'S'
                                  );        
        $select_special_sem = $this->db->select('semester')->where($spl_data_array)->get('reg_exam_rc_form');         
        
         //print_r($select_special_sem); die();
        if ($select_special_sem->num_rows()){
            $select_special_sem=$select_special_sem->row();
          if(substr_count($select_special_sem->semester, ',')>0)
             $sem_string="'". implode("','", explode(',', $select_special_sem->semester)) ."'";
          else 
             $sem_string= ($sem_string==""?$select_special_sem->semester:  ("','".$select_special_sem->semester));
        }
         $select_other_sem = $this->db->select('semester')->where($other_data_array)->get('reg_exam_rc_form');
         if ($select_other_sem->num_rows()){
                $select_other_sem=$select_other_sem->row();
         
          if(substr_count($select_other_sem->semester, ',')>0)
             $sem_string="'". implode("','", explode(',', $select_other_sem->semester)) ."'";
           else 
              $sem_string=($sem_string==""?$select_other_sem->semester:  ("','".$select_other_sem->semester));
         } 
          $select_regular_sem = $this->db->select('semester')->where($reg_data_array)->get('reg_regular_form');         
          if($select_regular_sem->num_rows()){
             $select_regular_sem=$select_regular_sem->row();                      
               $sem_string=($sem_string==""?$select_regular_sem->semester:  ("','".$select_regular_sem->semester));                                                     
          }
          
           return $sem_string;     
      }
      
      function cumm_OGPA_status($admn_no,$h_status){
            if($h_status=='Y') $status="status"; else $status="core_status"; 
          $sql="
select SUM(IF (( trim(x.passfail='F') or   trim(x.passfail)='FAIL'  or    trim( x.passfail)='fail'), 1, 0)) AS count_status,
group_concat( 
if((trim(x.passfail)='F' or   trim(x.passfail)='FAIL'  or     trim(x.passfail)='fail'),  concat(x.sem, 'th','-','INC')  ,null) separator ', ') as incstr
 from

(
(select B.* from 
(select a.".$status." as passfail, a.exam_type, null as sem_code, a.semester as sem from  final_semwise_marks_foil a where a.admn_no=?  group by 
 a.admn_no,a.semester,a.exam_type 
order by  a.semester desc, a.exam_type desc )B
 group by B.sem)
 
union

(select A.* from 
(select a.passfail, a.examtype as exam_type,a.sem_code, CAST(REVERSE(a.sem_code ) AS UNSIGNED) as sem from tabulation1 a where a.adm_no=?  group by 
a.sem_code, 
a.examtype,
a.wsms 
order by sem desc, a.examtype desc )A
group by A.sem_code) 
)x

 ";
          
         $query = $this->db->query($sql, array($admn_no,$admn_no));       
         //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
           return $query->row();
        else {
            return 0;
        }
       }
      
      
      
       function cummulative_OGPA_STATUSFromTabulation1($admn_no){
          $sql="select SUM(IF (( z.passfail='F'), 1, 0)) AS count_status from
(select A.* from 
(select a.passfail, a.examtype,a.sem_code, CAST(REVERSE(a.sem_code ) AS UNSIGNED) as  latestsem from tabulation1 a where a.adm_no=?  group by 
a.adm_no,
a.sem_code, 
a.examtype,
a.wsms 
order by  CAST(REVERSE(a.sem_code ) AS UNSIGNED) desc, a.examtype desc )A
 group by A.sem_code)z " ;
          
           $query = $this->db->query($sql, array($admn_no));       
         //  echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
           return $query->row()->count_status;
        else {
            return 0;
        }
       }
       
   function cummulative_OGPA_STATUSFromMIS($admn_no,$h_status){
   if($h_status=='Y') $status="status"; else $status="core_status"; 
  $sql="
select SUM(IF (( z.".$status."='FAIL'), 1, 0)) AS count_status from
(select A.* from 
(select a.".$status.", a.exam_type,a.course,a.branch ,a.semester from  final_semwise_marks_foil a where a.admn_no=?  group by 
a.admn_no,a.course,a.branch ,a.semester,a.exam_type 
order by  a.semester desc, a.exam_type desc )A
group by A.course,A.branch ,A.semester)z 
 " ;
  
          
           $query = $this->db->query($sql, array($admn_no));              
           echo $this->db->last_query(); die();
        if ($query->num_rows() > 0)
           return $query->row()->count_status;
        else {
            return 0;
        }
       }
      
      
      function getLatestSemesterFromOldDatabase($admno){
          
         $sql="select  CAST(REVERSE(a.sem_code ) AS UNSIGNED) as  latestsem from tabulation1 a where a.adm_no=?  group by a.adm_no,a.sem_code  order by  CAST(REVERSE(a.sem_code ) AS UNSIGNED) desc  limit 1" ;
         $query = $this->db->query($sql, array($admno));              
        if ($query->num_rows() > 0)
           return $query->row()->latestsem;
        else {
            return 0;
        }
      }
      
     function getStudentStatusFromOldDatabase($admno){           
         $sql=" select  passfail  from tabulation1  where adm_no=?  group by examtype,wsms,sem_code order by examtype desc,wsms desc limit 1" ;
         $query = $this->db->query($sql, array($admno));              
          if ($query->num_rows() > 0)
           return $query->row()->passfail;
        else {
            return 0;
        }
     }
          function  getStudentStatusFromMIS($admno){  
              $sql=" select  passfail  from final_semwise_marks_foil  where adm_no=?  group by exam_type,wsms,sem_code order by examtype desc,wsms desc limit 1" ;
         $query = $this->db->query($sql, array($admno));              
          if ($query->num_rows() > 0)
           return $query->row()->passfail;
        else {
            return 0;
        }  
          }
        
}
?>
