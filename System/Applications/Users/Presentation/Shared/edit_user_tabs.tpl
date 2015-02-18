<ul class="tabset">
    <li{if $method == 'editUser'} class="current"{/if}><a href="{$domain}{$section}/editUser?user_id={$user.id}">Profile</a></li>
    <li{if $method == 'editUserProfilePic'} class="current"{/if}><a href="{$domain}{$section}/editUserProfilePic?user_id={$user.id}">Profile Picture</a></li>
    {if $show_tokens_edit_tab && $user.type == 'SM_USERTYPE_SYSTEM_USER'}<li{if $method == 'editUserTokens'} class="current"{/if}><a href="{$domain}{$section}/editUserTokens?user_id={$user.id}">Permissions</a></li>{/if}
    <li{if $method == 'userAssociatedContent'} class="current"{/if}><a href="{$domain}{$section}/userAssociatedContent?user_id={$user.id}">Associated content</a></li>
</ul>