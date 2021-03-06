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

<div class="special-box">
      <form action="" method="get" id="assets-search-form" onsubmit="return false">
        {$_l10n_strings.files.search_instruction}: <input type="text" class="search" name="query" id="assets-search-name" />
        <!--<select name="query_type">
          <option value="ALL">Names, Full-text and Tags</option>
          <option value="NT">Names and Tags</option>
          <option value="NFT">Names and Full-text</option>
          <option value="NO">Names only</option>
        </select>-->
      </form>
      <script type="text/javascript">{literal}$('assets-search-name').observe('submit', function(){return false;});{/literal}</script>
  </div>

<div id="autocomplete_choices" class="autocomplete"></div>
  
  <script type="text/javascript">
  
    var url = sm_domain+'ajax:assets/assetSearch';
  
    {literal}
    
    function getSelectionId(text, li) {
        var bits = li.id.split('-');
        window.location=sm_domain+'smartest/file/edit/'+bits[1];
    }
    
    new Ajax.Autocompleter("assets-search-name", "autocomplete_choices", url, {
        paramName: "query", 
        minChars: 2,
        delay: 50,
        width: '300px',
        afterUpdateElement : getSelectionId
    });
    
    {/literal}
  </script>

<div class="text" style="margin-bottom:10px">{$_l10n_strings.files.types_instruction}</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="asset_type" value="" />
</form>
  
{foreach from=$assetTypeCats item="assetTypeCategory"}

<div class="form-section-label-full">{$assetTypeCategory.l10n_label}</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">

{foreach from=$assetTypeCategory.types item="assetType"}
  <li ondblclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$assetType.id}'">
    <a href="javascript:;" id="item_{$assetType.id}" class="option" onclick="setSelectedItem('{$assetType.id}', '{$assetType.label|escape:quotes}');">
      <img border="0" src="{$domain}Resources/Icons/folder.png" />{$assetType.label}</a></li>{* $assetType.icon *}
{/foreach}
<li class="breaker"></li>
</ul>
<div class="breaker"></div>

{/foreach}



</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>{$_l10n_strings.general.selected_file_type_label}</b></li>
  	<li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){ workWithItem('getAssetTypeMembers'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> {$_l10n_action_strings.sidebar_options.selected_type_show_all}</a></li>
  	<li class="permanent-action"><a href="#" onclick="workWithItem('addAsset');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> {$_l10n_action_strings.sidebar_options.selected_type_add_new}</a></li>
  	<li class="permanent-action"><a href="#" onclick="workWithItem('newAssetGroup');" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_add.png" border="0" alt="" /> {$_l10n_action_strings.sidebar_options.selected_type_make_group}</a></li>
  </ul>
  
  {load_interface file="assets_front_sidebar.tpl"}
  
  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">{$_l10n_strings.general.recently_edited_label}</span></li>
    {foreach from=$recent_assets item="recent_asset"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_asset.action_url}'"><i class="fa fa-{$recent_asset.type_info.fa_iconname}"></i> {$recent_asset.label|summary:"30"}</a></li>
    {/foreach}
  </ul>

</div>