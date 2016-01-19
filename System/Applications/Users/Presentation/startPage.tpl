<script type="text/javascript">
{literal}
  var userList = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'item', 'options_list', function(newID, oldID){
    
    if($('user_'+newID).readAttribute('data-onsite').charAt(2)){
      var hasSiteAccess = ($('user_'+newID).readAttribute('data-onsite') == 'true') ? true: false;
      
      if(hasSiteAccess){
        $('revoke-user-site-access').show();
        $('grant-user-site-access').hide();
      }else{
        $('revoke-user-site-access').hide();
        $('grant-user-site-access').show();
      }
    }
    
    /* var archived = ($('item_'+newID).readAttribute('data-archived') == 'true') ? true : false;
    if(archived){
      $('archive-action-name').update('Un-archive');
    }else{
      $('archive-action-name').update('Archive');
    }
    
    var published = ($('item_'+newID).readAttribute('data-published') == 'true') ? true : false;
    if(published){
      $('item-unpublish-option').show();
      // $('item-publish-option').hide();
      // $('item-publish-option').show();
      $('item-publish-option-link').update('Re-publish');
    }else{
      $('item-unpublish-option').hide();
      // $('item-publish-option').show();
      // $('item-publish-option').show();
      $('item-publish-option-link').update('Publish');
    } */
    
  });
{/literal}
</script>

<div id="work-area">

<h3>User accounts</h3>

{if count($users)}

<div class="instruction">Double click a user to edit or choose from the options on the right.</div>

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
    <a href="{$domain}smartest/users/add" class="add"><i>+</i>Add a user</a>
  </li>
{foreach from=$users key=key item=user}
  <li style="list-style:none;" ondblclick="window.location='{$domain}{$section}/editUser?user_id={$user.id}'" class="{if $show_site_access}{if in_array($user.id, $user_ids_on_this_site)}access{else}no-access{/if}{/if}">
    <a href="#" class="option" id="user_{$user.id}" onclick="return userList.setSelectedItem('{$user.id}', 'user'); return false;" {if $show_site_access}data-onsite="{if in_array($user.id, $user_ids_on_this_site)}true{else}false{/if}"{/if}>
      {if $user.profile_pic.id > 1 && $user.profile_pic.id != $default_user_profile_pic_id}
      <div class="user-avatar-holder" style="{if $sm_user_agent.is_supported_browser}background-image:url({$user.profile_pic.image.150x150.web_path});background-size:75px 75px;{else}background-image:url({$user.profile_pic.image.75x75.web_path}){/if}"></div>
      {else}
      {getsystemcolor assign="usercolor"}
      <div class="user-avatar-holder" style="background-color:#{$usercolor.hex};color:{if $usercolor.text_white}#fff{else}#000{/if}">{$user.profile_initials}</div>
      {/if}
  {$user.fullname}{if $show_site_access}{if in_array($user.id, $user_ids_on_this_site)}{else} <i class="fa fa-lock"></i>{/if}{/if}</a></li>
{/foreach}
</ul>
{else}
<div class="special-box">There are no ordinary users in the system yet. To add a user, <a href="{$domain}smartest/users/add" class="button">click here</a></div>
{/if}

</div>

<div id="actions-area">

  <ul class="actions-list" id="user-specific-actions" style="display:none">
    <li><b>Selected user</b></li>
    <li class="permanent-action"><a href="#" onclick="return userList.workWithItem('editUser');" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Edit User Details</a></li>
    <li class="permanent-action" style="display:none" id="grant-user-site-access"><a href="#" onclick="return userList.workWithItem('grantUserCurrentSiteAccess');" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Grant access to this site</a></li>
    <li class="permanent-action" style="display:none" id="revoke-user-site-access"><a href="#" onclick="return userList.workWithItem('revokeUserCurrentSiteAccess');" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/pencil.png"> Revoke access to this site</a></li>
    <li class="permanent-action"><a href="#" onclick="return userList.workWithItem('editUserTokens');" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_edit.png"> Edit User Tokens</a></li>
    <li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to delete this user?')){return userList.workWithItem('deleteUser');}{/literal}"><img border="0" src="{$domain}Resources/Icons/user_delete.png"> Delete User</a></li>
  </ul>

  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users/add'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user_add.png"> Add User</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/user_roles'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/vcard.png"> Roles</a></li>
  </ul>

</div>