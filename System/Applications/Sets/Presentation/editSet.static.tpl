<script language="javascript">
{literal}

function setMode(mode){

	document.getElementById('transferAction').value=mode;

	if(mode == "add"){
		document.getElementById('add_button').disabled=false;
		document.getElementById('remove_button').disabled=true;
		
	}else if(mode == "remove"){
		document.getElementById('add_button').disabled=true;
		document.getElementById('remove_button').disabled=false;
		formList = document.getElementById('used_items');
	}	
	
}

function executeTransfer(){
	document.transferForm.submit();
}

{/literal}

var set_id = {$set.id};

</script>

<div id="work-area">

{load_interface file="edit_set_tabs.tpl"}

<h3>Edit set <span id="set-label-h3" class="light">"{$set.label}"</span></h3>

<div class="edit-form-row">
  <div class="form-section-label">Set label</div>
  <p class="editable" id="set-label">{$set.label}</p>
  <script type="text/javascript">
  new Ajax.InPlaceEditor('set-label', sm_domain+'ajax:sets/updateSetLabelFromInPlaceEditField', {ldelim}
    callback: function(form, value) {ldelim}
      return 'set_id={$set.id}&new_label='+encodeURIComponent(value);
    {rdelim},
    onComplete: function(t, e){ldelim}
      $('set-label-h3').update('&quot;'+$('set-label').innerHTML+'&quot;');
    {rdelim},
    highlightColor: '#ffffff',
    hoverClassName: 'editable-hover',
    savingClassName: 'editable-saving'
  {rdelim});
  </script>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Set name</div>
  {if $can_edit_set_name}
  <p class="editable" id="set-name">{$set.name}</p>
  <div class="form-hint">This value is used in templates and queries.</div>
  <script type="text/javascript">
  new Ajax.InPlaceEditor('set-name', sm_domain+'ajax:sets/updateSetNameFromInPlaceEditField', {ldelim}
    callback: function(form, value) {ldelim}
      return 'set_id={$set.id}&new_name='+encodeURIComponent(value);
    {rdelim},
    highlightColor: '#ffffff',
    hoverClassName: 'editable-hover',
    savingClassName: 'editable-saving'
  {rdelim});
  </script>
  {else}
  {$set.name}
  {/if}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Order</div>
  <select name="set_sort" id="set-sort-direction-dropdown">
    <option value="ASC"{if $set.sort_direction == "ASC"} selected="selected"{/if}>Start with first item added</option>
    <option value="DESC"{if $set.sort_direction == "DESC"} selected="selected"{/if}>Start with most recently added item</option>
  </select>
</div>

<script type="text/javascript">
{literal}

  $('set-sort-direction-dropdown').observe('change', function(){
    var url = sm_domain+'ajax:sets/updateSetSortDirection';
    var direction = $('set-sort-direction-dropdown').value;
    new Ajax.Request(url, {
      method: 'post',
      parameters: {'set_id': set_id, 'sort_direction': direction}
    });
  });

{/literal}
</script>

{if $show_shared}
<div class="edit-form-row">
  <div class="form-section-label">Shared</div>
  <input type="checkbox" name="set_shared" id="set-shared" value="1"{if $set.shared == "1"} checked="checked"{/if} />
  <span class="form-hint" id="shared-hint">{if $set.shared == "1"}Un-check{else}Check{/if} this box to make this set (but not its contents){if $set.shared == "1"} no longer{/if} shared with other sites.</span>
</div>
<script type="text/javascript">

  var shared_text = 'Un-check this box to make this set (but not its contents) no longer shared with other sites.';
  var unshared_text = 'Check this box to make this set (but not its contents) shared with other sites.';

  {literal}
  $('set-shared').observe('click', function(){
    var url = sm_domain+'ajax:sets/updateSetShared';
    var checked = $('set-shared').checked ? 1 : 0;
    new Ajax.Request(url, {
      method: 'post',
      parameters: {'set_id': set_id, 'is_shared': checked}
    });
    if(checked){
        $('shared-hint').update(shared_text);
    }else{
        $('shared-hint').update(unshared_text);
    }
  });
  {/literal}
