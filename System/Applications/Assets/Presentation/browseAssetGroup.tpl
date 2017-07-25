<script type="text/javascript">
  var assets = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<div id="work-area">

{load_interface file="edit_filegroup_tabs.tpl"}

<h3>{if $group.is_gallery}{$_l10n_strings.groups.gallery_files}{else}{$_l10n_strings.groups.group_files}{/if}<span class="light">"{$group.label}"</span></h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
  <input type="hidden" name="group_id" value="{$group.id}" />
</form>

<div class="special-box">
  <form id="mode-form" method="get" action="">
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
    Show: <select name="mode" onchange="$('mode-form').submit();">
      <option value="1"{if $mode == 1} selected="selected"{/if}>Files not in archive</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All files in this group</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Archived files</option>
    </select>
  </form>
</div>

{if $group.is_gallery}<div class="instruction">Drag and drop files in this gallery to change their order. The new order is saved automatically.</div>{/if}

<div id="options-view-header">

  <div id="options-view-info">
    {if $num_assets == 0}No files in this group yet.{else}{$num_assets} file{if $num_assets != 1}s{/if} in this group.{/if}
  </div>
  
  {if $num_assets > 0}
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return assets.setView('list', 'asset_list_style')" id="options-view-list-button" class="{if $list_view == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return assets.setView('grid', 'asset_list_style')" id="options-view-grid-button" class="{if $list_view == "grid"}on{else}off{/if}"></a>
  </div>
  {/if}
  
  <div class="breaker"></div>
  
</div>

<ul class="options-{$list_view}{if $contact_sheet_view} images{/if}{if $group.is_gallery} reorderable{/if}" style="margin-top:0px" id="options_grid">
{foreach from=$assets item="asset"}

<li id="file_{$asset.id}">
    <a href="#select-file" class="option" id="editableasset_{$asset.id}" onclick="return assets.setSelectedItem('{$asset.id}', 'editableasset');" ondblclick="assets.workWithItem('editAsset')" >

{if in_array($asset.type, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$asset.image._ui_preview.web_path}" class="grid" />
{else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" class="grid" />
{/if}
<img border="0" src="{$asset.small_icon}" class="list" />
<span class="asset label">{$asset.label}</span></a>
</li>
{/foreach}
</ul>

{if $error}{$error}{/if}

{if $group.is_gallery}

<script type="text/javascript" src="/Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
<script type="text/javascript">

var url = sm_domain+'ajax:assets/updateGalleryOrder';
var groupId = {$group.id};
{literal}
var IDs;
var IDs_string;

var itemsList = Sortable.create('options_grid', {
      
      onUpdate: function(){
          
        IDs = Sortable.sequence('options_grid');
        IDs_string = IDs.join(',');
        
        new Ajax.Request(url, {
          method: 'get',
          parameters: {group_id: groupId, new_order: IDs_string},
          onSuccess: function(transport) {
            
          }
        });
      },
      
      constraint: false,
      scroll: window,
      scrollSensitivity: 35
      
  });
{/literal}
</script>
{/if}
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

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><i class="fa fa-info-circle"></i> About this file...</a></li>
	{* <li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li> *}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><i class="fa fa-eye"></i> Preview this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><i class="fa fa-folder-open"></i> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('removeAssetFromGroup');" class="right-nav-link"><i class="fa fa-times"></i> Remove this file from {if $group.is_gallery}gallery{else}group{/if}</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><i class="fa fa-eye"></i> Download this file</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected File</b></li>
  <li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><i class="fa fa-info-circle"></i> About this file...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAsset');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this file</a></li>
	{if $group.is_gallery}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAssetGalleryMembership');" class="right-nav-link"><i class="fa fa-pencil-square-o"></i> Edit gallery membership</a></li>{/if}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><i class="fa fa-eye"></i> Preview this file</a></li>
	{if $allow_source_edit}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editTextFragmentSource');" class="right-nav-link"><i class="fa fa-file-code-o"></i> Edit file source</a></li>{/if}
	{* <li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt="" /> Add a new to-do</a></li> *}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><i class="fa fa-folder-open"></i> Archive/unarchive this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('removeAssetFromGroup');" class="right-nav-link"><i class="fa fa-times"></i> Remove this file from {if $group.is_gallery}gallery{else}group{/if}</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><i class="fa fa-download"></i> Download this file</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>File group options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/file/new?group_id={$group.id}'" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add a file to this group</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAssetGroupContents?group_id={$group.id}'" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this group</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup'" class="right-nav-link"><i class="fa fa-plus-square"></i> Create a new file group</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Other options</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/files/groups'" class="right-nav-link"><i class="fa fa-folder-o"></i> View all file groups</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/files/types'" class="right-nav-link"><i class="fa fa-folder"></i> View all files by type</a></li>
</ul>

</div>