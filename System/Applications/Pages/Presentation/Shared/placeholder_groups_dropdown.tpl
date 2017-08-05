  <select name="placeholder_filegroup" id="placeholder-filegroup-{$random_nonce}">
    <option value="NONE">Do not limit - Allow all files of the correct types</option>
    <option value="NEW">Create new empty file group...</option>
    {foreach from=$groups item="group"}
    <option value="{$group.id}">{$group.label}</option>
    {/foreach}
  </select>
  
  <div class="edit-form-sub-row" id="new-group-name-div-{$random_nonce}" style="display:none">
    <p><input type="text" placeholder="New group name..." name="new_group_name" /></p>
  </div>
  
  {if empty($groups)}
  <div class="form-hint">No groups currently exist that exlusively contain files that accepted by this placeholder type but you can create a new one.</div>
  {/if}
  
  <script type="text/javascript">
    var selectId = 'placeholder-filegroup-{$random_nonce}';
    var divId = 'new-group-name-div-{$random_nonce}';
    {literal}
    $(selectId).observe('change', function(){
      if($F(selectId) == 'NEW'){
        $(divId).show();
      }else{
        $(divId).hide();
      }
    });
    {/literal}
  </script>