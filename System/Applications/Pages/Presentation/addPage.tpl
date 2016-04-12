<script language="javascript">

{literal}

function showUploader(){
	document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('addPageDetails').style.display = 'none';
	document.getElementById('add_type').value = 'upload';
}

function hideUploader(){
	document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('addPageDetails').style.display = 'block';
	document.getElementById('add_type').value = 'direct';
}

var check = function(){
	var editForm = document.getElementById('insertPage');
	if(editForm.page_url.value==''){
		alert ('please enter the url');
		editForm.page_url.focus();
		return false;
	}else{
		return true;
	}
}

var addField = function(input_id, field_name){
	
	var myInput = $(input_id);
	
	if(myInput){
		
		myInput.focus();
		
		if(field_name == 'name' || field_name == 'id' || field_name == 'long_id'){
			myInput.value = myInput.value+':'+field_name;
		}else{
			myInput.value = myInput.value+'$'+field_name;
		}
	}else{
		alert('input not found');
	}
}

{/literal}

</script>

<div id="work-area">

{load_interface file=$_stage_template}

</div>