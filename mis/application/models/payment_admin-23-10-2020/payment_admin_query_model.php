<?php

class Payment_admin_query_model extends CI_Model{

        private $tabulation='parentlive';
        private $pbeta = 'parentlive';
        //private $misdev = 'misdev';
        //private $pbeta = 'parentbeta';

	function __construct(){
		parent::__construct();
    }


    public function get_no_dues_status($auth){
		$ts=$this->ndqm->curr_time_stamp();
		$session_year_curr=$this->ndqm->get_session($ts);
		//echo $sql = "SELECT * FROM no_dues_start where session_year='$session_year_curr' and access_to='$auth' and status!=2"; exit;
        $res=$this->db->query("SELECT * FROM payment_manage_portal where session_year='$session_year_curr' and access_to='$auth' and status!=2")->result_array();
        if(!$res) return 10;
        else return $res;
    }

    public function curr_time_stamp(){
		$curr_time = date_create();
		$ts = date_format($curr_time, 'Y-m-d H:i:s');
		return $ts;
	}


	public function get_session($ts){
		 $year = explode('-', $ts)[0];
        $month=explode('-',$ts)[1];
        $m=strval((int)$month);
        if($m>=7&&$m<=12){  
            $p_year = strval((int)$year);  // 2019
            $year = $p_year +1;  // if month is greater than 6 then year will be present year + 1 

        }
        else
        $p_year = strval((int)$year - 1);  // if month is less than 6 then year will be present year will be  year - 1
        return $p_year.'-'.$year;
  }
  
  public function insert_into_sbi_merchant_panel_details($data)
  {

    $this->db->insert('sbi_merchant_panel_details', $data);

  }

  public function getMerchantDetails()
  {
    $sql = "select * from sbi_merchant_panel_details";
    $query = $this->db->query($sql);
    return $query->result_array();

  }

  public function get_last_updated_settlement_date()
  {
    $sql = "SELECT settlement_date
    FROM  sbi_final_settlement_data
    group BY settlement_date ORDER BY STR_TO_DATE(settlement_date,'%d-%m-%y') DESC LIMIT 1";

    $query = $this->db->query($sql);
    $settle_date = $query->result_array();
    return $settle_date['0']['settlement_date'];
  }


  public function get_last_updated_refund_date()
  {
    $sql = "SELECT refund_booking_date_time
    FROM  sbi_final_refund_data
    group BY refund_booking_date_time ORDER BY refund_booking_date_time DESC LIMIT 1";

    $query = $this->db->query($sql);
    $settle_date = $query->result_array();
    return $settle_date['0']['refund_booking_date_time'];
  }


  public function get_last_updated_order_date()
  {
    $sql = "SELECT order_booking_date_time
    FROM  sbi_full_transaction
    group BY order_booking_date_time ORDER BY STR_TO_DATE(order_booking_date_time,'%d-%m-%y') DESC LIMIT 1";

    $query = $this->db->query($sql);
    $settle_date = $query->result_array();
    return $settle_date['0']['order_booking_date_time'];
  }


  public function search_duplicate_entry_full_trans($order_number)
  {

    $sql = "select * from sbi_full_transaction where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    if($query != false)
    {
      return $query->num_rows();
    }
    else
    {
    return false;
    }

  }

  public function getMerchant_settlement_Details()
  {

    
    $sql = "select * from sbi_final_settlement_data";
    $query = $this->db->query($sql);
    if($query != false)
    {

      return $query->result_array();
    }
    else
   {

      return false;
   }

  }

  public function getMerchant_full_trans_Details()
  {

    $sql = "select * from sbi_full_transaction";
    $query = $this->db->query($sql);
    return $query->result_array();

      
  }

  public function insert_merchant_full_transactions($data)
  {

    $sql = "select * from sbi_full_transaction where merchant_order_number = ?";
    $query = $this->db->query($sql,array($data['merchant_order_number']));
    //echo $this->db->last_query();
    if($query->num_rows() > 0)
    {
      $this->db->where('merchant_order_number', $data['merchant_order_number']);
      $this->db->update('sbi_full_transaction', $data);
      return 'update_success';
    }
    else
    {
    $this->db->insert('sbi_full_transaction', $data);
    return 'insert_success';
    }
  }

  public function insert_reg_regular_fee($data_bank_fee_pending)
  {

    $query = $this->db->select('*')->from('bank_fee_sinking_pending') 
                    ->where('bank_fee_id',$data_bank_fee_pending['bank_fee_id'])
                    ->where('txnid',$data_bank_fee_pending['txnid'])
                    ->where('form_id',$data_bank_fee_pending['form_id'])
                    ->get();

     if($query->num_rows() == 0)
     {

    $this->db->insert('bank_fee_sinking_pending', $data_bank_fee_pending);

     }

     else
     {

      return false;
     }

  }

