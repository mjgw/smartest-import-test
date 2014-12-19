<script language="javascript" type="text/javascript">

</script>

<div id="work-area">

<h3>User accounts</h3>

{if count($users)}

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

<ul class="tabset">
  <li{if $active_tab == 'system'} class="current"{/if}><a href="{$domain}smartest/users/system">System users</a></li>
  <li{if $active_tab == 'ordinary'} class="current"{/if}><a href="{$domain}smartest/users/ordinary">Other users</a></li>
</ul>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="user_id" id="item_id_input" value="" />
</form>

<ul class="options-grid" id="options_list">
  <li class="add">
    <a href="{$domain}smartest/users/add" class="add"><i>+</i>Add a user</a>
  </li>
{foreach from=$users key=key item=user}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editUser?user_id={$user.id}'">
    <a href="#" class="option" id="item_{$user.id}" onclick="setSelectedItem('{$user.id}'); return false;" >
      {if $user.profile_pic.id > 1 && $user.profile_pic.id != $default_user_profile_pic_id}
      <div class="user-avatar-holder" style="{if $sm_user_agent.is_supported_browser}background-image:url({$user.profile_pic.image.150x150.web_path});background-size:75px 75px;{else}background-image:url({$user.profile_pic.image.75x75.web_path}){/if}"></div>
      {else}
      {getsystemcolor assign="usercolor"}
      <div class="user-avatar-holder" style="background-color:#{$usercolor.hex};color:{if $usercolor.text_white}#fff{else}#000{/if}">{$user.profile_initials}</div>
      {/if}
  {$user.fullname}</a></li>
{/foreach}
</ul>
{else}
<div class="special-box">There are no ordinary users in the system yet. To add a user, <a href="{$domain}smartest/users/add" class="button">click here</a></div>
{/if}

</div>

<div id="actions-area">

  <ul class="actions-list" id="item-specific-actions" style="display:none">
    <li><b>Selected user</b></li>
    <li class="permanent-action"><a href="#" onclick="workWithItem('editUser'); return false;" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit User Details</a></li>
    <li class="permanent-action"><a href="#" onclick="workWithItem('editUserTokens'); return false;" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_edit.png"> Edit User Tokens</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to delete this user?')){workWithItem('deleteUser');}{/literal} return false;"><img border="0" src="{$domain}Resources/Icons/user_delete.png"> Delete User</a></li>
  </ul>

  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/vcard.png"> Roles</a></li>
  </ul>

</div>