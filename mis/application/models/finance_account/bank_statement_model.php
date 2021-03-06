<?php 
class bank_statement_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
    }

    function getEmpNumForBankStatement(){
        $q=$this->db->query("select distinct(apd.EMPNO), case when apd.BACNO is null then uod.bank_accno else apd.BACNO end as BACNO from acc_pay_details_temp apd left join user_other_details uod on uod.id=apd.EMPNO");
        //echo $this->db->last_query();die();
        if($q->num_rows() > 0){
           return $q->result();
        }
        return false;
    }
    function getEmpNumForBankStatementOld($mon,$yr){
    	$myquery="select a.EMPNO,a.BACNO from acc_pay_details a where a.MON=? and a.YR=?";
        $q=$this->db->query($myquery,array($mon,$yr));
        //echo $this->db->last_query();die();
        if($q->num_rows() > 0){
           return $q->result();
        }
        return false;
    }
    function insertStatemetTemp($data){
    	//var_dump($data);
    	$query="delete from acc_bank_statement_temp";
    	if($this->db->query($query)){
    	   foreach ($data as $r) {
	    	$this->db->insert('acc_bank_statement_temp',$r);
	    	}
	    	return true;	
    	}
    	else{
    		return false;
    	}
   }

    function getBankStatemetTemp(){
    	$q="SELECT abst.*, case when apd.NAME is null then CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) else apd.NAME end as name,(
SELECT dpt.name
FROM departments dpt
WHERE dpt.id=ud.dept_id) AS dept
FROM acc_bank_statement_temp abst
left join acc_pay_details_temp apd on apd.EMPNO=abst.EMPNO
LEFT JOIN emp_basic_details ebd ON ebd.emp_no=abst.EMPNO
LEFT JOIN user_details ud ON ud.id=abst.EMPNO";
    	$query=$this->db->query($q);
    	//echo $this->db->last_query();die();
    	if($query->num_rows()>0){
    		return $query->result();
    	}
    	else{
    		return false;
    	}
    }

    function getMonthYear(){
    $myquery="select (select distinct(MON)  from acc_pay_details_temp ) as MON,(select distinct(YR) from acc_pay_details_temp) as YR  from acc_pay_details_temp limit 1";
    $query=$this->db->query($myquery);
   // echo $this->db->last_query();die();
    if($query->num_rows()>0){
        return $query->result();
    }
    else{
        return false;
    }
   }
    
   function getMonthYearForVerification(){
	   	$myquery="select (select distinct(MON)  from acc_bank_statement_temp ) as MON,(select distinct(YR) from acc_bank_statement_temp) as YR  from acc_bank_statement_temp limit 1";
	    $query=$this->db->query($myquery);
	    //echo $this->db->last_query();die();
	    if($query->num_rows()>0){
	        return $query->result();
	    }
	    else{
	        return false;
	    }
   }

   function updateMonYr($set){
   		$this->db->set($set);
   		$this->db->update('acc_bank_statement_temp');
   }

   function save_statement(){
   	$result=$this->getMonthYearForVerification();
   	$cond=array(
   		'MON'=>$result[0]->MON,
   		'YR'=>$result[0]->YR
   		);
   	$this->db->where($cond);
   	if($q=$this->db->get('acc_bank_statement')){
   		if($q->num_rows()>0){
   			$this->db->where($cond);
   			if($this->db->delete('acc_bank_statement')){
   				if($this->performInsertion()){
   					return true;
   				}
   				else{
   					return false;
   				}
   			}
   			else{
   				return false;
   			}
   		}
   		else{
   			if($this->performInsertion()){
   					return true;
   				}
   				else{
   					return false;
   				}
   		}
   	}
   }

   function performInsertion(){
   		$q="insert into acc_bank_statement (EMPNO,MON,YR,BACNO,AMOUNT)  select * from acc_bank_statement_temp";
   		if($this->db->query($q)){
   			return true;
   		}
   		else{
   			return false;
   		}
   }

   function getdepartmentsFA(){
    $myquery="select distinct(DEPT) as id, DEPT as name from acc_pay_details order by dept";
    $query=$this->db->query($myquery);
    //echo $this->db->last_query();die();
    if($query->num_rows()>0){
      return $query->result();
    }
    else{
      return false;
    }
   }

    function getMonForQuery(){
    $myquery="select distinct(MON) from acc_bank_statement order by MON";
    if($query=$this->db->query($myquery)){
        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
   }
   function getMonForQueryOld(){
    $myquery="select distinct(MON) from acc_pay_details order by MON";
    if($query=$this->db->query($myquery)){
        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
   }
   
   function getYrForQuery(){
    $myquery="select distinct(YR) from acc_bank_statement order by YR";
    if($query=$this->db->query($myquery)){
        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
   }
   function getYrForQueryOld(){
    $myquery="select distinct(YR) from acc_pay_details order by YR";
    if($query=$this->db->query($myquery)){
        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
   }

   function getReport($arr){
    //$q="select concat(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) as name,al.*,(select dpt.name from departments dpt where dpt.id=ud.dept_id) as dept from acc_bank_statement al left join user_details ud on ud.id=al.EMPNO where al.AMOUNT>0";
    /* Stopped on 30-10-2018 by sourav
    $q="SELECT al.ID,al.EMPNO,al.MON,al.YR, CASE WHEN al.BACNO IS NULL THEN uod.bank_accno ELSE al.BACNO END AS BACNO, CASE WHEN apd.NAME IS NULL THEN UCASE(CONCAT(ud.first_name,' ',ud.middle_name,' ',ud.last_name)) ELSE UCASE(apd.NAME) END AS name, CASE WHEN apd.DEPT IS NULL THEN (
      SELECT dsg.name
      FROM designations dsg
      WHERE dsg.id=ebd.designation) ELSE apd.DESIG END AS DESIG,al.AMOUNT
      FROM acc_bank_statement al
      LEFT JOIN acc_pay_details apd ON al.EMPNO=apd.EMPNO AND al.MON=apd.MON AND al.YR=apd.YR
      LEFT JOIN user_other_details uod ON uod.id=al.EMPNO
      LEFT JOIN user_details ud ON ud.id=al.EMPNO
      LEFT JOIN emp_basic_details ebd ON ebd.emp_no=al.EMPNO
      WHERE al.AMOUNT>0";
     */ 
    
    # Added by Sourav for ASC Accno. & caps dept on 30-10-2018
     $q="SELECT al.ID,al.EMPNO,al.MON,al.YR, CASE WHEN al.BACNO IS NULL THEN uod.bank_accno ELSE al.BACNO END AS BACNO, CASE WHEN apd.NAME IS NULL THEN UCASE(CONCAT(ud.first_name,' ',ud.middle_name,' ',ud.last_name)) ELSE UCASE(apd.NAME) END AS name, CASE WHEN apd.DEPT IS NULL THEN (
      SELECT UPPER(dsg.name)
      FROM designations dsg
      WHERE dsg.id=ebd.designation) ELSE UPPER(apd.DESIG) END AS DESIG,al.AMOUNT
      FROM acc_bank_statement al
      LEFT JOIN acc_pay_details apd ON al.EMPNO=apd.EMPNO AND al.MON=apd.MON AND al.YR=apd.YR
      LEFT JOIN user_other_details uod ON uod.id=al.EMPNO
      LEFT JOIN user_details ud ON ud.id=al.EMPNO
      LEFT JOIN emp_basic_details ebd ON ebd.emp_no=al.EMPNO
      WHERE al.AMOUNT>=0";

    foreach ($arr as $key => $value) {
        $q.=" and al.".$key."=".$value;
    }
    $q.=" ORDER BY BACNO ASC";  # Added by Sourav for ASC Accno. & caps dept on 30-10-2018

    $query=$this->db->query($q);
    #echo $this->db->last_query();die();
    if($query->num_rows()>0){
        return $query->result();
    }
    else{
        return false;
    }
   }

   function grossPayableAmt($id,$mon,$yr){
        $sum=0;
        $str="";
        $this->load->model('finance_account/payble_fields_model');
        $fields = $this->payble_fields_model->getAllActive();
        //echo $this->db->last_query();die();
        foreach($fields as $val){
            $f= $val->field;
            $this->db->select($f);
            (array)$d=$this->db->get_where(acc_pay_details,['EMPNO' =>$id,'MON'=>$mon,'YR'=>$yr])->row(); 
            //echo $this->db->last_query()."<br>";die();
           $sum += $d->$f;
           //$str=$str.$d->$f.",";
        }
        //echo "<br>".$str;
        //die();
        return $sum;
    }


     function grossDeductionAmt($id,$mon,$yr){
        $sum=0;
        $this->load->model('finance_account/deduction_fields_model');
        $fields = $this->deduction_fields_model->getAllActive();
        foreach($fields as $val){
            $f= $val->field;
            $this->db->select($f);
            (array)$d=$this->db->get_where(acc_pay_details,['EMPNO' =>$id,'MON'=>$mon,'YR'=>$yr])->row(); 
          	///echo $this->db->last_query()."<br>";die();
           $sum += $d->$f;
        }
        return $sum;
    }
}

?>
