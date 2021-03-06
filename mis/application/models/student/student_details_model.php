<?php

Class Student_details_model extends CI_Model
{
	var $table = 'stu_details';

	function __construct()
	{
		parent::__construct();
	}

	function insert($data)
	{
		if($this->db->insert($this->table,$data))
			return TRUE;
		else
			return FALSE;
	}

	function pending_insert($data)
	{
		if($this->db->insert('pending_'.$this->table,$data))
			return TRUE;
		else
			return FALSE;
	}

	function get_all_student_id()
	{
		$query = $this->db->select('admn_no')->order_by('admn_no')->get($this->table);
		if($query->num_rows() > 0)
			return $query->result();
		else
			return FALSE;
	}

	function get_student_type_a_student($student = '')
	{
		$this->db->select('stu_type')->where('admn_no',$student);
		$query = $this->db->get($this->table);
		foreach($query->result() as $row)
			return $row->type;
	}

	function get_student_details_by_id($stu_id = '')
	{
		if($stu_id != '')
		{
			$query = $this->db->where('admn_no="'.$stu_id.'"','',FALSE)->get($this->table);
			if($query->num_rows() === 1)
				return $query->row();
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	function get_pending_student_details_by_id($stu_id = '')
	{
		if($stu_id != '')
		{
			$query = $this->db->where('admn_no="'.$stu_id.'"','',FALSE)->get('pending_'.$this->table);
			if($query->num_rows() === 1)
				return $query->row();
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	function getCourseByCourseId($id='')
	{
		if($id != '')
		{
			$query = $this->db->where('id="'.$id.'"','',FALSE)->get('cs_courses');
			if($query->num_rows() === 1)
				return $query->row();
			else
				return FALSE;
		}
		else
			return FALSE;
	}
	

	function getBranchByBranchId($id='')
	{
		if($id != '')
		{
			$query = $this->db->where('id="'.$id.'"','',FALSE)->get('cs_branches');
			if($query->num_rows() === 1)
				return $query->row();
			else
				return FALSE;
		}
		else
			return FALSE;
	}

	function update_by_id($data,$id)
	{
		$this->db->update($this->table,$data,array('admn_no'=>$id));
	}

	function update_pending_by_id($data,$id)
	{
		$this->db->update('pending_'.$this->table,$data,array('admn_no'=>$id));
	}

	function deletePendingDetailsWhere($data)
	{
		$this->db->delete('pending_'.$this->table,$data);
	}
}

?>