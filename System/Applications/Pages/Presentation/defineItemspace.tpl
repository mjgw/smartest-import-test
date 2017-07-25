<div id="work-area">
  
  <h3>Define Itemspace</h3>
  
  <div class="instruction">Choose an item to fill itemspace '{$itemspace.label}' on page '{$page.title}'</div>
  
  {if count($options)}
  <form action="{$domain}{$section}/updateItemspaceDefinition" method="post">
    
    <input type="hidden" name="itemspace_id" value="{$itemspace.id}" />
    <input type="hidden" name="itemspace_name" value="{$itemspace.name}" />
    <input type="hidden" name="page_id" value="{$page.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Chosen {$model.name|lower}</div>
      <select name="item_id">
        {foreach from=$options item="option"}
        <option value="{$option.id}"{if $option.id == $definition_id} selected="sselected"{/if}>{$option.label}</option>
        {/foreach}
      </select>
      <div class="form-hint">You are selecting <strong>{$model.plural_name|lower}</strong> from the set <strong>{$set.label|lower}</strong>.</div>
    </div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="button" value="Cancel" onclick="cancelForm()" />
        <input type="submit" value="Save changes" />
      </div>
    </div>
    
  </form>
  {else}
  <div class="special-box">There are no <strong>{$model.plural_name|lower}</strong> for you to choose from in the set <strong>{$set.label|lower}</strong>. {if $set.type == 'DYNAMIC'}<a href="{$domain}sets/editDynamicSetConditions?set_id={$set.id}" class="button">Edit set</a>{else}<a href="{$domain}sets/editSet?set_id={$set.id}" class="button">Edit set</a>{/if}</div>
  {/if}
  
</div>