  public function sinking_reg_regular_bank_fee_pending()
  {

    $query = $this->db->select('*')->from('bank_fee_sinking_pending')
                      ->where('status','Pending')
                      ->get();
    
    return $query->result_array();


  }

  public function sinking_reg_regular_bank_fee_success()
  {

    $query = $this->db->select('*')->from('bank_fee_sinking_pending')
                      ->where('status','sink_done')
                      ->get();
    
    return $query->result_array();


  }


  public function update_reg_regular_fee_data($form_id,$admn_no,$data_fee_receipt_update)

  {
  
  
      $this->db->where('form_id', $form_id);
      $this->db->where('admn_no', $admn_no);
      $this->db->update('reg_regular_fee',$data_fee_receipt_update);


  }

  public function get_payment_status()
  {

     $sql = "select order_status from sbi_full_transaction";
     $query = $this->db->query($sql);
     return $query->result_array();

  }

  public function get_bank_fee_sbi_details()
  {

    $sql = "SELECT a.*,b.*,b.amount AS sbi_success_amount , a.id as bank_fee_id FROM bank_fee_details a INNER JOIN sbi_success_details_semester_fees b on a.admn_no = b.admn_no WHERE a.payment_mode = 'online' and a.payment_status = 1 group by a.admn_no , b.admn_no";
    $query = $this->db->query($sql);
    return $query->result_array();
      
  }

  public function update_reg_regular_fee($data_reg_regular_fee,$form_id,$admn_no,$bank_fee_id)
  {

    $this->db->where('form_id', $form_id);
    $this->db->where('admn_no', $admn_no);
    $this->db->update('reg_regular_fee', $data_reg_regular_fee);


    $data = array(

      'status' => 'sink_done'
    );

    $this->db->where('bank_fee_id', $bank_fee_id);
    $this->db->update('bank_fee_sinking_pending', $data);


  }

  public function get_list_student_paid_online($session_year,$session)
  {

    $sql = "SELECT *
    FROM bank_fee_details a
    INNER JOIN sbi_success_details_semester_fees e ON e.admn_no = a.admn_no
    INNER JOIN reg_regular_fee d ON d.form_id = e.form_id
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_mode='online' and a.payment_status='1' AND a.session_year='".$session_year."' AND a.`session`='".$session."' group by a.admn_no , e.admn_no , d.admn_no , f.form_id";
    $query = $this->db->query($sql);
    return $query->result_array();

  }


  public function get_list_student_paid_online_failure($session_year,$session)
  {

    $sql = "SELECT *
    FROM bank_fee_details a
    INNER JOIN sbi_failure_details_semester_fees e ON e.admn_no = a.admn_no
    INNER JOIN reg_regular_fee d ON d.form_id = e.form_id
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_mode='online' and a.payment_status='2' AND a.session_year='".$session_year."' AND a.`session`='".$session."' group by a.admn_no , e.admn_no , d.admn_no , f.form_id";
    $query = $this->db->query($sql);
    return $query->result_array();

  }

  public function get_list_student_paid_offline($session_year,$session)
  {

    $sql = "SELECT * FROM bank_fee_details a 
    INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_mode='offline' AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.admn_no , f.form_id";
    $query = $this->db->query($sql);
    return $query->result_array();
 
  }

  public function get_list_student_not_paid($session_year,$session)
  {

    $sql = "SELECT * FROM bank_fee_details a 
    INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_status=0 AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.form_id , f.form_id";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); exit;
    return $query->result_array();

  }

  public function get_course_name($couse_id)
  {

      $sql = "select * from cs_courses where id = ?";
      $query = $this->db->query($sql,array($couse_id));
      $course_array = $query->result_array();
      return $course_array['0']['name'];

  }

  public function get_branch_name($branch_id)
  {

    $sql = "select * from cs_branches where id = ?";
    $query = $this->db->query($sql,array($branch_id));
    $course_array = $query->result_array();
    return $course_array['0']['name'];
  
  }

  public function get_bank_fee_details($id)
  {

      //$CI = &get_instance();
      //$this->db2 = $CI->load->database($this->tabulation, TRUE);
      $sql = "select * from bank_fee_details where id = ?";
      $query = $this->db->query($sql,array($id));
      return $query->result_array();
  }

