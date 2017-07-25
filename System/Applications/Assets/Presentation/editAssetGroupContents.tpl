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
</script>

<div id="work-area">
  
  {load_interface file="edit_filegroup_tabs.tpl"}
  
  <h3>{if $group.is_gallery}{$_l10n_strings.groups.gallery_files}{else}{$_l10n_strings.groups.group_files}{/if}<span class="light">"{$group.label}"</span></h3>
  
  <form action="{$domain}{$section}/transferAssets" method="post" name="transferForm">

    <input type="hidden" id="transferAction" name="transferAction" value="" /> 
    <input type="hidden" name="group_id" value="{$group.id}" />
    {if $from}
    <input type="hidden" name="from" value="{$from}" />
    {if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}
    <input type="hidden" name="item_id" value="{$workflow_item.id}" />
    {if $workflow_page}<input type="hidden" name="page_id" value="{$workflow_page.id}" />{/if}
    {elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}
    <input type="hidden" name="page_id" value="{$workflow_page.id}" />
    {if $workflow_item}<input type="hidden" name="item_id" value="{$workflow_item.id}" />{/if}
    {elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}
    <input type="hidden" name="page_id" value="{$workflow_page.id}" />
    <input type="hidden" name="placeholder_id" value="{$workflow_placeholder.id}" />
    {if $workflow_item}<input type="hidden" name="item_id" value="{$workflow_item.id}" />{/if}
    {/if}
    
    {/if}

    <table width="100%" border="0" cellpadding="0" cellspacing="5" style="border:1px solid #ccc">
      <tr>
        <td align="center">
          <div style="text-align:left">Files that <strong>aren't</strong> in this group</div>

  		    <select name="available_assets[]"  id="available_assets" size="2" multiple="multiple" style="width:270px; height:400px;"  onclick="setMode('add')"  >

{foreach from=$non_members key="key" item="asset"}
  		      <option value="{$asset.id}" >{$asset.label}</option>
{/foreach}

  		    </select>

  		  </td>
        
        <td valign="middle" style="width:40px">
  		    <input type="button" value="&gt;&gt;" id="add_button" disabled="disabled" onclick="executeTransfer();" /><br /><br />
          <input type="button" value="&lt;&lt;" id="remove_button" disabled="disabled" onclick="executeTransfer();" />
        </td>
        
        <td align="center">
          <div style="text-align:left">Files that <strong>are</strong> in this group</div>
{if $group.is_gallery}
   	      <select name="used_assets[]"  id='used_assets' size="2" multiple="multiple" style="width:270px; height:400px" onclick="setMode('remove')" >	
{foreach from=$group.members key="key" item="lookup"}
  		      <option value="{$lookup.asset.id}" >{$lookup.position}. {$lookup.asset.label}</option>
{/foreach}
          </select>
{else}
   	      <select name="used_assets[]"  id='used_assets' size="2" multiple="multiple" style="width:270px; height:400px" onclick="setMode('remove')" >	
{foreach from=$members key="key" item="asset"}
  		      <option value="{$asset.id}" >{$asset.label}</option>
{/foreach}
          </select>
{/if}
          
  	    </td>
      </tr>
    </table>
  </form>
  
</div>

<div id="actions-area">
  
  {if $request_parameters.from}
  <ul class="actions-list">
    <li><b>Workflow options</b></li>
    {if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}
    <li class="permanent-action"><a href="{$domain}datamanager/editItem?item_id={$workflow_item.id}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}"><i class="fa fa-check"></i> Return to editing {$workflow_item._model.name|lower}</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}
    <li class="permanent-action"><a href="#" onclick="cancelForm();"><i class="fa fa-check"></i> Return to page preview</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}
    <li class="permanent-action"><a href="{$domain}websitemanager/definePlaceholder?assetclass_id={$workflow_placeholder.name}&amp;page_id={$workflow_page.webid}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}"><i class="fa fa-check"></i> Return to placeholder</a></li>
    {/if}
  </ul>
  {/if}
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Group options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/file/new?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="right-nav-link"><i class="fa fa-plus-circle"></i> Upload a file into this group</a></li>
  	<li class="permanent-action"><a href="{$domain}assets/browseAssetGroup?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="right-nav-link"><i class="fa fa-search"></i> Browse this group</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Repository options</b></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/assetGroups'" class="right-nav-link"><i class="fa fa-folder-o"></i> View all file groups</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><i class="fa fa-files-o"></i> View all files by type</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/newAssetGroup'" class="right-nav-link"><i class="fa fa-plus-square"></i> Create a new file group</a></li>
  </ul>
  
</div>