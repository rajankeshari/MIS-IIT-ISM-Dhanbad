<?php
/*
 * Generated by CRUDigniter v3.2
 * www.crudigniter.com
 */

class tender_model extends CI_Model
{
	private $tabulation='iitism';
    function __construct()
    {
        parent::__construct();
    }

    function insert_tender($data){
    //  exit;
      $CI = &get_instance();
      $this->db2 = $CI->load->database($this->tabulation, TRUE);
      $tender_no=$data['tender_number'];
      $discription=$data['discription'];
      $b_date=$data['b_date'];
			$date=date_create($b_date);
			$b_date=date_format($date,"Y-m-d h:i");
      $category=$data['category'];
      $intender_email=$data['intender_email'];
      $tender_no=$data['tender_number'];
      $file_mis=$data['file_add'];
      $file_web=$data['file_add_web'];
      $auth = $this->session->userdata('id');
			//echo $auth;exit;
      $sqlweb = "insert into tender_data (tno, bdesc, ldate,lastdatetime,tcat,file,uploaded_by,int_email)
          values ('$tender_no', '$discription', '$b_date', '$b_date', '$category', '$file_web', '$auth', '$intender_email')";

        $savedata=$this->db2->query($sqlweb);
			//	echo"1". $this->db2->last_query(); exit;
        if(!empty($savedata)){
        //  echo "data Saved";
         $sql = "insert into web_tender_data (tno, bdesc, ldate,lastdatetime,tcat,file,uploaded_by,int_email)
              values ('$tender_no', '$discription', '$b_date', '$b_date', '$category', '$file_mis', '$auth', '$intender_email')";
          $result= $savedata=$this->db->query($sql);
          if(!empty($result)){
            $this->session->set_flashdata('Success' , 'Tender Upload Successfully.');
						//				echo"2". $this->db2->last_query(); exit;
            redirect('web_tender/web_tender/');

          }else{
            $this->session->set_flashdata('Error' , 'Unable to Upload Tender.Please try Again');
									//	echo"3". $this->db2->last_query(); exit;
            redirect('web_tender/web_tender/');

          }
        }else{
              $this->session->set_flashdata('Error' , 'Unable to Upload Tender.Please try Again');
										//	echo"4". $this->db2->last_query(); exit;
            redirect('web_tender/web_tender/');

        }

    }


}
?>
