<script type="text/javascript">
  var assets = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
</script>

<div id="work-area">

  <h3>{$type_label} files</h3>

  {load_interface file="assettype_tabs.tpl"}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="assettype_code" value="{$type_code}" />
  <input type="hidden" name="asset_id" id="item_id_input" value="" />
</form>

{if $num_assets < 1 && $mode != 2}
<div class="special-box">There are no {$type_label|strtolower} files yet. <a href="{$domain}{$section}/addAsset?asset_type={$type_code}" class="button">Click here</a> to add one.</div>
{else}
<div class="special-box">
{if $type.editable && $type.storage.type == "database"}
  <form id="full-text-search-form" method="get" action="" onsubmit="return false">
    <div class="special-box-key">Text search: </div><input type="text" name="query" id="assets-search-query-box" style="width:250px" />
    
    <div id="autocomplete_choices" class="autocomplete"></div>

      <script type="text/javascript">
{literal}
        function getSelectionId(text, li) {
            var bits = li.id.split('-');
            window.location=sm_domain+'assets/editAsset?asset_id='+bits[1];
        }
{/literal}

        new Ajax.Autocompleter("assets-search-query-box", "autocomplete_choices", "/ajax:smartest/assets/{$type_code}/full_text_search", {literal}{
            paramName: "query", 
            minChars: 3,
            delay: 50,
            width: 300,
            afterUpdateElement : getSelectionId
        });
        
        $('full-text-search-form').observe('submit', function(){
          return false;
        });

        {/literal}
      </script>
  </form>
{/if}

  <form id="mode-form" method="get" action="">
    <input type="hidden" name="asset_type" value="{$type_code}" />
    <div class="special-box-key">{if $type.editable && $type.storage.type == "database"}Or show{else}Only show{/if}: </div><select name="mode" onchange="$('mode-form').submit();">
      <option value="1"{if $mode == 1} selected="selected"{/if}>{$type_label} files not in archive</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$type_label} files</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Archived {$type_label} files</option>
    </select>
  </form>
</div>

<div id="options-view-header">

  <div id="options-view-info">
    Found {$num_assets} file{if $num_assets != 1}s{/if}.
  </div>
  
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return assets.setView('list', 'asset_list_style')" id="options-view-list-button" class="{if $list_view == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return assets.setView('grid', 'asset_list_style')" id="options-view-grid-button" class="{if $list_view == "grid"}on{else}off{/if}"></a>
  </div>
  
  <div class="breaker"></div>
  
</div>

<ul class="options-{$list_view}{if $contact_sheet_view} images{/if}" style="margin-top:0px" id="options_grid">
  <li class="add">
    <a href="{$domain}{$section}/addAsset?asset_type={$type_code}" class="add"><i>+</i>Add a new file of this type</a>
  </li>
{foreach from=$assets item="asset"}
<li>
    <a href="#select-file" class="option" id="{$sidebartype}_{$asset.id}" onclick="return assets.setSelectedItem('{$asset.id}', '{$sidebartype}');" ondblclick="assets.workWithItem('editAsset');">

{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$asset.image._ui_preview.web_path}" class="grid" />
{else}
  {if $asset.type_info.large_icon}
    <img border="0" src="{$domain}Resources/System/Images/{$asset.type_info.large_icon}" class="grid" />
  {else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" class="grid" />
  {/if}
{/if}

{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}
    <img border="0" src="{$asset.image.16x16.web_path}" class="list" />
{else}
  {if $asset.small_icon}
    <img border="0" src="{$asset.small_icon}" class="list" />
  {else}
    <img border="0" src="{$domain}Resources/Icons/blank_page.png" class="list" />
  {/if}
{/if}

<span class="asset label">{$asset.label}</span></a>

</li>

{/foreach}
</ul>
{/if}

</div>

<div id="actions-area">

<ul class="actions-list" id="noneditableasset-specific-actions" style="display:none">
  <li><b>Selected file</b></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><i class="fa fa-info-circle"></i> About this file...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><i class="fa fa-check"></i> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><i class="fa fa-eye"></i> Preview this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><i class="fa fa-folder-o"></i> Archive/unarchive this file...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('deleteAssetConfirm');" class="right-nav-link"><i class="fa fa-trash-o"></i> Delete this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('duplicateAsset');" class="right-nav-link"><i class="fa fa-clone"></i> Duplicate this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><i class="fa fa-download"></i> Download this file</a></li>
</ul>

<ul class="actions-list" id="editableasset-specific-actions" style="display:none">
  <li><b>Selected file</b></li>
  <li class="permanent-action"><a href="#" onclick="assets.workWithItem('assetInfo');" class="right-nav-link"><i class="fa fa-info-circle"></i> About this file...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editAsset');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('previewAsset');"><i class="fa fa-eye"></i> Preview this file</a></li>
	{if $allow_source_edit}<li class="permanent-action"><a href="#" onclick="assets.workWithItem('editTextFragmentSource');" class="right-nav-link"><i class="fa fa-pencil-square-o"></i> Edit file source</a></li>{/if}
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('addTodoItem');" class="right-nav-link"><i class="fa fa-check"></i> Add a new to-do</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('toggleAssetArchived');" class="right-nav-link"><i class="fa fa-folder-o"></i> Archive/unarchive this file...</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('deleteAssetConfirm');" class="right-nav-link"><i class="fa fa-trash-o"></i> Delete this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('duplicateAsset');" class="right-nav-link"><i class="fa fa-clone"></i> Duplicate this file</a></li>
	<li class="permanent-action"><a href="#" onclick="assets.workWithItem('downloadAsset');" class="right-nav-link"><i class="fa fa-download"></i> Download this file</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>{$_l10n_strings.general.general_options_label}</b></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addAsset?asset_type={$type_code}'" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add a new file of this type</a></li>
	{if in_array($type_code, array('SM_ASSETTYPE_JPEG_IMAGE', 'SM_ASSETTYPE_GIF_IMAGE', 'SM_ASSETTYPE_PNG_IMAGE'))}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup?is_gallery=true&amp;asset_type={$type_code}&amp;group_label=Unnamed+{$type_label.urlencoded}+gallery'" class="right-nav-link"><i class="fa fa-picture"></i> Add a new {$type_label} gallery</a></li>{/if}
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/newAssetGroup?filter_type={$type_code}'" class="right-nav-link"><i class="fa fa-folder"></i> Add a new group from these files</a></li>
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/assets'" class="right-nav-link"><i class="fa fa-files-o"></i> View all files by type</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recent {$type_label|strtolower} files</span></li>
  {foreach from=$recent_assets item="recent_asset"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><i class="fa fa-{$recent_asset.type_info.fa_iconname}"></i> {$recent_asset.label|summary:"30"}</a></li>
  {/foreach}
</ul>

</div>