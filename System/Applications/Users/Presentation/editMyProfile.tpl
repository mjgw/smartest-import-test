<div id="work-area">
  <h3>Your profile</h3>
  <form action="{$domain}users/updateMyProfile" method="post">
    <div class="edit-form-row">
      <div class="form-section-label">Profile picture</div>
      {image_select for="user_profile_pic" user=$user id="profile-pic-selector" name="profile_pic_asset_id" value=$user.profile_pic}
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Username</div>
      {if $allow_username_change}<input type="text" name="username" value="{$user.username}" />{else}{$user.username}{/if}
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Password</div>
      <a href="#password" class="button" id="password-change-modal-button">Click here to change your password</a>
      <script type="text/javascript">
      {literal}
      $('password-change-modal-button').observe('click', function(e){
        e.stop();
        MODALS.load('smartest/account/password', 'Change your password');
      });
      {/literal}
      </script>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">First name</div>
      <input type="text" name="user_firstname" value="{$user.firstname}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Last name</div>
      <input type="text" name="user_lastname" value="{$user.lastname}" />
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Email address (for notifications)</div>
      {email_input name="user_email" value=$user.email id="user-email"}
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Twitter username</div>
      @<input type="text" name="user_twitter" value="{$twitter_handle}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Organization name{if $_site.organization}, if not <strong>{$_site.organization}</strong>{/if}</div>
      <input type="text" name="user_organization_name" value="{$user.organization_name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Website</div>
      <input type="text" name="user_website" value="{$user.website}" />
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
      <div class="form-section-label">Bio</div>
      <div class="edit-form-sub-row">
        <textarea name="user_bio" style="width:500px;height:60px" id="user-bio-tinymce">{$bio_text_editor_content}</textarea>
      </div>
      <div class="form-hint">Tell us who you are.</div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">User interface language (where possible)</div>
      <select name="user_language">
{foreach from=$_languages item="lang" key="langcode"}
        {if $langcode != "zxx" && $langcode != "mul"}<option value="{$langcode}"{if $user.ui_language == $langcode} selected="selected"{/if}>{$lang.label}</option>{/if}
{/foreach}        
      </select>
    </div>
    
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Cancel" />
      <input type="submit" value="Save" />
    </div>
  </form>
  <div class="breaker"></div>
  <script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
  <script language="javascript" type="text/javascript">
  {literal}

  tinymce.init({
      selector: "#user-bio-tinymce",
      menubar: false,
      plugins: [
          "advlist autolink lists charmap print preview anchor",
          "searchreplace visualblocks code fullscreen",
          "media table contextmenu paste link wordcount"
      ],
      style_formats: [
          {title: 'Headers', items: [
              {title: 'h1', block: 'h1'},
              {title: 'h2', block: 'h2'},
              {title: 'h3', block: 'h3'},
              {title: 'h4', block: 'h4'},
              {title: 'h5', block: 'h5'},
              {title: 'h6', block: 'h6'}
          ]},

          {title: 'Blocks', items: [
              {title: 'p', block: 'p'},
              {title: 'div', block: 'div'},
              {title: 'pre', block: 'pre'}
          ]},

          {title: 'Containers', items: [
              {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
              {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
              {title: 'blockquote', block: 'blockquote', wrapper: true},
              {title: 'hgroup', block: 'hgroup', wrapper: true},
              {title: 'aside', block: 'aside', wrapper: true},
              {title: 'figure', block: 'figure', wrapper: true}
          ]}
      ],
    
      paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,p",
      toolbar: "styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
      relative_urls : false,
      convert_urls: false,
      document_base_url : sm_domain,
      skin: "smartest"

  });
  
  {/literal}

  </script>
</div>

<div id="actions-area">
  <ul class="actions-list">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="#" onclick="MODALS.load('smartest/account/password', 'Change your password'); return false;" class="right-nav-link"><i class="fa fa-lock"> </i> Change your password</a></li>
  </ul>
</div>