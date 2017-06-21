<div id="work-area">
  <h3>Edit blocklist</h3>
  <form action="{$domain}blocklists/updateBlockList" method="post">
    
    <input type="hidden" name="blocklist_id" value="{$blocklist.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Blocklist name</div>
      <input type="text" name="blocklist_name" value="{$blocklist.label}" id="blocklist-name" />
    </div>
  
    <div class="edit-form-row">
      <div class="form-section-label">Style</div>
      {if count($blocks)}
      {$blocklist.style.label}
      {else}
      <select name="blocklist_style_id" id="blocklist-style-id">
{foreach from=$blocklist_styles item="style"} 
        <option value="{$style.id}"{if $blocklist.style_id == $style.id} selected="selected"{/if}>{$style.label}</option>
{/foreach}        
      </select> <a href="#edit-style" class="button" onclick="window.location='{url_for}@blocklists:edit_blocklist_style{/url_for}?style_id='+$F('blocklist-style-id');return false;">Edit</a>
      <div class="form-hint">Once blocks are added to this blocklist, you will not be able to change its style</div>
      {/if}
    </div>
    
    <div class="form-section-label-full">Blocklist structure</div>
    
    <div id="blocks-container">
    {load_interface file="blocklist_reorderable_blocks.tpl"}
    </div>
    
    
    
    <a class="button" href="#add-block" onclick="MODALS.load('blocklists/createBlock?blocklist_id={$blocklist.id}', 'Add block');return false">Add a block</a>
  
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Done" />
    </div>
  
  </form>
</div>