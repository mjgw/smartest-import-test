<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Authors of this {$item._model.name|lower}</h3>
    
    <div class="instruction">Check the boxes next to the users you'd like to link to this page as authors.</div>
    
    <div class="special-box">Are people missing from this list? Users must have the 'author_credit' {help id="users:tokens"}token{/help} to be credited as the author of an item.{if $provide_tokens_link} <a href="{$domain}smartest/users">Click here</a> to edit user tokens.{/if}</div>
    
    <form action="{$domain}{$section}/updateAuthors" method="post">
    
      <input type="hidden" name="item_id" value="{$item.id}" />
    
      <ul class="authors-list scroll-list" style="height:350px;border:1px solid #ccc">
      
        {foreach from=$users item="user"}
        <li>
          <input type="checkbox" name="users[{$user.id}]" id="author-{$user.id}-checkbox"{if in_array($user.id, $author_ids)} checked="checked"{/if} />
          <a href="#toggle-user-{$user.username}" id="author-{$user.id}"{if in_array($user.id, $author_ids)} class="selected"{/if}>
            {if $user.profile_pic.id > 1 && $user.profile_pic.url != "default_user_profile_pic.jpg"}
            <div class="user-avatar-holder" style="background-image:url({$user.profile_pic.image.60x60.web_path})"></div>
            {else}
            {getsystemcolor assign="usercolor"}
            <div class="user-avatar-holder" style="background-color:#{$usercolor.hex};color:{if $usercolor.text_white}#fff{else}#000{/if}">{$user.profile_initials}</div>
            {/if}
            <div class="user-name-holder-outer">
              <div class="user-name-holder-inner">
                {$user.full_name}
              </div>
            </div>
            <div class="breaker"></div>
          </a>
        </li>
        {/foreach}
      
      </ul>
      
      <script type="text/javascript">
      {literal}
      $$('ul.authors-list li a').each(function(el){
        el.observe('click', function(evt){
          evt.stop();
          if($(el.id+'-checkbox').checked){
            $(el.id+'-checkbox').checked = false;
            el.removeClassName('selected');
          }else{
            $(el.id+'-checkbox').checked = true;
            el.addClassName('selected');
          }
        });
      });
      {/literal}
      </script>
  
      <div id="edit-form-layout">
        <div class="edit-form-row">
          <div class="buttons-bar">
            <input type="button" value="Cancel" onclick="cancelForm();" />
            <input type="submit" name="action" value="Save" />
          </div>
        </div>
      </div>
  
    </form>
  
</div>

<div id="actions-area">
  
</div>