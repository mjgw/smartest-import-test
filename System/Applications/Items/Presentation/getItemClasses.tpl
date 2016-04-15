<script type="text/javascript">
  var modelList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_grid');
</script>

<div id="work-area">

<h3>Items</h3>

{if empty($models)}
  <div class="special-box">No models yet. {if $allow_create_models}<a href="{$domain}{$section}/addItemClass?createmetapage=true" class="button">Click here</a> to create one. {/if}{help id="datamanager:models"}What are models?{/help}</div>
{else}
  
  {load_interface file="items_front_tabs.tpl"}
  
  {load_interface file="items_autocomplete_search.tpl"}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="class_id" id="item_id_input" value="" />
</form>

<ul class="options-grid" id="options_grid">
  {if $allow_create_models}<li class="add">
    <a href="{$domain}smartest/model/new" class="add"><i>+</i>Build a new model</a>
  </li>{elseif $allow_create_new_items}
  <li class="add">
      <a href="{$domain}datamanager/addItem" class="add"><i>+</i>Add a new...</a>
    </li>{/if}
{foreach from=$models key="key" item="itemClass"}
  <li ondblclick="window.location='{$domain}smartest/items/{$itemClass.varname}'">
    <a id="model_{$itemClass.id}" class="option" href="{dud_link}" onclick="modelList.setSelectedItem('{$itemClass.id}', 'model', {literal}{{/literal}updateFields: {literal}{{/literal}'model_name_field': '{$itemClass.name|summary:"29"|escape:quotes}', 'model_plural_name_field': '{$itemClass.plural_name|summary:"29"|escape:quotes}'{literal}}{/literal}{literal}}{/literal});" data-hidden="{if $itemClass.is_hidden}true{else}false{/if}">
      {if $itemClass.is_hidden}
      <img border="0" src="{$domain}Resources/System/Images/model_private.png" alt="" />
      {else}
      <img border="0" src="{$domain}Resources/System/Images/model.png" alt="" />
      {/if}
      {$itemClass.plural_name}</a></li>
{/foreach}
</ul>

{/if}

</div>


<div id="actions-area">

  <ul class="actions-list" id="model-specific-actions" style="display:none">
    <li><b>Model: <span class="model_plural_name_field"></b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('addItem');"><i class="fa fa-plus-circle"></i> Create a new <span class="model_name_field">item</span></a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('getItemClassMembers');"><i class="fa fa-search"></i> Browse <span class="model_plural_name_field">items</span></a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="return MODALS.load('datamanager/modelInfo?class_id='+modelList.lastItemId, 'Model info')"><i class="fa fa-info-circle"></i> Model info</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('getItemClassProperties');"><i class="fa fa-sliders"></i> Edit Model Properties</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('editModel');"><i class="fa fa-pencil"></i> Edit Model</a></li>
    {* <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('getItemClassComments');"><i class="fa fa-comments"></i> Browse comments</a></li> *}
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('getItemClassSets');"><i class="fa fa-folder-open"></i> View data sets for this model</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('addSet');"><i class="fa fa-plus-square-o"></i> Create a new set from this model</a></li>
    {* <li class="permanent-action"><a href="{dud_link}" onclick="modelList.workWithItem('publishManager');"><img border="0" src="{$domain}Resources/Icons/lightning.png"> <span class="model_name_field">item</span> publish manager</a></li> *}
    {if $allow_delete_models}<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(confirm('Are you sure you want to permanently delete this model and all its items?')){modelList.workWithItem('deleteItemClass');}{/literal}"><i class="fa fa-times-circle"></i> Delete This Model</a></li>{/if}
    {* <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('importData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Import Data</a></li> *}
    {* Remember this option is now being moved to datasets <li class="permanent-action"><a href="{dud_link}" onclick="workWithItem('exportData');"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export XML</a></li> *}
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Model Options</b></li>
    {if $allow_create_models}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/model/new'"><i class="fa fa-plus-square"></i> Build a New Model</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><i class="fa fa-folder"></i> View all item sets</a></li>
    {* <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/schemas'"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li> *}
  </ul>

  <ul class="actions-list" id="non-specific-actions">
    <li><span style="color:#999">Recently edited items</span></li>
    {foreach from=$recent_items item="recent_item"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><i class="fa fa-cube"></i> {$recent_item.label|summary:"28"}</a></li>
    {/foreach}
  </ul>

</div>