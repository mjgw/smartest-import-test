<div id="work-area">

<script type="text/javascript">
  {literal}var propertyList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options-list', function(newID, oldID){});{/literal}
</script>

{load_interface file="model_list_tabs.tpl"}

<h3>Properties of {$model.name|lower} model</h3>

<div class="text" style="margin-bottom:10px">Click a property once and choose from the options on the right.</div>

<form id="pageViewForm" method="get" action="">
<input type="hidden" name="class_id" value="{$model.id}" />
<input type="hidden" name="itemproperty_id" value="" id="item_id_input" />
</form>

<ul class="options-list" id="tree-root">
    {foreach from=$model_properties item="property"}
    <li>
       <a id="item_{$property.id}" class="option" href="javascript:nothing()" onclick="return propertyList.setSelectedItem('{$property.id}','item');" ondblclick="window.location='{$domain}{$section}/editItemClassProperty?class_id={$model.id}&amp;itemproperty_id={$property.id}'">		 
         <i class="fa fa-tag"></i>{$property.name}</a>
     </li>
    {/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
	<li><b>Selected item property</b></li>
	<li class="permanent-action"><a href="#" onclick="propertyList.workWithItem('editItemClassProperty'); return false;" class="right-nav-link"><i class="fa fa-pencil-square"></i> Edit this property</a></li>
	<li class="permanent-action"><a href="#" onclick="propertyList.workWithItem('viewItemClassPropertyValueSpread'); return false;" class="right-nav-link"><i class="fa fa-pie-chart"></i> View values spread</a></li>
	<li class="permanent-action"><a href="#" onclick="propertyList.workWithItem('startItemClassPropertyRegularization'); return false;" class="right-nav-link"><i class="fa fa-wrench"></i> Regularize this property</a></li>
	{if $can_delete_properties}<li class="permanent-action"><a href="{dud_link}" onclick="{literal}if(confirm('Are you sure you want to delete this property?')){propertyList.workWithItem('deleteProperty');}{/literal}" class="right-nav-link"><i class="fa fa-times-circle"></i> Delete this property</a></li>{/if}
</ul>

<ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    {if $can_add_properties}<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addPropertyToClass?class_id={$model.id}';" class="right-nav-link"> <i class="fa fa-plus-square"></i> Add a property to this model</a></li>{/if}
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}';" class="right-nav-link"> <i class="fa fa-plus-circle"></i> Create a new {$model.name|strtolower}</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><i class="fa fa-cube"></i> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>

</div>
