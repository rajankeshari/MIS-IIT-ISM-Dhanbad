	
// this funtion is called when the user click the 'don't remember the employee id '
	function getEmpId()
	{
		$('#emp_id').val(($('#employee_select').val()));
		
	}
   
	function onclick_emp_id()
	{
		document.getElementById('search_eid').style.display="inherit";
		//changes
		var dept=document.getElementById('emp_dept');
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		 	xmlhttp=new XMLHttpRequest();
		}
		else
	  	{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
	  	{
	  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    dept.innerHTML += xmlhttp.responseText;
		    }
	  	}
		xmlhttp.open("POST",site_url("delegation_of_power/delegate_ajax/get_dept"),true);
		xmlhttp.send();
	}

	// function onclick_emp_nameid()
	// {
	// 	var emp_name_id=document.getElementById('employee_select').value;
	// 	document.getElementById('emp_id').value=emp_name_id;
	// }

	function onclick_empname()
	{
		document.getElementById('employee').style.display="inherit";
		var emp_name=document.getElementById('employee_select');
		var dept=document.getElementById('emp_dept').value;
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		 	xmlhttp=new XMLHttpRequest();
		}
		else
	  	{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
	  	{
	  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    emp_name.innerHTML += xmlhttp.responseText;
		    }
	  	}
		xmlhttp.open("POST",site_url("ajax/empNameByDept/"+dept),true);
		xmlhttp.send();
		emp_name.innerHTML = "<i class=\"loading\"></i>";
	}
// this function is used to update the delegated_power table  on click
// of deny button in delegated_power_view in operation section of table generated
	 function update_auth(id)
	{  
	
		var result=confirm("Do you really want to deny the authorization ?");

		if(result==true)
		{   //var result=confirm("result");
			var view_users=document.getElementById('view_users');
			var xmlhttp;
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			 	xmlhttp=new XMLHttpRequest();
			}
			else
		  	{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function()
		  	{
		  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				    view_users.innerHTML = xmlhttp.responseText;
			    }
			    console.log(xmlhttp.status);
		  	}
		  	// calling update  function defined in delegate_ajax with sending the S_no of the table to identify the record 
			xmlhttp.open("POST",site_url("delegation_of_power/delegate_ajax/updateAuth/"+id),true);
			xmlhttp.send();
			location.reload();
			console.log(xmlhttp.status);
			view_users.innerHTML = "<center><i class=\"loading\"></i></center>";

		}
	}
	// this function is used to cancel the delegated_power table  on click
// of cancel button in delegated_power_view in operation section of table generated
	function cancel_auth(id)
	{  
	
		var result=confirm("Do you really want to Cancel the authorization ?");

		if(result==true)
		{   //var result=confirm("result");
			var view_users=document.getElementById('view_users');
			var xmlhttp;
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			 	xmlhttp=new XMLHttpRequest();
			}
			else
		  	{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function()
		  	{
		  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
				    view_users.innerHTML = xmlhttp.responseText;
			    }
			    console.log(xmlhttp.status);
		  	}
			xmlhttp.open("POST",site_url("delegation_of_power/delegate_ajax/cancelAuth/"+id),true);
			xmlhttp.send();
			location.reload();
			console.log(xmlhttp.status);
			view_users.innerHTML = "<center><i class=\"loading\"></i></center>";

		}
	}


	function onload_emp_id()
	{
		var emp=document.getElementById('emp_id');
		var xmlhttp;
		if (window.XMLHttpRequest)
		{// code for IE7+, Firefox, Chrome, Opera, Safari
		 	xmlhttp=new XMLHttpRequest();
		}
		else
	  	{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function()
	  	{
	  		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		    {
			    emp.innerHTML += xmlhttp.responseText;
		    }
	  	}
		xmlhttp.open("POST",site_url("delegation_of_power/delegate_ajax/get_emp_id"),true);
		xmlhttp.send();
	}

