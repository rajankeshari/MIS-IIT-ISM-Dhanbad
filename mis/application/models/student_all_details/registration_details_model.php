<?php

class Registration_details_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_personal_details($admn_no) {

        $sql = "select a.id,concat_ws(' ',a.first_name,a.middle_name,a.last_name)as stu_name,c.name as dname,d.name as cname,e.name as bname,b.auth_id,a.dept_id,b.course_id,b.branch_id from user_details a 
inner join stu_academic b on a.id=b.admn_no
left join departments c on c.id=a.dept_id
left join cs_courses d on d.id=b.course_id
left join cs_branches e on e.id=b.branch_id
where a.id=?";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    //===================Regulart================================
    function get_regular_registration($admn_no) {

        $sql = "select a.* from reg_regular_form a where a.admn_no=? order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
    //===================Other================================
    function get_other_registration($admn_no) {

        $sql = "select a.* from reg_other_form a where a.admn_no=? and a.`type`='R' order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
    //===================Special================================
    function get_special_registration($admn_no) {

        $sql = "select a.* from reg_other_form a where a.admn_no=? and a.`type`='S' order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
    //===================Summer================================
    function get_summer_registration($admn_no) {

        $sql = "select a.* from reg_summer_form a where a.admn_no=? order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
    //===================Exam================================
    function get_exam_registration($admn_no) {

        $sql = "select a.* from reg_exam_rc_form a where a.admn_no=? and a.`type`='R' order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
//===================Exam Special================================
    function get_exam_spl_registration($admn_no) {

        $sql = "select a.* from reg_exam_rc_form a where a.admn_no=? and a.`type`='S' order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================
    //===================Idle Special================================
    function get_idle_registration($admn_no) {

        $sql = "select a.* from reg_idle_form a where a.admn_no=? order by a.session_year desc,a.`session` desc,a.semester desc;";

        $query = $this->db->query($sql, array($admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //  ======================================================


    function get_core_subject($form_id, $admn_no) {

        $sql = "select c.id,c.subject_id,c.name,'Core' as paper_type,c.`type`,b.aggr_id,b.semester from reg_regular_form a 
inner join course_structure b on (b.aggr_id=a.course_aggr_id and b.semester=a.semester)
inner join subjects c on c.id=b.id
where a.form_id=? and a.admn_no=? and b.sequence not like '%.%'
order by c.subject_id";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //common


    function get_core_subject_common($form_id, $admn_no) {

        $sql = "SELECT c.id,c.subject_id,c.name,'Core' AS paper_type,c.`type`,b.aggr_id,b.semester
FROM reg_regular_form a
INNER JOIN course_structure b ON (b.aggr_id=a.course_aggr_id AND b.semester=concat_ws('_',a.semester,a.section))
INNER JOIN subjects c ON c.id=b.id
WHERE a.form_id=? AND a.admn_no=? AND b.sequence NOT LIKE '%.%'
ORDER BY c.subject_id";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_elective_subject($form_id, $admn_no) {

        $sql = "select c.id,c.subject_id,c.name,'Elective' as paper_type,c.`type`,e.aggr_id,e.semester from reg_regular_form a
inner join reg_regular_elective_opted d on d.form_id=a.form_id
inner join subjects c on c.id=d.sub_id
inner join course_structure e on e.id = c.id
where a.form_id=? and a.admn_no=?";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_honours_subject($form_id, $admn_no) {

        $sql = "select d.id,d.subject_id,d.name, 'Honours' as paper_type,d.`type`,c.aggr_id,c.semester from reg_regular_form a
inner join hm_form b on b.admn_no=a.admn_no
inner join course_structure c on (c.aggr_id=b.honours_agg_id and c.semester=a.semester)
inner join subjects d on d.id=c.id
where a.form_id=? and a.admn_no=?
and b.honours='1' and b.honour_hod_status='Y'";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_minor_subject($form_id, $admn_no) {

        $sql = "select e.id,e.subject_id,e.name,'Minor' as paper_type,e.`type`,d.aggr_id,d.semester from reg_regular_form a
inner join hm_form b on b.admn_no=a.admn_no
inner join hm_minor_details c on c.form_id=b.form_id
inner join course_structure d on (d.aggr_id=c.minor_agg_id and d.semester=a.semester)
inner join subjects e on e.id=d.id
where a.form_id=? and a.admn_no=?
and b.minor='1' and b.minor_hod_status='Y'
and c.offered='1'";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_latest_registration_status($form_id, $admn_no) {

        $sql = "select a.* from reg_regular_form a where a.form_id=? and a.admn_no=?";

        $query = $this->db->query($sql, array($form_id, $admn_no));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_subject_map_id($data) {
        $sql = "select a.* from subject_mapping a
where a.session_year=?
 and a.`session`=? 
 and a.dept_id=?
 and a.course_id=? 
 and a.branch_id=?
and a.aggr_id=? 
and a.semester=?
";

        $query = $this->db->query($sql, array($data['syear'], $data['sess'], $data['did'], $data['cid'], $data['bid'], $data['aggr_id'], $data['sem']));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    function get_subject_map_id_jrf($data) {
        $sql = "select a.* from subject_mapping a
            inner join subject_mapping_des b on a.map_id=b.map_id
where a.session_year=?
 and a.`session`=? 
 and a.dept_id=?
 and a.course_id=? 
 and a.branch_id=?
 and b.sub_id=?
";

        $query = $this->db->query($sql, array($data['syear'], $data['sess'], $data['did'], $data['cid'], $data['bid'],$data['sub_id']));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_subject_map_id_com($data) {
        $sql = "select a.* from subject_mapping a
where a.session_year=?
 and a.`session`=? 
 and a.dept_id=?
 and a.course_id=? 
 and a.branch_id=?
and a.aggr_id=? 
and a.semester=?
and a.section=?";

        $query = $this->db->query($sql, array($data['syear'], $data['sess'], $data['did'], $data['cid'], $data['bid'], $data['aggr_id'], $data['sem'], $data['section']));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_subject_map_id_minor($data) {
        $sql = "select a.* from subject_mapping a
where a.session_year=?
 and a.`session`=? 
 and a.course_id=? 
and a.aggr_id=? 
and a.semester=?
";

        $query = $this->db->query($sql, array($data['syear'], $data['sess'], $data['cid'], $data['aggr_id'], $data['sem']));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_student_section($admn_no, $syear) {
        $sql = "select a.section from stu_section_data a  where a.admn_no=? and a.session_year=?";

        $query = $this->db->query($sql, array($admn_no, $syear));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row()->section;
        } else {
            return false;
        }
    }

    function get_marks_master_id($data) {

        if ($data['etype'] == 'regular') {
            $et = 'R';
        }
        if ($data['etype'] == 'other') {
            $et = 'O';
        }
        if ($data['etype'] == 'special') {
            $et = 'S';
        }
        //if($data['summer']=='special'){ $et='S'; }
        if ($data['etype'] == 'summer') {
            $et = 'R';
        }
        if($data['etype']=='exam_spl' && $data['auth_id']=='jrf'){ $et='JS'; }
                if($data['etype']=='exam' && $data['auth_id']=='jrf'){ $et='J'; }
                if($data['etype']=='exam_spl' && $data['auth_id']!='jrf'){ $et='S'; }
                if($data['etype']=='exam' && $data['auth_id']!='jrf'){ $et='O'; }

        $sql = "select a.* from marks_master a
where a.session_year=? and a.`session`=?
and a.sub_map_id=? and a.subject_id=? and a.`status`='Y' and a.`type`=?";

        $query = $this->db->query($sql, array($data['syear'], $data['sess'], $data['sub_mapp_id']->map_id, $data['sub_id'], $et));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_marks_description_id($data) {

        $sql = "select a.* from marks_subject_description a where a.marks_master_id=?  and a.admn_no=?";

        $query = $this->db->query($sql, array($data['marks_master_id']->id, $data['admn_no']));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    //=========================Other Table ======================
    function get_latest_registration_status_other($form_id, $admn_no, $tbl) {

        if ($tbl == 'other') {
            $et = 'R';
        }
        if ($tbl == 'special') {
            $et = 'S';
        }

        $sql = "select a.* from reg_other_form a where a.form_id=? and a.admn_no=? and a.`type`=?";

        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    
    //========================Exam Table End===========================
    
    function get_latest_registration_status_exam($form_id, $admn_no, $tbl) {

        if ($tbl == 'exam') {
            $et = 'R';
        }
        if ($tbl == 'exam_spl') {
            $et = 'S';
        }

        $sql = "select a.* from reg_exam_rc_form a where a.form_id=? and a.admn_no=? and a.`type`=?";

        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    //========================Exam Table End===========================

    function get_subjects_other($form_id, $admn_no, $tbl) {
        if ($tbl == 'other') {
            $et = 'R';
        }
        if ($tbl == 'special') {
            $et = 'S';
        }

        $sql = "select b.sub_id as id,c.subject_id,c.name,
CASE 
	  WHEN d.aggr_id like '%honour%' THEN 'Honour'
     WHEN d.aggr_id like '%minor%' THEN 'Minor'
     ELSE 'Core'
END AS paper_type,
c.`type`,d.aggr_id,d.semester from reg_other_form a 
inner join reg_other_subject b on b.form_id=a.form_id
inner join subjects c on c.id=b.sub_id
inner join course_structure d on d.id=b.sub_id
where a.form_id=? and a.admn_no=? and a.`type`=?";

        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    //==========================================exam subjecsh================
    function get_subjects_exam($form_id, $admn_no, $tbl) {
		
		$aid=$this->get_auth_id($admn_no);
		
		
        if ($tbl == 'exam') {
            $et = 'R';
        }
        if ($tbl == 'exam_spl') {
            $et = 'S';
        }
if($aid<>'jrf'){
        $sql = "select b.sub_id as id,c.subject_id,c.name,
CASE 
	  WHEN d.aggr_id like '%honour%' THEN 'Honour'
     WHEN d.aggr_id like '%minor%' THEN 'Minor'
     ELSE 'Core'
END AS paper_type,
c.`type`,d.aggr_id,d.semester from reg_exam_rc_form a 
inner join reg_exam_rc_subject b on b.form_id=a.form_id
inner join subjects c on c.id=b.sub_id
/*inner join course_structure d on d.id=b.sub_id*/
where a.form_id=? and a.admn_no=? and a.`type`=?";
	}
	else{
		$sql = "SELECT b.sub_id AS id,c.subject_id,c.name, 'Core' AS paper_type, c.`type`,'na' as aggr_id, 'na' as semester
FROM reg_exam_rc_form a
INNER JOIN reg_exam_rc_subject b ON b.form_id=a.form_id
INNER JOIN subjects c ON c.id=b.sub_id /*inner join course_structure d on d.id=b.sub_id*/
WHERE a.form_id=? AND a.admn_no=? AND a.`type`=?";
	}
        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    //=================================Summer Table============
    function get_latest_registration_status_summer($form_id, $admn_no) {

        $sql = "select a.* from reg_summer_form a where a.form_id=? and a.admn_no=?";

        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    function get_subjects_summer($form_id, $admn_no) {
        $sql = "SELECT b.sub_id AS id,c.subject_id,c.name, CASE WHEN d.aggr_id LIKE '%honour%' THEN 'Honour' WHEN d.aggr_id LIKE '%minor%' THEN 'Minor' ELSE 'Core' END AS paper_type, c.`type`,d.aggr_id,d.semester
FROM reg_summer_form a
INNER JOIN reg_summer_subject b ON b.form_id=a.form_id
INNER JOIN subjects c ON c.id=b.sub_id
INNER JOIN course_structure d ON d.id=b.sub_id
WHERE a.form_id=? AND a.admn_no=?";

        $query = $this->db->query($sql, array($form_id, $admn_no, $et));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    function get_heighest_marks($subid, $syear) {

        $sql = "select a.highest_marks from marks_master a where a.subject_id=? and a.session_year=? and a.`type`='R'";

        $query = $this->db->query($sql, array($subid, $syear));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row()->highest_marks;
        } else {
            return false;
        }
    }

    function insert_marks_master_row($data) {
        if ($this->db->insert('marks_master', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    function insert_subject_mapping($data){
        if ($this->db->insert('subject_mapping', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    
    function insert_subject_mapping_desc($data){
        if ($this->db->insert('subject_mapping_des', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    
    function insert_marks_subject_description_row($data) {
        if ($this->db->insert('marks_subject_description', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }

    
//==========================================================
    function get_subject_type($subid) {

        $sql = "SELECT 
        CASE type
           WHEN 'Theory' THEN 'T'
           WHEN 'Practical' THEN 'P'
           ELSE 'U'
        END AS subtype
     from subjects where id=?";

        $query = $this->db->query($sql, array($subid));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row()->subtype;
        } else {
            return false;
        }
    }
    
    function get_auth_id($id){
        $sql = "select auth_id from stu_academic where admn_no=?";

        $query = $this->db->query($sql, array($id));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row()->auth_id;
        } else {
            return false;
        }
        
    }
    function get_course_structure($admn_no,$sy,$sess){
        $sql = "select * from reg_regular_form where admn_no=? and session_year=? and session=?";

        $query = $this->db->query($sql, array($admn_no,$sy,$sess));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    function get_subjects_core($cstruc,$sem){
        $sql = "select a.*,b.subject_id,b.name from course_structure a 
inner join subjects b on a.id=b.id
where a.aggr_id=? and a.semester=? and a.sequence not like '%.%'";

        $query = $this->db->query($sql, array($cstruc,$sem));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_subjects_elec($form_id){
        $sql = "select a.*,b.subject_id,b.name from reg_regular_elective_opted a 
inner join subjects b on b.id=a.sub_id
where a.form_id=?";

        $query = $this->db->query($sql, array($form_id));

        // echo $this->db->last_query(); die();
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
        function insert_sub_in_summer($data) {
        if ($this->db->insert('reg_summer_subject', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    function add_mod_sub($data){
        if ($this->db->insert('moderation_sub_add', $data))
            return $this->db->insert_id();
        else
            return FALSE;
    }
    
    
    
}

?>