<script type="text/javascript">
var itemList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list');
</script>

<div id="work-area">

{load_interface file="edit_set_tabs.tpl"}

<h3>Browse set <span class="light">&ldquo;{$set.label}&rdquo;</span></h3>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id"  value="{$set.id}" />
  <input type="hidden" name="item_id" id="item_id_input" value="" />
</form>

<form action="{$domain}{$section}/previewSet" method="get" id="mode-form">
  
  <div class="special-box">Show:
    <input type="hidden" name="set_id"  value="{$set.id}" />
    <select name="mode" onchange="$('mode-form').submit();">
      {if $set.type == 'DYNAMIC'}
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$model.plural_name|strtolower}, using draft property values</option>
      <option value="1"{if $mode == 1} selected="selected"{/if}>All {$model.plural_name|strtolower}, using draft property values, but only in archive</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>All {$model.plural_name|strtolower}, using draft property values, excluding those that are archived</option>
      <option value="3"{if $mode == 3} selected="selected"{/if}>All {$model.plural_name|strtolower}, using live property values</option>
      <option value="4"{if $mode == 4} selected="selected"{/if}>All {$model.plural_name|strtolower}, using live property values, but only in archive</option>
      <option value="5"{if $mode == 5} selected="selected"{/if}>All {$model.plural_name|strtolower}, using live property values, excluding those that are archived</option>
      <option value="6"{if $mode == 6} selected="selected"{/if}>Published {$model.plural_name|strtolower}, but using draft property values</option>
      <option value="7"{if $mode == 7} selected="selected"{/if}>Published {$model.plural_name|strtolower}, but using draft property values, but only in archive</option>
      <option value="8"{if $mode == 8} selected="selected"{/if}>Published {$model.plural_name|strtolower}, but using draft property values, excluding those that are archived</option>
      <option value="9"{if $mode == 9} selected="selected"{/if}>Published {$model.plural_name|strtolower}, using live property values</option>
      <option value="10"{if $mode == 10} selected="selected"{/if}>Published {$model.plural_name|strtolower}, using live property values, but only in archive</option>
      <option value="11"{if $mode == 11} selected="selected"{/if}>Published {$model.plural_name|strtolower}, using live property values, excluding those that are archived</option>
      {else}
      <option value="0"{if $mode == 0} selected="selected"{/if}>All {$model.plural_name|strtolower}</option>
      <option value="1"{if $mode == 1} selected="selected"{/if}>All archived {$model.plural_name|strtolower}</option>
      <option value="2"{if $mode == 2} selected="selected"{/if}>All {$model.plural_name|strtolower}, excluding those that are archived</option>
      <option value="6"{if $mode == 6} selected="selected"{/if}>Published {$model.plural_name|strtolower}</option>
      <option value="7"{if $mode == 7} selected="selected"{/if}>Published {$model.plural_name|strtolower} that are archived</option>
      <option value="8"{if $mode == 8} selected="selected"{/if}>Published {$model.plural_name|strtolower}, excluding those that are archived</option>
      {/if}
    </select>
    {help id="datamanager:query_modes"}What's this?{/help}
  </div>
</form>



{if empty($items)}

{if $set.type == 'STATIC'}
  <div class="warning">There are currently no items in this set. <a href="{$domain}{$section}/editSet?set_id={$set.id}">Click here</a> to add some.</div>
{else}
  <div class="warning">Please note: This saved query is currently empty because there are no items that match its conditions in the current mode. {help id="datamanager:query_modes"}What's a mode{/help}</div>
{/if}

{else}

<div id="options-view-header">

  <div id="options-view-info">
    Found {$count} {if $count != 1}{$model.plural_name|lower}{else}{$model.name|lower}{/if} in this set.
  </div>
  
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return itemList.setView('list', 'item_list_style')" id="options-view-list-button" class="{if $list_view == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return itemList.setView('grid', 'item_list_style')" id="options-view-grid-button" class="{if $list_view == "grid"}on{else}off{/if}"></a>
  </div>
  
  <div class="breaker"></div>
  
</div>

  <ul class="options-{$list_view}" id="options_list">
  {foreach from=$items key="key" item="item"}
    <li>
      <a href="{dud_link}" class="option" id="item_{$item.id}" onclick="setSelectedItem('{$item.id}', '{$item.name|escape:quotes}');" ondblclick="window.location='{$domain}datamanager/openItem?item_id={$item.id}'">
        {if $item.public == 'TRUE'}<img src="{$domain}Resources/Icons/item.png" border="0" class="grid" /><i class="fa fa-cube list"></i>{else}<img src="{$domain}Resources/Icons/item_grey.png" border="0" class="grid" /><i class="fa fa-cube list not-live"></i>{/if}{$item.name}</a></li>
  {/foreach}
  </ul>

{/if}

</div>

<div id="actions-area">
    
    <ul class="actions-list" id="item-specific-actions" style="display:none">
      <li><b>Selected Item</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/openItem?item_id='+selectedPage+'&amp;from=previewSet'"><i class="fa fa-pencil"></i> Edit this {$model.name|strtolower}</a></li>	
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/publishItem?item_id='+selectedPage"><i class="fa fa-globe"></i> Publish this {$model.name|strtolower}</a></li>	
      <li class="permanent-action"><a href="{dud_link}" onclick="itemList.workWithItem('deleteItemForward', {ldelim}confirm: 'Are you sure you want to delete this {$model.name|lower} ?'{rdelim});"><i class="fa fa-times-circle"></i> Delete this {$model.name|strtolower}</a></li>
    </ul>
    
    <ul class="actions-list">
      <li><b>Sets Options</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editSet?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}'"><i class="fa fa-pencil-square-o"></i> Edit this set</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/deleteSetConfirm?set_id={$set.id}'"><i class="fa fa-times-circle"></i> Delete this set</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editStaticSetOrder?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}';" ><i class="fa fa-random"></i> Change order</a></li>
      {if $model.id}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassSets?class_id={$model.id}'"><i class="fa fa-folder-open"></i> Back to sets</a></li>{else}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'"><img border="0" src="{$domain}Resources/Icons/folder.png" style="width:16px;height:18px"> Back to sets</a></li>{/if}
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/getItemClassMembers?class_id={$set.model.id}'"><i class="fa fa-search"></i> Browse {$set.model.plural_name}</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/models'"><i class="fa fa-cubes"></i> Back to models</a></li>
    </ul>
        
</div>
