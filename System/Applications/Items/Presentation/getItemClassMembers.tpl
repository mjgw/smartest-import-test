<script type="text/javascript">
{literal}
  var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list', function(newID, oldID){
    
    var approved = ($('item_'+newID).readAttribute('data-approved') == 'true') ? true: false;
    
    var archived = ($('item_'+newID).readAttribute('data-archived') == 'true') ? true : false;
    if(archived){
      $('archive-action-name').update('Un-archive');
    }else{
      $('archive-action-name').update('Archive');
    }
    
    var published = ($('item_'+newID).readAttribute('data-published') == 'true') ? true : false;
    if(published){
      $('item-unpublish-option').show();
      // $('item-publish-option').hide();
      // $('item-publish-option').show();
      $('item-publish-option-link').update('Re-publish');
    }else{
      $('item-unpublish-option').hide();
      // $('item-publish-option').show();
      // $('item-publish-option').show();
      $('item-publish-option-link').update('Publish');
    }
    
  });
{/literal}
</script>

<script language="javascript" type="text/javascript">
{literal}

function exportItem(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	
	if(selectedPage && editForm){
		{/literal}		
		editForm.action="/{$section}/"+pageAction; 
		{literal}
		editForm.submit();
	}
}

function openPage(pageAction){
	
	var editForm = document.getElementById('pageViewForm');
	if(editForm){
{/literal}		editForm.action="/{$section}/"+pageAction+"?item_id=";{literal}
		editForm.submit();
	}
}

{/literal}
</script>

<div id="work-area">

{if $require_item_select}

<h3>{$model.plural_name}</h3>

<div class="special-box">
  <p>{$model.plural_name} only exist in attachment to specific {$parent_model.plural_name|lower}. Please choose one to continue.</p>
  <form action="{$domain}datamanager/getSubModelItems">
  <select name="item_id">
{foreach from=$possible_parent_items item="ppi"}
    <option value="{$ppi.id}">{$ppi.name}</option>
{/foreach}
  </select>
  <input type="submit" value="Go" />
  <input type="hidden" name="sub_model_id" value="{$model.id}" />
</div>

{else}

{load_interface file="model_list_tabs.tpl"}

<h3>{$model.plural_name}</h3>

{if $items_exist}

<a name="top"></a>
<div class="instruction">Double click one of the {$model.plural_name|strtolower} below to edit it, or click once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="item_id" id="item_id_input" value="" />
  <input type="hidden" name="class_id" value="{$model.id}" />
</form>

<div class="special-box">
  <form id="mode-form" method="get" action="">
    <input type="hidden" name="class_id" value="{$model.id}" />
    Show: <select name="mode" onchange="$('mode-form').submit();">
      <option value="7"{if $mode == 7} selected="selected"{/if}>All {$model.plural_name|strtolower} not archived</option>
      <option value="1"{if $mode == 1} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower}</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower} that have not been approved</option>
      <option value="3"{if $mode == 3} selected="selected"{/if}>Unpublished {$model.plural_name|strtolower} that have been approved</option>
      <option value="4"{if $mode == 4} selected="selected"{/if}>Published {$model.plural_name|strtolower}</option>
      <option value="5"{if $mode == 5} selected="selected"{/if}>Published {$model.plural_name|strtolower} that have been modified, but not re-approved</option>
      <option value="6"{if $mode == 6} selected="selected"{/if}>Published {$model.plural_name|strtolower} that have been modified and re-approved</option>
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$model.plural_name|strtolower}</option>
      <option value="8"{if $mode == 8} selected="selected"{/if}>All archived {$model.plural_name|strtolower}</option>
    </select>
    filter by {$model.item_name_field_name|lower}: <input type="text" name="q" id="items-search-name" value="{$query}" style="width:300px" />
    <input type="submit" value="Go" />
  </form>
</div>

<!--<div id="autocomplete_choices" class="autocomplete"></div>-->
  
  <script type="text/javascript">
    {literal}
    
    function getSelectionId(text, li) {
        var bits = li.id.split('-');
        window.location=sm_domain+'datamanager/openItem?item_id='+bits[1];
    }
    
    /* new Ajax.Autocompleter("items-search-name", "autocomplete_choices", "/ajax:datamanager/simpleItemTextSearch", {
        paramName: "query", 
        minChars: 2,
        delay: 50,
        width: 300,
        afterUpdateElement : getSelectionId
    }); */
    
    {/literal}
  </script>

