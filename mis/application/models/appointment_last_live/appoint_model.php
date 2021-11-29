<?php
/* 
 * Generated by CRUDigniter v3.2 
 * www.crudigniter.com
 */
 
class appoint_model extends CI_Model
{
	private $tabulation='iitism';
    function __construct()
    {
        parent::__construct();
    }


 
function get_badge_count($appointer_auth,$status) {       

        $CI = &get_instance();       
         $this->db2 = $CI->load->database($this->tabulation, TRUE);         
       
		 
		 if($status=='all'){
		  $where= " where appointer_auth_id=? ";              
          $secure_array = array($appointer_auth);
		 }
		 else{
   		  $where= " where status=? and appointer_auth_id=? ";              
          $secure_array = array($status, $appointer_auth);
		  }
    
            $sql="  select count(*) as appiont_ctr, date_format( appoint_timestamp,'%d-%m-%Y') as appoint_date from  appoint_master  $where  group by   date_format( appoint_timestamp,'%d-%m-%Y') ";            

           $query = $this->db2->query($sql,$secure_array);
           //echo $this->db2->last_query(); die();

        if ($query->num_rows() > 0)
             return $query->result();
        else 
            return 0;
// throw new Exception(($this->db2->_error_message()==null?"Internal Error Occured":$this->db2->_error_message()));
        
    }
	
/*function get_badge_counttwo($appointer_auth,$status) {       
       //  try{
		 $CI = &get_instance();       
         $this->db2 = $CI->load->database($this->tabulation, TRUE);         
       
		 
		 if($status=='all'){
		  $where= " where appointer_auth_id=? ";              
          $secure_array = array($appointer_auth);
		 }
		 else{
   		  $where= " where status=? and appointer_auth_id=? ";              
          $secure_array = array($status, $appointer_auth);
		  }
    
            $sql="  select count(*) as appiont_ctr, date_format( appoint_timestamp,'%d-%m-%Y') as appoint_date from  appoint_master  $where  group by   date_format( appoint_timestamp,'%d-%m-%Y') ";            

           $query = $this->db2->query($sql,$secure_array);
           //echo $this->db->last_query(); die();

        if ($query->num_rows() > 0)
             return $query->result();
        else 
            return 0;
  throw new Exception(($this->db2->_error_message()==null?"Internal Error Occured":$this->db2->_error_message()));
        
    }*/
	
	
	function get_appointee_name_given_date($appoint_date,$appointer_auth,$status) {       
           $CI = &get_instance();       
         $this->db2 = $CI->load->database($this->tabulation, TRUE);         
		 if($status=='all'){
		  $where= " where  date_format( appoint_timestamp,'%d-%m-%Y')=? and  appointer_auth_id=?  ";              
          $secure_array = array($appoint_date,$appointer_auth);
		 }
		 else{
   		  $where= " where date_format( appoint_timestamp,'%d-%m-%Y')=?  and status=? and appointer_auth_id=? ";              
          $secure_array = array($appoint_date,$status, $appointer_auth);
		  }
    
            $sql="  
                   select  x.id as appointee_master_key,(case  when x.appointee_type ='I'  then 'Internal' else 'External' end) as appointee_type  ,  concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name)  as name,
                    d.name as department  from   
                    (select appointee_emp_id , appointee_type,id from  appoint_master $where)x
                     left join user_details u on
                    u.id= x.appointee_emp_id
                     left join departments_android d on
                      d.id=u.dept_id      
              ";            

           $query = $this->db2->query($sql,$secure_array);
          // echo $this->db2->last_query(); die();

