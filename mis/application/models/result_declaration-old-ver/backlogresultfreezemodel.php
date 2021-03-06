<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Manage All Partial data in freeze Table get old data into freeze tables....... 

class Backlogresultfreezemodel extends CI_Model {
      
  /*  protected function InsertPartialintoFreezedTbl() {
        $q = " INSERT
  INTO `final_semwise_marks_foil_freezed`
  (`old_id`, `session_yr`, `session`, `dept`, `course`, `branch`, `semester`, `admn_no`, `tot_cr_hr`, `tot_cr_pts`,`core_tot_cr_hr` ,`core_tot_cr_pts`,`ctotcrpts`, `core_ctotcrpts`, `ctotcrhr`, `core_ctotcrhr`, `gpa`, `core_gpa`,`cgpa` ,`core_cgpa`, `status`, `core_status`, `hstatus`, `repeater`, `type`, `exam_type`, `final_status`, `published_on`, `actual_published_on`, `result_dec_id`)
  SELECT
    a.`id`,
    a.`session_yr`,
    a.`session`,
    a.`dept`,
    a.`course`,
    a.`branch`,
    a.`semester`,
    a.`admn_no`,
    a.`tot_cr_hr`,
    a.`tot_cr_pts`,
    a.`core_tot_cr_hr`,
    a.`core_tot_cr_pts`,
    a.`ctotcrpts`,
    a.`core_ctotcrpts`,
    a.`ctotcrhr`,
    a.`core_ctotcrhr`,
    a.`gpa`,
    a.`core_gpa`,
    a.`cgpa` ,
    a.`core_cgpa`,
    a.`status`,
    a.`core_status`,
    a.`hstatus`,
    a.`repeater`,
    a.`type`,
    a.`exam_type`,
    a.`final_status`,
    x.`published_on`,
    x.`actual_published_on`,
    x.`res_dec_id`
  FROM
    `final_semwise_marks_foil` a
      JOIN `result_declaration_log` b
        ON
          a.`session_yr` = b.`s_year`
            AND a.`session` = b.`session`
            AND a.`dept` = b.`dept_id`
            AND a.`course` = b.`course_id`
            AND a.`branch` = (CASE WHEN UPPER(a.`course`) = 'COMM' THEN LOWER(b.`section`) ELSE LOWER(b.`branch_id`) END)
            AND a.`semester` = (CASE WHEN UPPER(a.`course`) = 'JRF' THEN '0' WHEN UPPER(a.`course`) = 'PREP' THEN '-1' ELSE b.`semester` END)
            AND (CASE
              WHEN a.`type` = 'R' THEN (CASE WHEN (UPPER(a.`course`) = 'JRF' && a.`session` = 'Winter') THEN b.`exam_type` = 'jrf_spl' WHEN UPPER(a.`course`) = 'PREP' THEN b.`exam_type` = 'prep' ELSE b.`exam_type` = 'regular' END)
              WHEN (a.`type` = 'S') THEN b.`exam_type` = 'spl'
              WHEN (a.`type` = 'O') THEN b.`exam_type` = 'other'
              WHEN (a.`type` = 'E') THEN b.`exam_type` = 'espl'
              WHEN (a.`type` = 'J') THEN b.`exam_type` = 'jrf'
            END)
            AND b.`type` = 'F'
            join
        (select c.* from  `result_declaration_log_partial_details` c  where c.`status` = 'D' GROUP BY  c.res_dec_id, c.`admn_no`,c.`published_on`, c.`actual_published_on`)x
          ON UPPER(a.`admn_no`) = UPPER(x.`admn_no`) AND b.`id` = x.`res_dec_id`    		  
          order by  x.`res_dec_id`,x.admn_no, x.`published_on`, x.`actual_published_on` ";

        if ($this->db->query($q)) {
            return true;
        }
        return false;
    }
*/
    protected function getOldPartialDeclearData($data) {
         if($data['s_year']!='')$granual.=" and b.s_year='".$data['s_year']."' "; 
            if($data['session']!='')$granual.=" and b.session='".$data['session']."' "; 
            if($data['dept_id']!='')$granual.=" and b.dept_id='".$data['dept_id']."' "; 
            if($data['exam_type']!='')$granual.=" and b.exam_type='".$data['exam_type']."' "; 
           
             if ($data['dept_id']=='comm' && ($section != 'all' && $section != null && $section != "")) {
                $granual.= " and b.section='".$data['section']."' ";
                
            } else {
                $granual.= "";
                
            }
           
           
           
           $admn_no = preg_replace('/\s+/', '', $data['admn_no']);      
        if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $replacer1 = "  and a.admn_no in(" . $admn_no . ") ";                
            } else {
                $granual.= " and a.admn_no='" . $admn_no . "' ";                
            }
        } else {
            $granual.= "";            
        }
           
         
        if ($data['granual_sel']=='max' && ($data['course_id'] != '' && $data['branch_id'] != '' && $data['semester']!= '')) {
             $granual.= " and b.course_id='". $data['course_id']."' and b.branch_id='". $data['branch_id']."' and  b.semester='". $data['semester']."' ";
         }
         
        $q = "SELECT a.* ,x.`res_dec_id`,x.admn_no, x.`published_on`, x.`actual_published_on`,x.id as partial_id            
               FROM
            `final_semwise_marks_foil` a
          JOIN `result_declaration_log` b
        ON
        a.`session_yr` = b.`s_year`
          AND a.`session` = b.`session`
          AND a.`dept` = b.`dept_id`
          AND a.`course` = b.`course_id`
          AND a.`branch` = (CASE WHEN UPPER(a.`course`) = 'COMM' THEN LOWER(b.`section`) ELSE LOWER(b.`branch_id`) END)
          AND a.`semester` = (CASE WHEN UPPER(a.`course`) = 'JRF' THEN '0' WHEN UPPER(a.`course`) = 'PREP' THEN '-1' ELSE b.`semester` END)
          AND (CASE
            WHEN a.`type` = 'R' THEN (CASE WHEN (UPPER(a.`course`) = 'JRF' && a.`session` = 'Winter') THEN b.`exam_type` = 'jrf_spl' WHEN UPPER(a.`course`) = 'PREP' THEN b.`exam_type` = 'prep' ELSE b.`exam_type` = 'regular' END)
            WHEN (a.`type` = 'S') THEN b.`exam_type` = 'spl'
            WHEN (a.`type` = 'O') THEN b.`exam_type` = 'other'
            WHEN (a.`type` = 'E') THEN b.`exam_type` = 'espl'
            WHEN (a.`type` = 'J') THEN b.`exam_type` = 'jrf'
          END)
          AND b.`type` = 'F'    ".$granual."
          join
     (select c.* from  `result_declaration_log_partial_details` c  where c.`status` = 'D' GROUP BY  c.res_dec_id, c.`admn_no`,c.`published_on`, c.`actual_published_on`)x
          ON UPPER(a.`admn_no`) = UPPER(x.`admn_no`) AND b.`id` = x.`res_dec_id`    		              
          order by  x.`res_dec_id`,x.admn_no, x.`published_on`, x.`actual_published_on`";

        $res = $this->db->query($q);
        if ($res->num_rows() > 0)
            return $res->result_array();
        return false;
    }

     protected function getOldDeclearData($data) {
         
           
           
            if($data['s_year']!='')$granual.=" and b.s_year='".$data['s_year']."' "; 
            if($data['session']!='')$granual.=" and b.session='".$data['session']."' "; 
            if($data['dept_id']!='')$granual.=" and b.dept_id='".$data['dept_id']."' "; 
            if($data['exam_type']!='')$granual.=" and b.exam_type='".$data['exam_type']."' "; 
           
             if ($data['dept_id']=='comm' && ($section != 'all' && $section != null && $section != "")) {
                $granual.= " and b.section='".$data['section']."' ";
                
            } else {
                $granual.= "";
                
            }
           
           
           
           $admn_no = preg_replace('/\s+/', '', $data['admn_no']);      
        if ($admn_no != null) {
            if (substr_count($admn_no, ',') > 0) {
                $admn_no = "'" . implode("','", explode(',', $admn_no)) . "'";
                $granual.= "  and a.admn_no in(" . $admn_no . ") ";                
            } else {
                $granual.= " and a.admn_no='" . $admn_no . "' ";                
            }
        } else {
            $granual.= "";            
        }
           
         
        if ($data['granual_sel']=='max' && ($data['course_id'] != '' && $data['branch_id'] != '' && $data['semester']!= '')) {
             $granual.= " and b.course_id='". $data['course_id']."' and b.branch_id='". $data['branch_id']."' and  b.semester='". $data['semester']."' ";
         }
         
         
        $q = "SELECT a.* ,b.`id` as res_dec_id, b.`published_on`, b.`actual_published_on`            
               FROM
            `final_semwise_marks_foil` a
          JOIN `result_declaration_log` b
        ON
        a.`session_yr` = b.`s_year`
          AND a.`session` = b.`session`
          AND a.`dept` = b.`dept_id`
          AND a.`course` = b.`course_id`
          AND a.`branch` = (CASE WHEN UPPER(a.`course`) = 'COMM' THEN LOWER(b.`section`) ELSE LOWER(b.`branch_id`) END)
          AND a.`semester` = (CASE WHEN UPPER(a.`course`) = 'JRF' THEN '0' WHEN UPPER(a.`course`) = 'PREP' THEN '-1' ELSE b.`semester` END)
          AND (CASE
            WHEN a.`type` = 'R' THEN (CASE WHEN (UPPER(a.`course`) = 'JRF' && a.`session` = 'Winter') THEN b.`exam_type` = 'jrf_spl' WHEN UPPER(a.`course`) = 'PREP' THEN b.`exam_type` = 'prep' ELSE b.`exam_type` = 'regular' END)
            WHEN (a.`type` = 'S') THEN b.`exam_type` = 'spl'
            WHEN (a.`type` = 'O') THEN b.`exam_type` = 'other'
            WHEN (a.`type` = 'E') THEN b.`exam_type` = 'espl'
            WHEN (a.`type` = 'J') THEN b.`exam_type` = 'jrf'
          END)
          AND b.`type` = 'F'    ".$granual."
          order by  b.`id` ,a.admn_no, b.`published_on`, b.`actual_published_on`  /*LIMIT 20000,1000 */";

        $res = $this->db->query($q);
   //      echo $this->db->last_query(); die();
        if ($res->num_rows() > 0)
            return $res->result_array();
        return false;
    }
    protected function insertIntoPartialFreezedDesc($data) {
        if ($this->db->insert_batch('final_semwise_marks_foil_desc_freezed', $data))
            return true;
        return false;
    }
  /*  function ProcessDescData() {
        $ids =$this->getOldPartialDeclearData();
        
        if ($ids) {
            $d = array();
            foreach ($ids as $value)
                array_push($d, $value['id']);
            $q = "select foil_id as old_foil_id,admn_no,sub_code,mis_sub_id,cr_hr,sessional,theory,total,grade,cr_pts,current_exam,remark from final_semwise_marks_foil_desc where foil_id in(" . implode(',', $d) . ")";
            $ff = $this->db->query($q);
            $this->InsertPartialintoFreezedTbl();
            if ($ff->num_rows() > 0) {
                $data = $ff->result_array();
                if ($this->insertIntoPartialFreezedDesc($data))
                    return "success";
            }else {
                return "NO Record IN Desc";
            }
        } else {
            return "No Record in Partial Result data";
        }
    }*/
    function get_old_modification_log($admn_no,$session_yr,$session,$semester,$dept,$course, $branch,$grp=null){
       if($grp){
        $preadd= " select group_concat(f.sub_code) as sub_code_list from ( ";
        $postadd= " )f  group by f.sub_code ";
       }else{
           $preadd= " ";
        $postadd= " ";
       }        
        $q= $preadd. " select z.*,grade_points.points, (z.cr_hr*grade_points.points) AS cr_pts from 
(select x.* from
  (SELECT b.sessional,b.theory,b.practical,b.total,b.grade,c.subject_id as sub_code,c.name,b.marks_master_id,c.credit_hours as cr_hr,e.aggr_id
                  FROM marks_master AS a
                  JOIN marks_subject_description_backup AS b ON a.id=b.marks_master_id and b.change_type='m2' 
                  JOIN subjects_old AS c ON a.subject_id=c.id 
                   join course_structure as e on e.id=c.id
                  JOIN subject_mapping AS d ON d.map_id=a.sub_map_id
                  WHERE b.admn_no=?  AND d.`session`=? AND d.session_year=? AND d.semester=?  AND d.course_id=? AND d.branch_id=? and d.dept_id=?
                  order by b.change_time)x
                  group by x.marks_master_id)z
                  LEFT JOIN grade_points ON grade_points.grade=z.grade ".$postadd ;		  
                          
       $res = $this->db->query($q,array($admn_no,$session,$session_yr,$semester,$course,$branch,$dept));
        if ($res->num_rows() > 0)
            return $res->result_array();
        return false;
    }      	     
    
    function ProcessDescData($data,$param) {
        $this->db->trans_enabled;
        $this->db->trans_start();
        $parent= ($param=='partial'?$this->getOldPartialDeclearData($data):$this->getOldDeclearData($data) );      
       //  echo '<pre>'; print_r($parent);echo '</pre>';  die();
            if (count($parent)>0) {
            $arr2 = null;
            $l = 0; 
              foreach($parent as $row){                  
                $arr2[$l]['old_id'] = $row['id'];                
                $arr2[$l]['session_yr'] = $row['session_yr'];
                $arr2[$l]['session'] = $row['session'];
                $arr2[$l]['dept'] = $row['dept'];
                $arr2[$l]['course'] = $row['course'];
                $arr2[$l]['branch'] = $row['branch'];
                $arr2[$l]['semester'] = $row['semester'];
                $arr2[$l]['admn_no'] = $row['admn_no'];
                $arr2[$l]['tot_cr_hr'] = $row['tot_cr_hr'];
                $arr2[$l]['tot_cr_pts'] = $row['tot_cr_pts'];
                $arr2[$l]['core_tot_cr_hr'] = $row['core_tot_cr_hr'];
                $arr2[$l]['core_tot_cr_pts'] = $row['core_tot_cr_pts'];
                $arr2[$l]['ctotcrpts'] = $row['ctotcrpts'];
                $arr2[$l]['core_ctotcrpts'] = $row['core_ctotcrpts'];
                $arr2[$l]['ctotcrhr'] = $row['ctotcrhr'];
                $arr2[$l]['core_ctotcrhr'] = $row['core_ctotcrhr'];
                $arr2[$l]['gpa'] = $row['gpa'];
                $arr2[$l]['core_gpa'] = $row['core_gpa'];
                $arr2[$l]['cgpa'] = $row['cgpa'];
                $arr2[$l]['core_cgpa'] = $row['core_cgpa'];
                $arr2[$l]['status'] = $row['status'];
                $arr2[$l]['core_status'] = $row['core_status'];
                $arr2[$l]['hstatus'] = $row['hstatus'];
                $arr2[$l]['repeater'] = $row['repeater'];
                $arr2[$l]['type'] = $row['type'];
                $arr2[$l]['exam_type'] = $row['exam_type'];
                $arr2[$l]['final_status'] = $row['final_status'];
                $arr2[$l]['published_on'] = $row['published_on'];
                $arr2[$l]['actual_published_on'] = $row['actual_published_on'];
                $arr2[$l]['result_dec_id'] = $row['res_dec_id'];                
                $this->db->insert("final_semwise_marks_foil_freezed", $arr2[$l]);                
                $curr = $this->db->insert_id();                                                 
                /*   query to update  published_date and  result_declaration_id for each combination */                
                //$this->db->where( array('foil_id' => $row['id'], 'admn_no' => $row['admn_no']));
                //$select_desc = $this->db->get('final_semwise_marks_foil_desc');                
                $q= "select b.*,e.aggr_id from(SELECT x.*  from final_semwise_marks_foil_desc x where x.admn_no=? and x.foil_id=? ) b  
                             left JOIN subjects_old AS c ON b.mis_sub_id=c.id 
                           left join course_structure as e on e.id=c.id";
                 $select_desc = $this->db->query($q,array($row['admn_no'], $row['id']));                  
                //     echo $this->db->last_query(); die();
              
                if ($select_desc->num_rows()) {
                    $log_arr_grp=null;
                    $log_arr=null;                   
                    $arr1 = null;
                    $k = 0;$overall_status=0;$effective_new_sum=0;$effective_old_sum=0;$overall_H_status=0;
                    if($param<>'partial')
                    $log_arr=$this->get_old_modification_log($row['admn_no'],$row['session_yr'],$row['session'],$row['semester'],$row['dept'],$row['course'], $row['branch']);
                    $log_arr_grp=$this->get_old_modification_log($row['admn_no'],$row['session_yr'],$row['session'],$row['semester'],$row['dept'],$row['course'], $row['branch'],'group');
                  //      echo '<pre>'; print_r($log_arr_grp);echo '</pre>';  die();
                        $prr=null;
                        $prr=explode(',',$log_arr_grp[0]['sub_code_list']);
                        
                  //  echo $this->db->last_query(); die();
                    foreach($select_desc->result_array() as $row1){
                    if($log_arr){
                          if(in_array($row1['sub_code'],$prr)){ 
                        foreach($log_arr as $row2){
                         if($row1['sub_code']==$row2['sub_code']){                                                                                                                               
                            $arr1[$k]['cr_hr'] = $row2['cr_hr'];
                            $arr1[$k]['sessional'] = $row2['sessional'];
                            $arr1[$k]['theory'] = $row2['theory'];
                            $arr1[$k]['total'] = $row2['total'];
                            $arr1[$k]['grade'] = $row2['grade'];
                            $arr1[$k]['cr_pts'] = $row2['cr_pts'];                            
                            $arr1[$k]['old_foil_id'] = $row1['foil_id'];
                            $arr1[$k]['foil_id'] = $curr;                        
                            $arr1[$k]['admn_no'] = $row1['admn_no'];
                            $arr1[$k]['sub_code'] = $row1['sub_code'];
                            $arr1[$k]['mis_sub_id'] = $row1['mis_sub_id'];
                            $arr1[$k]['current_exam'] = $row1['current_exam'];
                            $arr1[$k]['remark'] = $row1['remark'];
                            $effective_new_sum+= $row2['cr_pts'];
                            $effective_old_sum+= $row1['cr_pts'];
                            if (strpos($row2['aggr_id'], 'honour') !== false){
                                if($row2['grade'] == null || (strtoupper(trim( $row2['grade'])) == 'F' &&  $row['branch'] <> 'JRF') || (strtoupper(trim( $row2['grade'])) == 'D' &&  $row['branch'] == 'JRF'))
                                $overall_H_status++;                             
                            }else{
                            if($row2['grade'] == null || (strtoupper(trim( $row2['grade'])) == 'F' &&  $row['branch'] <> 'JRF') || (strtoupper(trim( $row2['grade'])) == 'D' &&  $row['branch'] == 'JRF'))
                                $overall_status++;                             
                            }
                             $k++;
                            }
                        } 
                    }   else{
                            $arr1[$k]['cr_hr'] = $row1['cr_hr'];
                            $arr1[$k]['sessional'] = $row1['sessional'];
                            $arr1[$k]['theory'] = $row1['theory'];
                            $arr1[$k]['total'] = $row1['total'];
                            $arr1[$k]['grade'] = $row1['grade'];
                            $arr1[$k]['cr_pts'] = $row1['cr_pts'];                      
                            $arr1[$k]['old_foil_id'] = $row1['foil_id'];
                            $arr1[$k]['foil_id'] = $curr;                        
                            $arr1[$k]['admn_no'] = $row1['admn_no'];
                            $arr1[$k]['sub_code'] = $row1['sub_code'];
                            $arr1[$k]['mis_sub_id'] = $row1['mis_sub_id'];
                            $arr1[$k]['current_exam'] = $row1['current_exam'];
                            $arr1[$k]['remark'] = $row1['remark'];
                             if (strpos($row1['aggr_id'], 'honour') !== false) {
                                if($row1['grade'] == null || (strtoupper(trim( $row1['grade'])) == 'F' &&  $row['branch'] <> 'JRF') || (strtoupper(trim( $row1['grade'])) == 'D' &&  $row['branch'] == 'JRF'))
                                $overall_H_status++;                             
                            }else{
                            if($row['grade'] == null || (strtoupper(trim( $row1['grade'])) == 'F' &&  $row['branch'] <> 'JRF') || (strtoupper(trim( $row1['grade'])) == 'D' &&  $row['branch'] == 'JRF'))
                                $overall_status++;                             
                            }
                             $k++;
                           } 
                         
                    }else{                                             
                        $arr1[$k]['cr_hr'] = $row1['cr_hr'];
                        $arr1[$k]['sessional'] = $row1['sessional'];
                        $arr1[$k]['theory'] = $row1['theory'];
                        $arr1[$k]['total'] = $row1['total'];
                        $arr1[$k]['grade'] = $row1['grade'];
                        $arr1[$k]['cr_pts'] = $row1['cr_pts']; 
                        $arr1[$k]['old_foil_id'] = $row1['foil_id'];
                        $arr1[$k]['foil_id'] = $curr;                        
                        $arr1[$k]['admn_no'] = $row1['admn_no'];
                        $arr1[$k]['sub_code'] = $row1['sub_code'];
                        $arr1[$k]['mis_sub_id'] = $row1['mis_sub_id'];
                        $arr1[$k]['current_exam'] = $row1['current_exam'];
                        $arr1[$k]['remark'] = $row1['remark'];
                         $k++;
                       }                                                                                      
                    } 
               
                 //   $result_declaration_drside = & get_instance();
                    //print_r($arr1); die();    
                     if($param<>'partial'){
                    $list=array('tot_cr_pts'=> $row['tot_cr_pts']+$effective_new_sum-$effective_old_sum,
                                'core_tot_cr_pts'=> $row['core_tot_cr_pts']+$effective_new_sum-$effective_old_sum,
                                'ctotcrpts'=> $row['ctotcrpts']+$effective_new_sum-$effective_old_sum,
                                'core_ctotcrpts' => $row['core_ctotcrpts']+$effective_new_sum-$effective_old_sum,
                                'gpa'=> ($overall_status>0?0: ($overall_H_status>0?0:round(( ($row['tot_cr_pts']+$effective_new_sum-$effective_old_sum) / $row['tot_cr_hr']), 2)) ),
                                'core_gpa'=> ($overall_status>0?0:round(( ($row['core_tot_cr_pts']+$effective_new_sum-$effective_old_sum) / $row['core_tot_cr_hr']), 2)),
                                'cgpa'=>  ($overall_status > 0?0: ($overall_H_status>0?0: ($this->numberOfDecimals($row['cgpa'])==2 ? round(( ($row['ctotcrpts']+$effective_new_sum-$effective_old_sum) / $row['ctotcrhr']), 2):( ($row['ctotcrpts']+$effective_new_sum-$effective_old_sum) / $row['ctotcrhr'])))),
                                'core_cgpa'=>  ($overall_status >0? 0: ($this->numberOfDecimals($row['core_cgpa'])==2 ? round(( ($row['core_ctotcrpts']+$effective_new_sum-$effective_old_sum) / $row['core_ctotcrhr']), 2):( ($row['core_ctotcrpts']+$effective_new_sum-$effective_old_sum) / $row['core_ctotcrhr']))),
                                'status'=>($overall_status > 0?'FAIL': ($overall_H_status>0?'FAIL': strtoupper($row['course'])<>'JRF'? ( (round(( ($row['tot_cr_pts']+$effective_new_sum-$effective_old_sum) / $row['tot_cr_hr']), 2)) >= 5 ? 'PASS' : 'FAIL' ): 'PASS'  )),
                                'core_status'=>($overall_status > 0?'FAIL': (strtoupper($row['course'])<>'JRF'?  ( (round(( ($row['core_tot_cr_pts']+$effective_new_sum-$effective_old_sum) / $row['core_tot_cr_hr']), 2)) >= 5 ? 'PASS' : 'FAIL' ): 'PASS'  )),                                                                    
                        );                                                            
                   $this->db->where('id' , $curr);
                   $this->db->update('final_semwise_marks_foil_freezed', $list);
                   }
                    $this->db->insert_batch("final_semwise_marks_foil_desc_freezed", /* $select_desc->result_array() */ $arr1); 
                }                               
                 $l++;
            }             
        }
       $this->db->trans_complete();
       if($this->db->trans_status()===FALSE){
           return false;
       }else
           return true;
     }
     
      function numberOfDecimals($value){
    if ((int)$value == $value)   
        return 0;   
    else if (! is_numeric($value))    
        // throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
        return false;    
     return strlen($value) - strrpos($value, '.') - 1;
}       

}

?>