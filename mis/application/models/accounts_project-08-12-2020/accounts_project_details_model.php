<?php 
	
	class Accounts_project_details_model extends CI_model{

		function __construct()
		{
			// Call the Model constructor
			parent::__construct(array('ft','project_so', 'project_admin','ar_project'));
		}

		function getIDsByAuth($auth)
		{
			$query = $this->db->get_where('user_auth_types', array('auth_id' => $auth));
			return $query->result_array();
		}
		
		// Get 'academic' depts 
		function getDept()
		{
			$query = $this->db->get_where('departments', array('type' => 'academic'));
			return $query->result_array();
		}

		// get all faculty
		function getFaculty()
		{
           $query=$this->db->query("
           		SELECT ud.id, CONCAT_WS(' ', ud.salutation, ud.first_name, ud.middle_name, ud.last_name) as name 
           		FROM users AS fd 
           		JOIN user_details AS ud ON fd.id = ud.id 
           		WHERE fd.auth_id='emp' AND ud.dept_id IN (SELECT id FROM departments WHERE type='academic')
           		ORDER BY name ASC ");
       		
          	return $query->result_array();
       	}

       	//check faculty
       	function checkFaculty($emp)
		{
           $query=$this->db->query("
           		SELECT ud.id
           		FROM users AS fd 
           		JOIN user_details AS ud ON fd.id = ud.id 
           		WHERE fd.auth_id='emp' AND ud.dept_id IN (SELECT id FROM departments WHERE type='academic')
      			");
           
          	foreach ($query->result_array() as $key => $value) 
          	{
          		if($value['id'] == $emp)
          			return true;
          	}
          	return false;
       	}
       	
       	function getFacultyByDept($dept)
		{
           $query=$this->db->query("
           		SELECT ud.id, CONCAT_WS(' ', ud.salutation, ud.first_name, ud.middle_name, ud.last_name) AS name 
           		FROM users AS fd 
           		JOIN user_details AS ud ON fd.id = ud.id 
           		WHERE fd.auth_id='emp' AND ud.dept_id = '$dept' 
           		ORDER BY name ASC ");
     
           return $query->result_array();
       	}
       	

       	// insert 'data' into table 
		function add($table, $data)
		{
			$this->db->insert($table, $data);
		}

		// insert_batch into table
		function add_batch($table, $data)
		{
			$this->db->insert_batch($table, $data);
		}

		// update project data from edit project form...
		function update_project($table,$data)
		{
			if($table=='accounts_project_p_co_i')
			{
				$this->db->delete($table,array('project_no' => $data[0]['project_no']));
				if ($data[0]['p_co_i_id']!='')
					$this->add($table,$data[0]);
				if ($data[1]['p_co_i_id']!='')
					$this->add($table,$data[1]);
				if ($data[2]['p_co_i_id']!='')
					$this->add($table,$data[2]);
			}
			else
			{
				$this->db->where('project_no', $data['project_no']);
				$this->db->update($table, $data);
			}
		}


		public function getProjectbyID($value)
		{
			$query1 = $this->db->get_where('accounts_project_details', array('project_no' => $value));
			$details = $query1->result_array();

			if(sizeof($details) == 0)
			{
				$this->session->set_flashdata('flashError','No project found by given Project no: '.$value.'');
				redirect('home');
			}
			else
			{
				$data['details'] = $details;
				$query2 = $this->db->get_where('accounts_project_p_co_i', array('project_no' => $value));
				$query3 = $this->db->get_where('accounts_project_funds', array('project_no' => $value));
				$query4 = $this->db->get_where('accounts_project_installment_details', array('project_no' => $value));
				$query5 = $this->db->query("
						SELECT max(inst_no)
						FROM accounts_project_installment_details
						WHERE project_no = '".$value."' ");
				$query6 = $this->db->get_where('accounts_project_installment_amounts', array('project_no' => $value));
				$query9 = $this->db->query("
						SELECT max(interest_no)
						FROM accounts_project_interest
						WHERE project_no = '".$value."' ");
				$query10 = $this->db->get_where('accounts_project_interest', array('project_no' => $value));

				$data['p_co_i'] = $query2->result_array();
				$data['expenditure'] = $query3->result_array();
				$data['installment_details'] = $query4->result_array();
				$data['max_inst_no'] = $query5->row_array();
				$data['installment_amounts'] = $query6->result_array();
				$data['max_interest_no'] = $query9->row_array();
				$data['interest'] = $query10->result_array();

				// echo $this->db->last_query();
				return $data;
			}
		}

		// get projects by faculty id
		function getProjectbyEmpId($empId)
		{
			$query1 = $this->db->query("

						SELECT DISTINCT(project_no), project_title, funding_agency, project_tenure 
						from accounts_project_details 
						WHERE project_no IN(
							SELECT DISTINCT(project_no) FROM accounts_project_details
							WHERE pi_id= '$empId'
							UNION
							SELECT DISTINCT(project_no) FROM accounts_project_p_co_i
							WHERE p_co_i_id = '$empId')
						ORDER BY entry_time DESC;
				");

			$details = $query1->result_array();
			if(sizeof($details) == 0)
			{
				return false;
			}
			else
			{
				$data['details'] = $details; 
				return $data;
			}
		}

		// only pi can add bill
		function getProjectbyEmpIdForBilling($empId)
		{
			$query1 = $this->db->query("

						SELECT project_no, project_title, funding_agency, project_tenure 
						from accounts_project_details 
						WHERE pi_id= '$empId'
						ORDER BY entry_time DESC;
				");

			$details = $query1->result_array();
			if(sizeof($details) == 0)
			{
				return false;
			}
			else
			{
				$data['details'] = $details; 
				return $data;
			}
		}


		// get all projects
		function getAllProjects()
		{
			/* by kalyan
			$query1 = $this->db->query("

						SELECT DISTINCT(project_no), project_title, funding_agency, start_date, project_tenure, sanction_amount,pi_id, pi_dept 
						from accounts_project_details 
						WHERE project_no IN(
							SELECT DISTINCT(project_no) FROM accounts_project_details
							UNION
							SELECT DISTINCT(project_no) FROM accounts_project_p_co_i)
						ORDER BY entry_time DESC;
				");
			*/ /* by sourav with co-pi & his respective dept.
				$query1 = $this->db->query("select a.*,b.head_name,b.head_type,b.sanctioned_expenditure,b.balance,b.amount_spent,c.p_co_i_id,c.p_co_i_dept 
				from accounts_project_details a  left join
				accounts_project_funds  b on a.project_no=b.project_no  left join
				accounts_project_p_co_i c on b.project_no=c.project_no
				ORDER BY a.entry_time DESC

				");
				/**/
/* stopped 04-01-2019
			$query1 = $this->db->query("SELECT a.*,b.head_name,b.head_type,b.sanctioned_expenditure,b.balance,b.amount_spent
										FROM accounts_project_details a
										LEFT JOIN accounts_project_funds b ON a.project_no=b.project_no
										ORDER BY a.entry_time DESC 
									  ");	
*/
									  
	$query1 = $this->db->query("select x.*,group_concat(x.p_co_i_id) as p_co_i_id ,group_concat(x.p_co_i_dept) as p_co_i_dept, group_concat(x.ename)as ename from(

select a.*,b.head_name,b.head_type,b.sanctioned_expenditure,b.balance,b.amount_spent,c.p_co_i_id,c.p_co_i_dept, concat(e.first_name,' ',e.middle_name,' ',e.last_name)as ename 
				from accounts_project_details a  left JOIN
				accounts_project_funds  b on a.project_no=b.project_no  left JOIN
				accounts_project_p_co_i c on b.project_no=c.project_no

				
LEFT JOIN user_details e ON e.id= c.p_co_i_id
				ORDER BY a.entry_time DESC
)x

group by x.head_name,x.project_no
order by trim(x.project_no)");									  
			$details = $query1->result_array();

			if(sizeof($details) == 0)
			{
				return false;
			}
			else
			{
				$data['details'] = $details;				
				return $data;
			}
		}

		//Increase project balance on new installment...
		function inc_balance($temp, $project_no)
		{
			foreach ($temp as $data)
			{
				$value = $data['head_amount'];
				$this->db->set('balance', 'balance + '.$value, FALSE);
				$this->db->set('emp_id', $this->session->userdata('id'));
				$this->db->where(array('head_name' => $data['head_name'], 'project_no' => $project_no));
				$this->db->update('accounts_project_funds');
				//echo $this->db->last_query();''
			}
		}

		//Decrease project balance on bill approval...
		function dec_balance($data, $project_no)
		{
			foreach ($data as $key => $value)
			{
				$this->db->set('balance', 'balance - '.$value, FALSE);
				$this->db->set('emp_id', $this->session->userdata('id'));
				$this->db->where(array('head_name' => $key, 'project_no' => $project_no));
				$this->db->update('accounts_project_funds');
			}
		}

		function report_query($table,$selector,$value,$date)
		{
			if($table=='accounts_project_details')
				$dateID = 'start_date';
			else if($table=='accounts_project_installment_details')
				$dateID = 'inst_date';
			else if($table=='accounts_project_trans_history')
				$dateID = 'admin_entry';
			else if($table=='accounts_project_funds')
				$dateID = 'last_update_time';
			
			if($selector=='*')
			{
				$sql = 'SELECT * FROM '.$table;	
				if($date[0]!='')
					$sql .= ' WHERE '.$dateID.'>="'.$date[0].'"';
				if($date[1]!='')
					$sql .= ' AND '.$dateID.'<="'.$date[1].'"';
				$query = $this->db->query($sql);
				return $query->result_array();
			}
			if($table=='accounts_project_details')
			{
				if($selector=='pi_id')
				{
					$sql = 'SELECT * FROM accounts_project_details WHERE pi_id="'.$value.'" OR project_no IN(SELECT project_no from accounts_project_p_co_i WHERE p_co_i_id="'.$value.'")';
					if($date[0]!='')
						$sql .= ' AND '.$dateID.'>="'.$date[0].'"';
					if($date[1]!='')
						$sql .= ' AND '.$dateID.'<="'.$date[1].'"';
					$query = $this->db->query($sql);
					return $query->result_array();
				}
				else if($selector=='dept')
				{
					$sql = 'SELECT * FROM accounts_project_details WHERE pi_dept="'.$value.'" ';
					if($date[0]!='')
						$sql .= ' AND '.$dateID.'>="'.$date[0].'"';
					if($date[1]!='')
						$sql .= ' AND '.$dateID.'<="'.$date[1].'"';
					$query = $this->db->query($sql);
					return $query->result_array();
				}
			}
			else
			{
				if($selector=='pi_id')
				{
					$sql = 'SELECT * FROM '.$table.' 
							WHERE project_no 
							IN(SELECT project_no 
								from accounts_project_p_co_i 
								WHERE p_co_i_id="'.$value.'" 
								UNION 
								SELECT project_no 
								from accounts_project_details 
								WHERE pi_id="'.$value.'")';

					if($date[0]!='')
						$sql .= ' AND '.$dateID.'>="'.$date[0].'"';
					if($date[1]!='')
						$sql .= ' AND '.$dateID.'<="'.$date[1].'"';
					$query = $this->db->query($sql);
					return $query->result_array();
				}
				else if($selector=='dept')
				{
					$sql = 'SELECT * FROM '.$table.' 
							WHERE project_no 
							IN(SELECT project_no 
							from accounts_project_details 
							WHERE pi_dept="'.$value.'")';

					if($date[0]!='')
						$sql .= ' AND '.$dateID.'>="'.$date[0].'"';
					if($date[1]!='')
						$sql .= ' AND '.$dateID.'<="'.$date[1].'"';
					$query = $this->db->query($sql);
					return $query->result_array();
				}
			}

		}

		function getEditProjectDetails($value)
		{
			$query1 = $this->db->get_where('accounts_project_details', array('project_no' => $value));
			$details = $query1->result_array();

			if(sizeof($details) == 0)
			{
				$this->session->set_flashdata('flashError','No project found by given Project no: '.$value.'');
				redirect('home');
			}
			else
			{
				$data['details'] = $details;
				$query2 = $this->db->get_where('accounts_project_funds', array('project_no' => $value));
				$data['funds'] = $query2->result_array();

				$query3 = $this->db->get_where('accounts_project_installment_details', array('project_no' => $value));
				$data['installments'] = $query3->result_array();

				$query4 = $this->db->query("
						SELECT max(edit_no) as edit_no
						FROM accounts_project_funds_archive
						WHERE project_no = '".$value."' ");
				$data['max_edit_no'] = $query4->row_array();
				
				return $data;
			}
		}

		function edit_project_and_balance($details, $funds, $installments)
		{
			foreach($details as $key => $value)	
				if($key != 'project_no' && $key!='emp_id')
					$this->db->set($key, $value);
			$this->db->where(array('project_no' => $details['project_no']));
			$this->db->update('accounts_project_details');
		
			foreach($funds as $fund)
			{
				foreach ($fund as $key => $value) 
				{
					if($key != 'project_no' && $key != 'head_name' && $key != 'head_type')
						$this->db->set($key, $value);
					
				}
				$this->db->where(array('project_no' => $fund['project_no'], 'head_name' => $fund['head_name'], 'head_type' => $fund['head_type']));
				$this->db->update('accounts_project_funds');
			}	

			if(count($installments)>0)
			foreach($installments as $installment)
			{
				foreach ($installment as $key => $value) 
				{
					if($key != 'project_no' && $key != 'inst_no')
						$this->db->set($key, $value);
					
				}
				$this->db->where(array('project_no' => $installment['project_no'], 'inst_no' => $installment['inst_no']));
				$this->db->update('accounts_project_installment_details');
			}	
		}

		function get_project_all_edits($value)
		{			
			$query1 = $this->db->get_where('accounts_project_funds_archive', array('project_no' => $value));

			$query2 = $this->db->get_where('accounts_project_details_archive', array('project_no' => $value));

			$query3 = $this->db->query("
						SELECT max(edit_no) as edit_no
						FROM accounts_project_funds_archive
						WHERE project_no = '".$value."' ");
				
			$data['funds'] = $query1->result_array();
			$data['details'] = $query2->result_array();
			$data['max_edit_no'] = $query3->row_array();

			return $data;
		}

		function get_history_by_edit_no($project_no, $edit_no)
		{
			$query1 = $this->db->get_where('accounts_project_funds_archive', array('project_no' => $project_no, 'edit_no' => $edit_no));

			$data['funds'] = $query1->result_array();

			return $data;
		}



		/* dated 15-08-2020*/

		function getallproject(){
			$sql = "SELECT * FROM accounts_project_details apd";
			$query = $this->db->query($sql);	
			// echo $this->db->last_query();	
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		function getproject($pno){

			$sql = "SELECT apd.project_no,apd.project_title,if(fa.name IS NULL,'N/A',fa.name) AS agency,CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) AS pi_name,dp.name AS dept,apd.sanction_no,apd.sanction_amount,apd.start_date,apd.project_tenure,apd.entry_time FROM accounts_project_details apd INNER JOIN user_details ud ON apd.pi_id=ud.id INNER JOIN departments dp ON apd.pi_dept=dp.id LEFT JOIN funding_agencies fa ON fa.id=apd.funding_agency WHERE apd.project_no='$pno'";
			$query = $this->db->query($sql);	
			// echo $this->db->last_query();	
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		function getproject_fund($pno){			

			// $sql = "SELECT x.fund_received,x.fund,apf.* FROM accounts_project_details apd LEFT JOIN accounts_project_funds apf ON apd.project_no=apf.project_no LEFT JOIN (SELECT apia.head_name,SUM(apia.head_amount) AS fund_received, GROUP_CONCAT(apia.head_amount) AS fund FROM accounts_project_installment_amounts apia WHERE apia.project_no='$pno' GROUP BY apia.head_name)x ON x.head_name=apf.head_name WHERE apf.project_no='$pno'";

			// $sql = "SELECT SUM(apia.head_amount) AS fund_received,x.fund,apf.* FROM accounts_project_details apd
			// LEFT JOIN accounts_project_funds apf ON apd.project_no=apf.project_no 
			// LEFT JOIN accounts_project_installment_amounts apia ON apia.project_no=apd.project_no AND apf.head_name=apia.head_name LEFT JOIN (SELECT y.head,GROUP_CONCAT('',y.s) AS fund  FROM (SELECT apth.head_name AS head,SUM(apth.approved_amount) AS s,YEAR(apth.admin_entry) AS y,apth.* FROM accounts_project_trans_history apth WHERE apth.project_no='$pno' GROUP BY YEAR(apth.admin_entry),apth.head_name)y GROUP BY y.head)x ON x.head=apf.head_name WHERE apf.project_no='$pno' GROUP BY apf.head_name";

			$sql = "SELECT DATE(x.ad_entry) AS admin_entry ,SUM(apia.head_amount) AS fund_received,x.fund,apf.*
					FROM accounts_project_details apd LEFT JOIN accounts_project_funds apf ON apd.project_no=apf.project_no
					LEFT JOIN accounts_project_installment_amounts apia ON apia.project_no=apd.project_no AND apf.head_name=apia.head_name LEFT JOIN (SELECT y.ad  AS ad_entry,y.head, SUM(y.s) AS fund FROM (SELECT apth.admin_entry AS ad,apth.head_name AS head, SUM(apth.approved_amount) AS s, YEAR(apth.admin_entry) AS Y,apth.* FROM accounts_project_trans_history apth WHERE apth.project_no='$pno' GROUP BY YEAR(apth.admin_entry),apth.head_name)y GROUP BY y.head)x ON x.head=apf.head_name WHERE apf.project_no='$pno' GROUP BY apf.head_name";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}


		function get_expenditure($head_name,$project_no,$d_range){
			$date_range = explode(" ",$d_range);
			$date1=trim(date('Y-m-d',strtotime($date_range[0])));
			$date2=trim(date('Y-m-d',strtotime($date_range[2])));

			$sql="SELECT SUM(apth.approved_amount) AS exp, apth.head_name AS head FROM accounts_project_trans_history apth
			 	WHERE apth.project_no='$project_no' AND apth.head_name='$head_name' 
				AND DATE(apth.admin_entry)>='$date1' AND DATE(apth.admin_entry)<='$date2'";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;


		}

		function get_interest($project_no,$d_range){
			$date_range = explode(" ",$d_range);
			$date1=trim(date('Y-m-d',strtotime($date_range[0])));
			$date2=trim(date('Y-m-d',strtotime($date_range[2])));

			$sql="SELECT SUM(api.interest_amount) AS intr FROM accounts_project_interest api WHERE api.project_no='$project_no' AND DATE(api.last_update_time)>='$date1' AND DATE(api.last_update_time)<='$date2'";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		function get_no_of_installement($pno){
			$sql = "SELECT * FROM accounts_project_installment_details apid WHERE apid.project_no='$pno'";

			$query = $this->db->query($sql);		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}
		function get_installement_detail($pno){
			$sql = "SELECT apia.*,SUM(apia.head_amount) AS fund_received FROM accounts_project_installment_amounts apia WHERE apia.project_no='$pno' GROUP BY apia.head_name";

			$query = $this->db->query($sql);		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		
	}



	/* End of file accounts_project_details_model.php */
	/* Location: ./application/models/accounts_project/accounts_project_details_model.php */
	