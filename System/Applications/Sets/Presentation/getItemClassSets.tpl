<div id="work-area">

{load_interface file="model_list_tabs.tpl"}

<h3>Sets of {$model.plural_name|lower}</h3>

<div class="instruction">Use Data Sets to organize your data into smaller groups. {help id="datamanager:sets"}What are sets?{/help}</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id" id="item_id_input" value="" />
</form>

{if count($sets)}

<ul class="{if count($sets) > 10}options-list{else}options-grid{/if}" id="{if count($sets) > 10}options_list{else}options_grid{/if}">
{foreach from=$sets key="key" item="set"}
  <li style="list-style:none;" 
			ondblclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}'">
			<a class="option" id="item_{$set.id}" onclick="setSelectedItem('{$set.id}', 'fff', '{if $set.type == 'STATIC'}static{else}dynamic{/if}');" >
			  <img border="0" src="{$domain}Resources/Icons/folder.png">
			  {$set.name} ({$set.type|lower})</a></li>
{/foreach}
</ul>

{else}

<div class="special-box">
    There are no sets of {$model.plural_name|lower} yet. <a href="{$domain}{$section}/addSet?class_id={$model.id}" class="button">Click here</a> to create one.
</div>

{/if}

</div>

<div id="actions-area">
  
<ul class="actions-list" id="static-specific-actions" style="display:none">
  <li><b>Selected Set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><i class="fa fa-pencil-square-o"></i> Change contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><i class="fa fa-search"></i> Browse items</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editStaticSetOrder');}{/literal}" ><i class="fa fa-random"></i> Change order</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('copySet');}{/literal}"><i class="fa fa-clipboard"></i> Duplicate<!--structure, not template because it does not propigate back to template--></a></li> *}
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this set?')){workWithItem('deleteSet');}{/literal}" ><i class="fa fa-times-circle"></i> Delete this set</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('chooseSchemaForExport');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export</a></li> *}
</ul>

<ul class="actions-list" id="dynamic-specific-actions" style="display:none">
  <li><b>Selected Dynamic Set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><i class="fa fa-pencil-square-o"></i> Change Contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><i class="fa fa-search"></i> Browse items</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('copySet');}{/literal}"><i class="fa fa-clipboard"></i> Duplicate<!--structure, not template because it does not propigate back to template--></a></li> *}
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage && confirm('Are you sure you want to delete this page?')){workWithItem('deleteSet');}{/literal}" ><i class="fa fa-times-circle"></i> Delete this set</a></li>
</ul>

<ul class="actions-list">
  <li><b>Set options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addSet?class_id={$model.id}'"><i class="fa fa-plus-square-o"></i> Make a new set of {$model.plural_name|lower}</a></li>
</ul>

</div>




