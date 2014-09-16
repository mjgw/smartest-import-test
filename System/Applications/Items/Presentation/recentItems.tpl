<script type="text/javascript">
  var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_grid');
</script>

<div id="work-area">

<h3>Recent items</h3>

{if empty($recent_items)}

  <div class="special-box">
      You haven't edited any items yet. {if $allow_create_new}<a href="{$domain}{$section}/addItem?class_id={$model.id}">Click here</a> to create one.{else}Your user account does not have permission to create them.{/if}
  </div>
  
  {else}
  
  {load_interface file="items_front_tabs.tpl"}
  
  {load_interface file="items_autocomplete_search.tpl"}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="class_id" id="item_id_input" value="" />
</form>

  <ul class="options-{$list_view}" id="options_list">
{foreach from=$recent_items key="key" item="item"}
	<li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}'" class="item {if $item.public=='FALSE'}unpublished{else}published{/if} {if $item.is_archived=='1'}archived{else}current{/if}">
      <a href="#" class="option" id="item_{$item.id}" onclick="return itemList.setSelectedItem('{$item.id}', 'item', {literal}{{/literal}updateFields: {literal}{{/literal}item_name_field: '{$item.name|summary:"29"|escape:quotes|trim}', archive_action_name: '{if $item.is_archived}Unarchive{else}Archive{/if}'{literal}}{/literal}{literal}}{/literal});">
        {if $item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><i class="fa fa-cube list"></i>{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><i class="fa fa-cube list"></i>{/if}{$item.name}</a></li>
{/foreach}
  </ul>

{/if}

</div>

<div id="actions-area">
  
  {if !empty($recent_items)}
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b><span class="item_name_field">{$model.name}</span></b></li>
    {if $can_edit_items}<li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/pencil.png"> <a href="{dud_link}" onclick="itemList.workWithItem('openItem');">Open</a></li>{/if}
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/information.png"> <a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id='+itemList.lastItemId+'&amp;enable_ajax=1', '{$model.name} info');">{$model.name} info</a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/lock_open.png"> <a href="{dud_link}" onclick="itemList.workWithItem('releaseItem');">Release</a></li>
    {if $has_metapages}<li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/eye.png"> <a href="{dud_link}" onclick="itemList.workWithItem('preview');">Preview</a></li>{/if}
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_lightning.png"> <a href="{dud_link}" onclick="itemList.workWithItem('publishItem');">Publish</a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="itemList.workWithItem('unpublishItem');">Un-Publish</a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/accept.png"> <a href="{dud_link}" onclick="itemList.workWithItem('addTodoItem');">Add new to-do</a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_code.png"> <a href="{dud_link}" onclick="itemList.workWithItem('toggleItemArchived');"><span class="archive_action_name">Archive/Un-archive<span></a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/page_white_copy.png"> <a href="{dud_link}" onclick="itemList.workWithItem('duplicateItem');">Duplicate</a></li>
    <li class="permanent-action"><img border="0" src="{$domain}Resources/Icons/package_delete.png"> <a href="{dud_link}" onclick="itemList.workWithItem('deleteItem', {ldelim}confirm: 'Are you sure you want to delete this {$model.name|lower} ?'{rdelim});">Delete</a></li>
  </ul>
  {/if}
  
</div>