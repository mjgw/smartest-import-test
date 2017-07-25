<div id="work-area">
    
  <h3>{$_l10n_strings.title}</h3>
  
{if count($locations)}
  <div class="warning">
    <p>{$_l10n_strings.warnings.storage_locations_unwriteable}</p>
    <ul class="location-list">
{foreach from=$locations item="l"}
      <li><i class="fa fa-folder"></i> <code>{$l}</code></li>
{/foreach}        
    </ul>
    {help id="desktop:permissions"}Tell me more{/help}
  </div>
{/if}
  
  {load_interface file="file_browse_tabs.tpl"}
  
  <div class="instruction">{$_l10n_action_strings.explanation}</div>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="group_id" id="item_id_input" value="" />
  </form>

  <ul class="{if count($groups) > 10}options-list{else}options-grid{/if}" id="{if count($groups) > 10}options_list{else}options_grid{/if}">
    <li class="add">
      <a href="{$domain}{$section}/newAssetGroup{if $gallery_mode}?is_gallery=true{/if}" class="add"><i>+</i>{$_l10n_action_strings.create_button}</a>
    </li>
  {foreach from=$groups key="key" item="group"}
    <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/browseAssetGroup?group_id={$group.id}'">
  			<a class="option" id="item_{$group.id}" onclick="setSelectedItem('{$group.id}', 'fff');" >
  			  {if $group.is_gallery}<img src="{$domain}Resources/Icons/folder.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/photos.png" class="list" />{else}<img src="{$domain}Resources/Icons/folder.png" border="0" class="grid" /><img border="0" src="/Resources/Icons/folder.png" class="list" />{/if}
  			  {$group.label}</a></li>
  {/foreach}
  </ul>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>{$_l10n_action_strings.sidebar_options.selected_group_heading}</b></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('addAsset');}{/literal}"><i class="fa fa-upload"></i> Add a new file to this group</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editAssetGroup');}{/literal}"><i class="fa fa-info-circle"></i> {$_l10n_action_strings.sidebar_options.selected_group_info}</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editAssetGroupContents');}{/literal}"><i class="fa fa-folder-open-o"></i> {$_l10n_action_strings.sidebar_options.selected_group_edit_contents}</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('browseAssetGroup');}{/literal}" ><i class="fa fa-search"></i> {$_l10n_action_strings.sidebar_options.selected_group_contents}</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('deleteAssetGroupConfirm');}{/literal}" ><i class="fa fa-trash"></i> {$_l10n_action_strings.sidebar_options.selected_group_delete}</a></li>
  </ul>
  
  {load_interface file="assets_front_sidebar.tpl"}
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">{$_l10n_strings.general.recently_edited_label}</span></li>
    {foreach from=$recent_assets item="recent_asset"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><i class="fa fa-{$recent_asset.type_info.fa_iconname}"></i> {$recent_asset.label|summary:"30"}</a></li>
    {/foreach}
  </ul>
  
</div>