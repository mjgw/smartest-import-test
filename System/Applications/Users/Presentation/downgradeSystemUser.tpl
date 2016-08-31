<div id="work-area">
  <h3>Downgrade system user</h3>
  <div class="special-box">
    User: <strong>{$user.full_name}</strong>
  </div>
  <form action="{$domain}users/downgradeSystemUserAction" method="post">
    <input type="hidden" name="user_id" value="{$user.id}" />
    <div class="instruction">Are you sure you would like to downgrade this system user? They will be unable to log in to Smartest and all permissions within Smartest will be deleted. The user's username and password will be unaffected and they may still be able to log in to any other services you authenticate from the same database.</div>
    {if $system_groups._count}
    <div>
    This user will also be removed from the following groups, which are for system users only:
    <ul>
{foreach from=$system_groups item="group"}
      <li>{$group.label}</li>
{/foreach}      
    </ul>
    </div>
    {/if}
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Downgrade" />
    </div>
  </form>
  
</div>