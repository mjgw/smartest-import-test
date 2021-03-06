<div id="work-area">

{load_interface file="edit_set_tabs.tpl"}

<h3>Edit set: <span class="light">{$set.label}</span></h3>

<div class="instruction">Create conditions to filter your data into a pre-saved set that can be used anywhere.</div>

  <form id="pageViewForm" method="post" action="{$domain}{$section}/updateDynamicSet">
    
    <input type="hidden" name="set_id" value="{$set.id}" />
    <input type="hidden" name="add_new_condition" value="false" id="add-new-condition" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Set Label</div>
      <input type="text"  name="set_label" value="{$set.label}" />
    </div>
    
    
    <div class="edit-form-row">
      <div class="form-section-label">Set Name</div>
      {if $can_edit_set_name}<input type="text"  name="set_name" value="{$set.name}" />{else}{$set.name}{/if}
      <div class="form-hint">This value is used in templates and queries: <code>{$set.name}</code></div>
    </div>
    
    {* 
    <div class="edit-form-row">
      <div class="form-section-label">Get {$model.plural_name}</div>
      <select name="set_data_source_site_id">
        {foreach from=$sites item="site"}
		    <option value="{$site.id}" {if $site.id == $set.data_source_site_id} selected="selected"{/if}>From {$site.internal_label}</option>
        {/foreach}
        {if count($sites) > 1}<option value="ALL" {if $set.data_source_site_id == "ALL"} selected="selected"{/if}>From all Sites</option>{/if}
        <option value="CURRENT"{if $set.data_source_site_id == "CURRENT"} selected="selected"{/if}>From the site where the set is in use at the time (contextual)</option>
	    </select>
    </div>  *}
    
    <div class="edit-form-row">
      <div class="form-section-label">Sort by</div>
      <select name="set_sort_field">
        <option value="_SMARTEST_ITEM_NAME" {if $set.sort_field == '_SMARTEST_ITEM_NAME'} selected="selected"{/if}>{$model.item_name_field_name}</option>
        <option value="_SMARTEST_ITEM_ID" {if $set.sort_field == '_SMARTEST_ITEM_ID'} selected="selected"{/if}>ID</option>
            <option value="{$random_value}" {if $set.sort_field == $random_value} selected="selected"{/if}>Random order</option>
{foreach from=$properties item="property"}
	      <option value="{$property.id}" {if $property.id == $set.sort_field} selected="selected"{/if}>{$property.name}</option>
{/foreach}
        <option value="_SMARTEST_ITEM_NUM_HITS" {if $set.sort_field == '_SMARTEST_ITEM_NUM_HITS'} selected="selected"{/if}>Number of Hits</option>
        <option value="_SMARTEST_ITEM_NUM_COMMENTS" {if $set.sort_field == '_SMARTEST_ITEM_NUM_COMMENTS'} selected="selected"{/if}>Number of Comments</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Sort direction</div>
      <select name="set_sort_direction">
		    <option value="ASC" {if $set.sort_direction == "ASC"} selected{/if}>Ascending</option>
		    <option value="DESC" {if $set.sort_direction == "DESC"} selected{/if}>Descending</option>
	    </select>
    </div>
    
    {if $show_shared}
    <div class="edit-form-row">
      <div class="form-section-label">Shared</div>
      <input type="checkbox" name="set_shared" value="1"{if $set.shared == "1"} checked="checked"{/if} />
      <span class="form-hint">Check this box to make this set and its rules (but not its contents) shared with other sites.</span>
    </div>
    {/if}
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        {save_buttons}
      </div>
    </div>
  
  </form>

</div>

<div id="actions-area">
		
		<ul class="actions-list">
		  <li><b>Options</b></li>
			{if $request_parameters.item_id && $request_parameters.from}<li class="permanent-action"><a href="{$domain}datamanager/editItem?item_id={$request_parameters.item_id}"><i class="fa fa-check"></i> Return to editing item</a></li>{/if}
			<li class="permanent-action"><a href="{$domain}{$section}/previewSet?set_id={$set.id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}"><i class="fa fa-search"></i> Browse set contents</a></li>
      <li class="permanent-action"><a href="{$domain}{$section}/deleteSetConfirm?set_id={$set.id}"><i class="fa fa-trash"></i> Delete this set</a></li>
			<li class="permanent-action">{if $model.id}<a href="{$domain}{$section}/getItemClassSets?class_id={$model.id}"><i class="fa fa-folder-o"></i> Browse sets of {$model.plural_name|strtolower}</a>{else}<a href="#" onclick="window.location='{$domain}smartest/sets'"><i class="fa fa-folder-o"></i> Back to data sets</a></li>{/if}		
			<li class="permanent-action"><a href="{$domain}smartest/models"><i class="fa fa-cubes"></i> Browse all items</a></li>
			<li class="permanent-action"><a href="{$domain}{$section}/syndicateSet?set_id={$set.id}"><i class="fa fa-code"></i> Export this set as XML or JSON</a></li>
		</ul>
		
</div>


