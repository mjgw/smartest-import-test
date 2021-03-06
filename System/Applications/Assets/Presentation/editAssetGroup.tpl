<div id="work-area">
  
  {load_interface file="edit_filegroup_tabs.tpl"}  
  
  <h3>Edit file group <span class="light">"{$group.label}"</span></h3>
  
  <form action="{$domain}{$section}/updateAssetGroup" method="post">
    
    <input type="hidden" name="group_id" value="{$group.id}" />
    
    {if $from}
    <input type="hidden" name="from" value="{$from}" />
    {if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}
    <input type="hidden" name="item_id" value="{$workflow_item.id}" />
    {if $workflow_page}<input type="hidden" name="page_id" value="{$workflow_page.id}" />{/if}
    {elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW' || $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW_FULL'}
    <input type="hidden" name="page_id" value="{$workflow_page.id}" />
    {if $workflow_item}<input type="hidden" name="item_id" value="{$workflow_item.id}" />{/if}
    {elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}
    <input type="hidden" name="page_id" value="{$workflow_page.id}" />
    <input type="hidden" name="placeholder_id" value="{$workflow_placeholder.id}" />
    {if $workflow_item}<input type="hidden" name="item_id" value="{$workflow_item.id}" />{/if}
    {/if}
    {/if}
    
    <div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Label</div>
        <input type="text" name="group_label" value="{$group.label}" />
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Short name</div>
        {if $allow_name_edit}<input type="text" name="group_name" value="{$group.name}" /><div class="form-hint">Letters, numbers and underscores only, please. E.g. {$group.label.varname}</div>{else}{$group.name}{/if}
      </div>
      
      {if $allow_type_change}
      <div class="edit-form-row">
        <div class="form-section-label">Which files can go in this group?</div>
        <select name="asset_group_type">
          <option value="ALL">Any type of file</option>

          <optgroup label="Placeholder types">
{foreach from=$placeholder_types item="type"}
            <option value="P:{$type.id}"{if $group.filter_value == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
          </optgroup>

          <optgroup label="Specific file types">
{foreach from=$asset_types item="type"}
            <option value="A:{$type.id}"{if $group.filter_value == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
          </optgroup>

        </select>
      </div>
      {else}
      <div class="edit-form-row">
        <div class="form-section-label">Accepted file types</div>
        {$group.type_labels_list} <span class="form-hint">({$group.filter_value})</span>
      </div>
      {/if}
    
      <div class="edit-form-row">
        <div class="form-section-label">Shared</div>
        <input type="checkbox" name="group_shared" id="shared" value="1"{if $group.shared == "1"} checked="checked"{/if}{if !$allow_shared_toggle} disabled="disabled"{/if} />
        {if $allow_shared_toggle}
          <label for="shared">{if $shared}Uncheck this box to make this group available only to this site. {else}Check this box to make this group available to all sites. {/if}</label>
        {else}
          <span class="form-hint">This group is in use to define one or more placeholders, which are not site specific, so it must be shared.</span>
        {/if}
      </div>
    
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="submit" value="Save changes" />
        </div>
      </div>
    
    </div>
  
  </form>
  
</div>

<div id="actions-area">
  
  {if $request_parameters.from}
  <ul class="actions-list">
    <li><b>Workflow options</b></li>
    {if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}
    <li class="permanent-action"><a href="{$domain}datamanager/editItem?item_id={$workflow_item.id}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}"><i class="fa fa-check"></i> Return to editing {$workflow_item._model.name|lower}</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW' || $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW_FULL'}
    <li class="permanent-action"><a href="#" onclick="cancelForm();"><i class="fa fa-check"></i> Return to page preview</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}
    <li class="permanent-action"><a href="{$domain}websitemanager/definePlaceholder?assetclass_id={$workflow_placeholder.name}&amp;page_id={$workflow_page.webid}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}"><i class="fa fa-check"></i> Return to placeholder</a></li>
    {/if}
  </ul>
  {/if}
  
</div>