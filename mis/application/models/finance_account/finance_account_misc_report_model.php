<?php

/**
 * Author: Anuj
*/
class Finance_account_misc_report_model extends CI_Model {



    function __construct() {
        parent::__construct();
    }

    /**------------------------------GIS REPORT----------------------------------------*/

    function getGIS($arr=''){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.GIS, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.GIS>0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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

    function getTotalGIS($arr=''){
    	$q="select sum(GIS) as TOTAL from acc_pay_details apd where apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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
    /*----------------------------------New GIS-------------*/
    function getNGIS($arr=''){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.NLIC AS GIS, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.NLIC>0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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

    function getTotalNGIS($arr=''){
    	$q="select sum(NLIC) as TOTAL from acc_pay_details apd where apd.NLIC>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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

    /*------------------------Canara Report------------------------------*/
    function getCanara($arr=''){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.CANARA, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.CANARA>0";
    	#$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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


    function getTotalCanara($arr=''){
    	$q="select sum(CANARA) as TOTAL from acc_pay_details apd where apd.CANARA>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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
	
	public function get_negative_salary($arr=''){
    	$q="(SELECT apd.EMPNO,apd.MON,apd.YR, apd.NAME , apd.DEPT, apd.DESIG, apd.PNRNO,apd.GROSS,apd.NETPAY FROM acc_pay_details apd  WHERE apd.NETPAY <= 0";

      if(count($arr)>0){
        foreach ($arr as $key => $value) {
        //	$q.=" and apd.".$key."=".$value. " and acbs.".$key."=".$value;
          $q.=" and apd.".$key."=".$value;
        }
      }
      $q.=" order by cast(apd.EMPNO as decimal))";
      $q.="union all (SELECT apdt.EMPNO,apdt.MON,apdt.YR, apdt.NAME , apdt.DEPT, apdt.DESIG, apdt.PNRNO,apdt.GROSS,apdt.NETPAY FROM acc_pay_details_temp apdt  WHERE apdt.NETPAY <= 0";

      if(count($arr)>0){
        foreach ($arr as $key => $value) {
        //	$q.=" and apd.".$key."=".$value. " and acbs.".$key."=".$value;
          $q.=" and apdt.".$key."=".$value;
        }
      }
      $q.=" order by cast(apdt.EMPNO as decimal))";
    //	echo $q;die();
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
    /*-------------------------------Income Tax-------------------------------*/
    // Following function stopped on 8.7.2019 by Abhijit
	/*
	function getITAX($arr=''){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.ITAX, apd.NAME , apd.DEPT, apd.DESIG, apd.PNRNO FROM acc_pay_details apd  WHERE apd.ITAX>0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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
*/

 /*-------------------------------Income Tax-------------------------------*/
    function getITAX($arr='',$pay_field,$ded_field){
      $pf="";
      $pf_sum="";
      $df="";
      $df_sum="";
      $i=0;
      $cond="";
      if(count($arr)>0){
                  foreach ($arr as $key => $value) {
                      $cond.=" and A.".$key."=".$value;
                  }
      }
      foreach ($pay_field as $r) {
          if($i==0){
              $pf.=' apd.'.$r['field'];
              $pf_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
          }
          else{
              $pf.=',apd.'.$r['field'];
              $pf_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
          }
          $i++;
      }
      $i=0;
      foreach ($ded_field as $r) {
          if($i==0){
              $df.=' apd.'.$r['field'];
              $df_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
          }
          else{
              $df.=',apd.'.$r['field'];
              $df_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
          }
          $i++;
      }
  //  	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.ITAX, apd.NAME , apd.DEPT, apd.DESIG, apd.PNRNO,apd.GROSS, $pf ,$pf_sum as GROSS FROM acc_pay_details apd  WHERE apd.ITAX>0";

  // 05-07-19	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.ITAX, apd.NAME , apd.DEPT, apd.DESIG, apd.PNRNO,acbs.AMOUNT as GROSS FROM acc_pay_details apd join acc_bank_statement acbs on apd.EMPNO =acbs.EMPNO WHERE apd.ITAX>0";

    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
      $q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.ITAX, apd.NAME ,apd.SREC, apd.DEPT, apd.DESIG, apd.PNRNO,$pf_sum as GROSS, $df_sum as DEDUCTION,($pf_sum)-($df_sum) as NETPAY
      FROM  acc_pay_details apd
      WHERE apd.ITAX>=0 ";
	    // In the above query for Other recovery from salary we have included apd.SREC which is used to display the data 
    
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    		//	$q.=" and apd.".$key."=".$value. " and acbs.".$key."=".$value;
        	$q.=" and apd.".$key."=".$value;
    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    //	echo $q;die();
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
    function getTotalITAX($arr=''){
    	$q="select sum(ITAX) as TOTAL from acc_pay_details apd where apd.ITAX>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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

    /*---------------------------------DCPS-----------------------------------*/
     function getDcps($arr=''){ /* MON,YR,GrP*/
    /* for GOV Contribution 30-5-19	$q="SELECT apd.EMPNO,apd.MON,apd.YR,case when apd.PRAN is null or apd.PRAN=0 then 'NOT AVAILABLE' else apd.PRAN end as PRAN,'CGV012577F' AS DDO_REG_NO, apd.DCPS, case when apd.NAME is null then UCASE(concat(uod.salutation,' ',uod.first_name,' ',uod.middle_name,' ',uod.last_name)) else UCASE(apd.NAME) end as NAME, CASE WHEN apd.DEPT IS NULL THEN uod.dept_id ELSE apd.DEPT END AS DEPT, CASE WHEN apd.DESIG IS NULL THEN ebd.designation ELSE apd.DESIG END AS DESIG,ucase(ebd.employment_nature) as CONT_TYPE
            FROM acc_pay_details apd
            LEFT JOIN emp_basic_details ebd ON apd.EMPNO= CAST(ebd.emp_no AS DECIMAL)
            LEFT JOIN user_details uod ON uod.id=apd.EMPNO
            WHERE apd.DCPS>0";*/
            	$q="SELECT apd.EMPNO,apd.MON,apd.YR,case when apd.PRAN is null or apd.PRAN=0 then 'NOT AVAILABLE' else apd.PRAN end as PRAN,'CGV012577F' AS DDO_REG_NO, apd.DCPS,ROUND(((apd.BASIC+apd.GRPAY+apd.NPA+apd.DA+apd.BARR+apd.DAARR)*14)/100,0) as GOV_CONT, case when apd.NAME is null then UCASE(concat(uod.salutation,' ',uod.first_name,' ',uod.middle_name,' ',uod.last_name)) else UCASE(apd.NAME) end as NAME, CASE WHEN apd.DEPT IS NULL THEN uod.dept_id ELSE apd.DEPT END AS DEPT, CASE WHEN apd.DESIG IS NULL THEN ebd.designation ELSE apd.DESIG END AS DESIG,ucase(ebd.employment_nature) as CONT_TYPE
            FROM acc_pay_details apd
            LEFT JOIN emp_basic_details ebd ON apd.EMPNO= CAST(ebd.emp_no AS DECIMAL)
            LEFT JOIN user_details uod ON uod.id=apd.EMPNO
            WHERE apd.DCPS>0";


    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			if(strcmp($key, 'GROUP')==0){
    				if(strcmp($value,"A")==0){
    					$q.=" and apd.$key"."='".$value."'";
    				}
    				else{
    					$q.=" and apd.$key"."<>'A'";
    				}
    			}
    			else{
    				$q.=" and ".$key."=".$value;
    			}

    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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

    function getTotalDcps($arr=''){
    	$q="select sum(ITAX) as TOTAL from acc_pay_details apd where apd.ITAX>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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
    /*---------------------------------DCPS-----------------------------------*/
     function getRD($arr=''){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.CTD as RD, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.CTD>0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			if(strcmp($key, 'GROUP')==0){
    				if(strcmp($value,"A")==0){
    					$q.=" and apd.$key"."='".$value."'";
    				}
    				else{
    					$q.=" and apd.$key"."<>'A'";
    				}
    			}
    			else{
    				$q.=" and ".$key."=".$value;
    			}

    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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

    function getTotalRD($arr=''){
    	$q="select sum(CTD) as TOTAL from acc_pay_details apd where apd.CTD>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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
    /*----------------------------------------------------------*/
    function getAdvances($arr='',$adv){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.".$adv." as adv, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.".$adv.">0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			if(strcmp($key, 'GROUP')==0){
    				if(strcmp($value,"A")==0){
    					$q.=" and apd.$key"."='".$value."'";
    				}
    				else{
    					$q.=" and apd.$key"."<>'A'";
    				}
    			}
    			else{
    				$q.=" and ".$key."=".$value;
    			}

    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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
    function getAdvName($adv){
        $q="select ucase(adf.alis) as adv from acc_deduction_field_tbl adf where adf.field=?";
        $query=$this->db->query($q,$adv);
        if($query->num_rows()>0){
            $result=$query->row();
            return $result->adv;
        }
        else{
            return false;
        }
    }
    function getTotalAdvances($arr=''){
    	$q="select sum(CTD) as TOTAL from acc_pay_details apd where apd.CTD>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			$q.=" and ".$key."=".$value;
    		}
    	}
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
    /*----------------------------------------------------------*/
    function getClubs($arr='',$club){
    	$q="SELECT apd.EMPNO,apd.MON,apd.YR, apd.".$club." as club, apd.NAME , apd.DEPT, apd.DESIG FROM acc_pay_details apd  WHERE apd.".$club.">0";
    	//$q="SELECT DISTINCT(apd.EMPNO),apd.MON,apd.YR, apd.GIS, CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name) AS name, (SELECT dpt.name FROM departments dpt WHERE dpt.id=ud.dept_id) AS dept,(SELECT UPPER(des.name) FROM designations des WHERE des.id=ebd.designation) AS desig FROM acc_pay_details apd LEFT JOIN user_details ud ON CAST(apd.EMPNO AS CHAR(50))=ud.id LEFT JOIN emp_basic_details ebd ON CAST(apd.EMPNO AS CHAR(50))=ebd.emp_no WHERE apd.GIS>0";
    	if(count($arr)>0){
    		foreach ($arr as $key => $value) {
    			if(strcmp($key, 'GROUP')==0){
    				if(strcmp($value,"A")==0){
    					$q.=" and apd.$key"."='".$value."'";
    				}
    				else{
    					$q.=" and apd.$key"."<>'A'";
    				}
    			}
    			else{
    				$q.=" and ".$key."=".$value;
    			}

    		}
    	}
    	$q.=" order by cast(apd.EMPNO as decimal)";
    	//echo $q;die();
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
    /*------------------------Salary Bill Payment----------------------------------*/
    function get_salary_bill_payment_data1($pay_field,$ded_field,$arr=''){
        $pf="";
        $pf_sum="";
        $df="";
        $df_sum="";
        $i=0;
        $cond="";
        if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and A.".$key."=".$value;
                    }
                }
        foreach ($pay_field as $r) {
            if($i==0){
                $pf.=' apd.'.$r['field'];
                $pf_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $pf.=',apd.'.$r['field'];
                $pf_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }
        $i=0;
        foreach ($ded_field as $r) {
            if($i==0){
                $df.=' apd.'.$r['field'];
                $df_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $df.=',apd.'.$r['field'];
                $df_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }

        $q="SELECT apd.EMPNO, CASE WHEN apd.NAME IS NULL THEN concat(ucase(ud.first_name),' ',ucase(ud.middle_name),' ',ucase(ud.last_name)) ELSE ucase(apd.NAME) END AS NAME,case when apd.DEPT is null then (select ucase(dpt.name) from departments dpt where dpt.id=ud.dept_id) else ucase(apd.DEPT) end as DEPT
        ,case when apd.DESIG is null then (select ucase(dsg.name) from designations dsg where dsg.id=ebd.designation) else ucase(apd.DESIG)  end as DESIG, $pf ,$pf_sum as GROSS, $df_sum as DEDUCTION,($pf_sum)-($df_sum) as NETPAY
        FROM   (select * from acc_pay_details A where 1=1 $cond) as apd
        LEFT JOIN user_other_details uod ON uod.id=apd.EMPNO
        LEFT JOIN user_details ud ON ud.id=apd.EMPNO
        left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO where apd.BASIC>0 order by cast(apd.EMPNO as DECIMAL)";
        /*if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $q.=" and ".$key."=".$value;
                    }
                }*/
       //echo $q;die();
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
   function get_salary_bill_payment_data($pay_field,$ded_field,$arr=''){
        $pf="";
        $pf_sum="";
        $df="";
        $df_sum="";
        $i=0;
		if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and A.".$key."=".$value;
                    }
        }
        foreach ($pay_field as $r) {
            if($i==0){
                $pf.=' apd.'.$r['field'];
                $pf_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $pf.=',apd.'.$r['field'];
                $pf_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }
        $i=0;
        foreach ($ded_field as $r) {
            if($i==0){
                $df.=' apd.'.$r['field'];
                $df_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $df.=',apd.'.$r['field'];
                $df_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }

        $q="SELECT apd.EMPNO, CASE WHEN apd.NAME IS NULL THEN concat(ucase(ud.first_name),' ',ucase(ud.middle_name),' ',ucase(ud.last_name)) ELSE ucase(apd.NAME) END AS employee_name, apd.DESIG
        ,case when apd.DEPT is null then (select dsg.name from designations dsg where dsg.id=ebd.designation) else apd.DESIG  end as DESIG, $pf ,$pf_sum as GROSS, $df_sum as DEDUCTION,($pf_sum)-($df_sum) as NETPAY
        FROM   (select * from acc_pay_details A where 1=1 $cond) as apd
        LEFT JOIN user_other_details uod ON uod.id=apd.EMPNO
        LEFT JOIN user_details ud ON ud.id=apd.EMPNO
        left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO where apd.BASIC>0";
		//echo $q;die();
        if($query=$this->db->query($q)){
            if($query->num_rows()>0){
				echo"true";die();
                return $query->result();
            }
            else{
				echo"False";die();
                return false;
            }
        }
        else{
			echo"Error";die();
            return false;
        }
    }
	function get_salary_bill_sumup($pay_field,$ded_field,$arr=''){
		$pf="";
        $pf_sum="";
		$df_sum="";
        $i=0;
        $cond="";
        if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and A.".$key."=".$value;
                    }
                }
        foreach ($pay_field as $r) {
            if($i==0){
                $pf.=' sum(apd.'.$r['field'].') as '. $r['field'];
				$pf_sum.='sum(apd.'.$r['field'].')';
            }
            else{
				$pf.=',sum(apd.'.$r['field'].') as '. $r['field'];
                $pf_sum.='+sum(apd.'.$r['field'].')';
            }
            $i++;
        }
		$i=0;
		foreach ($ded_field as $r) {
            if($i==0){
				$df_sum.='sum(apd.'.$r['field'].')';
            }
            else{
                $df_sum.='+sum(apd.'.$r['field'].')';
            }
            $i++;
        }

        $q="SELECT $pf,$pf_sum as gross_sum, $df_sum as ded_sum FROM (select * from acc_pay_details A where 1=1 $cond) as apd";
       //echo $q;die();
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
    /*function get_salary_bill_payment_data($pay_field,$ded_field,$arr=''){
        $pf="";
        $pf_sum="";
        $df="";
        $df_sum="";
        $i=0;
        foreach ($pay_field as $r) {
            if($i==0){
                $pf.=' apd.'.$r['field'];
                //$pf_sum.='apd.'.$r['field'];
            }
            else{
                $pf.=',apd.'.$r['field'];
                //$pf_sum.='+apd.'.$r['field'];
            }
            $i++;
        }
        $i=0;
        foreach ($ded_field as $r) {
            if($i==0){
                $df.=' apd.'.$r['field'];
                //$df_sum.='apd.'.$r['field'];
            }
            else{
                $df.=',apd.'.$r['field'];
                //$df_sum.='+apd.'.$r['field'];
            }
            $i++;
        }
        //echo $df_sum;die();
        $q="SELECT apd.EMPNO, CASE WHEN apd.NAME IS NULL THEN concat(ucase(ud.first_name),' ',ucase(ud.middle_name),' ',ucase(ud.last_name)) ELSE ucase(apd.NAME) END AS employee_name, apd.DESIG
        ,case when apd.DEPT is null then (select dsg.name from designations dsg where dsg.id=ebd.designation) else apd.DESIG  end as DESIG, $pf
        FROM   acc_pay_details apd
        LEFT JOIN user_other_details uod ON uod.id=apd.EMPNO
        LEFT JOIN user_details ud ON ud.id=apd.EMPNO
        left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO where apd.BASIC>0";
        echo $q;die();
        $query=$this->db->query($q);
        echo $this->db->last_query();die();
    }*/
    /*------------------------------------------------------------------------------*/
	/*-----------------------------------DEDUCTION REPORT----------------------------*/
	function get_salary_bill_deduction_data($pay_field,$ded_field,$arr=''){
        $pf="";
        $pf_sum="";
        $df="";
        $df_sum="";
        $i=0;
        $cond="";
        if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and A.".$key."=".$value;
                    }
                }
        foreach ($pay_field as $r) {
            if($i==0){
                $pf.=' apd.'.$r['field'];
                $pf_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $pf.=',apd.'.$r['field'];
                $pf_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }
        $i=0;
        foreach ($ded_field as $r) {
            if($i==0){
                $df.=' apd.'.$r['field'];
                $df_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $df.=',apd.'.$r['field'];
                $df_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }

        $q="SELECT apd.MON,apd.YR,apd.EMPNO, CASE WHEN apd.NAME IS NULL THEN concat(ucase(ud.first_name),' ',ucase(ud.middle_name),' ',ucase(ud.last_name)) ELSE ucase(apd.NAME) END AS NAME,case when apd.DEPT is null then (select ucase(dpt.name) from departments dpt where dpt.id=ud.dept_id) else ucase(apd.DEPT) end as DEPT
        ,case when apd.DESIG is null then (select ucase(dsg.name) from designations dsg where dsg.id=ebd.designation) else ucase(apd.DESIG)  end as DESIG, $df ,$pf_sum as GROSS, $df_sum as DEDUCTION,($pf_sum)-($df_sum) as NETPAY
        FROM   (select * from acc_pay_details A where 1=1 $cond) as apd
        LEFT JOIN user_other_details uod ON uod.id=apd.EMPNO
        LEFT JOIN user_details ud ON ud.id=apd.EMPNO
        left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO where apd.BASIC>0 order by cast(apd.EMPNO as DECIMAL)";
		//echo $q;die();
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

	function get_salary_bill_deduction_sumup($ded_field,$arr=''){
		$df="";
        $pf_sum="";
        $i=0;
        $cond="";
        if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and A.".$key."=".$value;
                    }
                }
         foreach ($ded_field as $r) {
            if($i==0){
                $df.=' sum(apd.'.$r['field'].') as '. $r['field'];
                $df_sum.='sum(apd.'.$r['field'].')';
            }
            else{
                $df.=',sum(apd.'.$r['field'].') as '. $r['field'];
                $df_sum.='+sum(apd.'.$r['field'].')';
            }
            $i++;
        }

        $q="SELECT $df,$df_sum as ded_sum FROM   (select * from acc_pay_details A where 1=1 $cond) as apd";
       //echo $q;die();
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
	/*----------------------------------PF(CPF/GPF)---------------------------------------------*/
    function getPF($arr,$type=''){
        $cond="";
        $q="SELECT apd.EMPNO, apd.PFACNO,case when apd.DESIG is null then (select ucase(D.name) from designations D where D.id=ebd.designation) else apd.DESIG end as DESIG, case when apd.NAME is null then (select ucase(concat(A.salutation,' ',A.first_name,' ',A.middle_name,' ',A.last_name)) as NAME from user_details A where A.id=apd.EMPNO ) else apd.NAME end as NAME,apd.BASIC + apd.GRPAY AS BG, CASE WHEN apd.BARR IS NULL THEN 0 ELSE apd.BARR END AS BARR, apd.PFD, apd.PFR FROM acc_pay_details apd left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO WHERE /*apd.PFACNO LIKE '%$type%' and */ apd.PFD>0 ";
        if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and apd.".$key."=".$value;
                    }
        }
        $q.=$cond;
        $q.=" order by apd.PFACNO";
        //echo $q;die();
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
	/*----------------------------------Professional Tax----------------------------------------*/
    function getPFTAX($arr){
        $cond="";
        //$q="SELECT apd.EMPNO,apd.DEPT,case when apd.DESIG is null then (select ucase(D.name) from designations D where D.id=ebd.designation) else apd.DESIG end as DESIG, case when apd.NAME is null then (select ucase(concat(A.salutation,' ',A.first_name,' ',A.middle_name,' ',A.last_name)) as NAME from user_details A where A.id=apd.EMPNO ) else apd.NAME end as NAME,apd.PROFTAX FROM acc_pay_details apd left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO WHERE apd.PROFTAX>0  ";
// Stopped on 8.7.2019 by Abhijit       
	   $q="SELECT apd.EMPNO,apd.DEPT,case when apd.DESIG is null then (select ucase(D.name) from designations D where D.id=ebd.designation) else apd.DESIG end as DESIG, case when apd.NAME is null then (select ucase(concat(A.salutation,' ',A.first_name,' ',A.middle_name,' ',A.last_name)) as NAME from user_details A where A.id=apd.EMPNO ) else apd.NAME end as NAME,apd.PROFTAX,apd.PNRNO FROM acc_pay_details apd left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO WHERE apd.PROFTAX>0  ";
       
		if(count($arr)>0){
                    foreach ($arr as $key => $value) {
                        $cond.=" and apd.".$key."=".$value;
                    }
        }
        $q.=$cond;
        $q.=" order by apd.GROUP,apd.DEPT";
        //echo $q;die();
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
    /*-------------------------------------------------------------------------------------------*/
    /*----------------------------------Payroll Register----------------------------------------*/
    function getEmpNoFor($fr,$to){
        $q="select distinct(apd.EMPNO) from acc_pay_details apd where makedate(apd.YR,apd.mon) between makedate(".$fr['YR'].",".$fr['MON'].") and makedate(".$to['YR'].",".$to['MON'].") order by apd.EMPNO";
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
    function getPayPrintPayroll($data){
        $i=0;
        foreach ($data['payable_fields'] as $r) {
            if($i==0){
                $pf.=' apd.'.$r['field'];
                $pf_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $pf.=',apd.'.$r['field'];
                $pf_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }
        $i=0;
        foreach ($data['deduction_fields'] as $r) {
            if($i==0){
                $df.=' apd.'.$r['field'];
                $df_sum.="(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            else{
                $df.=',apd.'.$r['field'];
                $df_sum.="+(case when apd.".$r['field']." is null then 0 else apd.".$r['field']." end)";
            }
            $i++;
        }
        $q="SELECT apd.MON,apd.YR,apd.EMPNO, case when apd.NAME is null then (select ucase(concat(A.salutation,' ',A.first_name,' ',A.middle_name,' ',A.last_name)) as NAME from user_details A where A.id=apd.EMPNO ) else apd.NAME end as NAME,apd.DEPT
        ,case when apd.DESIG is null then (select ucase(D.name) from designations D where D.id=ebd.designation) else apd.DESIG end as DESIG, $pf ,$df,$pf_sum as GROSS,$df_sum as DEDUC, $pf_sum-$df_sum as NET
        FROM   (select * from acc_pay_details A where 1=1  and makedate(A.YR,A.MON) between makedate(".$data['YRF'].','.$data['MONF'].") and makedate(".$data['YRT'].",".$data['MONT'].") order by A.EMPNO) as apd
        left join emp_basic_details ebd on ebd.emp_no=apd.EMPNO where apd.EMPNO in (".implode(',', $data['emp_string']).")order by cast(apd.EMPNO as DECIMAL),apd.YR,apd.MON";
        //echo $q;die();
        if($query=$this->db->query($q)){
            if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
        }
    }
    /*-------------------------------------------------------------------------------*/
    public function get_all_payable_fields()
    {
        $q="select apf.field,apf.alis from acc_pay_field_tbl apf where apf.`status`='Y' order by apf.sn";
        $query=$this->db->query($q);
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return array();
        }
    }
    //only for annual TDS report generator
    public function get_all_payable_fieldsTDS()
    {
        $q="SELECT apf.field,apf.alis
FROM acc_pay_field_tbl apf
WHERE apf.`status`='Y' AND apf.field NOT LIKE '%PP%' AND apf.field NOT LIKE '%WALLOW%'
ORDER BY apf.sn";
        $query=$this->db->query($q);
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return array();
        }
    }
    public function get_all_deduction_fields()
    {
        $q="select apf.field,apf.alis from acc_deduction_field_tbl apf where apf.`status`='Y' order by apf.sn";
        $query=$this->db->query($q);
        if ($query->num_rows() > 0)
        {
            return $query->result_array();
        }
        else
        {
            return array();
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

   function getYrForQueryOld(){
   // $myquery="select distinct(YR) from acc_pay_details order by YR";
	 $myquery="(select distinct(YR) from acc_pay_details order by YR DESC)union(select distinct(YR) from acc_pay_details_temp order by YR DESC)";
	// Added Desc in the order by to sort the year from 2019 to 2016 at present
    if($query=$this->db->query($myquery)){
        if($query->num_rows()>0){
            return $query->result();
        }
        else{
            return false;
        }
    }
   }

}
