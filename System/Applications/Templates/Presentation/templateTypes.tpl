<div id="work-area">

<h3>Templates</h3>

{if count($locations)}
  <div class="warning">
      <p>For smooth operation of the templates repository, the following locations need to be made writable:</p>
      <ul class="location-list">
{foreach from=$locations item="l"}
        <li><i class="fa fa-folder"></i> <code>{$l}</code></li>
{/foreach}        
      </ul>
      {help id="desktop:permissions"}Tell me more{/help}
  </div>
{/if}

{load_interface file="template_browse_tabs.tpl"}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" id="item_id_input" name="type" value="" />
</form>

<div class="form-section-label-full">There are six different kinds of template.</div>

<ul class="options-grid-no-scroll" style="margin-top:0px">
  {foreach from=$types item="assetType"}
    <li ondblclick="window.location='{$domain}smartest/templates/{$assetType.id}'">
      <a href="javascript:nothing();" id="item_{$assetType.id}" class="option" onclick="setSelectedItem('{$assetType.id}', '{$assetType.label|escape:quotes}');">
        <img border="0" src="{$domain}Resources/Icons/folder.png" />{$assetType.label}s</a></li>{* $assetType.icon *}
  {/foreach}
</ul><br clear="all" />

</div>

<div id="actions-area">
  
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><strong>Select template type</strong></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('addTemplate')" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add a template of this type</a></li>
    <li class="permament-action"><a href="#" onclick="workWithItem('listByType')" class="right-nav-link"><i class="fa fa-search"></i> Browse these templates</a></li>
    
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><strong>Options</strong></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/addTemplate'" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add a new template</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}templates/import'" class="right-nav-link"><i class="fa fa-search-plus"></i> Detect new templates</a></li>
  </ul>
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Recently edited templates</b></li>
    {foreach from=$recently_edited item="recent_template"}
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><i class="fa fa-file-code-o"></i> {$recent_template.label|summary:"30"}</a></li>
    {/foreach}
  </ul>
  
</div>