        if ($query->num_rows() > 0)
             return $query->result();
        else 
            return 0;
        
    }
	
	
	function get_appoint_details_by_id($id){
	  $CI = &get_instance();       
         $this->db2 = $CI->load->database($this->tabulation, TRUE);         
	  $secure_array = array($id);
	$sql="select x.*,  concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name)  as name, ebd.designation,
d.name as department ,u.email,uod.mobile_no
from  
  (select appointee_emp_id,(case  when appointee_type ='I'  then 'Internal' else 'External' end) as appointee_type, date_format( appoint_timestamp,'%d-%m-%Y %h:%i:%s %p') as appoint_date,venue,purpose,other_info from 
   appoint_master where id=?)x
   left join user_details u on
   u.id= x.appointee_emp_id
    left join user_other_details uod on
    uod.id= x.appointee_emp_id
    left join emp_basic_details ebd on
        ebd.emp_no= x.appointee_emp_id
   left join departments_android d on
   d.id=u.dept_id
   ";
	   $query = $this->db2->query($sql,$secure_array);
          // echo $this->db2->last_query(); die();

        if ($query->num_rows() > 0)
             return $query->result();
        else 
            return 0;
	}
	
    function appointer__appointee_details(){
    	$q="SELECT usr.id AS EMPNO, UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS NAME
			FROM users usr
			LEFT JOIN user_details ud ON ud.id=usr.id where usr.auth_id='emp'";
		if($query=$this->db->query($q)){
			if($query->num_rows()>0){
				return $query->result();
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
    }

    function get_appointee_all_given_date($appoint_date,$appointer_auth,$status) {       
         $CI = &get_instance();       
         $this->db2 = $CI->load->database($this->tabulation, TRUE);         
		 if($status=='all'){
		  $where= " where  date_format( appoint_timestamp,'%d-%m-%Y')=? and  appointer_auth_id=?  ";              
          $secure_array = array($appoint_date,$appointer_auth);
		 }
		 else{
   		  $where= " where date_format( appoint_timestamp,'%d-%m-%Y')=?  and status=? and appointer_auth_id=? ";              
          $secure_array = array($appoint_date,$status, $appointer_auth);
		  }

 $sql="    select  x.*,  concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name)  as name,ebd.designation,u.email,uod.mobile_no,
d.name as department
from  
  (select id as appointee_master_key,appointee_emp_id,(case  when appointee_type ='I'  then 'Internal' else 'External' end) as appointee_type, date_format( appoint_timestamp,'%d-%m-%Y %h:%i:%s %p') as appoint_date,venue,purpose,other_info  from  appoint_master  $where)x
   left join user_details u on
   u.id= x.appointee_emp_id
   left join user_other_details uod on
    uod.id= x.appointee_emp_id
    left join emp_basic_details ebd on
        ebd.emp_no= x.appointee_emp_id
   left join departments_android d on
   d.id=u.dept_id
   
     ";            

           $query = $this->db2->query($sql,$secure_array);
          //echo $this->db2->last_query(); die();

        if ($query->num_rows() > 0)
             return $query->result();
        else 
            return 0;
        
    }
    function select_dropdown(){
    	if($query=$this->db->get('appoint_life_cycle_master')){
    		if($query->num_rows()>0){
    			return $query->result();
    		}
    		else{
    			return false;
    		}
    	}
    	else{
    		return false;
    	}
	}

	function insert_outsider_on_mis($data){
		if($this->db->insert('appoint_outsider_appointee',$data)){
			//echo $this->db->last_query();die();
			$q="select max(id) as id from appoint_outsider_appointee";
			if($query=$this->db->query($q)){
				if($query->num_rows()>0){
					$result=$query->result();
          return $result[0]->id;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
	}

  function insert_appoint_master_on_mis($data){
    if($this->db->insert('appoint_master',$data)){
      $q="select max(id) as id from appoint_master";
      if($query=$this->db->query($q)){
        if($query->num_rows()>0){
          return $query->result();
        }
        else{
          return false;
        }
      }
    }
      else{
        return false;
      }
    }

   function insert_outsider_on_iitism($outsider){
      $CI = &get_instance();       
      $this->db2 = $CI->load->database($this->tabulation, TRUE);
      if($this->db2->insert('appoint_outsider_appointee',$outsider)){
        $q="select max(id) as id from appoint_outsider_appointee";
        if($query=$this->db2->query($q)){
          if($query->num_rows()>0){
          $result=$query->result();
          return $result[0]->id;
        }
      }
    }
    else{
         return false;
    }
   }

   function insert_appoint_on_iitism($data){
      $CI = &get_instance();       
      $this->db2 = $CI->load->database($this->tabulation, TRUE);
	  //var_dump$this->db2;die();
      if($this->db2->insert('appoint_master',$data)){
		 //echo $this->db->last_query();die();
        return true;   
      }
      else{
		 //echo $this->db->last_query();die();
         return false;
      }
    }

}
?>