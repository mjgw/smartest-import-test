<div id="work-area">
  
  <script type="text/javascript">
    var groups = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
  </script>
  
  <h3>User groups</h3>
  
  <ul class="tabset">
    <li{if $active_tab == 'system'} class="current"{/if}><a href="{$domain}smartest/users/system">System users</a></li>
    <li{if $active_tab == 'ordinary'} class="current"{/if}><a href="{$domain}smartest/users/ordinary">Other users</a></li>
    <li{if $active_tab == 'groups'} class="current"{/if}><a href="{$domain}smartest/user_groups">User groups</a></li>
  </ul>
  
  <form id="pageViewForm" method="get" action="">
    <input type="hidden" name="user_id" id="item_id_input" value="" />
  </form>
  
  <ul class="options-grid" id="options_list">
    <li class="add">
      <a href="{$domain}smartest/user_groups/add" class="add"><i>+</i>Add a group</a>
    </li>
    {foreach from=$groups item="group"}
    <li ondblclick="window.location='{$domain}{$section}/editUserGroup?group_id={$group.id}'">
      <a href="#group-{$group.id}" class="option" id="group_{$group.id}" onclick="return groups.setSelectedItem('{$group.id}', 'group');">
        <div class="user-avatar-holder" style="background-color:#ccc;color:#444"><i class="fa fa-users"></i></div>
        {$group.label} {if $group._count}<span class="count">({$group._count})</span>{/if}</a>
    </li>
    {/foreach}
  </ul>
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="group-specific-actions" style="display:none">
    <li><b>Selected group</b></li>
    <li class="permanent-action"><a href="#" onclick="return groups.workWithItem('editUserGroup');"><img border="0" src="{$domain}Resources/Icons/folder_edit.png"> Edit group</a></li>
    <li class="permanent-action"><a href="#" onclick="return groups.workWithItem('deleteUserGroupConfirm');" ><img border="0" src="{$domain}Resources/Icons/folder_delete.png"> Delete group</a></li>
  </ul>
  
</div>