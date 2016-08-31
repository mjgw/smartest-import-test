<div id="work-area">
  <h3>Upgrade ordinary user</h3>
  <form action="{$domain}users/upgradeOrdinaryUserAction" method="post">
    <input type="hidden" name="user_id" value="{$user.id}" />
    <div class="edit-form-row">
      <div class="form-section-label">Site where this user can work</div>
      {if $allow_all_sites}
      <select name="site_permissions">
        <option value="CURRENT">This site only</option>
        <option value="GLOBAL">All sites</option>
      </select>
      {else}
      <input type="hidden" name="site_permissions" value="CURRENT" />This site only
      {/if}
    </div>
    <div class="edit-form-row" id="role-chooser">
      <div class="form-section-label">Role </div>
      <select name="user_role">
        {foreach from=$roles item="role"}
        <option value="{if $role.type == 'nondb'}system:{/if}{$role.id}">{$role.label}</option>
        {/foreach}
      </select>
    </div>
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Upgrade" />
    </div>
  </form>
  
</div>