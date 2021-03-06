<?php 
class Common_sem_model extends CI_Model{
  
    function sectionGroup($session_year){
	    	//echo "inmodel";
          $q= $this->db->get_where('section_group_rel',array('session_year'=>$session_year,'section !='=>"PURE") ); // 'pure ' condition was added @3-5-18 @rituraj
          if($q->num_rows() >0){
            return $q->result_array();
        	}
        return false;
    }
    function getSectionMarks($sub_map_id,$subject_id){
    	$q= $this->db->get_where('marks_master',array('sub_map_id'=>$sub_map_id,'subject_id'=>$subject_id,'type'=>'R' ));
    	if($q->num_rows() >0){
            return $q->result_array();
        	}
        return false;
    }

    function get_details_common($session,$session_year,$crs_struct=null)
    {
		if($crs_struct<>'all'|| $crs_struct<>null)
		{
			$crs_sruct_const=" and c.aggr_id='".$crs_struct."' ";
		}
		else $crs_sruct_const="";
    	$this->load->database();
        	$query=$this->db->query("SELECT DISTINCT r.semester,r.session,s.session_year,s.group,s.section, c.id , m.map_id 
		                        FROM reg_regular_form as r INNER JOIN section_group_rel as s INNER JOIN course_structure as c INNER JOIN dept_course as d INNER JOIN subject_mapping as m  
								WHERE r.session_year=s.session_year 
								AND c.aggr_id=r.course_aggr_id 
								AND d.aggr_id=c.aggr_id 
								AND m.aggr_id=d.aggr_id   ".$crs_sruct_const."
								AND r.semester=SUBSTRING_INDEX(c.semester, '_', 1) 
								AND s.group=SUBSTRING_INDEX(c.semester, '_', -1) 
								AND d.dept_id='comm'
        						AND r.session='$session' 
        						AND r.session_year='$session_year'
        						AND m.session=r.session 
        						AND m.session_year=r.session_year  
        						AND r.course_aggr_id LIKE 'comm%' 
        						AND s.section=m.section 
        						AND s.group=m.group 
                                AND hod_status='1' 
								ORDER BY s.group,sequence,s.section 
								");
        	$result=$query->result_array();

        	
        	//print_r($result);
        	return $result;
    }
            //mid is in Array
    /*function get_comm_grading_all($max,$mid){
            
            if( is_array($mid) ){
                $d=implode(',',$mid); 
            }else{
             $d=$mid;
            }

        $q="update marks_master a join marks_subject_description b  on a.id=b.marks_master_id set b.grade= (select (if(b.sub_type = 'T',if(b.theory < 20,'F',c.grade),c.grade)) as grade from relative_grading_table c where b.total between c.min and c.max and c.highest_marks=? ) where a.id in (?); ";
	   // $q="update marks_master a join marks_subject_description b  on a.id=b.marks_master_id set b.grade= (select c.grade from relative_grading_table c where b.total between c.min and c.max and c.highest_marks=? ) where a.id in (?); ";
         if($this->db->query($q,[$max,$d]))
            return true;
        return fasle;

    }*/
	
	 function get_comm_grading_all($max,$mid){
            
            if( is_array($mid) ){
                $d=implode(',',$mid); 
            }else{
             $d=$mid;
            }
 
        $q="update marks_master a join marks_subject_description b  on a.id=b.marks_master_id set b.grade= (select (if(b.sub_type = 'T',if(b.theory < 21,'F',c.grade),c.grade)) as grade from relative_grading_table c where b.total between c.min and c.max and c.highest_marks=? ) where a.id in (".$d."); ";
	   // $q="update marks_master a join marks_subject_description b  on a.id=b.marks_master_id set b.grade= (select c.grade from relative_grading_table c where b.total between c.min and c.max and c.highest_marks=? ) where a.id in (?); ";
         if($this->db->query($q,$max))
            return true;
        return false;

    }


    function get_section_marks($session,$session_year,$subject_id)
    {
    $this->db->from('marks_master');
    $this->db->where('subject_id' , $subject_id );
    $this->db->where('session' , $session);
    $this->db->where('session_year' , $session_year );
   // $this->db->where('status','Y');
    $this->db->where('type','R');
    $query = $this->db->get();
  	$result=$query->result_array();
    	
        	
       return $result;
    }
    function get_relative_gradetable($highest_marks)
    {
        $this->db->from('relative_grading_table');

        $this->db->where('highest_marks' , $highest_marks);
        $query = $this->db->get();
        $result=$query->result_array();
        return $result;
    }
    function get_marks_des($marks_master_id)
    {
        $this->db->from('marks_subject_description');
        $this->db->where('marks_master_id' , $marks_master_id);
        $query = $this->db->get();
        $result=$query->result_array();
        return $result;

    }
    function get_subject_name($subject_id)
    {
        $this->db->select('name');
        $this->db->from('subjects');
        $this->db->where('id' , $subject_id);
        $query = $this->db->get();
        $result=$query->result_array();
        return $result;   
    }
  
}
?>