<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
class Reg_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function get_doctor() {
        $this->db->select('*');
        $this->db->from('user_details ');
        $this->db->where('dept_id', 'hc');
        //$this->db->where('dept_id','Hc');
        $this->db->where('salutation', 'Dr');

        $query = $this->db->get();
        if ($query->num_rows() > 0)
            return $query->result();
        else
            return FALSE;
    }


    function insert($data) {
        if ($this->db->insert('hc_pat_reg', $data))
        //return $this->db->insert_id();
            return TRUE;
        else
            return FALSE;
    }
    function insert_entry($data){
       // echo '<pre>';print_r($data);echo '</pre>';die();
        // $this->db->insert('hc_counter_batch_no_detail',$data);
        // echo $this->db->last_query();die();
        if($this->db->insert('hc_counter_batch_no_detail',$data)){
            // echo '<script>alert("You Have Successfully updated this Record!");</script>';

            return TRUE;
        }
        else{
            return FALSE;
        }
    }

    function get_appointment_byDate() {
        $id = $this->session->userdata('id');
        $query = $this->db->query("SELECT * FROM hc_pat_reg WHERE date(reg_date)= CURDATE() and doc_id=" . $id);

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function GetAll_patientName_byDocID() {
        $id = $this->session->userdata('id');

        $query = $this->db->query("SELECT
            user_details.id,
            user_details.first_name,
            user_details.middle_name,
            user_details.last_name
                FROM user_details
                INNER JOIN hc_pat_reg
                ON user_details.id = hc_pat_reg.p_id
                WHERE DATE(hc_pat_reg.reg_date) = CURDATE() and doc_id=" . $id);


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function getPatient_byID($id) {

        $doc_id = $this->session->userdata('id');

        $query = $this->db->query("SELECT
  hc_pat_reg.ppulse,
  hc_pat_reg.pbp,
  hc_pat_reg.ptemp,
  hc_pat_reg.pweight,
  user_address.line1,
  user_address.line2,
  user_address.city,
  user_details.first_name,
  user_details.middle_name,
  user_details.last_name,
  user_details.sex,
  user_details.dob
FROM hc_pat_reg
  INNER JOIN user_details
    ON hc_pat_reg.p_id = user_details.id
  INNER JOIN user_address
    ON hc_pat_reg.p_id = user_address.id
WHERE hc_pat_reg.p_id = '" . $id . "'
AND hc_pat_reg.doc_id =" . $doc_id .
                " AND user_address.type = 'present'
                                      ");


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }

        /* $query = $this->db->select("*");
          $query = $this->db->from('hc_pat_reg');
          $query = $this->db->where('p_id',$id);
          $query=$this->db->get();
          if($query->num_rows() > 0)
          {

          return $query->result();
          }
          else
          {
          return FALSE;
          } */
    }

    //------------------------------------Appointment Module Functiions--------------------------------
    function get_pat_profile($id) {
        $query = $this->db->query("SELECT
                user_details.*
              FROM user_details
              WHERE user_details.id ='" . $id . "'");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_pat_family($id) {
        $query = $this->db->query("SELECT
                emp_family_details.*
              FROM emp_family_details
              WHERE emp_family_details.emp_no ='" . $id . "' and emp_family_details.active_inactive='Active'");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_countMember($id) {
        $query = $this->db->query("SELECT
                    COUNT(emp_family_details.sno) AS nrow
                    FROM emp_family_details
                    WHERE emp_family_details.emp_no  ='" . $id . "'");
        if ($query->num_rows() >= 0) { // =0 is here for that condition when emp is not married 
            return $query->result();
        } else {
            return false;
        }
    }

    function get_dept($id) {
        $query = $this->db->query("SELECT
                    departments.name,
                    user_details.dept_id
                  FROM user_details
                    INNER JOIN departments
                      ON user_details.dept_id = departments.id
                  WHERE user_details.id ='" . $id . "'");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    //-----------------------------------Set Patient Observation after click on Hold by Doctor---------------------------------
    function set_pat_obser($data) {
        if ($this->db->insert('hc_pat_obser', $data)) {
            return $this->db->insert_id();
        } else {
            return FALSE;
        }
    }

    function set_pat_hold($data) {
        if ($this->db->insert('hc_pat_hold', $data)) {
            // return $this->db->insert_id();
            $id = $this->db->insert_id();
            $q = $this->db->get_where('hc_pat_hold', array('hold_id' => $id));
            return $q->row();
        }

        // return TRUE;
        else {
            return FALSE;
        }
    }

    function set_pat_remarks($data) {
        if ($this->db->insert('hc_pat_remarks', $data))
        //return $this->db->insert_id();
            return TRUE;
        else
            return FALSE;
    }

    function check_pat_obser($data) {
        //$query =$this->db->get_where('hc_pat_obser',$data);

        $myquery = "SELECT * from hc_pat_obser
            WHERE hc_pat_obser.pid = '" . $data['pid'] . "'
            AND hc_pat_obser.prel = '" . $data['prel'] . "'
            And date(hc_pat_obser.obser_date) = '" . $data['obser_date'] . "'";
        $query = $this->db->query($myquery);

        if ($query->num_rows() > 0) {
            return $query->row()->obser_id;
        } else {
            return FALSE;
        }
    }
    
    function check_pat_remarks($data) {
        //$query =$this->db->get_where('hc_pat_obser',$data);

        $myquery = "SELECT * from hc_pat_remarks
            WHERE hc_pat_remarks.pid = '" . $data['pid'] . "'
            AND hc_pat_remarks.prel = '" . $data['prel'] . "'
            And DATE_FORMAT(hc_pat_remarks.obser_date,'%Y-%m-%d') = DATE_FORMAT('".$data['obser_date']."','%Y-%m-%d')";
        
        //echo $this->db->last_query();
       // print_r($myquery);
        $query = $this->db->query($myquery);

        if ($query->num_rows() > 0) {
            return $query->row()->remarks_id;
        } else {
            return FALSE;
        }
    }

    function update_pat_obser($pat_data, $id) {
        $this->db->update('hc_pat_obser', $pat_data, array('obser_id' => $id));
        return TRUE;
    }
    // updation if Diagnosis of patient already exists
    function update_pat_remarks($rem,$id)
    {
                $myquery = "update hc_pat_remarks set remarks='".$rem."' where remarks_id=".$id;
        
        
       // print_r($myquery);
        $query = $this->db->query($myquery);

            return TRUE;
        
     

            // $this->db->set('remarks', $rem, FALSE);
            //$this->db->where('remarks_id', $id);
           // $this->db->update('hc_pat_remarks');
           
    }

    function get_pat_obser($id) {
        $this->db->select('*');
        $this->db->from('hc_pat_obser ');
        $this->db->where('obser_id', $id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function get_pat_address($id) {
        $this->db->select('*');
        $this->db->from('user_address');
        $this->db->where('id', $id);
        $this->db->where('type', 'present');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function get_hold_list_self($tpid) {
        /* $query = $this->db->query("SELECT
          hc_pat_obser.*
          FROM hc_pat_obser
          WHERE hc_pat_obser.status = 'hold' AND   DATE(hc_pat_obser.	obser_date) = CURDATE() "); */

        $query = $this->db->query("select concat_ws(' ',first_name,middle_name,last_name) as name,photopath from user_details where id='" . $tpid . "'");

        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_hold_list($tpid, $tprel) {
        /* $query = $this->db->query("SELECT
          hc_pat_obser.*
          FROM hc_pat_obser
          WHERE hc_pat_obser.status = 'hold' AND   DATE(hc_pat_obser.	obser_date) = CURDATE() "); */

        $query = $this->db->query("select name from  emp_family_details where emp_no='" . $tpid . "' and sno=" . $tprel);

        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_hold_list1($tpid, $tprel) {
        /* $query = $this->db->query("SELECT
          hc_pat_obser.*
          FROM hc_pat_obser
          WHERE hc_pat_obser.status = 'hold' AND   DATE(hc_pat_obser.	obser_date) = CURDATE() "); */

        $query = $this->db->query("select name,relationship,photopath from  emp_family_details where emp_no='" . $tpid . "' and sno=" . $tprel);

        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_dose() {
        $this->db->from('hc_med_doses');
        $this->db->order_by('dtype');
        $query = $this->db->get();
        $query_result = $query->result();
        return $query_result;
    }
    
    function get_doc_name() {
        

        $myquery = "SELECT concat(`user_details`.`first_name`,' ',`user_details`.`middle_name`,`user_details`.`last_name`) as dmname,
   `user_auth_types`.`id`, `user_auth_types`.`auth_id` FROM   `user_details`   INNER JOIN `user_auth_types` ON `user_details`.`id` = `user_auth_types`.`id` WHERE (`user_auth_types`.`auth_id` = 'hc_lmo') OR (`user_auth_types`.`auth_id` = 'hc_smo') OR  (`user_auth_types`.`auth_id` = 'hc_mo')";

                $query = $this->db->query($myquery);


                if ($query->num_rows() > 0) {
                    // print_r($query->result());
                    return $query->result();
                } else {
                    return false;
                }
    }

    //---------------------------------------------------------------------------------------------------------------------------
    function save_prescription($data) {
        if ($this->db->insert('hc_patient', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    
    function save_prescription_dues($data) {
        if ($this->db->insert('hc_patient_med_dues', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    

    //-----------------Patient Pathology Test save----------

    function save_patho_test($data) {
        if ($this->db->insert('hc_pat_test', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }

    //--------------------------------------------------------

    function get_Prescription_DesById($id) {
        $query = $this->db->query("SELECT
  hc_medicine.*,
  hc_patient.*
FROM hc_patient
  INNER JOIN hc_medicine
    ON hc_patient.mid = hc_medicine.m_id
WHERE hc_patient.visitor_id =" . $id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }
    
    // patient medicine dues table getting id --------------------------------------
    
    function get_Prescription_DesById_duestbl($id) {
        $query = $this->db->query("SELECT
  hc_medicine.*,
  hc_patient_med_dues.*
FROM hc_patient_med_dues
  INNER JOIN hc_medicine
    ON hc_patient_med_dues.mid = hc_medicine.m_id
WHERE hc_patient_med_dues.visitor_id =" . $id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }
    
    // both patient and patient dues table---------------
    
    function get_Prescription_DesById_bothtbl($id) {
        $query = $this->db->query("
            SELECT
  `hc_medicine`.`m_name`,
  `hc_patient`.`dose`,
  `hc_patient`.`ndays`,
  Sum(`hc_patient`.`mqty`) + Sum(`hc_patient_med_dues`.`mqty`) AS `mqty`,
  `hc_patient`.`visitor_id`,
  `hc_patient_med_dues`.`m_status`
FROM
  `hc_patient`
  INNER JOIN `hc_medicine` ON `hc_medicine`.`m_id` = `hc_patient`.`mid`
  INNER JOIN `hc_patient_med_dues` ON `hc_medicine`.`m_id` =
    `hc_patient_med_dues`.`mid` AND
    `hc_patient`.`pid` = `hc_patient_med_dues`.`pid` AND `hc_patient`.`prel` =
    `hc_patient_med_dues`.`prel` AND
    `hc_patient`.`mid` = `hc_patient_med_dues`.`mid` AND
    Date_Format(`hc_patient`.`visit_date`, '%Y-%m-%d') =
    Date_Format(`hc_patient_med_dues`.`visit_date`, '%Y-%m-%d')
GROUP BY
  `hc_medicine`.`m_name`,
  `hc_patient`.`dose`,
  `hc_patient`.`ndays`,
  `hc_patient`.`visitor_id`,
  `hc_patient`.`mqty`,
  `hc_patient_med_dues`.`mqty`,
  `hc_patient_med_dues`.`m_status`
HAVING
  hc_patient.visitor_id =" . $id);
	  
  	  

        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }
    
    
    //----------------------------------------------
    
    
    //-----------------------------------------------------------------------------------------

    //-------------------get save patient pathological test by id--

    function get_patho_test_DesById($id) {
        $query = $this->db->query("SELECT
  `hc_pat_test`.*,
  `hc_pathology_test`.`t_name`
FROM
  `hc_pat_test`
  INNER JOIN `hc_pathology_test` ON `hc_pat_test`.`ptest` =
    `hc_pathology_test`.`t_id`
    WHERE hc_pat_test.test_id =" . $id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    //--------------------------------------------------------------
    // save log data in hc_patient_delete_log table (delete in appointment_manual)
    function insert_hc_patient_delete_log($data) {
        if ($this->db->insert('hc_patient_delete_log', $data))
        //return $this->db->insert_id();
            return TRUE;
        else
            return FALSE;
    }
    // end save log data in hc_patient_delete_log table (delete in appointment_manual)
    function delete_Prescription($id) {
        $this->db->where('visitor_id', $id);
        $this->db->delete('hc_patient');
    }
    function delete_Prescription_dues($id) {
        $this->db->where('visitor_id', $id);
        $this->db->delete('hc_patient_med_dues');
    }
    
    function delete_Prescription_duetbl($id) {
        $ms="duesboth".$id;
        $this->db->where('m_status',$ms);
        $this->db->delete('hc_patient_med_dues');
    }
    
    
    function delete_from_doc_table($mid,$qty) {
        
        $myquery="update hc_doc_medi_issue set qty=qty-".$qty." where m_id=".$mid;
         $query = $this->db->query($myquery);
    }
    
    function get_med_id_from_patient_table($id)
    {
         $this->db->select('mid,mqty'); //from medicine receive table
        $this->db->from('hc_patient');
        $this->db->where('visitor_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }
    function get_med_id_from_patient_table_dues($id)
    {
         $this->db->select('mid,mqty'); //from medicine receive table
        $this->db->from('hc_patient');
        $this->db->where('visitor_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function delete_pat_test($id) {
        $this->db->where('test_id', $id);
        $this->db->delete('hc_pat_test');
    }

    function delete_hold_patient($id) {
        $this->db->where('hold_id', $id);
        $this->db->delete('hc_pat_hold');
    }

    function get_Medicine_ID($mnm) {
        $this->db->select('m_id');
        $this->db->from('hc_medicine');
        $this->db->where('m_name', html_entity_decode($mnm));
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_pat_history($pid, $prel) {
        $query = $this->db->query("

SELECT
  hc_medicine.*,
  hc_patient.*,
  hc_pat_obser.*,
  hc_pat_remarks.*
FROM hc_patient
  INNER JOIN hc_pat_obser
    ON hc_patient.pid = hc_pat_obser.pid
    AND hc_patient.prel = hc_pat_obser.prel
    AND hc_patient.visit_date = hc_pat_obser.obser_date
  INNER JOIN hc_pat_remarks
    ON hc_pat_obser.pid = hc_pat_remarks.pid
    AND hc_pat_obser.prel = hc_pat_remarks.prel
    AND hc_pat_obser.obser_date = hc_pat_remarks.obser_date
  INNER JOIN hc_medicine
    ON hc_medicine.m_id = hc_patient.mid
WHERE hc_patient.pid =" . $pid . " AND hc_patient.prel = '" . $prel . "' ");



        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    //----------------------Query for Repeat last Medicine

    function get_last_prescription($pid, $prel) {
        $query = $this->db->query(" SELECT
  hc_medicine.m_name,
  hc_patient.*,
  hc_medicine.*
FROM hc_patient
  INNER JOIN hc_medicine
    ON hc_patient.mid = hc_medicine.m_id
WHERE hc_patient.visit_date = (SELECT
    hc_patient.visit_date
  FROM hc_patient
  WHERE hc_patient.pid = '" . $pid . "'
  AND hc_patient.prel = '" . $prel . "'
  ORDER BY hc_patient.visit_date DESC
  LIMIT 1)
AND hc_patient.pid = '" . $pid . "'
AND hc_patient.prel = '" . $prel . "'");



        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    //--------------------------------Edit Prescription----------------------------------------------------------------------

    function medit_pres($id) {
        $query = $this->db->query("SELECT
  hc_medicine.*,
  hc_patient.*
FROM hc_medicine
  INNER JOIN hc_patient
    ON hc_medicine.m_id = hc_patient.mid
WHERE hc_patient.visitor_id =" . $id);



        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    function get_med_history($id, $rel) {
// edit hc_patient.batch_no
        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname,hc_patient.visit_no,hc_patient.pres_no,hc_patient.batchno
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id
WHERE hc_patient.pid = '" . $id . "'
AND hc_patient.prel = '" . $rel . "'
    GROUP BY hc_patient.pres_no
ORDER BY date(hc_patient.visit_date) DESC
");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    //-----------------Medical History For Dues patient-----------------------------
    
    function get_med_history_duesPatient($id, $rel) {

        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient_med_dues.visit_date, '%d %b %Y') AS visit,
  hc_patient_med_dues.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient_med_dues
  INNER JOIN user_details
    ON hc_patient_med_dues.doc_id = user_details.id
WHERE hc_patient_med_dues.pid = '" . $id . "'
AND hc_patient_med_dues.prel = '" . $rel . "'
ORDER BY date(hc_patient_med_dues.visit_date) DESC
");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    
    //----------------------------------------------------------------------------------
    
    function get_med_history_due($id, $rel) {

        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient_med_dues.visit_date, '%d %b %Y') AS visit,
  hc_patient_med_dues.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient_med_dues
  INNER JOIN user_details
    ON hc_patient_med_dues.doc_id = user_details.id
WHERE hc_patient_med_dues.pid = '" . $id . "'
AND hc_patient_med_dues.prel = '" . $rel . "'
ORDER BY date(hc_patient_med_dues.visit_date) DESC
");
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    

    function get_med_details_byDate($visit, $pid, $prel,$vno) {



        $myquery = "SELECT
  hc_patient.*,
  hc_medicine.m_name
FROM hc_patient
  INNER JOIN hc_medicine
    ON hc_patient.mid = hc_medicine.m_id
WHERE hc_patient.pid = '" . $pid . "'
AND hc_patient.prel = '" . $prel . "'
And date(hc_patient.visit_date) = '" . $visit . "' and hc_patient.visit_no=".$vno;
        // print_r($myquery);
        //  die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            // print_r($query->result());
            return $query->result();
        } else {
            return false;
        }
    }
    //=========================get Pataient by date and prescription number======
    function get_med_details_byDate_by_vno_pno($visit, $pid, $prel,$vno,$pno) {



        $myquery = "SELECT
  hc_patient.*,
  hc_medicine.m_name
FROM hc_patient
  INNER JOIN hc_medicine
    ON hc_patient.mid = hc_medicine.m_id
WHERE hc_patient.pid = '" . $pid . "'
AND hc_patient.prel = '" . $prel . "'
And date(hc_patient.visit_date) = '" . $visit . "' and hc_patient.visit_no='".$vno."' and hc_patient.pres_no='".$pno."'";
        // print_r($myquery);
          //die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            // print_r($query->result());
            return $query->result();
        } else {
            return false;
        }
    }
    
    
    //=========================================END=================================
	function get_pat_test_list_issues_status($visit, $pid, $prel,$vno,$pno) {



        $myquery = "SELECT
  hc_patient.*,
  hc_medicine.m_name
FROM hc_patient
  INNER JOIN hc_medicine
    ON hc_patient.mid = hc_medicine.m_id
WHERE hc_patient.pid = '" . $pid . "'
AND hc_patient.prel = '" . $prel . "'
And date(hc_patient.visit_date) = '" . $visit . "' and hc_patient.m_status='Pending'  and hc_patient.visit_no='".$vno."' and hc_patient.pres_no='".$pno."'";
        // print_r($myquery);
         // die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            // print_r($query->result());
            return $query->result();
        } else {
            return false;
        }
    }
	function get_pat_test_list_issues_status_dues($visit, $pid, $prel) {



        $myquery = "SELECT
  hc_patient_med_dues.*,
  hc_medicine.m_name
FROM hc_patient_med_dues
  INNER JOIN hc_medicine
    ON hc_patient_med_dues.mid = hc_medicine.m_id
WHERE hc_patient_med_dues.pid = '" . $pid . "'
AND hc_patient_med_dues.prel = '" . $prel . "'
And date(hc_patient_med_dues.visit_date) = '" . $visit . "' and hc_patient_med_dues.m_status='dues'";
        // print_r($myquery);
        //  die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            // print_r($query->result());
            return $query->result();
        } else {
            return false;
        }
    }
    
    function get_med_details_byDate_dues($visit, $pid, $prel) {



        $myquery = "SELECT
  hc_patient_med_dues.*,
  hc_medicine.m_name
FROM hc_patient_med_dues
  INNER JOIN hc_medicine
    ON hc_patient_med_dues.mid = hc_medicine.m_id
WHERE hc_patient_med_dues.pid = '" . $pid . "'
AND hc_patient_med_dues.prel = '" . $prel . "'
And date(hc_patient_med_dues.visit_date) = '" . $visit . "' and hc_patient_med_dues.visit_no=(select max(hc_patient_med_dues.visit_no) from hc_patient_med_dues WHERE hc_patient_med_dues.pid = '" . $pid . "'
AND hc_patient_med_dues.prel = '" . $prel . "'
And date(hc_patient_med_dues.visit_date) = '" . $visit . "')";
         //print_r($myquery);
         //die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            // print_r($query->result());
            return $query->result();
        } else {
            return false;
        }
    }

    //------------------Patient Test-------------------------------------------

    function get_pat_test_list($visit, $pid, $prel) {
        $myquery = "SELECT
  `hc_pat_test`.*,
  `hc_pathology_test`.`t_name`
FROM
  `hc_pat_test`
  INNER JOIN `hc_pathology_test` ON `hc_pat_test`.`ptest` =
    `hc_pathology_test`.`t_id`
              WHERE hc_pat_test.pid = '" . $pid . "'
              AND hc_pat_test.prel = '" . $prel . "'
              And date(hc_pat_test.test_obser_date) = '" . $visit . "'";

        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //--------------------------------------------------------------------------

    function get_pat_observation_ByDate($visit, $pid, $prel) {

        /*  $this->db->select('*');
          $this->db->from('hc_pat_obser');
          $this->db->where('pid', $pid);
          $this->db->where('prel', $prel);
          $this->db->where('date(obser_date)', $visit);
          $query= $this->db->get(); */
        $myquery = "SELECT * from hc_pat_obser
WHERE hc_pat_obser.pid = '" . $pid . "'
AND hc_pat_obser.prel = '" . $prel . "'
And date(hc_pat_obser.obser_date) = '" . $visit . "'";
        // print_r($myquery);
        //  die();
        $query = $this->db->query($myquery);


        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_pat_remarks_ByDate($visit, $pid, $prel)
    {

        /*
          $this->db->select('*');
          $this->db->from('hc_pat_remarks');
          $this->db->where('pid', $pid);
          $this->db->where('prel', $prel);
          $this->db->where('date(obser_date)', $visit);

          $query= $this->db->get(); */

        $myquery = "SELECT * from hc_pat_remarks
WHERE hc_pat_remarks.pid ='" . $pid . "'
AND hc_pat_remarks.prel='" . $prel . "'
And date(hc_pat_remarks.obser_date) = '" . $visit . "'";
        
       
        $query = $this->db->query($myquery);
         //print_r($query->result());
        if ($query->num_rows() > 0) {
            
            $users = $query->result();
            $r = array();
            $i = 0;
            foreach ($users as $u) {
                $r[$i]['name'] = $u->remarks;
                $i++;
            }
            return $r;
            //return $query->row();
        } else {
            return false;
        }
    }

    function get_patient_id_rel_details($id) {
        $this->db->select('*');
        $this->db->from('hc_pat_hold ');
        $this->db->where('hold_id', $id);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    //---------------------------------------Stock for Dr. Prescription-------------


    function get_mainstore_stock($mid) {
        $this->db->select_sum('ms_qty'); //from medicine receive table
        $this->db->from('hc_mainstore_stock');
        $this->db->where('m_id', $mid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_counter_stock($mid) {
        $this->db->select_sum('cs_qty'); // from counter 
        $this->db->from('hc_counter_master');
        $this->db->where('m_id', $mid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }
    //expire stock status starts-------------
        function get_expire_stock($mid) {

         $today = date('Y-m-d');

        $this->db->select_sum('mrec_qty'); // from counter 
        $this->db->from('hc_medi_receive');
        $this->db->where('m_id', $mid);
        $this->db->where('exp_date <= ', $today);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    //expire stock status ends

    function get_doctor_stock($mid) {
        $this->db->select_sum('qty'); // from counter 
        $this->db->from('hc_doc_medi_issue');
        $this->db->where('m_id', $mid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    //-------------------------------------------------
    //-------------Logic to find group medicine of that particular medicine------

    function get_Medicine_Group($mnm) {

       // $myquery="SELECT `m_generic_nm` FROM (`hc_medicine`) WHERE `m_name` = '".html_entity_decode($mnm,ENT_NOQUOTES)."'";
        $myquery="SELECT `m_generic_nm` FROM (`hc_medicine`) WHERE `m_name` = '".html_entity_decode($mnm)."'";
    
        $query = $this->db->query($myquery);
        
        if ($query->num_rows() > 0) { // 
            return $query->row();
            
        } else {
            return false;
        }
    }

    function get_GroupMedicine_ID($mgroup, $mnm) {

        $myquery = "select m_id,m_name from hc_medicine where m_generic_nm='" . $mgroup . "' and m_name!='" . html_entity_decode($mnm) . "'";

      //   print_r($myquery);
         //          die();
        $query = $this->db->query($myquery);

        // $query=$this->db->select('m_id,m_name')->get_where('hc_medicine',array('m_generic_nm'=>$mgroup));

        if ($query->num_rows() > 0) {
            $users = $query->result();
            $r = array();
            $i = 0;
            foreach ($users as $u) {
                $r[$i]['name'] = $u->m_name;
                /*  $r[$i]['med_stock'] = $this->get_Stock_hcmedicine($u->m_id);
                  $r[$i]['receive_stock'] = $this->get_Stock_hcmedireceive($u->m_id);
                  $r[$i]['counter_stock'] = $this->get_Stock_hcmediCounter($u->m_id); */
                $r[$i]['main_stock'] = $this->reg_model->get_mainstore_stock($u->m_id);
                $r[$i]['counter_stock'] = $this->reg_model->get_counter_stock($u->m_id);
                $r[$i]['doc_stock'] = $this->reg_model->get_doctor_stock($u->m_id);


                $i++;
            }
            return $r;
        } else {
            return false;
        }
    }

    //--------------------------------------------------------------------------------
    function get_ExpiryDate_List($id) {

        $myquery = "SELECT m_id,DATE_FORMAT(exp_date, '%d %b %Y') AS expdate FROM hc_medi_expdate WHERE m_id =" . $id . " and qty > 0  group by m_id,exp_date order by exp_date asc";

        //print_r($myquery);
        //  die();
        $query = $this->db->query($myquery);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    function get_Batch_No($dt, $mid) {

        $dt = $newDate = date("Y-m-d", strtotime($dt));
        $myquery = "select batchno,qty from hc_medi_expdate where m_id=" . $mid . " and exp_date='" . $dt . "'";

        //print_r($myquery);
        // die();
        $query = $this->db->query($myquery);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    //---------------------Save Data to Counter----------------

    function save_counter_stock($data) {

        if ($this->db->insert('hc_counter_stock', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }

    function get_Counter_Stock_ById($id) {
        /* $query = $this->db->query("SELECT
          hc_medicine.*,
          hc_counter_stock.*
          FROM hc_counter_stock
          INNER JOIN hc_medicine
          ON hc_counter_stock.m_id = hc_medicine.m_id
          WHERE hc_counter_stock.cs_id =".$id); */

        $query = $this->db->query(" SELECT hc_medicine.m_name, hc_counter_stock.cs_id, hc_counter_stock.m_id, DATE_FORMAT(hc_counter_stock.cs_exp_date, '%d %b %Y') AS cdate, hc_counter_stock.cs_batchno, hc_counter_stock.cs_qty FROM hc_medicine INNER JOIN hc_counter_stock ON hc_medicine.m_id = hc_counter_stock.m_id WHERE hc_counter_stock.cs_id =" . $id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

    function delete_counter_stock($id) {
        $this->db->where('cs_id', $id);
        $this->db->delete('hc_counter_stock');
    }

    function update_expdate_tbl($data) {
        $tqty = $this->db->select('qty')->get_where('hc_medi_expdate', array('m_id' => $data['m_id'], 'exp_date' => $data['cs_exp_date'], 'batchno' => $data['cs_batchno']));


        if ($tqty->num_rows() > 0) {
            $dt_qty['qty'] = ($tqty->row()->qty) - $data['cs_qty'];


            $this->db->update('hc_medi_expdate', $dt_qty, array('m_id' => $data['m_id'], 'exp_date' => $data['cs_exp_date'], 'batchno' => $data['cs_batchno']));
        } else {
            return false;
        }
    }

    function update_main_stock($mid, $qty) {
        $tqty = $this->db->select('ms_qty')->get_where('hc_mainstore_stock', array('m_id' => $mid));

        if ($tqty->num_rows() > 0) {
            $data['ms_qty'] = ($tqty->row()->ms_qty - $qty);

            $this->db->update('hc_mainstore_stock', $data, array('m_id' => $mid));
        } else {
            return false;
        }



        /* $tqty=
          $data = array('ms_qty' => ($tqty-$qty));
          $where = "m_id =".$mid;
          $str = $this->db->update('hc_mainstore_stock', $data, $where);



          /*$myquery="update hc_mainstore_stock set ms_qty=ms_qty-".$qty." where m_id=".$mid;

          $query = $this->db->query($myquery);
          if($query->num_rows() > 0) //
          {
          return true;
          }
          else
          {
          return false;
          } */
    }

    function getHcPatientCurrentDate($dt = '') {
		

        //  $q=$this->db->get_where('hc_patient',array('visit_date'=>date('Y-m-d')));
    if ($dt == '')
    { 
   $q = $this->db->query("select distinct pid, prel,m_status , visit_date,visit_no,pres_no,batchno from hc_patient WHERE date(visit_date)= CURDATE() group BY pid,prel,visit_no order by visit_date desc"); 
    } 
        else {
                
            $q = $this->db->query("select distinct pid, prel,m_status,visit_date,visit_no,pres_no,batchno from hc_patient WHERE date(visit_date)='" . $dt . "' group BY pid,prel,visit_no order by visit_date desc");
			/*$q = $this->db->query("select distinct pid, prel,m_status,visit_date,visit_no,pres_no,batchno from hc_patient WHERE m_status='Pending' group BY pid,prel,visit_no order by visit_date desc");*/
            
        }

   //echo $q;die();
//print_r($q->result());die();
        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_no'] = $e->visit_no;
                    $data[$i]['pres_no'] = $e->pres_no;
                    $data[$i]['batchno'] = $e->batchno;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                } else {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_no'] = $e->visit_no;
                    $data[$i]['pres_no'] = $e->pres_no;
                    $data[$i]['batchno'] = $e->batchno;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                }
                $i++;
            }
            return $data;
        }
        return false;
    }
    
    //-----------------------------------------Patient dues table------------------
    
      function getHcPatientCurrentDate_dues($dt = '') {

        //  $q=$this->db->get_where('hc_patient',array('visit_date'=>date('Y-m-d')));
    if ($dt == '')
    {
   $q = $this->db->query("select distinct pid, prel,m_status , visit_date,visit_no,pres_no from hc_patient_med_dues WHERE date(visit_date)= CURDATE() group BY 

pid,prel order by visit_date"); 
    } 
        else {
                        
            $q = $this->db->query("select distinct pid, prel,m_status,visit_date,visit_no,pres_no from hc_patient_med_dues WHERE date(visit_date)='" . $dt . "' group BY 

pid,prel order by visit_date");
            
            
        }
    //echo $this->db->last_query();
        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['visit_no'] = $e->visit_no;
                    $data[$i]['pres_no'] = $e->pres_no;
                     
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                } else {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['visit_no'] = $e->visit_no;
                    $data[$i]['pres_no'] = $e->pres_no; 
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                }
                $i++;
            }
            return $data;
        }
        return false;
    }
    
    //-----------------------------------------------------------------------
    

    function getPatientID_rel($id) {
        $this->db->select('pid');
        $this->db->select('prel');
        $this->db->from('hc_patient');
        $this->db->where('visitor_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function get_counter_stock_details($dt) {
        /* $query = $this->db->query("SELECT hc_medicine.m_name, hc_counter_stock.* FROM hc_counter_stock INNER JOIN hc_medicine
          ON hc_counter_stock.m_id = hc_medicine.m_id WHERE date(cs_date)= CURDATE()"); */

       /* $query = $this->db->query("SELECT hc_medicine.m_name, hc_counter_stock.* FROM hc_counter_stock INNER JOIN hc_medicine
                         ON hc_counter_stock.m_id = hc_medicine.m_id WHERE DATE_FORMAT(hc_counter_stock.cs_date, '%Y-%m-%d %H:%i')='" . $dt . "'");*/
		 $query = $this->db->query("SELECT hc_medicine.m_name, hc_counter_stock.* FROM hc_counter_stock INNER JOIN hc_medicine
                         ON hc_counter_stock.m_id = hc_medicine.m_id WHERE DATE_FORMAT(hc_counter_stock.cs_date, '%Y-%m-%d')/*date(cs_date)*/='" . $dt . "' and hc_counter_stock.status is null");
		
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_counter_stock_details_rejected($dt) {
        /* $query = $this->db->query("SELECT hc_medicine.m_name, hc_counter_stock.* FROM hc_counter_stock INNER JOIN hc_medicine
          ON hc_counter_stock.m_id = hc_medicine.m_id WHERE date(cs_date)= CURDATE()"); */

        $query = $this->db->query("SELECT hc_medicine.m_name, hc_counter_stock_log.* FROM hc_counter_stock_log INNER JOIN hc_medicine
                         ON hc_counter_stock_log.m_id = hc_medicine.m_id WHERE date(cs_date)='" . $dt . "'");

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_counter_stock_details_datewise() {
        //$myquery = "select DISTINCT  DATE_FORMAT(hc_counter_stock.cs_date, '%d %b %Y %H:%i') AS mrec_date from hc_counter_stock";
		//being changes as per request by ajay ji at data entry time
	$myquery = "select DISTINCT  DATE_FORMAT(hc_counter_stock.cs_date, '%d-%m-%Y') AS mrec_date from hc_counter_stock order by cs_id ";
        $query = $this->db->query($myquery);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function get_counter_rejected_details_datewise() {
        $myquery = "select DISTINCT  DATE_FORMAT(hc_counter_stock_log.cs_date, '%d %b %Y') AS mrec_date from hc_counter_stock_log";
        $query = $this->db->query($myquery);
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return FALSE;
        }
    }

    function update_patientTable($pid, $prel, $pvdate,$pvno,$ppresno) {
		
        $myquery = "update hc_patient set m_status='Issued',hc_patient.med_issued_by='".$this->session->userdata('id')."', hc_patient.med_issued_on = date_format('".$pvdate."','%Y-%m-%d %H:%i:%s')
 WHERE hc_patient.pid = '".$pid."' AND hc_patient.prel = '".$prel."' and visit_no='".$pvno."' and pres_no='".$ppresno."' "  ;
        $tqty = $this->db->query($myquery);
		//echo $this->db->last_query();die();
        return $tqty;
    }
	function update_patientTable_dues($pid, $prel, $pvdate,$pvno,$ppresno) {
		
        
						
$myquery = "update hc_patient_med_dues set m_status='Issued',hc_patient_med_dues.med_issued_by='".$this->session->userdata('id')."', hc_patient_med_dues.med_issued_on = date_format('".$pvdate."','%Y-%m-%d %H:%i:%s')
 WHERE hc_patient_med_dues.pid = '".$pid."' AND hc_patient_med_dues.prel = '".$prel."' and visit_no='".$pvno."' and pres_no='".$ppresno."' "  ;						
        $tqty = $this->db->query($myquery);
		//echo $this->db->last_query();die();
        return $tqty;
    }

    function Insert_or_Update_counterMaster($mid, $qty) {
        $tqty = $this->db->select('cs_qty')->get_where('hc_counter_master', array('m_id' => $mid));
        if ($tqty->num_rows() > 0) {
            $data['cs_qty'] = ($tqty->row()->cs_qty + $qty);

            $this->db->update('hc_counter_master', $data, array('m_id' => $mid));
            return TRUE;
        } else {
            $data = array(
                'm_id' => $mid,
                'cs_qty' => $qty
            );
            if ($this->db->insert('hc_counter_master', $data))
                return TRUE;
        }
    }
    //edit
    function insert_or_update_batch_detail($mid,$batno,$qty){
         $tqty = $this->db->select('qty')->get_where('hc_counter_batch_no_detail', array('batch_no' => $batno));
        if ($tqty->num_rows() > 0) {
            $data['qty'] = ($tqty->row()->qty + $qty);

            $this->db->update('hc_counter_batch_no_detail', $data, array('batch_no' => $batno));
            return TRUE;
        } else {
            $data = array(
                'm_id' => $mid,
                'batch_no' => $batno,
                'qty' => $qty
            );
            if ($this->db->insert('hc_counter_batch_no_detail', $data))
                return TRUE;
        }

    }

    // check dublicate entry on issue medicine to patient
//   function check_existing_entry_issue_to_patient($pid,$m_id,$dose,$ndays,$mqty,$batchno)
  function check_existing_entry_issue_to_patient($pid,$m_id,$hcpresno)
  {
    // $query = $this->db->query("SELECT a.* from hc_patient a where a.pid='".$pid."' and a.mid='".$m_id."' and a.dose='".$dose."' 
    // and a.ndays='".$ndays."' and a.mqty='".$mqty."' and a.batchno='".$batchno."' and date(a.visit_date)=CURDATE() ");
    $query = $this->db->query("SELECT a.* from hc_patient a where a.pid='".$pid."' and a.mid='".$m_id."' and a.pres_no='".$hcpresno."' and date(a.visit_date)=CURDATE() ");
            if ($query->num_rows() > 0) 
            {
                // duplicate entry found
                return true;
            }
    }
  // end check dublicate entry on issue medicine to patient

    // #@amit after reject issue to counter
    function update_hc_counter_stock_for_delete($id,$uid)
    {
        $status = "deleted";
        $reason = "deleted";

        $this->db->query("UPDATE hc_counter_stock set status = 'deleted' , reason = 'deleted' , rec_user_id = '$uid' where cs_id =" .$id);
        //$this->db->update('hc_counter_stock', array('cs_id' => $id), array('status' => $status , 'reason' => $reason , 'rec_user_id' => $uid));
        return TRUE;
    }

    function update_counter_batch_detail_tbl_after_reject($mid,$batno,$qty){
        $tqty = $this->db->select('qty')->get_where('hc_counter_batch_no_detail', array('batch_no' => $batno));
       if ($tqty->num_rows() > 0) {
           $data['qty'] = ($tqty->row()->qty - $qty);

           $this->db->update('hc_counter_batch_no_detail', $data, array('batch_no' => $batno));
           return TRUE;
       } else {
        return false;
       }

   }

   function get_hc_counter_stock_ById($id) {

    $query = $this->db->query(" SELECT hc_medicine.m_name, hc_counter_stock.cs_exp_date, 
    hc_counter_stock.cs_id, hc_counter_stock.m_id, 
    DATE_FORMAT(hc_counter_stock.cs_exp_date, '%d %b %Y') AS cdate, 
    hc_counter_stock.cs_batchno, hc_counter_stock.cs_qty 
    FROM hc_medicine INNER JOIN hc_counter_stock ON hc_medicine.m_id = hc_counter_stock.m_id 
    WHERE hc_counter_stock.cs_id =" . $id);
    if ($query->num_rows() > 0) { // 
        return $query->result();
    } else {
        return false;
    }
}

// #@amit end after reject issue to counter 


    //edit
    function update_batch_detail_one($batno,$qty){
         $tqty = $this->db->select('qty')->get_where('hc_counter_batch_no_detail', array('batch_no' => $batno));
        if ($tqty->num_rows() > 0) {
            $data['qty'] = ($tqty->row()->qty - $qty);

            $this->db->update('hc_counter_batch_no_detail', $data, array('batch_no' => $batno));
            return TRUE;
        } else {
            $data = array(
                'm_id' => $mid,
                'batch_no' => $batno,
                'qty' => $qty
            );
            // if ($this->db->insert('hc_counter_batch_no_detail', $data))
            //     return TRUE;
        }

    }

    function update_counter_master($pid, $prel, $pvdate,$pvno,$ppresno,$pbatchno) {
        date_default_timezone_set('Asia/Calcutta');
       /* if(!empty($pbatchno)){
        $myquery = "select * from hc_patient where pid='" . $pid . "' and prel='" . $prel . "' and date(visit_date)='" . $pvdate . "' and visit_no='".$pvno."' and pres_no='".$ppresno."' and batchno='".$pbatchno."'";
        }else{
          $myquery = "select * from hc_patient where pid='" . $pid . "' and prel='" . $prel . "' and date(visit_date)='" . $pvdate . "' and visit_no='".$pvno."' and pres_no='".$ppresno."'";  
        }*/
        $myquery = "select * from hc_patient where pid='" . $pid . "' and prel='" . $prel . "' and date(visit_date)='" . $pvdate . "' and visit_no='".$pvno."' and pres_no='".$ppresno."'";  
        $query = $this->db->query($myquery);
		//echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $users = $query->result();
            foreach ($users as $u) {
                $r = array();
                $r['pid'] = $u->pid;
                $r['prel'] = $u->prel;
                $r['m_id'] = $u->mid;
                $r['mqty'] = $u->mqty;
                $r['m_rec_date'] = date("Y-m-d H:i:s");
                $r['user_id'] = $this->session->userdata('id');
                $r['visit_no'] = $u->visit_no;
                $r['pres_no'] = $u->pres_no;
                $r['batchno'] = $u->batchno;
                $this->db->insert('hc_counter_med_issue', $r);
				//echo $this->db->last_query();die();
                // print_r($r);
                // To update counter master table
                $data = $this->db->select('cs_qty')->from('hc_counter_master')->where("m_id", $u->mid)->get();
                $p = $data->row()->cs_qty;
                if ($p) {
                    $up['cs_qty'] = $p - ($u->mqty);
                    $this->db->update('hc_counter_master', $up, array('m_id' => $u->mid));
                }
                // To update doctor issue table

                $data = $this->db->select('qty')->from('hc_doc_medi_issue')->where("m_id", $u->mid)->get();
                $p = $data->row()->qty;
                if ($p) {
                    $doc_up['qty'] = $p - ($u->mqty);
                    $this->db->update('hc_doc_medi_issue', $doc_up, array('m_id' => $u->mid));
                }
                // To update hc_counter_batch_no_detail
                $multipleWhere = ['m_id' => $u->mid, 'batch_no' =>  $u->batchno];
                $data = $this->db->select('qty')->from('hc_counter_batch_no_detail')->where($multipleWhere)->get();
                //echo $this->db->last_query();
                $p1 = $data->row()->qty;
                if ($p) {
                    $up1['qty'] = $p1 - ($u->mqty);
                    $this->db->update('hc_counter_batch_no_detail', $up1, array('m_id' => $u->mid,'batch_no' => $u->batchno));
                }
                //echo $this->db->last_query();die();
                
            }
            return TRUE;
        } else {
            return false;
        }
    }
	//Dues============
	function update_counter_master_dues($pid, $prel, $pvdate,$pvno,$ppresno) {
        date_default_timezone_set('Asia/Calcutta');
        $myquery = "select * from hc_patient_med_dues where pid='" . $pid . "' and prel='" . $prel . "' and date(visit_date)='" . $pvdate . "' and visit_no='".$pvno."' and pres_no='".$ppresno."'";
        $query = $this->db->query($myquery);
		//echo $this->db->last_query();die();
        if ($query->num_rows() > 0) {
            $users = $query->result();
            foreach ($users as $u) {
                $r = array();
                $r['pid'] = $u->pid;
                $r['prel'] = $u->prel;
                $r['m_id'] = $u->mid;
                $r['mqty'] = $u->mqty;
                $r['m_rec_date'] = date("Y-m-d H:i:s");
                $r['user_id'] = $this->session->userdata('id');
                $this->db->insert('hc_counter_med_issue', $r);
				//echo $this->db->last_query();die();
                // print_r($r);
                // To update counter master table
                $data = $this->db->select('cs_qty')->from('hc_counter_master')->where("m_id", $u->mid)->get();
                $p = $data->row()->cs_qty;
                if ($p) {
                    $up['cs_qty'] = $p - ($u->mqty);
                    $this->db->update('hc_counter_master', $up, array('m_id' => $u->mid));
                }
                // To update doctor issue table

                $data = $this->db->select('qty')->from('hc_doc_medi_issue')->where("m_id", $u->mid)->get();
                $p = $data->row()->qty;
                if ($p) {
                    $doc_up['qty'] = $p - ($u->mqty);
                    $this->db->update('hc_doc_medi_issue', $doc_up, array('m_id' => $u->mid));
                }
            }
            return TRUE;
        } else {
            return false;
        }
    }
	
	//===================

    function medi_rec_by_counter_status($dt, $s, $r, $uid, $id) {

        $myquery = "update hc_counter_stock set rec_datetime='" . $dt . "',status='" . $s . "',reason='" . $r . "', rec_user_id='" . $uid . "' where cs_id=" . $id;
        $query = $this->db->query($myquery);
        return true;
    }

    function getRejected_rows() {

        $this->db->select('*');
        $this->db->from('hc_counter_stock');
        $this->db->where('status', "rejected");
        $query = $this->db->get();
        //returns result objects array
        return $query->result();
    }

    function update_expdate_tbl_after_reject($mid, $qty, $expdt, $bno) {
        $tqty = $this->db->select('qty')->get_where('hc_medi_expdate', array('m_id' => $mid, 'exp_date' => $expdt, 'batchno' => $bno));

        if ($tqty->num_rows() > 0) {
            $data['qty'] = ($tqty->row()->qty + $qty);

            $this->db->update('hc_medi_expdate', $data, array('m_id' => $mid, 'exp_date' => $expdt, 'batchno' => $bno));
        } else {
            return false;
        }
    }

    function Update_counterMaster_reject($mid, $qty) {
        $tqty = $this->db->select('cs_qty')->get_where('hc_counter_master', array('m_id' => $mid));
        if ($tqty->num_rows() > 0) {
            $data['cs_qty'] = ($tqty->row()->cs_qty - $qty);

            $this->db->update('hc_counter_master', $data, array('m_id' => $mid));
        }

        return TRUE;
    }

    function insert_to_counter_logTBL($id) {
        $myquery = "insert into hc_counter_stock_log select * from hc_counter_stock where cs_id=" . $id;
        $query = $this->db->query($myquery);
        return true;
    }

    function delete_to_counter_TBL($id) {
        $myquery = "delete from hc_counter_stock where cs_id=" . $id;
        $query = $this->db->query($myquery);
        return true;
    }

    function save_docTemp_tbl($mid, $qty, $data_doc) {

        $tqty = $this->db->select('qty')->get_where('hc_doc_medi_issue', array('m_id' => $mid));
        //  print_r($tqty);
        // die();
        if ($tqty->num_rows() > 0) {
            $data['qty'] = ($tqty->row()->qty + $qty);

            $this->db->update('hc_doc_medi_issue', $data, array('m_id' => $mid));
            return TRUE;
        } else {
            if ($this->db->insert('hc_doc_medi_issue', $data_doc))
                return TRUE;
        }
    }

    function getNotificationUser($id) {
        $q = $this->db->get_where('user_auth_types', array('auth_id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    //--------Medical History of Employee------------

    function get_med_history_emp($id) {
        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname,
  hc_patient.visit_no,hc_patient.pres_no
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id
WHERE hc_patient.pid ='" . $id . "' ORDER BY date(hc_patient.visit_date) DESC
");


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    function get_med_history_emp_dues($id) {
        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient_med_dues.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient_med_dues.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname,
  hc_patient_med_dues.visit_no,hc_patient_med_dues.pres_no
FROM hc_patient_med_dues
  INNER JOIN user_details
    ON hc_patient_med_dues.doc_id = user_details.id
WHERE hc_patient_med_dues.pid ='" . $id . "' ORDER BY date(hc_patient_med_dues.visit_date) DESC
");


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //------------------------------------------------
    function get_emp_relation($id, $prel) {
        $query = $this->db->query("select * from emp_family_details where emp_no='" . $id . "' and sno=" . $prel);


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //------------SAave test of patient--------
    function get_pathology_test_list() {
        $this->db->from('hc_pathology_test');
        $this->db->order_by('t_name');
        $query = $this->db->get();
        $query_result = $query->result();
        return $query_result;
    }

    //-------Report based on date range-----------------------

    function getHcPatientDateRange($sdt, $edt) {

        //  $q=$this->db->get_where('hc_patient',array('visit_date'=>date('Y-m-d')));


        $q = $this->db->query("select distinct pid, prel,m_status,visit_date from hc_patient WHERE date(visit_date) between '" . $sdt . "' and '" . $edt . "' group BY pid ");

        //   print_r($q);
        //   die();

        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                } else {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                }
                $i++;
            }
            return $data;
        }
        return false;
    }

    //--------------------------------------------------------
    //----------------------Medicine by Name for Doctor Report-------

    function getHcPatient_byMedicineID($mid) {



        $q = $this->db->query("select * from hc_patient where mid=" . $mid);


        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {

                    $data[$i]['mid'] = $e->mid;
                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                    $data[$i]['mqty'] = $e->mqty;
                    $data[$i]['uprice'] = $this->reg_model->get_med_qty_from_hc_medi_rec_tbl($e->mid);
                    $data[$i]['cost'] = $data[$i]['mqty']*$data[$i]['uprice'];
                    
                } else {

                   
                    $data[$i]['mid'] = $e->mid;
                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                    $data[$i]['mqty'] = $e->mqty;
                    $data[$i]['uprice'] = $this->reg_model->get_med_qty_from_hc_medi_rec_tbl($e->mid);
                    $data[$i]['cost'] = $data[$i]['mqty']*$data[$i]['uprice'];
                    
                }
                $i++;
            }
            return $data;
        }
        return false;
    }

    //--------------------------------------------------------------
    //-------------------------Report for Doctor Group Name wise of medicine----------

    function getHcPatient_byGroupID($gid) {



        $q = $this->db->query("SELECT hc_patient.*, hc_med_group.mgroupc_id, hc_medicine.m_name
FROM hc_med_group INNER JOIN (hc_patient INNER JOIN hc_medicine ON hc_patient.mid = hc_medicine.m_id) ON hc_med_group.mgroupc_id = hc_medicine.m_generic_nm
WHERE hc_med_group.mgroupc_id=" . $gid);




        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {
                    $data[$i]['mid'] = $e->mid;
                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                    $data[$i]['mqty'] = $e->mqty;
                    $data[$i]['m_name'] = $e->m_name;
                      $data[$i]['uprice'] = $this->reg_model->get_med_qty_from_hc_medi_rec_tbl($e->mid);
                    $data[$i]['cost'] = $data[$i]['mqty']*$data[$i]['uprice'];
                } else {

                     $data[$i]['mid'] = $e->mid;
                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['visit_date'] = $e->visit_date;
                    $data[$i]['m_status'] = $e->m_status;
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                    $data[$i]['mqty'] = $e->mqty;
                    $data[$i]['m_name'] = $e->m_name;
                      $data[$i]['uprice'] = $this->reg_model->get_med_qty_from_hc_medi_rec_tbl($e->mid);
                    $data[$i]['cost'] = $data[$i]['mqty']*$data[$i]['uprice'];
                }
                $i++;
            }
            return $data;
        }
        return false;
    }

    //--------------------------------------------------------------------------------
    //-------------Function to find out cost of individual employee----------

    function get_emp_ind_cost($id) {
        $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname,hc_patient.pres_no
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id
WHERE hc_patient.pid ='" . $id . "' ORDER BY date(hc_patient.visit_date) DESC
");
//echo $this->db->last_query();die();

        if ($query->num_rows() > 0) {
            $users = $query->result();
            $r = array();
            $i = 0;
            foreach ($users as $u) {

                $r[$i]['emp_list'] = $this->reg_model->get_pat_list($u->pid, $u->prel, $u->visit,$u->pres_no);
				
                $i++;
            }


            return $r;
        } else {
            return false;
        }
    }

    //-----------------------------------------------------------------------

    function get_pat_list($pid, $prel, $dt,$pres_no) {

        $dtc = date('Y-m-d', strtotime($dt));
        $query = $this->db->query("select * from hc_patient where date(visit_date)='" . $dtc . "' and pid='" . $pid . "' and prel='" . $prel . "' and pres_no='" . $pres_no . "'");
//echo $this->db->last_query();die();
        if ($query->num_rows() > 0) {
            //return $query->result();
            //$tmp=$query->result();//print_r($tmp);//  print_r($tmp[0]->mid);   //  print_r($tmp[1]->mid);

            $i = 0;
            foreach ($query->result() as $e) {
                //print_r($e->mid); 
                $r[$i]['hc_pat_qty'] = $this->reg_model->get_med_qty_from_hc_patient($e->mid, $pid, $prel, $dtc,$pres_no);
				//echo $this->db->last_query();echo '<br>';
                $r[$i]['hc_med_rec_qty'] = $this->reg_model->get_med_qty_from_hc_medi_rec_tbl($e->mid,$e->batchno);
				//echo $this->db->last_query();die();
                $c[$i]['cost'] = $r[$i]['hc_pat_qty'] * $r[$i]['hc_med_rec_qty'];

                //  print_r($r[$i]['hc_pat_qty']*$r[$i]['hc_pat_qty']);
                // echo "-------------";
                //  print_r("A".$r[$i]['hc_pat_qty']);
                // die();

                $i++;
            }

            return $c;
        } else {
            return false;
        }
    }

    function get_med_qty_from_hc_patient($mid, $pid, $prel, $dtc,$pres_no) {
//$query = $this->db->query("select * from hc_patient where date(visit_date)='".$dtc."' and pid='".$pid."' and prel='".$prel."'");
//$query=$this->db->query("select mqty from  mqty where date(visit_date)='".$dtc."' and pid='".$pid."' and prel='".$prel."' and mid=".$mid.");

        $tqty = $this->db->select('mqty')->get_where('hc_patient', array('mid' => $mid, 'pid' => $pid, 'prel' => $prel, 'date(visit_date)' => $dtc, 'pres_no' => $pres_no));
        if ($tqty->num_rows() > 0) {
            //$data['qty_hcp'] =($tqty->row()->mqty);

            return($tqty->row()->mqty);
        }
    }

    function get_med_qty_from_hc_medi_rec_tbl($mid,$batchno) {
        $query = $this->db->query("select (amount/mrec_qty) as cost from hc_medi_receive where m_id=" . $mid . "  and batch_no='".$batchno."'  order by supp_date limit 1");
        if ($query->num_rows() > 0) {
            //$data['qty_hcp'] =($tqty->row()->mqty);

            return($query->row()->cost);
        }
    }
    
    function get_emp_ind_cost_all()
        {
             $query = $this->db->query("
 SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id
 ORDER BY date(hc_patient.visit_date) DESC
");
                          

		if($query->num_rows() > 0)  
             {
		$users = $query->result();
                $r = array();
               $i=0;
                foreach($users as $u){
                    
                    $r[$i]['emp_list']=$this->reg_model->get_pat_list($u->pid,$u->prel,$u->visit);
                    
                $i++;}
                
                
                return $r;
             }
	     else
             {
		return false;   
             }
       	
            
        }
         function get_med_history_all($pdate,$edate)
        {
             $q="SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id";
     if($pdate && $edate){  
$q.="  where  date(visit_date) between CAST('".$pdate."' AS DATE) AND CAST('".$edate."' AS DATE)";
     }
 $q.=" ORDER BY date(hc_patient.visit_date) DESC";
 
 
             $query = $this->db->query($q);
                          

		if($query->num_rows() > 0)  
                {
			return $query->result();
                }
		else
                    {
			return false;   
                }	
            
        }
        
        //------------------get only employee--------------------
        
         function get_med_history_all_emp($eid,$pdate,$edate)
        {
             $q="SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id";
 
     if($eid!="none")
     {
         $q.=" where pid='".$eid."'";
     }
     if($eid!="none" && $pdate && $edate){  
$q.="  and  date(visit_date) between CAST('".$pdate."' AS DATE) AND CAST('".$edate."' AS DATE)";
     }
     
     if($eid=="none")
     {
         $q.=" WHERE pid REGEXP '^[0-9]+$' ";
     }
     
     if($eid=="none" && $pdate && $edate){  
$q.="  and  date(visit_date) between CAST('".$pdate."' AS DATE) AND CAST('".$edate."' AS DATE)";
     }
     
 $q.=" ORDER BY date(hc_patient.visit_date) DESC";
 
 
          
             $query = $this->db->query($q);
                          
		if($query->num_rows() > 0)  
                {
			return $query->result();
                }
		else
                    {
			return false;   
                }	
            
        }
        
        //--------------get only student
        
        
         function get_med_history_all_stu($sid,$pdate,$edate)
        {
             $q="SELECT DISTINCT
  DATE_FORMAT(hc_patient.visit_date, '%d %b %Y') AS visit,pid,prel,
  hc_patient.doc_id,concat(user_details.first_name,' ', user_details.middle_name,' ', user_details.last_name)as dname
FROM hc_patient
  INNER JOIN user_details
    ON hc_patient.doc_id = user_details.id";
 
     if($sid!="")
     {
         $q.=" where pid='".$sid."'";
     }
     if($sid!="" && $pdate && $edate){  
$q.="  and  date(visit_date) between CAST('".$pdate."' AS DATE) AND CAST('".$edate."' AS DATE)";
     }
     
     if($sid=="")
     {
         $q.=" WHERE pid NOT REGEXP '^[0-9]+$' ";
     }
     
     if($sid=="" && $pdate && $edate){  
$q.="  and  date(visit_date) between CAST('".$pdate."' AS DATE) AND CAST('".$edate."' AS DATE)";
     }
     
 $q.=" ORDER BY date(hc_patient.visit_date) DESC";
 
 
          
             $query = $this->db->query($q);
                          
		if($query->num_rows() > 0)  
                {
			return $query->result();
                }
		else
                    {
			return false;   
                }	
            
       
        }
        
        function update_pat_test($data,$id)
        {
            $this->db->update('hc_pat_test', $data, array('test_id' => $id));
        return TRUE;
        }
        
        function getHcPatientCurrentDate_test($dt = '') {

        //  $q=$this->db->get_where('hc_patient',array('visit_date'=>date('Y-m-d')));
        if ($dt == '') {
            $q = $this->db->query("select distinct pid, prel,test_status,test_obser_date from hc_pat_test WHERE date(test_obser_date)= CURDATE() group BY pid");
        } else {
            $q = $this->db->query("select distinct pid, prel,test_status,test_obser_date from hc_pat_test WHERE date(test_obser_date)='" . $dt . "' group BY pid ");
        }

        if ($q->num_rows > 0) {
            $i = 0;
            foreach ($q->result() as $e) {
                if ($e->prel == 'Self') {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['test_obser_date'] = $e->test_obser_date;
                    $data[$i]['test_status'] = $e->test_status;
                     
                    $data[$i]['vname'] = $this->get_hold_list_self($e->pid)->name;
                } else {

                    $data[$i]['pid'] = $e->pid;
                    $data[$i]['prel'] = $e->prel;
                    $data[$i]['test_obser_date'] = $e->test_obser_date;
                    $data[$i]['test_status'] = $e->test_status;
                     
                    $data[$i]['vname'] = $this->get_hold_list($e->pid, $e->prel)->name;
                }
                $i++;
            }
            return $data;
        }
        return false;
    }

    function check_medicineIssue_patientTable($pid,$prel,$dt,$pvno,$ppresno)
    {
        $q = $this->db->query("select distinct pid, prel,m_status , visit_date,visit_no,pres_no from hc_patient WHERE pid='".$pid."' and prel='".$prel."' and date(visit_date)= '".$dt."' and visit_no='".$pvno."' and pres_no='".$ppresno."' group BY prel");
        
       // echo $this->db->last_query();
       
        if($q->num_rows() > 0)  
                {
			return $q->result();
                }
		else
                    {
			return false;   
                }
    }
	function check_medicineIssue_patientTable_dues($pid,$prel,$dt)
    {
        $q = $this->db->query("select distinct pid, prel,m_status , visit_date from hc_patient_med_dues WHERE pid='".$pid."' and prel='".$prel."' and date(visit_date)= '".$dt."' group BY prel");
        
       // echo $this->db->last_query();
       
        if($q->num_rows() > 0)  
                {
			return $q->result();
                }
		else
                    {
			return false;   
                }
    }
	function check_medi_issue_vs_counter($pid,$prel,$pvdate,$vno,$pno) {
        $query = $this->db->query("select x.* from(
SELECT  a.pid, a.prel,a.m_status,a.visit_date,a.mid,a.mqty,
CASE WHEN b.cs_qty is null THEN 0 ELSE b.cs_qty END as cs_qty,
CASE WHEN a.mqty > b.cs_qty THEN 'false' WHEN b.cs_qty is null THEN 'false' ELSE 'true' END as medi_status,c.m_name
 FROM hc_patient a
left join hc_counter_master b on b.m_id=a.mid
inner join hc_medicine c on c.m_id=a.mid
WHERE a.pid='".$pid."' AND a.prel='".$prel."' AND DATE(a.visit_date)= '".$pvdate."' and a.visit_no='".$vno."' and a.pres_no='".$pno."')x
where x.medi_status='false'");

        //print_r($query);die();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    function get_visit_number_patient($pid,$prel,$pvdate) {
        $query = $this->db->query("select x.* from ( select a.visit_no from hc_patient a where a.pid='".$pid."' and a.prel='".$prel."' and DATE_FORMAT(a.visit_date,'%Y-%m-%d')='".$pvdate."' 
group by a.pid,a.prel,a.visit_no )x
order by x.visit_no desc limit 1
");


        if ($query->num_rows() > 0) {
            return $query->row()->visit_no;
        } else {
            return '0';
        }
    }
    
    function check_current_dat($dt) {
        $query = $this->db->query("select * from hc_patient_prescription_counter 
where DATE_FORMAT(vdate,'%Y-%m-%d')='".$dt."' ");


        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
             return false;
        }
    }
    function insert_current_dat($dt) {
        $data = array(
            'vdate' => date('Y-m-d',strtotime($dt)),
            'pres_no' =>'1'
        );
        $this->db->insert('hc_patient_prescription_counter', $data);
    }
    
    function update_current_dt_presno($dt,$value){
   $data = array(
            'pres_no' =>$value
        ); 
$this->db->where('vdate', date('Y-m-d',strtotime($dt)));  
$this->db->update('hc_patient_prescription_counter', $data); 
        
    }
    
    function get_datewise_counter_receive($dt){
        $query = $this->db->query("select hc_counter_stock.*,hc_medicine.m_name from hc_counter_stock 
inner join hc_medicine on hc_medicine.m_id=hc_counter_stock.m_id
where DATE_FORMAT(hc_counter_stock.cs_date, '%Y-%m-%d')='".$dt."'");


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
             return false;
        }
        
    }

    //Medicine Return functions

        function get_batch_number_list_from_received_tbl($id){
        $query = $this->db->query("select a.batch_no,a.exp_date,a.mrp from hc_medi_receive a 
left join hc_pur_order b on b.po_id=a.po_id
left join hc_indent c on c.indent_id=b.indent_id
left join hc_supplier d on d.s_id=c.s_id
where a.m_id='".$id."'
group by a.batch_no,a.exp_date,a.mrp");


        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
             return false;
        }
        
    }
    function save_return_stock($data) {

        if ($this->db->insert('hc_return_stock', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }

     function get_Return_Stock_ById($id) {
     

        $query = $this->db->query(" SELECT hc_return_stock.id,hc_medicine.m_name, hc_return_stock.m_id, 
hc_return_stock.batchno,
DATE_FORMAT(hc_return_stock.exp_date, '%d %b %Y') AS rdate, 
hc_return_stock.mrp,
 hc_return_stock.ret_qty
FROM hc_medicine
INNER JOIN hc_return_stock ON hc_medicine.m_id = hc_return_stock.m_id
WHERE hc_return_stock.id =" . $id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }
    }

     function delete_return_stock($id) {
        $this->db->where('id', $id);
        $this->db->delete('hc_return_stock');
    }

    function get_return_date_by_suppID($id){

             $query = $this->db->query(" SELECT DATE_FORMAT(hc_return_stock.ret_timestamp, '%d %b %Y') AS rdate
from hc_return_stock
WHERE hc_return_stock.supp_id='".$id."'
group by DATE_FORMAT(hc_return_stock.ret_timestamp, '%d %b %Y')");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
    function get_supplier_return_datewise($suppid,$retdt){

 $query = $this->db->query(" select a.*,b.m_name from hc_return_stock a
inner join hc_medicine b on b.m_id=a.m_id
where a.supp_id='".$suppid."' and 
DATE_FORMAT(a.ret_timestamp, '%Y-%m-%d')='".$retdt."'
and a.`status` is null
order by b.m_name");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
    function update_counter_for_return($qty,$mid){

        $sql = "update hc_counter_master set cs_qty=cs_qty-".$qty." where m_id=".$mid;
            $query = $this->db->query($sql);

    }
    function check_counter_for_return($mid){
$query = $this->db->query(" select a.* from hc_counter_master a where a.m_id =".$mid);
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }

    function update_return_table_status($suppid,$ret_date,$qty,$mid){

        $sql = "update hc_return_stock set rec_datetime='".date("Y-m-d H:i:s")."' , status='success',rec_user_id='".$this->session->userdata('id')."' 
where supp_id='".$suppid."' and DATE_FORMAT(ret_timestamp, '%Y-%m-%d')='".$ret_date."' and m_id='".$mid."' and ret_qty='".$qty."'";
            $query = $this->db->query($sql);
    }

    function get_batch_no_details($id){
//modififed code
$query = $this->db->query("select a.cs_batchno from hc_counter_stock a where a.m_id='".$id."'
group by a.cs_batchno");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }

       function get_batch_no_details_prescription($id){
//modififed code
        // edit
        // $query = $this->db->query("SELECT hc_counter_batch_no_detail.batch_no FROM hc_counter_batch_no_detail INNER JOIN hc_medi_expdate ON
        // (hc_counter_batch_no_detail.m_id = hc_medi_expdate.m_id AND hc_counter_batch_no_detail.batch_no = hc_medi_expdate.batchno)
        //   WHERE hc_counter_batch_no_detail.m_id = '".$id."' and hc_counter_batch_no_detail.qty>0 AND hc_medi_expdate.exp_date >= CURDATE()");
    $query = $this->db->query("select a.batch_no from hc_counter_batch_no_detail a where a.m_id='".$id."' and a.qty>0");
    
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
    function get_batchno_medi_reci($id){
//modififed code
        // edit

$query = $this->db->query("select a.batch_no from hc_medi_receive a where a.m_id='".$id."' group by a.batch_no");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }

     function get_batch_no_details_one($id,$batchno){
//modififed code  a.m_id='".$id."'
        // edit to be added if lala-> and a.batch_no='".$batchno."'
        // echo $id." ".$batchno;
$query = $this->db->query("select a.qty as sum from hc_counter_batch_no_detail a where  a.batch_no='".$batchno."'");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
    
    function get_exp_date_batchno_wise($batchno,$mid){

$query = $this->db->query("select DATE_FORMAT(a.cs_exp_date,'%d-%m-%Y') as exp_date from hc_counter_stock a
where a.cs_batchno='".$batchno."'and a.m_id=".$mid);
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }

    }

    function get_medicine_qty_from_received_table($mid,$dt,$batchno){

$query = $this->db->query("select a.mrec_qty from hc_medi_receive a where a.m_id='".$mid."'
and a.supp_date='".$dt."' and a.batch_no='".$batchno."'");
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }

    }

    function get_medicine_supply_date_from_received_table($mid,$batchno){
$query = $this->db->query("select a.supp_date from hc_medi_receive a where a.m_id='".$mid."' and a.batch_no='".$batchno."'");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }

    //by anuj 28-02-2020
    
    function check_existing_medicine_batchno($mid,$batchno){
$query = $this->db->query("select a.* from hc_counter_batch_no_detail a where a.m_id='".$mid."' and a.batch_no='".$batchno."'");
        if ($query->num_rows() > 0) { // 
            return true;
        } else {
            return false;
        }

    }

    function update_entry($mid,$batchno,$qty)
    {
   
     $myquery = "update hc_counter_batch_no_detail SET qty=qty+".$qty." WHERE m_id='".$mid."' AND batch_no='".$batchno."'";
     $query = $this->db->query($myquery);
     return TRUE;
          
    }

    function check_unaccecpted_medicine(){
        $query = $this->db->query("SELECT a.* FROM hc_counter_stock a WHERE  a.status IS NULL");
        if ($query->num_rows() > 0) { // 
            return true;
        } else {
            return false;
        }

    }
    
    function get_batch_number_details($id){
        $query = $this->db->query("SELECT a.batch_no,a.qty FROM hc_counter_batch_no_detail a WHERE a.m_id=".$id);
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
    //===========================
    
    function get_counter_stock_medicine($id){
        /*$query = $this->db->query("SELECT a.*,b.m_name FROM hc_counter_master a 
INNER JOIN hc_medicine b ON b.m_id=a.m_id ORDER BY a.cs_qty");*/
$query = $this->db->query("SELECT a.*,b.m_name,b.activity,b.`type` FROM hc_mainstore_stock a 
INNER JOIN hc_medicine b ON b.m_id=a.m_id ORDER BY a.ms_qty");
        if ($query->num_rows() > 0) {  
            return $query->result();
        } else {
            return false;
        }

    }
    function pending_report(){
        $query = $this->db->query("SELECT a.mid,b.m_name,sum(a.mqty)AS qty FROM hc_patient a 
INNER JOIN hc_medicine b ON b.m_id=a.mid
WHERE a.m_status='Pending'
GROUP BY a.mid
ORDER BY trim(b.m_name)");
        if ($query->num_rows() > 0) { // 
            return $query->result();
        } else {
            return false;
        }

    }
	
	function update_counter_master_direct($mid,$qty)
	{
		$myquery = "UPDATE hc_counter_master SET cs_qty=cs_qty-".$qty." WHERE m_id=".$mid;
        
        
       
        $query = $this->db->query($myquery);

            return TRUE;
        
		
	}
	function update_counter_batch_no_detail_direct($mid,$qty,$batchno)
	{
		$myquery = "UPDATE hc_counter_batch_no_detail SET qty=qty-".$qty." WHERE m_id=".$mid." AND batch_no='".$batchno."'";
        
        
       
        $query = $this->db->query($myquery);

            return TRUE;
        
					
	}
	function get_med_id_from_patient_table_new($id)
    {
         $this->db->select('*'); 
        $this->db->from('hc_patient');
        $this->db->where('visitor_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) { // 
            return $query->row();
        } else {
            return false;
        }
    }
	
	function add_back_to_counter_master($mid,$qty){
		$myquery = "UPDATE hc_counter_master SET cs_qty=cs_qty+".$qty." WHERE m_id=".$mid; 
       
        $query = $this->db->query($myquery);

            return TRUE;

	}		
		function add_back_to_counter_batchno($mid,$qty,$batchno){
			
			$myquery = "UPDATE hc_counter_batch_no_detail SET qty=qty+".$qty." WHERE m_id=".$mid." AND batch_no='".$batchno."'";
        
        
       
        $query = $this->db->query($myquery);

            return TRUE;
		}
    
}
?>



