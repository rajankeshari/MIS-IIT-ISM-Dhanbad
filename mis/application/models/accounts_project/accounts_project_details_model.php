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
			$value=trim($value);
			// echo $value ; die();
			$query1 = $this->db->get_where('accounts_project_details', array('project_no' => $value));
			$details = $query1->result_array();
			//echo $this->db->last_query();
			
			// echo "<pre>";
			// print_r($details);
			// exit;

			if(sizeof($details) == 0){
				$this->session->set_flashdata('flashError','No project found by given Project no: '.$value.'');
				redirect('home');
			}
			else
			{

				//echo 'wqewqe'; die();
				$data['details'] = $details;

				$query2 = $this->db->query("SELECT a.p_co_i_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name
				FROM accounts_project_p_co_i a LEFT JOIN user_details u ON a.p_co_i_id=u.id WHERE a.project_no='$value'
				UNION 
				SELECT apd.pi_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name FROM accounts_project_details apd LEFT JOIN user_details u ON apd.pi_id=u.id WHERE apd.project_no='$value'"); /* change by sujit */
				/*$query2 = $this->db->query("SELECT a.p_co_i_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name
				FROM accounts_project_p_co_i a LEFT JOIN user_details u ON a.p_co_i_id=u.id WHERE a.project_no='$value'");*//* change by shobhan */
				// $query2 = $this->db->get_where('accounts_project_p_co_i', array('project_no' => $value));
				
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
				$query11 = $this->db->query('SELECT * FROM accounts_project_supplier_ms');
				//$query12 = $this->db->query("SELECT * FROM accounts_project_employee ape WHERE ape.project_no='$value'");
				$query12 = $this->db->query("SELECT * FROM project_emp_details ape WHERE ape.project_no='$value'");



				$data['p_co_i'] = $query2->result_array();
				$data['expenditure'] = $query3->result_array();
				$data['installment_details'] = $query4->result_array();
				$data['max_inst_no'] = $query5->row_array();
				$data['installment_amounts'] = $query6->result_array();
				$data['max_interest_no'] = $query9->row_array();
				$data['interest'] = $query10->result_array();
				$data['supplier'] = $query11->result_array();
				$data['project_employee'] = $query12->result_array();

				// echo $this->db->last_query();
				// die();
				
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
	$style='style="border-top: 1px solid #000;margin: 4px -6px;"';
	$ww="select X.*,group_concat(X.p_co_i_id) as p_co_i_id ,group_concat(X.p_co_i_dept) as p_co_i_dept, group_concat(X.ename SEPARATOR '<hr ".$style.">')as ename 
from(

select a.*,f.domain_name,g.mobile_no,b.head_name,b.head_type,b.sanctioned_expenditure,b.balance,b.amount_spent,c.p_co_i_id,c.p_co_i_dept, 
concat(e.first_name,' ',e.middle_name,' ',e.last_name,',<br>',h.domain_name,',<br>',i.mobile_no)as ename
from accounts_project_details a  
left JOIN accounts_project_funds  b on a.project_no=b.project_no  
left JOIN accounts_project_p_co_i c on b.project_no=c.project_no			
left JOIN user_details e ON e.id= c.p_co_i_id
left JOIN emaildata_emp f ON a.pi_id=f.emp_id
left JOIN user_other_details g ON a.pi_id=g.id
left JOIN emaildata_emp h ON c.p_co_i_id=h.emp_id
left JOIN user_other_details i ON c.p_co_i_id=i.id
GROUP BY a.project_no,a.pi_id,c.p_co_i_id
ORDER BY a.entry_time DESC

) X

group by /*x.head_name,*/X.project_no
order by trim(X.project_no)";					  
	/*$query1 = $this->db->query("select x.*,group_concat(x.p_co_i_id) as p_co_i_id ,group_concat(x.p_co_i_dept) as p_co_i_dept, group_concat(x.ename)as ename from(

select a.*,b.head_name,b.head_type,b.sanctioned_expenditure,b.balance,b.amount_spent,c.p_co_i_id,c.p_co_i_dept, concat(e.first_name,' ',e.middle_name,' ',e.last_name)as ename 
				from accounts_project_details a  left JOIN
				accounts_project_funds  b on a.project_no=b.project_no  left JOIN
				accounts_project_p_co_i c on b.project_no=c.project_no

				
LEFT JOIN user_details e ON e.id= c.p_co_i_id
				ORDER BY a.entry_time DESC
)x

group by x.project_no
order by trim(x.project_no)");	*/	

$query1 = $this->db->query($ww);							  
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
				$query5 = $this->db->query("SELECT a.p_co_i_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name
				FROM accounts_project_p_co_i a LEFT JOIN user_details u ON a.p_co_i_id=u.id WHERE a.project_no='$value'");/* Added by shobhan */
				$data['p_co_i_id'] = $query5->result_array();
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

			$sql="SELECT SUM(api.interest_amount) AS intr FROM accounts_project_interest api WHERE api.project_no='$project_no' /*AND DATE(api.last_update_time)>='$date1' AND DATE(api.last_update_time)<='$date2'*/";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();		exit;//
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		function get_non_rec_interest($project_no,$d_range){
			$date_range = explode(" ",$d_range);
			$date1=trim(date('Y-m-d',strtotime($date_range[0])));
			$date2=trim(date('Y-m-d',strtotime($date_range[2])));

			$sql="SELECT SUM(api.non_rec_interest_amount) AS intr FROM accounts_project_interest api WHERE api.project_no='$project_no' /*AND DATE(api.last_update_time)>='$date1' AND DATE(api.last_update_time)<='$date2'*/";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		function get_rec_interest($project_no,$d_range){
			$date_range = explode(" ",$d_range);
			$date1=trim(date('Y-m-d',strtotime($date_range[0])));
			$date2=trim(date('Y-m-d',strtotime($date_range[2])));

			$sql="SELECT SUM(api.rec_interest_amount) AS intr FROM accounts_project_interest api WHERE api.project_no='$project_no' /*AND DATE(api.last_update_time)>='$date1' AND DATE(api.last_update_time)<='$date2'*/";

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

		/* Sujit Section */
		public function get_head_type($p_no,$p_head){
		
			$query = $this->db->get_where('accounts_project_funds', array('project_no' => $p_no, 'head_name' => $p_head));
			if($query->num_rows() > 0)
				return $query->row()->head_type;
			else
				return false;

		}

		public function add_bill_batch($table,$data){
			if($this->db->insert_batch($table, $data)){
				return true;
			}
			else{
				return false;
			}
		}

		public function settle_bill($run_trans,$change_amt,$head_name,$head_type,$project_no,$trans_his){
			$this->db->trans_start();

				$this->db->insert_batch('accounts_project_new_bill', $run_trans);

				$this->db->set('balance', 'balance - '.$change_amt, FALSE);
				$this->db->set('emp_id', $this->session->userdata('id'));
				$this->db->where(array('head_name' => $head_name, 'head_type'=>$head_type, 'project_no' => $project_no));
				$this->db->update('accounts_project_funds');

				$this->db->set('amount_spent', 'amount_spent'.'+'.(float)$change_amt, FALSE);
				$this->db->set('emp_id', $this->session->userdata('id'));
				$this->db->where('head_name', $head_name);	
				$this->db->where('project_no', $project_no);
				$this->db->update('accounts_project_funds');

				$this->db->insert('accounts_project_trans_history', $trans_his);

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return false;
			}
			else{
				$this->db->trans_commit();
				return true;
			}
		}

		public  function get_pi($project_no){
			$query = $this->db->query("SELECT a.p_co_i_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name
			FROM accounts_project_p_co_i a LEFT JOIN user_details u ON a.p_co_i_id=u.id WHERE a.project_no='$project_no'
			UNION 
			SELECT apd.pi_id AS emp_id, CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name) AS p_name FROM accounts_project_details apd LEFT JOIN user_details u ON apd.pi_id=u.id WHERE apd.project_no='$project_no'");
		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;

		}

		public  function get_supplier(){
			$query = $this->db->query('SELECT * FROM accounts_project_supplier_ms ORDER BY sup_name ASC');		
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;
			
		}

		public function get_project_emp($project_no){
			//$query = $this->db->query("SELECT * FROM accounts_project_employee ape WHERE ape.project_no='$project_no'");
			$query = $this->db->query("SELECT * FROM project_emp_details ape WHERE ape.project_no='$project_no'");
			
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;
		}

		public function get_individual_project($project_no){
			// if($fin_year==='ALL'){
			// 	$sql = "SELECT CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) AS pi_name,
			// 					dept.name AS dept,fa.name AS fa_name,apd.* 
			// 					FROM accounts_project_details apd 
			// 					LEFT JOIN user_details ud ON ud.id=apd.pi_id 
			// 					LEFT JOIN cbcs_departments dept ON dept.id=apd.pi_dept
			// 					LEFT JOIN funding_agencies fa ON fa.id=apd.funding_agency
			// 					WHERE apd.project_no='$project_no'";

			// }
			// else{
			// 	$sql = "SELECT CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) AS pi_name,
			// 					dept.name AS dept,fa.name AS fa_name,apd.* 
			// 					FROM accounts_project_details apd 
			// 					LEFT JOIN user_details ud ON ud.id=apd.pi_id 
			// 					LEFT JOIN cbcs_departments dept ON dept.id=apd.pi_dept
			// 					LEFT JOIN funding_agencies fa ON fa.id=apd.funding_agency
			// 					WHERE apd.project_no='$project_no' AND apd.financial_year='$fin_year'";
			// }

			$sql = "SELECT CONCAT_WS(' ',ud.salutation,ud.first_name,ud.middle_name,ud.last_name) AS pi_name,
								dept.name AS dept,fa.name AS fa_name,apd.* 
								FROM accounts_project_details apd 
								LEFT JOIN user_details ud ON ud.id=apd.pi_id 
								LEFT JOIN cbcs_departments dept ON dept.id=apd.pi_dept
								LEFT JOIN funding_agencies fa ON fa.id=apd.funding_agency
								WHERE apd.project_no='$project_no'";

			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;
			
		}

		public function get_head_amount_detail($project_no,$fin_year){
			if($fin_year==="ALL"){
				$token='';
			}
			else{
				if($fin_year!='ALL'){
					$fin_array=explode('-',$fin_year);
					$d1=$fin_array[0].'-04-01';
					$d2=$fin_array[1].'-03-31';
					$token=" AND apnb.bill_date>='".$d1."' AND apnb.bill_date<='".$d2."'";
				}else{
					$token='';
				}
			}

			$sql = "SELECT apf.project_no,apf.head_name,apf.head_type,apf.sanctioned_expenditure,apf.balance,apf.amount_spent,x.bill_amount FROM accounts_project_funds apf LEFT JOIN (SELECT apnb.project_no,apnb.head_name,apnb.head_type,apnb.created_date,SUM(apnb.bill_amount) AS bill_amount FROM accounts_project_new_bill apnb WHERE apnb.project_no='$project_no' $token	GROUP BY apnb.head_name)x ON apf.head_name=x.head_name WHERE apf.project_no='$project_no'";
							 
			$query = $this->db->query($sql);
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;


		}

		public function head_bill_detail($project_no,$head,$fin_year){
			if($fin_year!='ALL'){
				$fin_array=explode('-',$fin_year);
				$d1=$fin_array[0].'-04-01';
				$d2=$fin_array[1].'-03-31';
				$token="AND a.bill_date>='".$d1."' AND a.bill_date<='".$d2."'";
			}else{
				$token='';
			}

			//$sql = "SELECT * FROM accounts_project_new_bill a WHERE a.project_no='$project_no' AND a.head_name='$head' AND a.bill_date>='$d1' AND a.bill_date<='$d2' ORDER BY a.created_date DESC";
			$sql = "SELECT * FROM accounts_project_new_bill a WHERE a.project_no='$project_no' AND a.head_name='$head' $token ORDER BY a.created_date DESC";
			$query = $this->db->query($sql);
			// echo $this->db->last_query();
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;
		}

		public function add_vendor($name){
			if($this->db->insert('accounts_project_supplier_ms',array('sup_name'=> $name))){
				return $this->db->insert_id();
			}
			else{
				return 0;
			}
		}	

		public function get_edit_detail($project_no,$head){
			// $sql = "SELECT * FROM accounts_project_trans_history ap WHERE ap.project_no='$project_no' AND ap.head_name='$head' AND ap.approved_amount!=0 ORDER BY ap.admin_entry DESC";

			$sql = "SELECT ap.*,apnb.transaction_id AS new_trans_id,apnb.project_no AS new_project_no,apnb.head_name AS new_head_name,apnb.head_type AS new_head_type,apnb.bill_no,apnb.bill_desc,apnb.payment_type,apnb.payment_to_id,apnb.payment_to_name FROM accounts_project_trans_history ap LEFT JOIN accounts_project_new_bill apnb ON ap.transaction_id=apnb.transaction_id AND ap.project_no=apnb.project_no AND ap.head_name=apnb.head_name WHERE ap.project_no='$project_no' AND ap.head_name='$head' AND ap.approved_amount!=0 ORDER BY ap.admin_entry DESC";

			$query = $this->db->query($sql);
			// echo $this->db->last_query();
			if($query->num_rows() > 0)
				return $query->result();
			else
				return false;
		}

		public function get_editupdate_bill($name){
			// echo "<pre>";
			// print_r($name);	
			// exit();
			$tdate = date("Y-m-d H:i:s");		

			$this->db->trans_start();
			foreach ($name['trans_id'] as $key => $value) {	
			
				$d = strtotime($name['bill_date'][$key]); 
				$t=date("H:i:s");
				$new_date = date("Y-m-d", $d);
				$new_dt1 = date("Y-m-d", $d);
				$new_date = $new_date.' '.$t;

				if(empty($name['new_trans_id'][$key])){

					$p_type='';
					$p_name='';
					if($name['payment_type'][$key]==='Vendor/Supplier'){
						$str=$name['supplier'][$key];						
						$p_array1=explode('#',$str);
						$p_type=$p_array1[0];
						$p_name=$p_array1[1];					
						
					}
					if($name['payment_type'][$key]==='PI/Co_PI'){
						$str=$name['pi'][$key];
						$p_array=explode('#',$str);
						$p_type=$p_array[0];
						$p_name=$p_array[1];				
					}
					if($name['payment_type'][$key]==='Project Employee'){
						$str=$name['project_employee'][$key];
						$p_array=explode('#',$str);
						$p_type=$p_array[0];
						$p_name=$p_array[1];
					}
					if($name['payment_type'][$key]==='External' || $name['payment_type'][$key]==='Others'){
						$p_name=$name['others'][$key];
					}
					if($name['payment_type'][$key]==='Overhead'){
						$p_name=$name['institute'][$key];
					}

					$insert_data =array(
						'transaction_id'=>$name['trans_id'][$key],
						'project_no'=>$name['project_no'][$key],
						'head_name'=>$name['head_name'][$key],
						'head_type'=>$name['head_type'][$key],
						'bill_no'=>$name['bill_no'][$key],
						'bill_desc'=>$name['bill_desc'][$key],
						'bill_date'=>$new_dt1,
						'claim_amount'=>$name['h_bill_amount'][$key],
						'bill_amount'=>$name['h_bill_amount'][$key],
						'payment_type' => $name['payment_type'][$key],
						'payment_to_id' => $p_type,
						'payment_to_name' => $p_name,
						'created_by' => $this->session->userdata('id'),
						'created_date' => $tdate
					);		
					
					$this->db->insert('accounts_project_new_bill', $insert_data);
					$update_data1 = array(
						'faculty_entry'=>$new_date,
						'admin_entry'=>$new_date,
						//'dean_entry'=>$new_date
					);
					$this->db->set($update_data1);
					$this->db->where(array('transaction_id' => $name['trans_id'][$key], 'project_no'=>$name['project_no'][$key], 'head_name' => $name['head_name'][$key]));
					$this->db->update('accounts_project_trans_history');

				}
				else{
					 echo "Not Empty ".$key."<br>";
					$update_data1 = array(
						'faculty_entry'=>$new_date,
						'admin_entry'=>$new_date,
						//'dean_entry'=>$new_date
					);
					$this->db->set($update_data1);
					$this->db->where(array('transaction_id' => $name['trans_id'][$key], 'project_no'=>$name['project_no'][$key], 'head_name' => $name['head_name'][$key]));
					$this->db->update('accounts_project_trans_history');
					$p_type='';
					$p_name='';
					if($name['payment_type'][$key]==='Vendor/Supplier'){
						$str=$name['supplier'][$key];						
						$p_array1=explode('#',$str);
						$p_type=$p_array1[0];
						$p_name=$p_array1[1];					
						
					}
					if($name['payment_type'][$key]==='PI/Co_PI'){
						$str=$name['pi'][$key];
						$p_array=explode('#',$str);
						$p_type=$p_array[0];
						$p_name=$p_array[1];				
					}
					if($name['payment_type'][$key]==='Project Employee'){
						$str=$name['project_employee'][$key];
						$p_array=explode('#',$str);
						$p_type=$p_array[0];
						$p_name=$p_array[1];
					}
					if($name['payment_type'][$key]==='External' || $name['payment_type'][$key]==='Others'){
						$p_name=$name['others'][$key];
					}
					if($name['payment_type'][$key]==='Overhead'){
						$p_name=$name['institute'][$key];
					}

					$update_data2 = array(
						'bill_no' => $name['bill_no'][$key],
						'bill_desc' => $name['bill_desc'][$key],
						'bill_date'=>$new_dt1,
						'claim_amount'=>$name['h_bill_amount'][$key],
						'bill_amount'=>$name['h_bill_amount'][$key],
						'payment_type' => $name['payment_type'][$key],
						'payment_to_id' => $p_type,
						'payment_to_name' => $p_name,
						'modified_by' => $this->session->userdata('id'),
						'modified_date' => $tdate
					);
					
					$this->db->set($update_data2);
					$this->db->where(array('transaction_id' => $name['trans_id'][$key], 'project_no'=>$name['project_no'][$key], 'head_name' => $name['head_name'][$key]));
					$this->db->update('accounts_project_new_bill');
				}
			}
			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return false;
			}
			else{
				$this->db->trans_commit();
				return true;
			}
		}

		public function add_supplier($name){
			// echo "<pre>";
			// print_r($name);
			// exit;
			$insert_data = array(
				'sup_name' => $name['supplier'],
				'created_by' => $this->session->userdata('id')
			);

			if($this->db->insert('accounts_project_supplier_ms', $insert_data)){
				return true;
			}
			else{
				return false;
			}
		}
	
		public function dalete_co_pi($project_no){
			$this->db->delete('accounts_project_p_co_i',array('project_no' => $project_no));
		}

		public function update_interest_details($data,$i,$project_no){
			$this->db->where(array('interest_no' => $i, 'project_no' => $project_no));
			$this->db->update('accounts_project_interest', $data);
		}
		
		function get_all_accounts_project_details_by_project_name($pid){
			  $sql="SELECT a.* FROM  accounts_project_details a where a.project_no=?";
                
                    $query = $this->db->query($sql,array($pid));
			//echo $this->db->last_query();	
                    if ($query->num_rows() > 0)
                    { 
                      
                         return $query->row();
                    }
                    else
                    {
                        return false;
                    }
			  
		  }
		// Delete and Backup
		function insert_backup_data($table, $data)
		{
			$this->db->insert($table, $data);
		}
		function delete_project_no_data($project_no){
			
			/*$tables = array("accounts_project_details","accounts_project_details_archive","accounts_project_funds","accounts_project_funds_archive","accounts_project_installment_amounts","accounts_project_installment_details","accounts_project_interest","accounts_project_p_co_i","accounts_project_running_trans","accounts_project_status","accounts_project_trans_files","accounts_project_trans_history");*/
			$tables = array('accounts_project_details','accounts_project_details_archive','accounts_project_funds','accounts_project_funds_archive','accounts_project_installment_amounts','accounts_project_installment_details','accounts_project_interest','accounts_project_new_bill','accounts_project_p_co_i','accounts_project_running_trans','accounts_project_status','accounts_project_trans_files','accounts_project_trans_history');
			
			foreach($tables as $table) {
				//$query = "DELETE FROM $table WHERE WHERE a.project_no='$project_no'";
				$query = $this->db->query("DELETE FROM $table WHERE  project_no='$project_no'");
				//echo $this->db->last_query();
				
						
			}
			if($this->db->affected_rows()){
				  return true;
				}
				else{
				  return false;
				}
			

			
		}
	
	
	}
	/* End of file accounts_project_details_model.php */
	/* Location: ./application/models/accounts_project/accounts_project_details_model.php */
	