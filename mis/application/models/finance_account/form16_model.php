<?php

/**
 * Author: Anuj
*/
class Form16_model extends CI_Model {



    function __construct() {
        parent::__construct();
    }
    function getEmpRegime($emp_no,$fy){
         //echo $emp_no;exit;
         $q="select * from acc_incometax_regime a
           where a.emp_code=? and a.fy=?";
       if($query=$this->db->query($q,array($emp_no,$fy))){
         //  echo $this->db->last_query();die();
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
    function getEmpName($empno){
    	$q="select ucase(concat(ud.first_name,'_',ud.middle_name,'_',ud.last_name)) as name from user_details ud where ud.id=?";
    	if($query=$this->db->query($q,$empno)){
    		if($query->num_rows()>0){
    			return $query->result_array();
    		}
    		else{
    			return false;
    		}
    	}
    }

    function getSalaryDetails($empno,$fy){
    	//$data=$this->getFYDetails($fy);
    	//$q="select apd.* from acc_pay_details apd where date(concat(2000+apd.yr,'-',apd.MON,'-01')) between '".$data['from']."-03-01' and '".$data['to']."-02-01' and apd.EMPNO=? order by date(concat(2000+apd.yr,'-',apd.MON,'-01'))";
        $q="select apd.* from acc_pay_details apd,(select * from acc_fyear_details a where a.fy=?) as fy  where date(concat(2000+apd.yr,'-',apd.MON,'-01')) between fy.start_from and fy.end_to and apd.EMPNO=? order by date(concat(2000+apd.yr,'-',apd.MON,'-01'))";
    	if($query=$this->db->query($q,array($fy,$empno))){
    		if($query->num_rows()>0){
    			#echo $this->db->last_query();die();
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

    function getConHon($empno,$fy,$type){
        //echo $type;die();
    	//$data=$this->getFYDetails($fy);
    	//$q="select apd.* from acc_other_income apd where apd.date between '".$data['from']."-03-01' and '".$data['to']."-02-01' and apd.emp_no=? and apd.type=? order by apd.date";
        $q="select apd.* from acc_other_income apd,(select * from acc_fyear_details a where a.fy=?) as fy where apd.date between fy.start_from and fy.end_to and apd.emp_no=? and apd.type=? order by apd.date";
    	if($query=$this->db->query($q,array($fy,$empno,$type))){
            //echo $this->db->last_query();die();
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

    function getAllOtherIncome($empno,$fy){
        //echo $type;die();
        //$data=$this->getFYDetails($fy);
        //$q="select apd.* from acc_other_income apd where apd.date between '".$data['from']."-03-01' and '".$data['to']."-02-01' and apd.emp_no=? and apd.type=? order by apd.date";
        $q="select sum(apd.gross) as gross,sum(apd.itax) as itax,sum(apd.net) as net from acc_other_income apd,(select * from acc_fyear_details a where a.fy=?) as fy where apd.date between fy.start_from and fy.end_to and apd.emp_no=? ";
        if($query=$this->db->query($q,array($fy,$empno))){
            //echo $this->db->last_query();die();
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

    function getAllArrear($empno,$fy){
        //echo $type;die();
        //$data=$this->getFYDetails($fy);
        //$q="select apd.* from acc_other_income apd where apd.date between '".$data['from']."-03-01' and '".$data['to']."-02-01' and apd.emp_no=? and apd.type=? order by apd.date";
        $q="select sum(apd.gross) as gross,sum(apd.itax) as itax,sum(apd.net) as net from acc_other_income apd,(select * from acc_fyear_details a where a.fy=?) as fy where apd.date between fy.start_from and fy.end_to and apd.emp_no=? and apd.type='ARR'  ";
        if($query=$this->db->query($q,array($fy,$empno))){
           # echo $this->db->last_query();die();
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
    function getEmpDetails($empno){
        $q="select ebd.emp_no, concat(COALESCE(ud.first_name,''),' ',COALESCE(ud.middle_name,''),' ',COALESCE(ud.last_name,'')) as name,(select ucase(dgs.name) from designations dgs where dgs.id=ebd.designation)as design,(select dpt.name from departments dpt where dpt.id=ud.dept_id) as dept,(select apd.PNRNO from acc_pay_details apd where apd.EMPNO=? and length(apd.PRAN) limit 1) as PRAN from (select A.emp_no,A.designation from emp_basic_details A where A.emp_no=?) as ebd left join user_details ud on ud.id=ebd.emp_no";
        if($query=$this->db->query($q,array($empno,$empno))){
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
    function getFYDetails($fy){
        $q="select afd.fy,DATE_FORMAT(afd.start_from,'%d-%m-%Y')as start_from,DATE_FORMAT(afd.end_to,'%d-%m-%Y')as end_to from acc_fyear_details afd where afd.fy=?";
       if($query=$this->db->query($q,array($fy))){
            if($query->num_rows()>0){
                return $query->result_array();
            }
            else{
                return false;
            }
       }
       else{
         return false;
       }
    	/*$data['from']=(int)substr($fy,0,4);
    	$yr=(int)substr($fy,2,4);
    	if($yr==99){
    		$data['to']=$data['from']-$yr+100;
    	}
    	else{
    		$data['to']=$data['from']-$yr+(int)substr($fy,5);
    	}

    	return $data;*/
    }

    function getTaxPayerType($empno,$fy,$end_to){
      $age=$this->getAge($empno,date('Y-m-d',strtotime($end_to)));
      if($age){
        $q="select att.id from acc_taxpayer_type att WHERE ?>=att.age_from and ? < att.age_to";
        if($query=$this->db->query($q,array($age,$age))){
            if($query->num_rows()>0){
                $result=$query->result_array();
                return $result[0]['id'];
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
        return false;
      }
    }

    function getAllFYDetails(){
        $q="select * from acc_fyear_details";
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

    function getAge($empno,$end_to){
        $q="SELECT TIMESTAMPDIFF(YEAR, ud.dob,?) AS age FROM user_details ud where ud.id=?";
        if($query=$this->db->query($q,array($end_to,$empno))){
            if($query->num_rows(0)>0){
                $result=$query->result_array();
                return $result[0]['age'];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    function getRigimOption($emp_code,$fy){
      $sql="select * from acc_incometax_regime a
            where a.emp_code='$emp_code' and a.fy='$fy'";
      $query = $this->db->query($sql);
      //  echo  $this->db->last_query();
      if ($this->db->affected_rows() > 0) {
        //   echo  $this->db->last_query();
        return $query->result();
      } else {
        return false;
      }
    }
    function saveRigimOption($emp_code,$fy,$emp_data){
      $selectValue=array(
        "emp_code"=>$emp_code,
        "fy"=>$fy
      );
      $this->db->select('*');
      $this->db->from('acc_incometax_regime');
      $this->db->where($selectValue);
      $cnt = $this->db->get()->num_rows();
      if($cnt==0){
        $this->db->insert('acc_incometax_regime', $emp_data);
  //echo  $this->db->last_query();die();
      if($this->db->affected_rows() > 0){
        return "Income Tax Regime Option Saved Successfully.";
      }else{
        return "Something Went Worng.Please try again.";
      }
      }else{
        $this->db->where($selectValue);
        $this->db->update('acc_incometax_regime', $emp_data);
        if(!$this->db->_error_message()){
          return "Income Tax Regime Option Updated Successfully.";

        }else{
          return "Something Went Worng.Please try again.";

        }

      }
    }
    function getTaxRates($type,$fy,$tax_type=null){
    //  echo $tax_type;
        if($tax_type=='new'){
          $tax_type="1";
        }elseif ($tax_type=='old') {
          // code...
          $tax_type="0";
        }else{
          $tax_type="0";
        }
      //  echo $tax_type;exit;
        $q="select * from acc_itax_rates atr where atr.fy=? and atr.tp_type=? and atr.tax_regime=? order by atr.r_from";
        if($query=$this->db->query($q,array($fy,$type,$tax_type))){
       //echo $this->db->last_query(); exit;
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

   function get_CEA_OT($empno,$fy,$type){
        //echo $type;die();
        //$data=$this->getFYDetails($fy);
        //$q="select apd.* from acc_other_income apd where apd.date between '".$data['from']."-03-01' and '".$data['to']."-02-01' and apd.emp_no=? and apd.type=? order by apd.date";
        $q="select sum(apd.net) as CEA_OT from acc_other_income apd,(select * from acc_fyear_details a where a.fy=?) as fy where apd.date between fy.start_from and fy.end_to and apd.emp_no=? and apd.type=? order by apd.date";
        if($query=$this->db->query($q,array($fy,$empno,$type))){
            //echo $this->db->last_query();die();
            if($query->num_rows()>0){
                $result=$query->result_array();
                return $result[0];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function getPayPrintTDS($data,$fy){
        $from=date('Y-m-d',strtotime($fy[0]['start_from']));
        $to=date('Y-m-d',strtotime($fy[0]['end_to']));
        $i=0;
        foreach ($data['payable_fields'] as $r) {
            if($i==0){
                $pf.="sum(apd.".$r['field'].") as ".$r['field'];
                $pf_sum="sum(apd.".$r['field'].")";
            }
            else{
                $pf.=",sum(apd.".$r['field'].") as ".$r['field'];
                $pf_sum.="+sum(apd.".$r['field'].")";
            }
            $i++;
        }
        $i=0;
        foreach ($data['deduction_fields'] as $r) {
            if($i==0){
                $df.="sum(apd.".$r['field'].") as ".$r['field'];
                $df_sum="sum(apd.".$r['field'].")";
            }
            else{
                $df.=",sum(apd.".$r['field'].") as ".$r['field'];
                $df_sum.="+sum(apd.".$r['field'].")";
            }
            $i++;
        }

        $f_year = $fy[0]['fy'];

        // echo "<pre>";
        // print_r($fy[0]['fy']);
        // exit;

    /*  05-04-19   $q="SELECT apd.EMPNO,(select distinct(x.PNRNO) from acc_pay_details x where x.EMPNO=apd.EMPNO limit 1   ) as PANNO,UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS name,
            UCASE(dpt.name) AS dept, UCASE(c.name) as design, $pf,($pf_sum) as total_payable,$df,($df_sum) as total_deduction,(select sum(aoi.itax) from acc_other_income aoi where aoi.emp_no=apd.EMPNO and aoi.date between '$from' and '$to') as tax
        FROM (select A.* from acc_pay_details A where str_to_date(concat(A.YR,'-',A.MON,'-01'),'%Y-%m-%d') between '$from' and '$to') apd
        INNER JOIN user_details ud ON ud.id=apd.EMPNO
        INNER JOIN departments dpt on dpt.id=ud.dept_id
        INNER JOIN emp_basic_details b ON b.emp_no=apd.EMPNO
        INNER join designations c on c.id=b.designation
        group by apd.EMPNO
        ORDER BY cast(apd.EMPNO as decimal) ";*/

    /*  09-04-19  $q="SELECT aafd.*,apd.EMPNO,(select distinct(x.PNRNO) from acc_pay_details x where x.EMPNO=apd.EMPNO limit 1   ) as PANNO,UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS name,UCASE(ud.sex) as sex,
          UCASE(dpt.name) AS dept, UCASE(c.name) as design, $pf,($pf_sum) as total_payable,$df,($df_sum) as total_deduction,(select sum(aoi.itax) from acc_other_income aoi where aoi.emp_no=apd.EMPNO and aoi.date between '$from' and '$to') as tax
      FROM (select A.* from acc_pay_details A where str_to_date(concat(A.YR,'-',A.MON,'-01'),'%Y-%m-%d') between '$from' and '$to') apd
      LEFT JOIN acc_assessment_form_details aafd ON aafd.EMPNO=apd.EMPNO
      INNER JOIN user_details ud ON ud.id=apd.EMPNO
      INNER JOIN departments dpt on dpt.id=ud.dept_id
      INNER JOIN emp_basic_details b ON b.emp_no=apd.EMPNO
      INNER join designations c on c.id=b.designation
      group by apd.EMPNO
      ORDER BY cast(apd.EMPNO as decimal) ";*/
     /* 25-03-2021  $q="SELECT IFNULL(aafd.EMPNO,apd.EMPNO) as EMPNO,aafd.FY as FY,aafd.HOUSE_PROPERTY_INCOME as HOUSE_PROPERTY_INCOME,aafd.ANY_OTHER_INCOME_A as ANY_OTHER_INCOME_A,aafd.ANY_OTHER_INCOME_B as ANY_OTHER_INCOME_B,
aafd.PPF AS PPF,aafd.PEN_FUND as PEN_FUND,aafd.GIS as GIS,aafd.LIC as LICS, aafd.PLI as PLI,aafd.NEW_NSC as NEW_NSC,aafd.HBL as HBL,aafd.ELSS as ELSS,
aafd.MED_INS_DED_80D as MED_INS_DED_80D,aafd.PH_REBATE_80DD as PH_REBATE_80DD,aafd.OTHER_DEDUCTIONS_A as  OTHER_DEDUCTIONS_A,aafd.OTHER_DEDUCTIONS_B as OTHER_DEDUCTIONS_B,
aafd.BONUS as BONUS,aafd.VALUE_OF_PREQUIESTES as VALUE_OF_PREQUIESTES,aafd.PER_OF_PREQUIESTS as PER_OF_PREQUIESTS,aafd.OTHER_INCOME_RECEIPTS as OTHER_INCOME_RECEIPTS,
aafd.PAY_RECOVERY as PAY_RECOVERY,aafd.TUITION_FEE_PAID as  TUITION_FEE_PAID,aafd.DEDUCTION_FOR_MEDICAL_INSURANCE_PREMIA as DEDUCTION_FOR_MEDICAL_INSURANCE_PREMIA,
aafd.CHILD_EDU_ALLOW_EXEM as CHILD_EDU_ALLOW_EXEM,apd.EMPNO,(select distinct(x.PNRNO) from acc_pay_details x where x.EMPNO=apd.EMPNO limit 1   ) as PANNO,UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS name,UCASE(ud.sex) as sex,
    UCASE(dpt.name) AS dept, UCASE(c.name) as design, $pf,($pf_sum) as total_payable,$df,($df_sum) as total_deduction,(select sum(aoi.itax) from acc_other_income aoi where aoi.emp_no=apd.EMPNO and aoi.date between '$from' and '$to') as tax
FROM (select A.* from acc_pay_details A where str_to_date(concat(A.YR,'-',A.MON,'-01'),'%Y-%m-%d') between '$from' and '$to') apd
LEFT JOIN acc_assessment_form_details aafd ON aafd.EMPNO=apd.EMPNO
INNER JOIN user_details ud ON ud.id=apd.EMPNO
INNER JOIN departments dpt on dpt.id=ud.dept_id
INNER JOIN emp_basic_details b ON b.emp_no=apd.EMPNO
INNER join designations c on c.id=b.designation
group by apd.EMPNO
ORDER BY cast(apd.EMPNO as decimal) "; */

$q="SELECT IFNULL(aafd.EMPNO,apd.EMPNO) as EMPNO,aafd.FY as FY,aafd.HOUSE_PROPERTY_INCOME as HOUSE_PROPERTY_INCOME,aafd.ANY_OTHER_INCOME_A as ANY_OTHER_INCOME_A,aafd.ANY_OTHER_INCOME_B as ANY_OTHER_INCOME_B,
aafd.PPF AS PPF,aafd.PEN_FUND as PEN_FUND,aafd.GIS as GIS,aafd.LIC as LICS, aafd.PLI as PLI,aafd.NEW_NSC as NEW_NSC,aafd.HBL as HBL,aafd.ELSS as ELSS,
aafd.MED_INS_DED_80D as MED_INS_DED_80D,aafd.PH_REBATE_80DD as PH_REBATE_80DD,aafd.OTHER_DEDUCTIONS_A as  OTHER_DEDUCTIONS_A,aafd.OTHER_DEDUCTIONS_B as OTHER_DEDUCTIONS_B,
aafd.BONUS as BONUS,aafd.VALUE_OF_PREQUIESTES as VALUE_OF_PREQUIESTES,aafd.PER_OF_PREQUIESTS as PER_OF_PREQUIESTS,aafd.OTHER_INCOME_RECEIPTS as OTHER_INCOME_RECEIPTS,
aafd.PAY_RECOVERY as PAY_RECOVERY,aafd.TUITION_FEE_PAID as  TUITION_FEE_PAID,aafd.DEDUCTION_FOR_MEDICAL_INSURANCE_PREMIA as DEDUCTION_FOR_MEDICAL_INSURANCE_PREMIA,
aafd.CHILD_EDU_ALLOW_EXEM as CHILD_EDU_ALLOW_EXEM,apd.EMPNO,(select distinct(x.PNRNO) from acc_pay_details x where x.EMPNO=apd.EMPNO limit 1   ) as PANNO,UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS name,UCASE(ud.sex) as sex,
    UCASE(dpt.name) AS dept, UCASE(c.name) as design, $pf,($pf_sum) as total_payable,$df,($df_sum) as total_deduction,(select sum(aoi.itax) from acc_other_income aoi where aoi.emp_no=apd.EMPNO and aoi.date between '$from' and '$to') as tax
FROM (select A.* from acc_pay_details A where str_to_date(concat(A.YR,'-',A.MON,'-01'),'%Y-%m-%d') between '$from' and '$to') apd
LEFT JOIN acc_assessment_form_details aafd ON aafd.EMPNO=apd.EMPNO and aafd.FY = '$f_year'
INNER JOIN user_details ud ON ud.id=apd.EMPNO
INNER JOIN departments dpt on dpt.id=ud.dept_id
INNER JOIN emp_basic_details b ON b.emp_no=apd.EMPNO
INNER join designations c on c.id=b.designation
group by apd.EMPNO
ORDER BY cast(apd.EMPNO as decimal) ";

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

    function getDCPS($EMPNO){
        $q="select aee.EMPNO,aee.DCPS from acc_emp_eligiblity aee where aee.DCPS=? and aee.EMPNO=?";
        if($query=$this->db->query($q,array(1,$EMPNO))){
            if($query->num_rows()>0){
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

    function getCessDetails($fy){
        $this->db->where('FY',$fy);
        if($query=$this->db->get('acc_assessment_cess_details')){
            if($query->num_rows()>0){
                $result=$query->result();
                return $result[0];
            }
            else{
                return false;
            }

        }
        else{
            return false;
        }
    }

    function getDetailsFilledByEmp($cond){
        $this->db->where($cond);
        if($query=$this->db->get('acc_assessment_form_details')){
            if($query->num_rows()>0){
                $result=$query->result();
                return $result[0];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

     function getDetailsFilledByEmpTDS($cond){
        #$this->db->where($cond);
        if($query=$this->db->get('acc_assessment_form_details')){
            if($query->num_rows()>0){
                $result=$query->result();
                return $result[0];
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function get80Deductions($fy, $empno){
        #$q="select apd.RFUND from acc_pay_details apd where apd.YR='18' and apd.MON='8' and  apd.EMPNO=?";
        $q="select sum(apd.RFUND) as sum from acc_pay_details apd,(select * from acc_fyear_details a where a.fy=?) as fy  where date(concat(2000+apd.yr,'-',apd.MON,'-01')) between fy.start_from and fy.end_to and apd.EMPNO=? order by date(concat(2000+apd.yr,'-',apd.MON,'-01'))";
        if($query=$this->db->query($q,array($empno,$fy))){
                //echo $this->db->last_query(); die();
                #$result=$query->result();
            /* This method returns a single result row. If your query has more than one row, it returns only the first row. */
                $row = $query->row();
                return $row;

        }
        else{
            return false;
        }
    }

}
