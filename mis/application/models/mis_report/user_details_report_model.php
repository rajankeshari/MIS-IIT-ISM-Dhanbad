<?php

class User_details_report_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_enrollment_year()
    {
              
        $sql="select distinct enrollment_year from stu_academic order by enrollment_year desc";

        
        
       $query = $this->db->query($sql);

           // echo $this->db->last_query(); die();
            if ($this->db->affected_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
    }
    function get_auth_id(){
        $sql="select distinct auth_id from stu_academic order by auth_id desc";

        
        
       $query = $this->db->query($sql);

           // echo $this->db->last_query(); die();
            if ($this->db->affected_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
    }

    
    
    
    function get_student($syear, $sauth, $did, $cid, $bid, $sem)
    {
              
        $sql="select a.id as admn_no,upper(b.auth_id)as stype,
concat_ws(' ',a.salutation,a.first_name,a.middle_name,a.last_name)as stu_name,
CASE (a.sex) WHEN 'm' THEN 'Male' WHEN 'f' THEN 'Female' END as 'sex',
a.category,
a.allocated_category,
DATE_FORMAT(a.dob, '%d-%m-%Y')as dob,
a.email,
a.physically_challenged,
f.mobile_no,
c.name as dname,
d.name as cname,
e.name as bname,
b.semester,
a.dept_id,
b.course_id,
b.branch_id,
f.father_name,
concat_ws(' ',g.line1,g.line2,g.city,g.state,g.pincode) as address
from user_details a
inner join stu_academic b on a.id=b.admn_no
left join departments c on c.id=a.dept_id
left join cs_courses d on d.id=b.course_id
left join cs_branches e on e.id=b.branch_id
inner join user_other_details f on f.id=a.id
inner join user_address g on g.id=a.id and g.`type`='permanent'
where 1=1";

        if($syear){
            $sql.=" and b.enrollment_year='".$syear."'";
        }
        if($sauth!='all'){
            $sql.=" and b.auth_id='".$sauth."'";
        }
       
        if($did!='all'){
            $sql.=" and a.dept_id='".$did."'";
        }
        if($cid!='all'){
            $sql.=" and b.course_id='".$cid."'";
        }
        if($bid!='all'){
            $sql.=" and b.branch_id='".$bid."'";
        }
        if($sem!=''){
            $sql.=" and b.semester='".$sem."'";
        }
        
        $sql.=" order by a.dept_id,b.course_id,b.branch_id,b.semester,a.id ";
        
       $query = $this->db->query($sql);

           // echo $this->db->last_query(); die();
            if ($this->db->affected_rows() > 0) {
                return $query->result();
            } else {
                return false;
            }
    }

}

?>