  public function get_filter_pay_details($to_date,$from_date,$pay_status)
  {

    //$to_date;
    //$from_date;
    //echo $pay_status;

    $sql = "select * from sbi_full_transaction where order_status = '".$pay_status."' and STR_TO_DATE(order_booking_date_time,'%d-%m-%Y') BETWEEN '".$to_date."' AND '".$from_date."' GROUP BY order_booking_date_time order by STR_TO_DATE(order_booking_date_time,'%d-%m-%Y') asc";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); die();

    return $query->result_array();

  }

  public function search_duplicate_entry($atrn)
  {

      $sql = "select * from sbi_final_settlement_data where sbi_reference_no_atrn = ?";
      $query = $this->db->query($sql,array($atrn));
      return $query->num_rows();

  }
  

  public function search_duplicate_entry_refund($atrn) 
  
  {

      $sql = "select * from sbi_final_refund_data where atrn_reference_number = ?";
      $query = $this->db->query($sql,array($atrn));
      return $query->num_rows();
  }

  public function approve_reject_receipt($id,$remark_status,$approve_reject_status)
  {

    $update_rows = array('verify_status' => $approve_reject_status, 'verify_remark' => $remark_status);
		$this->db->where('id',$id);
    $this->db->update('bank_fee_offline_verify_receipt', $update_rows);	
    
    $this->db->select('verify_status')->from('bank_fee_offline_verify_receipt')
              ->where('id',$id);

  }

  public function get_bank_fee_details_by_id($id)
  {

     $query = $this->db->select('*')->from('bank_fee_details')
                      ->where('id',$id);

      return $query->result_array();

  }

  public function approve_reject_receipt_insert($datainsert)
  {

       $bank_fee_id = $datainsert['bank_fee_id'];

       $query = $this->db->select('*')->from('bank_fee_offline_verify_receipt')
                    ->where('bank_fee_id',$bank_fee_id)
                    ->get();

                    $rows_present = $query->num_rows();

       if($rows_present > 0)
       {

        $this->db->where('bank_fee_id', $bank_fee_id);
        $this->db->update('bank_fee_offline_verify_receipt', $datainsert);

        $verify_status = $this->get_verify_status($bank_fee_id); 

       }

       else {
        

       $this->db->insert('bank_fee_offline_verify_receipt',$datainsert);
       //$insert_id = $this->db->insert_id();
      
       $verify_status = $this->get_verify_status($bank_fee_id); 


       }

       return  $verify_status;

       //}
  }

  public function get_session_year()
  {

    //$this->db->select('*');
    $query = $this->db->get('mis_session_year');
    //echo $this->db->last_query(); //die();
    return $query->result_array();
  }

  public function get_session_upload()
  {

    //$this->db->select('*');
    $query = $this->db->get('mis_session');
   // echo $this->db->last_query(); die();
    return $query->result_array();
  }

  public function get_offline_receipt_list($session_year,$session)
  {

    $query = $this->db->select('*')->from('bank_fee_details')
              ->where('session_year',$session_year)
              ->where('session',$session)
              ->where('payment_mode','offline')
              ->get();

    return $query->result_array();
    
  }

  public function get_verify_status($offline_id)
  {

    $query =  $this->db->select('*')->from('bank_fee_offline_verify_receipt')
              ->where('bank_fee_id', $offline_id)
              ->get();

    $rows_present = $query->num_rows();

    if($rows_present > 0)
    {

      $query =  $this->db->select('verify_status')->from('bank_fee_offline_verify_receipt')
      ->where('bank_fee_id', $offline_id)
      ->get();

      $query_status = $query->result_array();

      return $query_status['0']['verify_status'];
       

    }

    else
    {

        return false;
    }

  }


  public function approve_reject_receipt_update($bank_fee_offline_id,$remark_status,$approve_reject_status)
  {

     $data = array(

      'verify_status' => $approve_reject_status,
      'verify_remark' => $remark_status,

     );

     $this->db->where('bank_fee_id',$bank_fee_offline_id);
     $this->db->update('bank_fee_offline_verify_receipt', $data);

     //echo $this->db->last_query(); die();

     $verify_status = $this->get_verify_status($bank_fee_offline_id); 

     return $verify_status;



  }


  public function get_offline_receipt_verify_details($offline_receipt_verify_id)
  {


    $query =  $this->db->select('*')->from('bank_fee_offline_verify_receipt')
              ->where('bank_fee_id', $offline_receipt_verify_id)
              ->get();

    $rows_present = $query->num_rows();

    if($rows_present > 0)
    {

      $query =  $this->db->select('*')->from('bank_fee_offline_verify_receipt')
      ->where('bank_fee_id', $offline_receipt_verify_id)
      ->get();

      $query_status = $query->result_array();

      return $query_status;
       

    }

    else
    {

        return '';
    }

  }


  public function get_verify_remark($offline_id)
  {

    $query =  $this->db->select('*')->from('bank_fee_offline_verify_receipt')
              ->where('bank_fee_id', $offline_id)
              ->get();

    $rows_present = $query->num_rows();

    if($rows_present > 0)
    {

      $query =  $this->db->select('verify_remark')->from('bank_fee_offline_verify_receipt')
      ->where('bank_fee_id', $offline_id)
      ->get();

      $query_status = $query->result_array();

      return $query_status['0']['verify_remark'];
       
    }

    else
    {

      return false;

    }

  }

  function get_student_details($id){
    // $sql = "SELECT UPPER(a.id) AS admn_no, UPPER(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)) AS stu_name,
    // CASE b.course_id WHEN  'exemtech' THEN  'M.TECH 3 YR' ELSE b.course_id END as course_id,b.branch_id,c.name, 
    // UPPER(CONCAT(CASE b.course_id WHEN  'exemtech' THEN 'M.TECH 3 YR' ELSE b.course_id END,' ( ',c.name,' ) ')) AS discipline,
    // a.photopath
    // , d.name as cname,c.name as bname
    // FROM user_details a
    // INNER JOIN stu_academic b ON b.admn_no=a.id
    // inner join cs_courses d on d.id=b.course_id
    // INNER JOIN cs_branches c ON c.id=b.branch_id
    // WHERE a.id=?";

//         $sql = "SELECT UPPER(a.id) AS admn_no, UPPER(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)) AS stu_name, 
// CASE b.course_id WHEN  'exemtech' THEN 'M.TECH 3 YR' ELSE b.course_id END as course_id,b.branch_id,c.name, UPPER(CONCAT(CASE b.course_id WHEN  'exemtech' THEN 'M.TECH 3 YR' ELSE b.course_id END,' ( ',c.name,' ) ')) AS discipline,a.photopath
// FROM user_details a
// INNER JOIN stu_academic b ON b.admn_no=a.id
// INNER JOIN cs_branches c ON c.id=b.branch_id
// WHERE a.id=?";

    $sql = "SELECT UPPER(a.id) AS admn_no, UPPER(CONCAT_WS(' ',a.first_name,a.middle_name,a.last_name)) AS stu_name
    FROM user_details a
    WHERE a.id=?";

    $query = $this->db->query($sql,array($id));
    //echo $this->db->last_query();die();
    if ($this->db->affected_rows() >= 0) {
        return $query->result_array();
    } else {
        return false;
    }
    
}

  public function insert_into_sbi_settlement_data($data)
  {

    $sql = "select * from sbi_final_settlement_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($data['merchant_order_number']));

    //echo "hii".$query->num_rows();

     if($query->num_rows() > 0) {
       
      $this->db->where('merchant_order_number', $data['merchant_order_number']);
      $this->db->update('sbi_final_settlement_data', $data);
      return 'update_success';
      
     }

     else

     {

     $this->db->insert('sbi_final_settlement_data',$data);
     $insert_id = $this->db->insert_id();
     if($insert_id != '')
     {
        return $insert_id;
     }

     else
     {
       return false;
     }

    }

  }

  public function insert_into_sbi_refund_data($data)
  {

    $sql = "select * from sbi_final_refund_data where merchant_order_number = ?"; 
    $query = $this->db->query($sql,array($data['merchant_order_number']));

    if($query->num_rows() > 0)
    {

      $this->db->where('merchant_order_number', $data['merchant_order_number']);
      $this->db->update('sbi_final_refund_data', $data);
      return 'update_success';
    }

    else
    {
     $this->db->insert('sbi_final_refund_data',$data);
     $insert_id = $this->db->insert_id();
     if($insert_id != '')
     {
        return $insert_id;
     }

     else
     {
       return false;
     }

    }

  }


  public function insert_into_sbi_refund_data_final($datanew)
  {

    $sql = "select * from sbi_final_check_refund_data where order_number = ?";
    $query = $this->db->query($sql,array($datanew['order_number']));

    if($query->num_rows() > 0)
    {
      $this->db->where('order_number', $datanew['order_number']);
      $this->db->update('sbi_final_check_refund_data', $datanew);
      return 'update_success';
    }

    else
    {
     $this->db->insert('sbi_final_check_refund_data',$datanew);
     $insert_id = $this->db->insert_id();
     if($insert_id != '')
     {
        return $insert_id;
     }

     else
     {
       return false;
     }
    }

  }

  public function insert_into_sbi_refund_settlement_data($datanew)
  {

    $sql = "select * from sbi_final_check_refund_settlement_data where order_number = ?";
    $query = $this->db->query($sql,array($datanew['order_number']));

    if($query->num_rows() > 0)
    {

      $this->db->where('order_number', $datanew['order_number']);
      $this->db->update('sbi_final_check_refund_settlement_data', $datanew);
      return 'update_success';

    }

    else
    {
     $this->db->insert('sbi_final_check_refund_settlement_data',$datanew);
     $insert_id = $this->db->insert_id();
     if($insert_id != '')
     {
        return $insert_id;
     }

     else
     {
       return false;
     }
    }

  }

  public function getMerchant_refund_Details()
  {

      $sql = "select * from sbi_final_refund_data";
      $query = $this->db->query($sql);
      return $query->result_array();

  }

  public function delete_not_paid_student_fine_id($not_paid_student_fine_id)
  {

    $this->db->where('id', $not_paid_student_fine_id);
    $this->db->delete('bank_fee_fine_details'); 

  }

  public function bank_fee_fine_details()
  {
    
    $query = $this->db->get('bank_fee_fine_details');
    return $query->result_array();

  }

  public function get_order_number($settlement_id)
  {

    $sql = "select `merchant_order_number` from sbi_final_settlement_data where id = ?";
    $query = $this->db->query($sql,array($settlement_id));
    $id_array = $query->result_array();
    return $id_array['0']['merchant_order_number'];
  }

  public function get_offline_receipt_lists()
  {

    $this->db->select('*');    
    $this->db->from('bank_fee_details');
    $this->db->where('payment_mode','offline');
    
    $query = $this->db->get();

    return $query->result_array();

    //echo $this->db->last_query(); die();

  }

  public function get_order_number_refund($refund_table_id)
  {

    $sql = "select `merchant_order_number` from sbi_final_refund_data where id = ?";
    $query = $this->db->query($sql,array($refund_table_id));
    $id_array = $query->result_array();
    return $id_array['0']['merchant_order_number'];
  }

  public function get_not_paid_admission_no_list($session_year,$session) {
    
    // $query = $this->db->select('student_name,admn_no,email_id,session_year,session,course_id,branch_id,category,pwd_status,amount,fine_amount,total_amount,verification_status,payment_status')->from('bank_fee_details')
    // //$this->db->where('verification_status', '1');
    // ->where('payment_status', '0')
    // ->get();

    $sql = "SELECT a.student_name,a.admn_no,a.email_id,a.session_year,a.session,a.course_id,a.branch_id,a.category,a.pwd_status,a.amount,a.fine_amount,a.total_amount,a.verification_status,a.payment_status FROM bank_fee_details a 
    INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_status=0 AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.form_id , f.form_id";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); die();

    return $query->result_array();

  }

  public function get_all_fine_data()
  {

    $query = $this->db->get('bank_fee_fine_details');

    return $query->result_array();
    
        
  }

  public function get_bank_fee_fine_details($bank_fee_id)
  {

    $query = $this->db->select('*')->from('bank_fee_fine_details')
                  ->where('id', $bank_fee_id)
                  ->get();

    return $query->result_array();

  }

  public function get_bank_fee_fine_details_after_sink($bank_fee_id_after_sink)
  {


    $query = $this->db->select('*')->from('bank_fee_details')
                  ->where('id', $bank_fee_id_after_sink)
                  ->get();

    return $query->result_array();



  }

  public function get_order_number_success($success_table_id)
  {

    //$CI = &get_instance();
    //$this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select `txnid` from sbi_success_details_semester_fees where id = ?";
    $query = $this->db->query($sql,array($success_table_id));
    $id_array = $query->result_array();
    return $id_array['0']['txnid'];
  }

  public function insert_into_sbi_final_settlement_data($datanew)
  {

    if ($datanew['sbi_reference_no'] == '' || $datanew['order_no'] == '') {
      
      return false;
    }

    else {
     

    $this->db->insert('sbi_final_check_settlement_data',$datanew);
     $insert_id = $this->db->insert_id();
     if($insert_id != '')
     {
        return $insert_id;
     }

     else
     {
       return false;
     }

    }

  }


  public function upload_csv_data_for_fine($data)
  {

    $this->db->insert('bank_fee_fine_details',$data);

  }


  public function check_sbi_success_details_parent($order_number)
  {

    //$CI = &get_instance();
    //$this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_success_details_semester_fees where txnid = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->num_rows();

  }


  public function check_sbi_failure_details_parent($order_number)
  {

    //$CI = &get_instance();
    //$this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_failure_details_semester_fees where txnid = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->num_rows();

  }



  public function insert_into_sbi_final_settlement_data_merchant($datanew)
  {

    $sql = "select * from sbi_final_check_settlement_data_merchant where order_no = ?";
    $query = $this->db->query($sql,array($datanew['order_no']));
    if($query->num_rows() > 0) {
     
      $this->db->where('order_no', $datanew['order_no']);
      $this->db->update('sbi_final_check_settlement_data_merchant', $datanew);
      return 'update_success';

    }

    else
    {
    $this->db->insert('sbi_final_check_settlement_data_merchant',$datanew);
    $insert_id = $this->db->insert_id();
    if($insert_id != '')
    {
       return $insert_id;
    }

    else
    {
      return false;
    }
  }

  }

  public function update_bank_fee_fine_details($update_fine,$bank_fee_id)
  {

      $this->db->where('id',$bank_fee_id);
      $this->db->update('bank_fee_fine_details', $update_fine);


  }

  public function update_bank_fee_fine_details_after_sink($update_fine,$bank_fee_id,$admn_no,$session_year,$session)
  {

       

      //$this->db->where('id',$bank_fee_id);
      $this->db->where('admn_no',$admn_no);
      $this->db->where('session_year',$session_year);
      $this->db->where('session',$session);
      $this->db->update('bank_fee_details', $update_fine);


      // $this->db->where('admn_no',$admn_no);
      // $this->db->where('session_year',$session_year);
      // $this->db->where('session',$session);
      // $this->db->update('bank_fee_fine_details', $update_fine);


      $CI = &get_instance();
      $this->db2 = $CI->load->database($this->pbeta, TRUE);

      
      $this->db2->where('admn_no',$admn_no);
      $this->db2->where('session_year',$session_year);
      $this->db2->where('session',$session);
      $this->db2->update('bank_fee_details', $update_fine);

      

      //echo $this->db2->last_query(); die();

  }

  public function delete_bank_fee_fine_details($id)
  {


    $this->db->where('id', $id);
    $this->db->delete('bank_fee_fine_details');
    //echo $this->db->last_query(); die();

  }

  public function delete_bank_fee_fine_details_after_sink($admn_no,$session_year,$session)

  {

    $this->db->where('admn_no',$admn_no);
    $this->db->where('session_year',$session_year);
    $this->db->where('session',$session);
    $this->db->delete('bank_fee_details');
       
  }

  public function check_sbi_settlement($order_number)
  {

    // $CI = &get_instance();
    // $this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_final_settlement_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->num_rows();

  }

  public function get_settlement_amount($order_number)
  {

     $sql = "select `payoutamount` from sbi_final_settlement_data where merchant_order_number = ?";
     $query = $this->db->query($sql,array($order_number));
     $pay_array = $query->result_array();
     return $pay_array['0']['payoutamount'];


  }

  public function get_settlement_gst($order_number)
  {

     $sql = "select `gst` from sbi_final_settlement_data where merchant_order_number = ?";
     $query = $this->db->query($sql,array($order_number));
     $pay_array = $query->result_array();
     return $pay_array['0']['gst'];


  }

  public function get_settlement_commission($order_number)
  {

     $sql = "select `commission_payable` from sbi_final_settlement_data where merchant_order_number = ?";
     $query = $this->db->query($sql,array($order_number));
     $pay_array = $query->result_array();
     return $pay_array['0']['commission_payable'];


  }

  public function check_mis_final_settlement_data($order_number)
  {
    $sql = "select * from sbi_final_check_settlement_data where order_no = ?";
    $query = $this->db->query($sql,array($order_number));
    //echo $this->db->last_query(); die();
    return $query->num_rows();
      
  }


  public function search_update_main_bank_fee_data($admn_no,$session_year,$session,$dataupdate)
  {

        $query = $this->db->select('*')->from('bank_fee_details')
                          ->where('admn_no',$admn_no)
                          ->where('session_year',$session_year)
                          ->where('session',$session)
                          ->where('payment_status',0)
                          ->get();


                          //echo $this->db->last_query(); die();



        $CI = &get_instance();
        $this->db2 = $CI->load->database($this->pbeta, TRUE);


        $query2 = $this->db2->select('*')->from('bank_fee_details')
                          ->where('admn_no',$admn_no)
                          ->where('session_year',$session_year)
                          ->where('session',$session)
                          ->where('payment_status',0)
                          ->get();




        // echo $query->num_rows();

        // echo $query2->num_rows();

        // exit;




         

        if($query->num_rows() > 0 && $query2->num_rows() > 0) {

         $this->db->where('session_year',$session_year);
         $this->db->where('session',$session);
         $this->db->where('admn_no',$admn_no);
         $this->db->where('payment_status',0);
         $this->db->update('bank_fee_details',$dataupdate);

         //echo $this->db->last_query();

         $this->db2->where('session_year',$session_year);
         $this->db2->where('session',$session);
         $this->db2->where('admn_no',$admn_no);
         $this->db2->where('payment_status',0);
         $this->db2->update('bank_fee_details',$dataupdate);

        //  $this->db->where('session_year',$session_year);
        //  $this->db->where('session',$session);
        //  $this->db->where('student_name',$student_name);
        //  $this->db->where('payment_status',0);
	      //  $this->db->delete('bank_fee_fine_details');

         //echo $this->db2->last_query(); die();

         return 1;

        }

        else
        {

          return 0;

        }

  }


  public function get_all_fine_data_from_bank_fee_table($session_year,$session)
  {


    $sql = "SELECT * FROM bank_fee_details a 
    INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_status=0 AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.form_id , f.form_id";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); exit;
    return $query->result_array();

    // $query = $this->db->get('bank_fee_fine_details');

    // return $query->result_array();

  }

  public function get_bank_fee_fine_details_after_update($session_year,$session)
  {

    $sql = "SELECT * FROM bank_fee_details a 
    INNER JOIN bank_fee_fine_details b ON a.admn_no = b.admn_no
    INNER JOIN reg_regular_fee d ON d.admn_no = b.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_status=0 AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.form_id , f.form_id";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); exit;
    return $query->result_array();

  }

  public function get_sbi_success_data()
  {

    //$CI = &get_instance();
    //$this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_success_details_semester_fees";
    $query = $this->db->query($sql);

    return $query->result_array();

  }

  public function update_final_settlement_data($order_number)
  {

    $data = array(

        'status' => 'success',
    );

    $this->db->where('order_no', $order_number);
    $this->db->update('sbi_final_check_settlement_data', $data);
      
      
  }

  public function update_final_settlement_data_merchant($order_number)
  {

    $data = array(

        'status' => 'success',
    );

    $this->db->where('order_no', $order_number);
    $this->db->update('sbi_final_check_settlement_data_merchant', $data);
      
      
  }

  public function update_final_settlement_data_merchant_failure($order_number)
  {

    $data = array(

        'status' => 'failure',
    );

    $this->db->where('order_no', $order_number);
    $this->db->update('sbi_final_check_settlement_data_merchant', $data);
      
      
  }

  public function update_final_refund_data_merchant($order_number)
  {

    $data = array(

        'status' => 'success',
    );

    $this->db->where('order_no', $order_number);
    $this->db->update('sbi_final_check_refund_data', $data);
      
      
  }

  public function update_final_refund_data_settlement_merchant($order_number)
  {

      $data = array(

        'status' => 'success',
    );

    $this->db->where('order_no', $order_number);
    $this->db->update('sbi_final_check_refund_settlement_data', $data);

  }


  public function get_mis_id($admn_no,$session_year,$session)
  {
       $query = $this->db->select('id')->from('bank_fee_details')
                        ->where('admn_no',$admn_no)
                        ->where('session_year',$session_year)
                        ->where('session',$session)
                        ->get();

       $query_array = $query->result_array();
       return $query_array['0']['id'];
  }

  public function get_parent_id($admn_no,$session_year,$session)
  {

    $CI = &get_instance();
    $this->db2 = $CI->load->database($this->pbeta, TRUE);

    $query = $this->db2->select('id')->from('bank_fee_details')
    ->where('admn_no',$admn_no)
    ->where('session_year',$session_year)
    ->where('session',$session)
    ->get();

    $query_array = $query->result_array();

    //echo $query_array['0']['id']; exit;
    return $query_array['0']['id'];

       
  }

  public function update_payment_receipt($mis_id,$parent_id,$data_pay_update)
  {

    $this->db->where('id', $mis_id);
    $this->db->update('bank_fee_details', $data_pay_update); 


    $CI = &get_instance();
    $this->db2 = $CI->load->database($this->pbeta, TRUE);
    
    $this->db2->where('id', $parent_id);
    $this->db2->update('bank_fee_details', $data_pay_update); 
    //echo $this->db2->last_query(); die();

  }


     public function get_reg_regular_form_id($admn_no,$session_year,$session)

     {


            $sql = "select form_id from reg_regular_form where admn_no = ? and session_year = ? and session = ? and hod_status = ? and acad_status = ?";
            //SELECT * FROM reg_regular_form WHERE session_year = '2020-2021' AND SESSION = 'Monsoon' AND admn_no = '18je0029' AND hod_status = '1' AND acad_status = '1'
            $query = $this->db->query($sql,array($admn_no,$session_year,$session,'1','1'));
            //echo $this->db2->last_query();
            $form_id_array = $query->result_array();
            
            return $form_id_array['0']['form_id'];


     }

  public function get_date_of_settlement($order_number)
  {

     $sql = "select `settlement_date` from sbi_final_settlement_data where merchant_order_number = ?";
     $query = $this->db->query($sql,array($order_number));
     $settledatearray = $query->result_array();
     return $settledatearray['0']['settlement_date'];

  }

  public function get_final_settlement_success_details()
  {
      
      $sql = "select * from sbi_final_check_settlement_data where status = 'success'";
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  public function get_final_settlement_pending_details()
  {
      
      $sql = "select * from sbi_final_check_settlement_data where status = 'pending'";
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  public function get_final_settlement_success_details_merchant()
  {
      
      $sql = "select * from sbi_final_check_settlement_data_merchant where status = 'success'";
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  public function get_final_refund_details_merchant()
  {

    $sql = "select * from sbi_final_check_refund_data where status = 'not_any'";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); die();
    return $query->result_array();
      
  }

  public function get_final_success_refund_details_merchant()
  {

    $sql = "select * from sbi_final_check_refund_data where status = 'success'";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); die();
    return $query->result_array();

  }

  public function get_offline_receipt_details($session_year,$session,$payment_mode,$admn_no)
  {
    $sql = "SELECT * FROM bank_fee_details a 
    INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
    INNER JOIN reg_regular_form f ON f.form_id = d.form_id
    WHERE a.payment_mode='offline' AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND a.admn_no = '".$admn_no."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.admn_no , f.form_id";
    $query = $this->db->query($sql);
    //return $query->result_array();

      if($query->num_rows() > 0)
      {

        $sql = "SELECT * FROM bank_fee_details a 
        INNER JOIN reg_regular_fee d ON d.admn_no = a.admn_no
        INNER JOIN reg_regular_form f ON f.form_id = d.form_id
        WHERE a.payment_mode='offline' AND f.session_year='".$session_year."' AND f.`session`='".$session."' AND a.admn_no = '".$admn_no."' AND f.hod_status = '1' AND f.acad_status = '1' group by a.admn_no , d.admn_no , f.form_id";
        $query = $this->db->query($sql);
                        //echo $this->db->last_query(); die();


                        return $query->result_array();
          
      }

      else
      {
         return false;
      }
  }

  public function get_final_settlement_refund_details_merchant()
  {

    $sql = "select * from sbi_final_check_refund_settlement_data where status = 'success'";
    $query = $this->db->query($sql);
    //echo $this->db->last_query(); die();
    return $query->result_array();

  }


  public function get_final_settlement_pending_details_merchant()
  {
      
      $sql = "select * from sbi_final_check_settlement_data_merchant where status = 'pending'";
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  public function get_final_settlement_failure_details_merchant()
  {
      
      $sql = "select * from sbi_final_check_settlement_data_merchant where status = 'failure'";
      $query = $this->db->query($sql);
      return $query->result_array();
  }

  public function get_success_details_mis($order_number){

    //$CI = &get_instance();
    //$this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_success_details_semester_fees where txnid = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();

  }

  public function insert_update_bank_fee_reference($dataupdate_bank,$admn_no,$session_year,$session,$payment_mode)
  {


        $this->db->insert('bank_fee_details_updated',$dataupdate_bank);
        
        $insert_id = $this->db->insert_id();

        //$sql = "select * from ";

        $data_update = array(

            'updated_bank_fee_id' => $insert_id,
            'updated_status' => 1
        );

        $this->db->where('admn_no',$admn_no);
        $this->db->where('session_year',$session_year);
        $this->db->where('session',$session);
        $this->db->update('bank_fee_details',$data_update);

        //$this->db->where('payment_mode',);


        

  }

  public function get_settlement_details_success($order_number){

    // $CI = &get_instance();
    // $this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_final_settlement_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();

  }

  public function get_settlement_details_pending($order_number){

    // $CI = &get_instance();
    // $this->db2 = $CI->load->database($this->tabulation, TRUE);
    $sql = "select * from sbi_final_settlement_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();

  }

  public function get_refund_details($order_number)
  {

    $sql = "select * from sbi_final_refund_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();
     
  }

  public function get_refund_success_details($order_number)
  {

    $sql = "select * from sbi_final_refund_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();
     
  }

  public function get_refund_settlement_details($order_number)
  {

    $sql = "select * from sbi_final_refund_data where merchant_order_number = ?";
    $query = $this->db->query($sql,array($order_number));
    return $query->result_array();
     
  }

}
    

    ?>