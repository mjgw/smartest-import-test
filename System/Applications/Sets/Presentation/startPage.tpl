<div id="work-area">

<h3>Sets of items</h3>

{load_interface file="items_front_tabs.tpl"}

<div class="instruction">Use sets to organize and group your items into more meaningful collections that belong together. {help id="datamanager:sets"}What are sets?{/help}</div>

{if count($sets)}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="set_id" id="item_id_input" value="" />
</form>


<ul class="{if count($sets) > 10}options-list{else}options-grid{/if}" id="{if count($sets) > 10}options_list{else}options_grid{/if}">
{foreach from=$sets key="key" item="set"}
  <li style="list-style:none;" 
			ondblclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}'">
			<a class="option" id="item_{$set.id}" onclick="setSelectedItem('{$set.id}');" >
			  <img border="0" src="{$domain}Resources/Icons/folder.png">
			  {$set.name} ({$set.type|lower})</a></li>
{/foreach}
</ul>

{else}

  <div class="special-box">
      There are no sets yet. <a href="{$domain}{$section}/addSet" class="button">Click here</a> to create one.
  </div>

{/if}

</div>

<div id="actions-area">
  
<ul class="actions-list" id="item-specific-actions" style="display:none">
  <li><b>Selected set</b></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('editSet');}{/literal}"><i class="fa fa-pencil-square-o"></i> Modify data set contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('previewSet');}{/literal}" ><i class="fa fa-list-ul"></i> List data set contents</a></li>
  <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('deleteSetConfirm');}{/literal}" ><i class="fa fa-times-circle"></i> Delete this data set</a></li>
{* <li class="permanent-action"><a href="#" onclick="{literal}if(selectedPage){workWithItem('chooseSchemaForExport');}{/literal}"><img border="0" src="{$domain}Resources/Icons/page_code.png"> Export</a></li> *}
</ul>

<ul class="actions-list">
  <li><b>Sets options</b></li>
  <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addSet'"><i class="fa fa-plus-square-o"></i> Create a new data set</a></li>  
  <li class="permanent-action"><a href="{$domain}smartest/models"><i class="fa fa-cubes"></i> Browse Data in Models</a></li>
  {* <li class="permanent-action"><a href="{$domain}sets/getDataExports"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Feeds</a></li>
  <li class="permanent-action"><a href="{$domain}smartest/schemas"><img border="0" src="{$domain}Resources/Icons/package_add.png"> View XML Schemas</a></li> *}
</ul>

</div>