</script>
{/if}

<div class="breaker"></div>

<div class="instruction">Use the arrow buttons below to move {$model.plural_name|lower} in and out of this set.</div>

<form action="{$domain}sets/transferItem" method="post" name="transferForm">
  
  <input type="hidden" id="transferAction" name="transferAction" value="" /> 
  <input type="hidden" name="set_id" value="{$set.id}" />
  {if $request_parameters.item_id}<input type="hidden" name="item_id" value="{$request_parameters.item_id}" />{/if}
  {if $request_parameters.from}<input type="hidden" name="from" value="{$request_parameters.from}" />{/if}
  
  <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
    <tr>
      <td align="center">
        
        <div style="text-align:left">{$model.plural_name} that <strong>aren't</strong> in this set</div>

		<select name="available_items[]"  id="available_items" size="2" multiple style="width:270px; height:300px;">
        {foreach from=$non_members key="key" item="item"}
		<option value="{$item.id}" >{if $item.public == "FALSE"}* {/if}{$item.name}</option>
		{/foreach}
		</select>
		
	 </td>
     
     <td valign="middle" style="width:40px">
		<input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
    <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
     </td>
     <td align="center">
        <div style="text-align:left">{$model.plural_name} that <strong>are</strong> in this set</div>
 	<select name="used_items[]"  id='used_items' size="2" multiple style="width:270px; height:300px" >	
	  {foreach from=$members key="key" item="item"}
		<option value="{$item.id}" >{if $item.public == "FALSE"}* {/if}{$item.name}</option>
		{/foreach}
        </select>
	</td>
   </tr>
</table>

<script type="text/javascript">
  {literal}$('available_items').observe('change', function(){setMode('add')});{/literal}
  {literal}$('used_items').observe('change', function(){setMode('remove')});{/literal}
</script>

</form>

</div>

<div id="actions-area">
		<ul class="actions-list">
		  <li><b>Options</b></li>
      {if $request_parameters.page_id && $request_parameters.from == 'preview'}
        <li class="permanent-action"><a href="#" onclick="window.location='{$domain}websitemanager/preview?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}'"><i class="fa fa-check"></i> Return to page preview</a></li>
      {elseif $request_parameters.page_id && $request_parameters.from == 'fullPreview'}
        <li class="permanent-action"><a href="#" onclick="window.location='{$domain}website/renderEditableDraftPage?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}&amp;hide_newwin_link=true'"><i class="fa fa-check"></i> Return to page preview</a></li>
		  {elseif $request_parameters.item_id && $request_parameters.from != 'preview' && $request_parameters.from != 'fullPreview'}
        <li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/editItem?item_id={$request_parameters.item_id}'"><i class="fa fa-check"></i> Return to editing item</a></li>
      {/if}
      <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}'"><i class="fa fa-search"></i> Browse set contents</a></li>
      <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/deleteSetConfirm?set_id={$set.id}'"><i class="fa fa-trash"></i> Delete this set</a></li>
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/editStaticSetOrder?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}'"><i class="fa fa-random"></i> Change the order of this set</a></li>
			<li class="permanent-action">{if $model.id}<a href="#" onclick="window.location='{$domain}{$section}/getItemClassSets?class_id={$model.id}'"><i class="fa fa-folder-o"></i> Browse sets of {$model.plural_name|strtolower}</a>{else}<a href="#" onclick="window.location='{$domain}smartest/sets'"><i class="fa fa-folder-o"></i> Back to data sets</a></li>{/if}		
			<li class="permanent-action"><a href="#" onclick="window.location='{$domain}smartest/models'"><i class="fa fa-cubes"></i> Browse all items</a></li>
		</ul>
		
</div>