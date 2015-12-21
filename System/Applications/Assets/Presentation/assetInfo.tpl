<script type="text/javascript">
  var asset_id = {$asset.id};
  var asset_label = '{$asset.label|escape:'quotes'}';
</script>

<div id="work-area">
    {load_interface file="edit_asset_tabs.tpl"}
    
    <h3>{$_l10n_action_strings.title}</h3>
    
    <input type="hidden" name="asset_id" value="{$asset.id}" />
    
    <table cellspacing="1" border="0" class="info-table">
      <tr>
        <td style="width:170px;background-color:#fff" valign="middle" class="field-name">{$_l10n_action_strings.field_names.name}:</td>
        <td>
          <p class="editable" id="asset-label">{$asset.label}</p>
          <script type="text/javascript">
          new Ajax.InPlaceEditor('asset-label', sm_domain+'ajax:assets/setAssetLabelFromInPlaceEditField', {ldelim}
            callback: function(form, value) {ldelim}
              return 'asset_id={$asset.id}&new_label='+encodeURIComponent(value);
            {rdelim},
            highlightColor: '#ffffff',
            hoverClassName: 'editable-hover',
            savingClassName: 'editable-saving'
          {rdelim});
          </script>
        </td>
      </tr>
      {if !$asset.is_external}
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">{$_l10n_action_strings.field_names.file_path}:</td>
        <td><code>{$asset.full_path}</code></td>
      </tr>
      {/if}
      {if $asset.is_web_accessible}
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">Direct URL</td>
        <td><code>{$asset.absolute_uri}</code></td>
      </tr>
      {else}
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">Public download URL</td>
        <td><code>{$asset.download_uri}</code></td>
      </tr>
      {/if}
      {if !$asset.is_external}
      <tr>
        <td class="field-name">Size:</td>
        <td>{$asset.size}{if $asset.is_image}, ({$asset.width} x {$asset.height} {$_l10n_action_strings.general.pixels}){/if}</td>
      </tr>
      {/if}
      <tr>
        <td class="field-name">Type:</td>
        <td><a href="{$domain}{$section}/getAssetTypeMembers?asset_type={$asset.type}">{$asset.type_info.label}</a> <span style="color:#666">({$asset.type})</span></td>
      </tr>
      {if $asset.created > 0}
      <tr>
        <td class="field-name">{if $asset.type_info.storage.type == 'file'}Uploaded{else}Created{/if}:</td>
        <td>{$asset.created|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      {if $asset.modified > 0}
      <tr>
        <td class="field-name">Modified:</td>
        <td>{$asset.modified|date_format:"%A %B %e, %Y, %l:%M%p"}</span></td>
      </tr>
      {/if}
      {if $asset.is_binary_image}
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">Preview</td>
        <td>
          <img src="{$asset.image.constrain_400x400.web_path}" alt="{$asset.label}" style="width:{$asset.image.constrain_200x200.width};height:{$asset.image.constrain_200x200.height}px" id="thumbnail-{$asset.id}">
        </td>
      </tr>
      {else}
      <tr>
        <td style="width:150px;background-color:#fff" class="field-name">Thumbnail image file:</td>
        <td>
          {image_select for="asset_thumbnail" name="asset_thumbnail_image" id="asset-thumbnail" value=$asset.thumbnail_image changehook="updateThumbnailImage"}
          <br /><span class="form-hint">An image that is used to represent this file when a static image is necessary in templates.</span>
          <script type="text/javascript">
          {literal}
          var updateThumbnailImage = function(imageId){
            
              var url = sm_domain+'ajax:assets/setAssetThumbnailId';
              $('primary-ajax-loader').show();
              
              new Ajax.Request(url, {
                method: 'post',
                parameters: {'asset_id': asset_id, 'thumbnail_id': imageId},
                onSuccess: function(){
                   $('primary-ajax-loader').hide();
                }
              });
              
          }
          {/literal}
          </script>
        </td>
      </tr>
      {/if}
      
      <tr>
        <td valign="top" class="field-name">Tags</td>
        <td>
          
        <ul class="checkbox-array-list" id="asset-tags-list">
  {foreach from=$asset_tags item="tag"}
          <li data-tagid="{$tag.id}"><label>{$tag.label} <a href="#remove-tag" class="tag-icon-button delete-tag"><i class="fa fa-times"></i></a></label></li>
  {/foreach}
        </ul>
        
        <span class="null-notice" id="no-tags-notice"{if count($asset_tags)} style="display:none"{/if}>No tags attached to this page</span>
        <div class="v-spacer half"></div>
        <input type="text" name="page_add_tag" value="Add a tag..." id="asset-add-tag-textbox" class="unfilled" />
        <div class="autocomplete" id="tags-autocomplete"></div>
    
        <script type="text/javascript">
        
          var assetId = {$asset.id};
        
          {literal}
        
          var tagsInUse = {};
        
          var removeTagFromClick = function(evt){
          
            evt.stop();
            var a = Event.element(evt);
            var li = a.up(2);
            var tagId = li.readAttribute('data-tagid');
          
            if(tagsInUse.hasOwnProperty('tag_'+tagId)){
            
              // remove tag by ID
              new Ajax.Request(sm_domain+'ajax:assets/unTagAsset', {
            
                parameters: 'tag_id='+tagId+'&asset_id='+assetId,
                onSuccess: function(response) {
                  // hide tag
                  li.fade({
                    duration: 0.3,
                    afterfinish: function(){
                        li.remove();
                        // console.log(tagsInUse.size());
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
        
          $$('#asset-tags-list li').each(function(li){
            var tkey = 'tag_'+li.readAttribute('data-tagid');
            tagsInUse[tkey] = true;
          });
        
          $$('#asset-tags-list li label a.tag-icon-button.delete-tag').each(function(a){
            a.observe('click', removeTagFromClick);
          });
        
          $('asset-add-tag-textbox').observe('focus', function(){
              if(($('asset-add-tag-textbox').getValue() == 'Add a tag...') || $('asset-add-tag-textbox').getValue() == ''){
                  $('asset-add-tag-textbox').removeClassName('unfilled');
                  $('asset-add-tag-textbox').setValue('');
              }
          });
        
          $('asset-add-tag-textbox').observe('blur', function(){
              if(($('asset-add-tag-textbox').getValue() == 'Add a tag...') || $('asset-add-tag-textbox').getValue() == ''){
                  $('asset-add-tag-textbox').addClassName('unfilled');
                  $('asset-add-tag-textbox').setValue('Add a tag...');
              }
          });
        
          new Ajax.Autocompleter('asset-add-tag-textbox', "tags-autocomplete", sm_domain+"ajax:settings/tagsAutoComplete", {
          
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
                    // console.log(newTag);
                  
                    new Ajax.Request(sm_domain+'ajax:assets/tagAsset', {
              
                      parameters: 'tag_id='+newTag.id+'&asset_id='+assetId,
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
              
                        $('asset-tags-list').appendChild(tag_li);
              
                        if($('no-tags-notice').visible()){
                          $('no-tags-notice').hide();
                        }
                
                        var tkey = 'tag_'+newTag.id;
                        tagsInUse[tkey] = true;
                  
                        $('asset-add-tag-textbox').value = "";
                        $('asset-add-tag-textbox').blur();
              
                      }
               
                    });
                  
                  }
                
                })
              
              }else{
              
                $('asset-add-tag-textbox').value = "";
                $('asset-add-tag-textbox').blur();
              
                if(tagsInUse.hasOwnProperty('tag_'+tagId)){
              
                  // That tag is already in use here
              
                }else{
              
                  new Ajax.Request(sm_domain+'ajax:assets/tagAsset', {
              
                    parameters: 'tag_id='+tagName+'&asset_id='+assetId,
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
                
                      $('asset-tags-list').appendChild(tag_li);
                
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
    
        </td>
      </tr>
      
      <tr>
        <td valign="middle" class="field-name">Owner:</td>
        <td>
          <select name="asset_user_id" id="asset-owner" style="width:300px">
  {foreach from=$potential_owners item="p_owner"}
            <option value="{$p_owner.id}"{if $asset.owner.id == $p_owner.id} selected="selected"{/if}>{$p_owner.fullname} ({$p_owner.id})</option>
  {/foreach}
          </select>
          
          <script type="text/javascript">
          {literal}
          $('asset-owner').observe('change', function(){
            $('primary-ajax-loader').show();
            var url = sm_domain+'ajax:assets/setAssetOwnerById';
            new Ajax.Request(url, {
              method: 'post',
              parameters: {'asset_id': asset_id, 'owner_id': $('asset-owner').value},
              onSuccess: function(){
                 $('primary-ajax-loader').hide();
              }
            });
          });
          {/literal}
          </script>
        </td>
      </tr>
      <tr>
        <td valign="middle" class="field-name">Language:</td>
        <td>

          <select name="asset_language" id="asset-language" style="width:300px">
        {foreach from=$_languages item="lang" key="langcode"}
            <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
        {/foreach}
          </select>
          
          <script type="text/javascript">
          {literal}
          $('asset-language').observe('change', function(){
            var url = sm_domain+'ajax:assets/setAssetLanguage';
            $('primary-ajax-loader').show();
            new Ajax.Request(url, {
              method: 'post',
              parameters: {'asset_id': asset_id, 'asset_language': $('asset-language').value},
              onSuccess: function(){
                 $('primary-ajax-loader').hide();
              }
            });
          });
          {/literal}
          </script>
          
        </td>
      </tr>
      <tr>
        <td class="field-name">Original site:</td>
        <td>{$asset.site.internal_label}</td>
      </tr>
      <tr>
        <td class="field-name">Shared with other sites:</td>
        <td>
          <input type="checkbox" id="asset-shared" name="asset_shared" value="1"{if $asset.shared==1} checked="checked"{if $asset.site_id!=$_site.id} disabled="disabled"{/if}{/if} />{if $asset.site_id!=$_site.id}<span class="form-hint">File cannot be un-shared because it belongs to a different site. To set sharing, edit the file in the site where it was created.</span>{/if}
          <script type="text/javascript">
          {literal}
          $('asset-shared').observe('click', function(){
            var url = sm_domain+'ajax:assets/setAssetShared';
            var checked = $('asset-shared').checked ? 1 : 0;
            new Ajax.Request(url, {
              method: 'post',
              parameters: {'asset_id': asset_id, 'is_shared': checked}
            });
          });
          {/literal}
          </script>
        </td>
      </tr>
      <tr>
        <td class="field-name">&nbsp;</td>
        <td>
          <a href="#open-file-notes" id="file-notes-link"><img src="{$domain}Resources/Icons/note.png"> Notes</a>
          <script type="text/javascript">
          {literal}
          $('file-notes-link').observe('click', function(e){
            MODALS.load('assets/assetCommentStream?asset_id='+asset_id, 'Notes on file: \''+asset_label+'\'');
            e.stop();
          });
          {/literal}
          </script>
        </td>
      </tr>
    </table>
  
  <div class="special-box">
    <div class="special-box-key">File groups:</div>
    {if count($groups)}{foreach from=$groups item="group"}<a href="{$domain}{$section}/browseAssetGroup?group_id={$group.id}">{$group.label}</a> <a href="{$domain}{$section}/transferSingleAsset?asset_id={$asset.id}&amp;group_id={$group.id}&amp;transferAction=remove" class="button">remove</a> {/foreach}{else}<em style="color:#666">None</em>{/if}
{if count($possible_groups)}
      <div>
        <form action="{$domain}{$section}/transferSingleAsset" method="post">
          <input type="hidden" name="asset_id" value="{$asset.id}" />
          <input type="hidden" name="transferAction" value="add" />
          Add this file to group:
          <select name="group_id">
{foreach from=$possible_groups item="possible_group"}
            <option value="{$possible_group.id}">{$possible_group.label}</option>
{/foreach}
          </select>
          <input type="submit" value="Go" />
        </form>
      </div>
{/if}
  </div>
  
  <div class="buttons-bar"><input type="button" id="done-button" value="{$_l10n_global_strings.system_wide_buttons.done}" /><script type="text/javascript">{literal}$('done-button').observe('click', cancelForm);{/literal}</script></div>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>{$_l10n_action_strings.sidebar.options_label}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?asset_type={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> {$_l10n_action_strings.sidebar.edit_file_option}</a></li>
    {if $allow_source_edit}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit This File's Source</a></li>{/if}
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This Text</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset.type_info.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset.type_info.label} files</a></li>
  </ul>
</div>