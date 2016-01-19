<div id="work-area">
  
  <script type="text/javascript">
  {literal}

  var setMode = function(mode){

  	document.getElementById('transferAction').value = mode;

  	if(mode == "add"){
  		$('add_button').disabled=false;
  		$('remove_button').disabled=true;
		  formList = $('non-members');
  	}else if(mode == "remove"){
  		$('add_button').disabled=true;
  		$('remove_button').disabled=false;
  		formList = $('members');
  	}	
	
  }

  var executeTransfer = function(){
  	$('transferForm').submit();
  }

  {/literal}
  </script>
  
  <h3>Edit user group</h3>
  
  <div class="edit-form-row">
    <div class="form-section-label">Label</div>
    <p class="editable" id="usergroup-label">{$group.label}</p>
    <script type="text/javascript">
    new Ajax.InPlaceEditor('usergroup-label', sm_domain+'ajax:users/setUserGroupLabelFromInPlaceEditField', {ldelim}
      callback: function(form, value) {ldelim}
        return 'usergroup_id={$group.id}&new_label='+encodeURIComponent(value);
      {rdelim},
      highlightColor: '#ffffff',
      hoverClassName: 'editable-hover',
      savingClassName: 'editable-saving'
    {rdelim});
    </script>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Name</div>
    
    {* if $allow_name_edit *}
    
    <p class="editable" id="usergroup-name">{$group.name}</p>
    <div class="form-hint">Used to access the user group from templates. Numbers, lowercase letters and underscores only</div>
    <script type="text/javascript">
    new Ajax.InPlaceEditor('usergroup-name', sm_domain+'ajax:users/setUserGroupNameFromInPlaceEditField', {ldelim}
      callback: function(form, value) {ldelim}
        return 'usergroup_id={$group.id}&new_name='+encodeURIComponent(value);
      {rdelim},
      highlightColor: '#ffffff',
      hoverClassName: 'editable-hover',
      savingClassName: 'editable-saving'
    {rdelim});
    </script>
    
    {* else}
      <code>{$group.name}</code>
    {/if *}
    
  </div>
  
  <form action="{$domain}{$section}/transferUsers" method="post" id="transferForm">
  
    <input type="hidden" id="transferAction" name="transferAction" value="" />
    <input type="hidden" name="group_id" value="{$group.id}" />
  
    <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
      
      <tr>
        
        <td align="center">
          <div style="text-align:left">Users that <strong>aren't</strong> in this group</div>

  		    <select name="non_members[]" id="non-members" size="2" multiple style="width:270px; height:300px;" onclick="setMode('add')">
{foreach from=$non_members key="key" item="user"}
  		      <option value="{$user.id}" >{$user.full_name}</option>
{/foreach}
  		    </select>

  		  </td>
    
        <td valign="middle" style="width:40px">
    		  <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
          <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
        </td>
    
        <td align="center">
          <div style="text-align:left">Users that <strong>are</strong> in this group</div>
   	      <select name="members[]" id='members' size="2" multiple style="width:270px; height:300px" onclick="setMode('remove')" >	
{foreach from=$members key="key" item="user"}
  	      	<option value="{$user.id}" >{$user.full_name}</option>
{/foreach}
          </select>
  	    </td>
      </tr>
    </table>
  
  </form>
  
  <div class="buttons-bar">
    <input type="button" value="Done" onclick="cancelForm();" />
  </div>
  
</div>

<div id="actions-area">
  
</div>