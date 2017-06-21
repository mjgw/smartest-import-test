<div id="work-area">
  <h3>Create blocklist style</h3>
  <form action="{$domain}blocklists/insertBlockListStyle" method="post">
    
    <div class="edit-form-row">
      <div class="form-section-label">Blocklist style name</div>
      <input type="text" name="blocklist_style_name" value="" id="blocklist-style-name" />
    </div>
    
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Cancel" />
      <input type="submit" value="Save Changes" />
    </div>
    
  </form>
</div>