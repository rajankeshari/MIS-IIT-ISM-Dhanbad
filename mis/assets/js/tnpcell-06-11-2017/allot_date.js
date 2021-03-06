// JavaScript Document
function checkslot_in_reschedule(company_id)
{
	if($("#date_reschedulefrom_"+company_id).val() == "" && $("#date_rescheduleto_"+company_id).val() == "")
	{
		$("#date_reschedulefrom_"+company_id).focus();
		alert("Please Select a Valid Date");
	}
	else if($("#date_reschedulefrom_"+company_id).val() == "")
		$("#date_reschedulefrom_"+company_id).focus();
	else if($("#date_rescheduleto_"+company_id).val() == "")
		$("#date_rescheduleto_"+company_id).focus();
	else
		checkslot_reschedule($("#date_reschedulefrom_"+company_id).val(),$("#date_rescheduleto_"+company_id).val(),company_id);
}

function checkslot_reschedule($from,$to,$company_id)
{
	$box_reschedule_top.showLoading();
	$.ajax({
		url:site_url("tnpcell/allot_date/json_get_company_inrange/"+$from+"/"+$to),
		success:function(data){
			$box_reschedule_bottom.show();
			$company_to_compare = $company_id;
			$base_str = "<thead><tr><th>S.No</th><th>Company Name</th><th>Date</th><th>Status</th><th>Compare</th></tr></thead>";
			if(data.length == 0)
				$base_str += "<tr><td colspan = '4'>No Company in this Slot.</td></tr>";
			for($d = 0;$d<data.length;$d++)
			{
				$company_to_compare_with = data[$d]['company_id'];
				$base_str += "<tr><td>"+($d+1)+"</td><td>"+data[$d]['company_name']+"</td><td>"+data[$d]['date_from'] + " to " + data[$d][
				'date_to']+"</td><td>"+data[$d]['status']+"</td><td><a href = 'compare_companies/Compare/"+$company_to_compare+"/"+
				$company_to_compare_with+"' target = '_blank'>Compare</a></td></tr>";
			}
			$("#table_reschedule_bottom").html($base_str);
			$box_reschedule_top.hideLoading();
		},
		type:"post",
		dataType:"json",
		fail:function(error){
			console.log(error);
			$box_reschedule_top.hideLoading();
		}
	});
}

function helper_reschedule_in_reschedule(company_id,ppt,test,interview)
{
	
	var date_from = $("#date_reschedulefrom_"+company_id).val();
	var date_to = $("#date_rescheduleto_"+company_id).val();
	reschedule_in_reschedule(company_id,$date_from,$date_to,ppt,test,interview);	
}

function reschedule_in_reschedule(company_id,date_from,date_to,ppt,test,interview)
{
	
	var stu_visibility = $("#stu_visibility_"+company_id+":checked").length;
	
	if($("#date_reschedulefrom_"+company_id).val() != "" && $("#date_rescheduleto_"+company_id).val() != "")
		var data_to_send = {'company_id':company_id,'date_from':date_from,'date_to':date_to,'stu_visibility':stu_visibility,'ppt':ppt,'test':test,'interview':interview};		
	else 
		var data_to_send = {'company_id':company_id,'stu_visibility':stu_visibility,'ppt':ppt,'test':test,'interview':interview};

	data_to_send = JSON.stringify(data_to_send);

	$("#box_reschedule_top").showLoading();
	$.ajax({
		url:site_url("tnpcell/allot_date/RescheduleCompany/"),
		type:"post",
		data:data_to_send,
		dataType:"json",
		success:function(data){
			console.log(data);
			// document.location.href = "allot_date";
			// $("#tabPanesreschedule").addClass("tab-pane active");
			// alert("Company Rescheduled successfully");
			$("#box_reschedule_top").hideLoading();
		},
		error:function(error){
			console.log(error);
			$("#box_reschedule_top").hideLoading();
		}
	});

	return false;
}

function RemoveAllotedDate(company_id)
{
	$box_reschedule_top.showLoading();
	$.ajax({
		url:site_url("tnpcell/allot_date/RemoveAllotedDate/"+company_id),
		success:function(data){
			//alert("Company Rescheduled successfully");
			$box_reschedule_top.hideLoading();
			document.location.href = "allot_date";
		},
		type:"post",
		fail:function(error){
			console.log(error);
			$box_reschedule_top.hideLoading();
		}
	});
}

function change_status(company_id)
{
	var status = $("#ddl_status_" + company_id).val();
	$("#box_changestatus").showLoading();
	$.ajax({
		
		//alert(status);
		url:site_url("tnpcell/allot_date/ChangeStatus/"+company_id+"/"+status),
		success:function(data){
			// alert("Company Rescheduled successfully");
			// console.log(data);
			$("#box_changestatus").hideLoading();
		},
		type:"post",
		error:function(error){
			console.log(error);
			$("#box_changestatus").hideLoading();
		}
	});
}


$(document).ready(function(){
	$date_from = $("#date_from");
	$date_to = $("#date_to");
	$box_makeschedule_top = $("#box_makeschedule_top");
	$box_makeschedule_bottom = $("#box_makeschedule_bottom");
	
	$box_reschedule_top = $("#box_reschedule_top");
	$box_reschedule_bottom = $("#box_reschedule_bottom");
	$box_makeschedule_bottom.hide();
	$box_reschedule_bottom.hide();
	
	//$date_to.on('change',function(){
	//	checkslot($date_from.val(),$date_to.val());
	//});
	
	$("#btn_checkslot").on('click',function(){
		if($date_from.val() == "" && $date_to.val() == "")
		{
			$date_from.focus();
			alert("Please Select a Valid Date");
		}
		else if($date_from.val() == "")
			$date_from.focus();
		else if($date_to.val() == "")
			$date_to.focus();
		else
			checkslot($date_from.val(),$date_to.val());
	});
	
	
		
	function checkslot($from,$to)
	{
		$company_to_compare = $("#ddl_company").val();
		$box_makeschedule_top.showLoading();
		$.ajax({
			url:site_url("tnpcell/allot_date/json_get_company_inrange/"+$from+"/"+$to),
			success:function(data){
				$box_makeschedule_bottom.show();
				
				
				$base_str = "<thead><tr><th>S.No</th><th>Company Name</th><th>Date</th><th>Status</th><th>Compare</th></tr></thead>";
				if(data.length == 0)
					$base_str += "<tr><td colspan = '4'>No Company in this Slot.</td></tr>";
				for($d = 0;$d<data.length;$d++)
				{    
					$company_to_compare_with = data[$d]['company_id'];
					$base_str += "<tr><td>"+($d+1)+"</td><td>"+data[$d]['company_name']+"</td><td>"+data[$d]['date_from'] + " to " + data[$d][
					'date_to']+"</td><td>"+data[$d]['status']+"</td><td><a href = 'compare_companies/Compare/"+$company_to_compare+"/"+
					$company_to_compare_with+"' target = '_blank'>Compare</a></td></tr>";
				}
				$("#table_makeschedule_bottom").html($base_str);
				$box_makeschedule_top.hideLoading();
			},
			type:"post",
			dataType:"json",
			fail:function(error){
				console.log(error);
				$box_makeschedule_top.hideLoading();
			}
		});
	}
});

