<script type="text/javascript">
  var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list');
</script>

<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>{$sub_model.plural_name} for {$item._model.name|lower} <span class="light">&lsquo;{$item.name}&rsquo;</span></h3>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="item_id" id="item_id_input" value="" />
    <input type="hidden" name="class_id" value="{$sub_model.id}" />
    <input type="hidden" name="parent_item_id" value="{$item.id}" />
  </form>
  
  <div id="options-view-header">

    <div id="options-view-info">
      Found {$num_items} {if $num_items != 1}{$sub_model.plural_name|lower}{else}{$sub_model.name|lower}{/if}
    </div>
  
    <div id="options-view-chooser">
      <a href="#list-view" onclick="return itemList.setView('list', 'item_list_style')" id="options-view-list-button" class="{if $list_view == "list"}on{else}off{/if}"></a>
      <a href="#grid-view" onclick="return itemList.setView('grid', 'item_list_style')" id="options-view-grid-button" class="{if $list_view == "grid"}on{else}off{/if}"></a>
    </div>
  
    <div class="breaker"></div>
  
  </div>
  
  <ul class="options-{$list_view}" id="options_list">
    {if $allow_create_new}<li class="add">
      <a href="{$domain}{$section}/addItem?class_id={$sub_model.id}&amp;parent_item_id={$item.id}"><i>+</i>Add a new {$sub_model.name|lower}</a>
    </li>{/if}
{foreach from=$items key="key" item="sub_item"}
    <li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$sub_item.id}'" class="item {if $sub_item.public=='FALSE'}unpublished{else}published{/if} {if $sub_item.is_archived=='1'}archived{else}current{/if}">
      <a href="#" class="option" id="item_{$sub_item.id}" onclick="return itemList.setSelectedItem('{$sub_item.id}', 'item', {literal}{{/literal}updateFields: {literal}{{/literal}item_name_field: '{$sub_item.name|summary:"29"|escape:quotes|trim}', archive_action_name: '{if $sub_item.is_archived}Unarchive{else}Archive{/if}'{literal}}{/literal}{literal}}{/literal});">
        {if $sub_item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><i class="fa fa-cube list"></i>{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><i class="fa fa-cube list not-live"></i>{/if}{$sub_item.name}</a></li>
{/foreach}
  </ul>
  
</div>

<div id="actions-area">
  
  {if count($items)}
  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b><span class="item_name_field">{$sub_model.name}</span></b></li>
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
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$sub_model.plural_name}</b></li>
    <li class="permanent-action"><a href="{$domain}datamanager/addItem?class_id={$sub_model.id}&amp;parent_item_id={$item.id}"><img border="0" src="{$domain}Resources/Icons/add.png" /> Add {$sub_model.name|lower}</a></li>
    {if $sub_model.manual_order}<li class="permanent-action"><a href="{$domain}datamanager/setSubModelItemOrder?item_id={$item.id}&amp;sub_model_id={$sub_model.id}"><img border="0" src="{$domain}Resources/Icons/arrow_switch.png"> Change order</a></li>{/if}
    <li class="permanent-action"><a href="#" onclick="MODALS.load('datamanager/modelInfo?class_id={$sub_model.id}', 'Model info');"><img border="0" src="{$domain}Resources/Icons/information.png" /> Model info</a></li>
    <li class="permanent-action"><a href="{$domain}datamanager/editModel?class_id={$sub_model.id}&amp;return_to_item_id={$item.id}"><img border="0" src="{$domain}Resources/Icons/pencil.png" /> Edit model</a></li>
  </ul>
  
</div>