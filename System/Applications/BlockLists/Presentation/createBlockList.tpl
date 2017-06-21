<div id="work-area">
  <h3>Create blocklist</h3>
  <form action="{$domain}blocklists/insertBlockList" method="post">
    
    <div class="edit-form-row">
      <div class="form-section-label">Blocklist name</div>
      <input type="text" name="blocklist_name" value="" id="blocklist-name" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Style</div>
      <select name="blocklist_style_id">
{foreach from=$blocklist_styles item="style"} 
        <option value="{$style.id}"{if $default_style.id == $style.id} selected="selected"{/if}>{$style.label}{if $default_style.id == $style.id} (default){/if}</option>
{/foreach}        
      </select>
    </div>
    
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Cancel" />
      <input type="submit" value="Save Changes" />
    </div>
    
  </form>
</div>