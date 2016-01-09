<script type="text/javascript">

{literal}

  var firstName, firstNameEntered, lastName, lastNameEntered, usernameSuggested;
  
  var defaultFirstName = 'Enter first name';
  var defaultUserName = 'Enter a username';
  var defaultEmailAddress = 'Enter an email address';
  
  var emailAddressRegex = /^[\w\._-]+@[\w-]+(\.[\w]+)+$/;

{/literal}

</script>

<div id="work-area">

{load_interface file="edit_user_tabs.tpl"}

<h3 id="user">Edit user: {if $user.id == $_user.id}You{else}{$user.fullname}{/if}</h3>

<form id="addUser" name="addUser" action="{$domain}{$section}/updateUser" method="post">
  
  <input type="hidden"  name="user_id" value="{$user.id}" >
  
  <div class="edit-form-row">
    <div class="form-section-label">Profile picture</div>
    {image_select name="user_profile_pic_id" id="user-profile-pic-id" value=$user.profile_pic for="user_profile_pic" user_id=$user.id}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">First name </div>
    <input type="text" name="user_firstname" id="ifn" value="{$user.firstname}" />
    <div class="form-hint">A first name is required</div>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Last name </div>
    <input type="text" name="user_lastname" id="iln" value="{$user.lastname}" />
  </div>

  <div class="edit-form-row">
    <div class="form-section-label"><script type="text/javascript">document.write('&#x55;&#x73;&#x65;');document.write('&#x72;&#x6e;&#x61;&#x6d;&#x65;')</script> </div>
    <input type="text" name="username" style="position:absolute;top:-100px" />
	{if $allow_username_change}
    <input type="text" style="width:200px" name="thing_that_aint_u5ern4me" id="thing-that-aint-u5ern4me" autocomplete="off" value="{$user.username}" /><div class="form-hint">Letters, numbers, dots and underscores only please</div>
    {else}{$user.username}{/if}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Password </div>
    <input type="password" style="width:200px" name="password" id="password" autocomplete="off" /><div class="form-hint">Try to make this at least eight characters, and include letters, mixed uppercase and lowercase, and punctuation</div>  
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Re-type password </div>
    <input type="password" style="width:200px" name="passwordconfirm" id="passwordconfirm" autocomplete="off" /><div class="form-hint">Type the password again if you are changing it, just to be sure you typed it right.</div>  
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Email address </div>
    <input type="text" name="email" id="email" value="{$user.email}" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">User's website address, if not this website </div>
    <input type="text" name="user_website" value="{$user.website}" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Organization name{if $_site.organization}, if not <strong>{$_site.organization}</strong>{/if}</div>
    <input type="text" name="user_orginaization_name" value="{$user.organization_name}" />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Tags</div>
    <div class="edit-form-sub-row">
      <ul class="checkbox-array-list" id="user-tags-list">
{foreach from=$user.tags item="tag"}
        <li data-tagid="{$tag.id}"><label>{$tag.label} <a href="#remove-tag" class="tag-icon-button delete-tag"><i class="fa fa-times"></i></a></label></li>
{/foreach}
      </ul>
      <span class="null-notice" id="no-tags-notice"{if count($user.tags)} style="display:none"{/if}>No tags attached to this user</span>
      <div class="v-spacer half"></div>
      <input type="text" name="user_add_tag" value="Add a tag..." id="user-add-tag-textbox" class="unfilled" />
      <div class="autocomplete" id="tags-autocomplete"></div>
    </div>
    
    <script type="text/javascript">
    
    var userId = {$user.id};
    
    {literal}
    
    var tagsInUse = {};
    
    var removeTagFromClick = function(evt){
      
      evt.stop();
      var a = Event.element(evt);
      var li = a.up(2);
      var tagId = li.readAttribute('data-tagid');
      
      if(tagsInUse.hasOwnProperty('tag_'+tagId)){
        
        // remove tag by ID
        new Ajax.Request(sm_domain+'ajax:users/untagUserWithTagId', {
        
          parameters: 'tag_id='+tagId+'&user_id='+userId,
          onSuccess: function(response) {
            // hide tag
            li.fade({
              duration: 0.3,
              afterfinish: function(){
                  li.remove();
                  console.log(tagsInUse.size());
                  $('no-tags-notice').appear({duration: 0.3});
                }
            });
            var key = 'tag_'+tagId;
            delete(tagsInUse[key]);
            
          }
          
        });
        
      }else{
        
        
        
      }
        
    }
    
    $$('#user-tags-list li').each(function(li){
      var tkey = 'tag_'+li.readAttribute('data-tagid');
      tagsInUse[tkey] = true;
    });
    
    // console.log(tagsInUse);
    
    $$('#user-tags-list li label a.tag-icon-button.delete-tag').each(function(a){
      a.observe('click', removeTagFromClick);
    });
    
    $('user-add-tag-textbox').observe('focus', function(){
        if(($('user-add-tag-textbox').getValue() == 'Add a tag...') || $('user-add-tag-textbox').getValue() == ''){
            $('user-add-tag-textbox').removeClassName('unfilled');
            $('user-add-tag-textbox').setValue('');
        }
    });
    
    $('user-add-tag-textbox').observe('blur', function(){
        if(($('user-add-tag-textbox').getValue() == 'Add a tag...') || $('user-add-tag-textbox').getValue() == ''){
            $('user-add-tag-textbox').addClassName('unfilled');
            $('user-add-tag-textbox').setValue('Add a tag...');
        }
    });
    
    new Ajax.Autocompleter('user-add-tag-textbox', "tags-autocomplete", sm_domain+"ajax:settings/tagsAutoComplete", {
      
      paramName: "string",
      minChars: 3,
      delay: 50,
      width: 300,
      
      afterUpdateElement : function(text, li) {
        
        var tagName = li.readAttribute('data-label');
        var tagId = li.readAttribute('data-id');
        
        if(tagId == 'new-tag'){
          
          new Ajax.Request(sm_domain+'ajax:settings/createNewTag', {
            
            parameters: 'new_tag_label='+li.readAttribute('data-label'),
            onSuccess: function(response){
              
              newTag = response.responseJSON;
              
              new Ajax.Request(sm_domain+'ajax:users/tagUserWithString', {
            
                parameters: 'tag_text='+tagName+'&user_id='+userId,
                onSuccess: function(useNewTagResponse) {
          
                  var i = new Element('i', {'class': 'fa fa-times'});
                  var a = new Element('a', {'class': 'tag-icon-button delete-tag'});
                  var label = new Element('label');
                  label.update(newTag.label+' ');
          
                  var tag_li = new Element('li');
                  tag_li.writeAttribute('data-tagid', newTag.id);
          
                  a.appendChild(i);
                  a.observe('click', removeTagFromClick);
          
                  label.appendChild(a);
                  tag_li.appendChild(label);
          
                  $('user-tags-list').appendChild(tag_li);
          
                  if($('no-tags-notice').visible()){
                    $('no-tags-notice').hide();
                  }
            
                  var tkey = 'tag_'+newTag.id;
                  tagsInUse[tkey] = true;
              
                  $('user-add-tag-textbox').value = "";
                  $('user-add-tag-textbox').blur();
          
                }
           
              });
              
            }
            
          })
          
        }else{
        
          $('user-add-tag-textbox').value = "";
          $('user-add-tag-textbox').blur();
          
          if(tagsInUse.hasOwnProperty('tag_'+tagId)){
            
            // That tag is already in use here
            
          }else{
            
            new Ajax.Request(sm_domain+'ajax:users/tagUserWithString', {
            
              parameters: 'tag_text='+tagName+'&user_id='+userId,
              onSuccess: function(response) {
            
                var i = new Element('i', {'class': 'fa fa-times'});
                var a = new Element('a', {'class': 'tag-icon-button delete-tag'});
                var label = new Element('label');
                label.update(tagName+' ');
              
                var tag_li = new Element('li');
                tag_li.writeAttribute('data-tagid', li.readAttribute('data-id'));
              
                a.appendChild(i);
                a.observe('click', removeTagFromClick);
              
                label.appendChild(a);
                tag_li.appendChild(label);
              
                $('user-tags-list').appendChild(tag_li);
              
                if($('no-tags-notice').visible()){
                  $('no-tags-notice').hide();
                }
                
                var tkey = 'tag_'+tag_li.readAttribute('data-tagid');
                tagsInUse[tkey] = true;
            
              }
             
            });
              
          }
          
        }
        
      }
      
    });
    
    {/literal}
      
    </script>
    
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">About the user </div>
    <textarea name="user_bio" style="width:500px;height:60px">{$user.bio}</textarea>
  </div>
  
