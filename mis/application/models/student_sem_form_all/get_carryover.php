<?php 
class Get_carryover extends CI_Model
{
	var $carryover = 'reg_carryover_form';
	
	function getCarryoverByformId($fid){
				$q=$this->db->get_where($this->carryover,array('form_id'=>$fid));
				if($q->num_rows > 0){
					return $sd['carry']= $q->result_array();
					}
				return false;
		}
}
?>