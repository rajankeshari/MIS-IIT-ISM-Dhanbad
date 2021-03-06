<?php

class cbcs_monitor_marks_submission_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }


    function get_data_template_wise1($syear,$sess)
    {

      

        $sql=" SELECT t1.lecture,t1.sub_code,
t1.sub_type,t1.subject_id,t1.stu_cnt,t1.emp_name,
t1.required_1,
t1.ctr_A_A_plus_temp_1,
t1.required_2,t1.ctr_B_B_plus_temp_1,t1.required_3,t1.ctr_C_C_plus_temp_1,
t1.required_4,t1.ctr_D_F_plus_temp_1
 FROM(
select  n.lecture,n.sub_code,n.sub_type, v.* from
(select y.subject_id, sum(y.tot) as stu_cnt,  (select     concat(concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name),'[',y.emp_id,']')   from user_details u where u.id=y.emp_id) as emp_name,
       'A+,A : TOP 15-25 %'   as  required_1,
      if( sum(y.tot)>50 , sum( ( case  when y.grade like 'A%' then  y.tot  end) )*100/sum(y.tot),'N/A' ) as  ctr_A_A_plus_temp_1,
      'B+,B : NEXT 35-45 %'   as  required_2,
        if(   sum(y.tot)>50 ,  sum( ( case  when y.grade like 'B%' then  y.tot  end)  )*100/sum(y.tot),'N/A' ) as  ctr_B_B_plus_temp_1,
        'C+,C : NEXT 25-35 %'   as  required_3,
              if( sum(y.tot)>50 , sum( ( case  when y.grade like 'C%' then  y.tot  end)  )*100/sum(y.tot),'N/A' ) as  ctr_C_C_plus_temp_1,
              'D,F : NEXT 5-15 %'   as  required_4,
                     if( sum(y.tot)>50 ,  sum( ( case  when (y.grade like 'D' or y.grade like 'F')  then  y.tot  end ))*100/sum(y.tot),'N/A' )  as  ctr_D_F_plus_temp_1,
                   
          'A+,A,B+ : TOP 40-50 %'   as  required_less_1,
                  if( sum(y.tot)<50 , sum( ( case  when (y.grade like 'A%' or y.grade like 'B+')  then  y.tot  end)  )*100/sum(y.tot) ,'N/A' )as  ctr_A_A_plus_B_plus_temp_2,
                'B,C+,C : NEXT 40-50 %'   as  required_less_2,
if( sum(y.tot)<50 ,  sum( ( case  when (y.grade like 'C%' or y.grade like 'B')  then  y.tot  end)  )*100/sum(y.tot) ,'N/A' )as  ctr_C_C_plus_B_temp_2,
               'D,F : NEXT 5-15 %'   as  required_less_3,
 if( sum(y.tot)<50 ,  sum( ( case  when (y.grade like 'D' or y.grade like 'F')  then  y.tot  end ))*100/sum(y.tot),'N/A' )  as  ctr_D_F_plus2_temp_2

 from
( select x.*,  count(x.admn_no)  as tot
 from
(select a.subject_id,b.grade,b.admn_no,a.emp_id
from cbcs_marks_master a
inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
where b.grade IS NOT NULL  AND a.session_year=?  AND a.`session`=? and b.grade<>'I'
order by a.subject_id)x group by x.subject_id,x.grade) y
group by y.subject_id  

     ) v

right join

( select  a.sub_code,a.sub_type,a.lecture   from  cbcs_subject_offered a  where  a.sub_type not in('Practical','Non-Contact','Audit')  
  union
  select  a.sub_code,a.sub_type,a.lecture from  old_subject_offered a   where a.sub_type not in('Practical','Non-Contact','Audit')
) n

on n.sub_code=v.subject_id)t1 WHERE t1.stu_cnt>50 AND t1.lecture>=1
";

        
        $query = $this->db->query($sql,array($syear,$sess));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	//Template 2
	
	function get_data_template_wise2($syear,$sess)
    {

      

        $sql=" SELECT t1.lecture,t1.sub_code,
t1.sub_type,t1.subject_id,t1.stu_cnt,t1.emp_name,
t1.required_less_1,
t1.ctr_A_A_plus_B_plus_temp_2,
t1.required_less_2,
t1.ctr_C_C_plus_B_temp_2,
t1.required_less_3,
t1.ctr_D_F_plus2_temp_2

 FROM(
select  n.lecture,n.sub_code,n.sub_type, v.* from
(select y.subject_id, sum(y.tot) as stu_cnt,  (select     concat(concat_ws(' ',u.salutation,u.first_name,u.middle_name,u.last_name),'[',y.emp_id,']')   from user_details u where u.id=y.emp_id) as emp_name,
       'A+,A : TOP 15-25 %'   as  required_1,
      if( sum(y.tot)>50 , sum( ( case  when y.grade like 'A%' then  y.tot  end) )*100/sum(y.tot),'N/A' ) as  ctr_A_A_plus_temp_1,
      'B+,B : NEXT 35-45 %'   as  required_2,
        if(   sum(y.tot)>50 ,  sum( ( case  when y.grade like 'B%' then  y.tot  end)  )*100/sum(y.tot),'N/A' ) as  ctr_B_B_plus_temp_1,
        'C+,C : NEXT 25-35 %'   as  required_3,
              if( sum(y.tot)>50 , sum( ( case  when y.grade like 'C%' then  y.tot  end)  )*100/sum(y.tot),'N/A' ) as  ctr_C_C_plus_temp_1,
              'D,F : NEXT 5-15 %'   as  required_4,
                     if( sum(y.tot)>50 ,  sum( ( case  when (y.grade like 'D' or y.grade like 'F')  then  y.tot  end ))*100/sum(y.tot),'N/A' )  as  ctr_D_F_plus_temp_1,
                   
          'A+,A,B+ : TOP 40-50 %'   as  required_less_1,
                  if( sum(y.tot)<50 , sum( ( case  when (y.grade like 'A%' or y.grade like 'B+')  then  y.tot  end)  )*100/sum(y.tot) ,'N/A' )as  ctr_A_A_plus_B_plus_temp_2,
                'B,C+,C : NEXT 40-50 %'   as  required_less_2,
if( sum(y.tot)<50 ,  sum( ( case  when (y.grade like 'C%' or y.grade like 'B')  then  y.tot  end)  )*100/sum(y.tot) ,'N/A' )as  ctr_C_C_plus_B_temp_2,
               'D,F : NEXT 5-15 %'   as  required_less_3,
 if( sum(y.tot)<50 ,  sum( ( case  when (y.grade like 'D' or y.grade like 'F')  then  y.tot  end ))*100/sum(y.tot),'N/A' )  as  ctr_D_F_plus2_temp_2

 from
( select x.*,  count(x.admn_no)  as tot
 from
(select a.subject_id,b.grade,b.admn_no,a.emp_id
from cbcs_marks_master a
inner join cbcs_marks_subject_description b on a.id=b.marks_master_id
where b.grade IS NOT NULL  AND a.session_year=?  AND a.`session`=? and b.grade<>'I'
order by a.subject_id)x group by x.subject_id,x.grade) y
group by y.subject_id  

     ) v

right join

( select  a.sub_code,a.sub_type,a.lecture   from  cbcs_subject_offered a  where  a.sub_type not in('Practical','Non-Contact','Audit')  
  union
  select  a.sub_code,a.sub_type,a.lecture from  old_subject_offered a   where a.sub_type not in('Practical','Non-Contact','Audit')
) n

on n.sub_code=v.subject_id)t1 WHERE t1.stu_cnt BETWEEN 16 AND 50 AND t1.lecture>=1
";

        
        $query = $this->db->query($sql,array($syear,$sess));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }
	//Template 3
	
	function get_data_template_wise3($syear,$sess)
    {

      

        $sql=" SELECT tt.* FROM(
SELECT t1.*,(
SELECT CONCAT(CONCAT_WS(' ',u.salutation,u.first_name,u.middle_name,u.last_name),'[',t1.emp_id,']')
FROM user_details u
WHERE u.id=t1.emp_id) AS emp_name
FROM (
SELECT p.*, /*o.sub_type AS old_sub_type, c.sub_type AS cbcs_sub_type,*/ 
(CASE WHEN CONCAT('o',o.id)=p.sub_map_id THEN o.sub_type ELSE c.sub_type END) AS sub_code,
(CASE WHEN CONCAT('o',o.id)=p.sub_map_id THEN o.lecture ELSE c.lecture END) AS lecture
FROM (
SELECT a.emp_id,a.sub_map_id,a.subject_id, COUNT(b.id) AS total_stu, SUM(IF(b.grade= NULL OR b.grade='',1,0)) AS gradingStatus, SUM(IF(b.grade='A+',1,0))*100/ COUNT(b.id) AS 'AP', SUM(IF(b.grade='A',1,0))*100/ COUNT(b.id) AS 'A', SUM(IF(b.grade='B+',1,0))*100/ COUNT(b.id) AS 'BP', SUM(IF(b.grade='B',1,0))*100/ COUNT(b.id) AS 'B', SUM(IF(b.grade='C+',1,0))*100/ COUNT(b.id) AS 'CP', SUM(IF(b.grade='C',1,0))*100/ COUNT(b.id) AS 'C', SUM(IF(b.grade='D',1,0))*100/ COUNT(b.id) AS 'D', SUM(IF(b.grade='F',1,0))*100/ COUNT(b.id) AS 'F', SUM(IF(b.grade='I',1,0))*100/ COUNT(b.id) AS 'I'
FROM cbcs_marks_master a
INNER JOIN cbcs_marks_subject_description b ON b.marks_master_id=a.id
WHERE a.session_year=? AND a.`session`=? and b.grade<>'I'
GROUP BY a.sub_map_id)p
LEFT JOIN old_subject_offered o ON CONCAT('o',o.id)=p.sub_map_id
LEFT JOIN cbcs_subject_offered c ON CONCAT('c',c.id)=p.sub_map_id)t1 
WHERE 
(
(t1.sub_code='Theory'  || t1.sub_code='Sessional'  ||  (t1.sub_code='Modular'  &&    t1.lecture>=1    ) )
 AND (t1.total_stu>1 && t1.total_stu<16)) 
	
		OR(!(t1.sub_code='Theory'  || t1.sub_code='Sessional'||   (t1.sub_code='Modular'  &&    t1.lecture>=1    )  )
		  )
		
)tt ";

        
        $query = $this->db->query($sql,array($syear,$sess));

       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return $query->result();
        } else {
            return false;
        }
    }


}

?>