{if $require_password_changes && $user.id != $_user.id}
  <div class="edit-form-row">
    <div class="form-section-label">Require password change </div>
    <input type="checkbox" name="require_password_change" value="1"{if $user.password_change_required} checked="checked"{/if} /><span class="form-hint">Roadblocks the user until they change their password. Takes effect next time they log in.</span>
  </div>
{/if}

  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="submit" value="Save" />
    </div>
  </div>  

  </form>

</div>

<div id="actions-area">
  <ul class="actions-list">
     <li><b>Users &amp; Tokens</b></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addRole'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/vcard_add.png"> Add Role</a></li>
     <li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/users'" class="right-nav-link"><img border="0" src="{$domain}Resources/Icons/user.png"> Go back to users</a></li>
  </ul>
</div>

{if $sites._count > 1}
<script type="text/javascript">
{literal}
  
  $('addUser').observe('submit', function(e){
    
    if($('ifn').value == '' || $('ifn').value == defaultFirstName){
        $('ifn').addClassName('error');
        e.stop();
    }
    
    if($('user_email').value == '' || $('user_email').value == defaultEmailAddress || !$('user_email').value.match(emailAddressRegex)){
        $('user_email').addClassName('error');
        e.stop();
    }
    
    if($('password').value != $('passwordconfirm').value){
        $('password').addClassName('error');
        $('passwordconfirm').addClassName('error');
        e.stop();
    }
    
  });
  
{/literal}

</script>
{/if}