<div id="options-view-header">

  <div id="options-view-info">
    Found {$num_items} {if $num_items != 1}{$model.plural_name|lower}{else}{$model.name|lower}{/if}
  </div>
  
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return itemList.setView('list', 'item_list_style')" id="options-view-list-button" class="{if $list_view == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return itemList.setView('grid', 'item_list_style')" id="options-view-grid-button" class="{if $list_view == "grid"}on{else}off{/if}"></a>
  </div>
  
  <div class="breaker"></div>
  
</div>

  <ul class="options-{$list_view}" id="options_list">
    {if $allow_create_new}<li class="add">
      <a href="{$domain}{$section}/addItem?class_id={$model.id}"><i>+</i>Add a new {$model.name|lower}</a>
    </li>{/if}
{foreach from=$items key="key" item="item"}
    <li ondblclick="window.location='{$domain}{$section}/openItem?item_id={$item.id}'" class="item {if $item.public=='FALSE'}unpublished{else}published{/if} {if $item.is_archived=='1'}archived{else}current{/if}">
      <a href="#" data-approved="{makebool value=$item.changes_approved assign="approved"}{$approved.truefalse}" data-archived="{makebool value=$item.is_archived assign="archived"}{$archived.truefalse}" data-created="{$item.created.unix}" data-published="{makebool value=$item.public assign="published"}{$published.truefalse}" class="option" id="item_{$item.id}" onclick='return itemList.setSelectedItem("{$item.id}", "item", {$item._item_list_json});' data-ui="">
        {if $item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><i class="fa fa-cube list"></i>{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><i class="fa fa-cube list not-live"></i>{/if}{$item.name}</a></li>
{/foreach}
  </ul>
  
{else}
  
  <div class="special-box">
      There are no {$model.plural_name|lower} yet. {if $allow_create_new}<a href="{$domain}{$section}/addItem?class_id={$model.id}" class="button">Click here</a> to create one.{else}Your user account does not have permission to create them.{/if}
  </div>
  
{/if}

{/if}

</div>

<div id="actions-area">

{if $items_exist}
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b><span class="item_name_field">{$model.name}</span></b></li>
  {if $can_edit_items}<li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('openItem');"><i class="fa fa-pencil"></i> Open</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id='+itemList.lastItemId+'&amp;enable_ajax=1', '{$model.name} info', true);"><i class="fa fa-info-circle"></i> {$model.name} info</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('releaseItem');"><i class="fa fa-unlock"></i> Release</a></li>
  {if $has_metapages}<li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('preview');"><i class="fa fa-eye"></i> Preview</a></li>{/if}
  <li class="permanent-action" id="item-publish-option"><a href="{dud_link}" onclick="itemList.workWithItem('publishItem');"><i class="fa fa-globe"></i> <span id="item-publish-option-link">Publish</span></a></li>
  <li class="permanent-action" id="item-unpublish-option" style="display:none"><a href="{dud_link}" onclick="itemList.workWithItem('unpublishItem');"><i class="fa fa-eye-slash"></i> Un-Publish</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('addTodoItem');"><i class="fa fa-share-square-o"></i> Add new to-do</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('toggleItemArchived');"><i class="fa fa-archive"></i> <span class="archive_action_name" id="archive-action-name">Archive/Un-archive<span></a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('duplicateItem');"><i class="fa fa-clipboard"></i> Duplicate</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('deleteItem', {ldelim}confirm: 'Are you sure you want to delete this {$model.name|lower} ?'{rdelim});"><i class="fa fa-trash"></i> Delete</a></li>
</ul>
{/if}

<ul class="actions-list" id="non-specific-actions">
  <li><b>Model Options</b></li>
  {if $allow_create_new}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}'"><i class="fa fa-plus-circle"></i> New {$model.name|lower}</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseUserHeldItems?class_id={$model.id}';"><i class="fa fa-unlock"></i> Release all {$model.plural_name|lower}</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/modelInfo?class_id={$model.id}', 'Model info');"><i class="fa fa-info"></i> Model info</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="return MODALS.load('datamanager/showItemClassTemplateAccess?class_id={$model.id}', 'Template data')"><i class="fa fa-file-code-o"></i> Template data</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/exportModelKit?class_id={$model.id}';"><i class="fa fa-arrow-circle-down"></i> Download model kit</a></li>
  {if $has_metapages && $elasticsearch_running}<li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/indexModel?class_id={$model.id}', 'Index {$model.plural_name|lower}');"><i class="fa fa-cubes"></i> Index for search</a></li>{/if}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/getItemClassSets?class_id={$model.id}'"><i class="fa fa-folder-open"></i> View sets of {$model.plural_name|lower}</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$model.id}'"><i class="fa fa-plus-square-o"></i> Create a new set of {$model.plural_name|lower}</a></li>
  {if $can_edit_properties}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}'"><i class="fa fa-sliders"></i> Edit model properties</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editItemClassPropertyOrder?class_id={$model.id}'"><i class="fa fa-random"></i> Edit property order</a></li>{/if}  
{* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/importData?class_id={$itemBaseValues.itemclass_id}';"><img border="0" src="{$domain}Resources/Icons/page_code.png" /> Import data from CSV</a></li> *}
</ul>

{if count($recent_items)}
<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><i class="fa fa-cube"></i> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>
{/if}

</div>