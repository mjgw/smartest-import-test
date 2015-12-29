<div id="work-area">

{load_interface file="edit_tabs.tpl"}

<h3>Edit {$item._model.name|lower}: <span class="light">{$item.editor_name}</span></h3>

{if $item.deleted}<div class="warning">Warning: This {$item._model.name|strtolower} is currently in the trash.</div>{/if}

<div class="instruction">You are editing the draft property values of the {$item._model.name|strtolower} &quot;<strong>{$item.name}</strong>&quot; <a href="{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}" class="button small">Back to {$item._model.plural_name|lower}</a></div>

{if $model_type == 'SM_ITEMCLASS_MT1_SUB_MODEL'}
<div class="instruction">This <strong>{$item._model|lower}</strong> is attached to the {$parent_model.name|lower} &ldquo;{$parent_item.name}&rdquo; <a href="{$parent_item.action_url}" class="button">Edit {$parent_model.name|lower}</a> <a href="{$domain}datamanager/getSubModelItems?item_id={$parent_item.id}&amp;sub_model_id={$item._model.id}" class="button">See all {$item._model.plural_name|lower} for this {$parent_model.name|lower}</a></div>
{/if}

<div id="sets" class="special-box">
     Sets: {if count($sets)}{foreach from=$sets item="set"}<a href="{$domain}sets/previewSet?set_id={$set.id}">{$set.label}</a> <a href="{$domain}sets/transferSingleItem?item_id={$item.id}&amp;set_id={$set.id}&amp;transferAction=remove&amp;returnTo=editItem" class="button">remove</a> {/foreach}{else}<em style="color:#666">None</em> <a href="{$domain}sets/addSet?class_id={$item._model.id}&amp;add_item={$item.id}" class="button">Create one</a>{/if}
{if count($possible_sets)}
       <div>
         <form action="{$domain}sets/transferSingleItem" method="post">
           <input type="hidden" name="item_id" value="{$item.id}" />
           <input type="hidden" name="transferAction" value="add" />
           <input type="hidden" name="returnTo" value="editItem" />
           Add this item to set:
           <select name="set_id">
{foreach from=$possible_sets item="possible_set"}
             <option value="{$possible_set.id}">{$possible_set.label}</option>
{/foreach}
           </select>
           <input type="submit" value="Go" />
         </form>
       </div>
{/if}
</div>

{if !count($metapages)}
<div class="warning">No {help id="websitemanager:metapages"}meta-pages{/help} have been created for displaying {$item._model.plural_name|strtolower}. {$item._model.plural_name} will only be visible in lists on other pages.</div>
{/if}

{if $item.has_metapage && count($metapages)}

<div class="special-box">
  
  {if $item.public == 'TRUE'}
  The primary web address for this {$item._model.name|strtolower} is <a href="{$item.absolute_url}" target="_blank">{$item.absolute_url}</a>
  {else}
  The primary web address for this {$item._model.name|strtolower} will be <strong>{$item.absolute_url}</strong>
  {/if} <a href="{$domain}websitemanager/editPage?page_id={$item._meta_page.webid}&amp;item_id={$item.id}#urls" class="button small">Edit URL structure</a>
  
</div>

{/if}

<form action="{$domain}{$section}/updateItem" enctype="multipart/form-data" method="post" id="edit-item-form">

<input type="hidden" name="class_id" value="{$item._model.id}" />
<input type="hidden" name="item_id" value="{$item.id}" />
{if $request_parameters.page_id}<input type="hidden" name="page_id" value="{$request_parameters.page_id}" />{/if}
<input type="hidden" name="nextAction" id="next-action" value="" />
<input type="hidden" name="property_id" id="property-id" value="" />

{if $request_parameters.from}<input type="hidden" name="from" value="{$smarty.get.from}" />{/if}

{if $item._model.item_name_field_visible || count($metapages)}
<div class="edit-form-row">
  <div class="form-section-label">{$item._model.name} {$item._model.item_name_field_name}</div>
  <input type="text" name="item_name" value="{$item.editor_name|escape_double_quotes}" />
</div>
{/if}

{if $item.has_metapage || $allow_edit_item_slug}

<div class="edit-form-row{if $allow_edit_item_slug && $item.public == "TRUE" && count($metapages)} warning{/if}">
  <div class="form-section-label">{$item._model.name} short name (Used in links and URLS)</div>
  {if $allow_edit_item_slug}
  <input type="text" name="item_slug" value="{$item.slug}" /><div class="form-hint">Numbers, lowercase letters and hyphens only, please</div>
  {else}
  {$item.slug}
  {/if}
  {if $allow_edit_item_slug && $item.public == "TRUE" && count($metapages)}<p style="clear:both">Warning: This {$item._model.name|strtolower} is live. Editing its short name may cause links to it to break.</p>{/if}
</div>

{/if}

<div class="edit-form-row">
  <div class="form-section-label">Current status</div>
  {if $item.public == "TRUE"}
    Live <a class="button" href="#publish" onclick="$('sm-form-submit-action').value='publish';$('edit-item-form').submit();return false;">Re-Publish</a>&nbsp;<a class="button" href="#un-publish" onclick="saveItemData(function(){ldelim}window.location='{$domain}{$section}/unpublishItem?item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}';{rdelim});return false;">Un-publish</a>
  {else}
    Not live <a class="button" href="#publish" onclick="saveItemData(function(){ldelim}window.location='{$domain}{$section}/publishItem?item_id={$item.id}';{rdelim});return false;">Publish</a>
  {/if}
</div>

{foreach from=$item._editable_properties key="pid" item="property"}
<div class="edit-form-row">
  <div class="form-section-label">{if $property.required == 'TRUE'}<strong>{/if}{$property.name}{* ({$property.varname}) *}{if $property.required == 'TRUE'}</strong> *{/if}{if $can_edit_properties}<a style="float:left" title="Edit this property" href="{$domain}datamanager/editItemClassProperty?from=item_edit&amp;item_id={$item.id}&amp;itemproperty_id={$property.id}" class="clickable-icon"><i class="fa fa-cog"></i></a>{/if}</div>
  {item_field property=$property value=$item[$pid]} {* <a href="{$domain}test:datamanager/ipv?item_id={$item.id}&amp;property_id={$property.id}" class="button small">Test</a> *}
</div>
{/foreach}

  <div class="edit-form-row">
    <div class="form-section-label">Tags</div>
    <div class="edit-form-sub-row">
      <ul class="checkbox-array-list" id="item-tags-list">
{foreach from=$item.tags item="tag"}
        <li data-tagid="{$tag.id}"><label>{$tag.label} <a href="#remove-tag" class="tag-icon-button delete-tag"><i class="fa fa-times"></i></a></label></li>
{/foreach}
      </ul>
      <span class="null-notice" id="no-tags-notice"{if count($item.tags)} style="display:none"{/if}>No tags attached to this item</span>
      <div class="v-spacer half"></div>
      <input type="text" name="item_add_tag" value="Add a tag..." id="item-add-tag-textbox" class="unfilled" />
      <div class="autocomplete" id="tags-autocomplete"></div>
    </div>
    
    <script type="text/javascript">
    
    var itemId = {$item.id};
    
    {literal}
    
    var tagsInUse = {};
    
    var removeTagFromClick = function(evt){
      
      evt.stop();
      var a = Event.element(evt);
      var li = a.up(2);
      var tagId = li.readAttribute('data-tagid');
      
      if(tagsInUse.hasOwnProperty('tag_'+tagId)){
        
        // remove tag by ID
        new Ajax.Request(sm_domain+'ajax:datamanager/unTagItem', {
        
          parameters: 'tag_id='+tagId+'&item_id='+itemId,
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
    
    $$('#item-tags-list li').each(function(li){
      var tkey = 'tag_'+li.readAttribute('data-tagid');
      tagsInUse[tkey] = true;
    });
    
    $$('#item-tags-list li label a.tag-icon-button.delete-tag').each(function(a){
      a.observe('click', removeTagFromClick);
    });
    
    $('item-add-tag-textbox').observe('focus', function(){
        if(($('item-add-tag-textbox').getValue() == 'Add a tag...') || $('item-add-tag-textbox').getValue() == ''){
            $('item-add-tag-textbox').removeClassName('unfilled');
            $('item-add-tag-textbox').setValue('');
        }
    });
    
    $('item-add-tag-textbox').observe('blur', function(){
        if(($('item-add-tag-textbox').getValue() == 'Add a tag...') || $('item-add-tag-textbox').getValue() == ''){
            $('item-add-tag-textbox').addClassName('unfilled');
            $('item-add-tag-textbox').setValue('Add a tag...');
        }
    });
    
    new Ajax.Autocompleter('item-add-tag-textbox', "tags-autocomplete", sm_domain+"ajax:settings/tagsAutoComplete", {
      
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
              
              new Ajax.Request(sm_domain+'ajax:datamanager/tagItem', {
          
                parameters: 'tag_id='+newTag.id+'&item_id='+itemId,
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
          
                  $('item-tags-list').appendChild(tag_li);
          
                  if($('no-tags-notice').visible()){
                    $('no-tags-notice').hide();
                  }
            
                  var tkey = 'tag_'+newTag.id;
                  tagsInUse[tkey] = true;
              
                  $('item-add-tag-textbox').value = "";
                  $('item-add-tag-textbox').blur();
          
                }
           
              });
              
            }
            
          })
          
        }else{
          
          $('item-add-tag-textbox').value = "";
          $('item-add-tag-textbox').blur();
          
          if(tagsInUse.hasOwnProperty('tag_'+tagId)){
          
            // That tag is already in use here
          
          }else{
          
            new Ajax.Request(sm_domain+'ajax:datamanager/tagItem', {
          
              parameters: 'tag_id='+tagName+'&item_id='+itemId,
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
            
                $('item-tags-list').appendChild(tag_li);
            
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
  <div class="form-section-label">Language</div>
  <select name="item_language">
{foreach from=$_languages item="lang" key="langcode"}
    <option value="{$langcode}"{if $item.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
{/foreach}
  </select>
</div>

{if count($metapages)}
<div class="edit-form-row">
  <div class="form-section-label">Meta-Page</div>
  <select name="item_metapage_id">
    {if $item._model.default_metapage_id}<option value="0">Model Default</option>{/if}
    {foreach from=$metapages item="page"}
    <option value="{$page.id}"{if $item.metapage_id == $page.id} selected="selected"{/if}>{$page.title}</option>
    {/foreach}
  </select>
</div>
{/if}

<div class="edit-form-row">
  <div class="form-section-label">Search Terms</div>
  <textarea name="item_search_field" rows="3" cols="20" style="width:350px;height:60px">{$item.search_field}</textarea>
</div>

<div class="edit-form-row">
  <div class="buttons-bar">
    <!--<a class="button" href="#save" onclick="saveItemData();return false;">Save</a>-->
    {url_for assign="publish_action"}@publish_item?item_id={$item.id}{/url_for}
    {save_buttons publish_action=$publish_action}
  </div>
</div>

</form>

<script type="text/javascript">
{literal}
var saveItemData = function(callbackFunction){
  $('primary-ajax-loader').show();
  $('edit-item-form').request({
    onSuccess: function(){
      $('primary-ajax-loader').hide();
      if(callbackFunction && typeof callbackFunction == 'function'){
        callbackFunction();
      }
    }
  });
}

$('form-save-button').observe('click', function(e){
  e.stop();
  saveItemData();
});

{/literal}
</script>

</div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>This {$item._model.name}</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/itemInfo?item_id={$item.id}', '{$item._model.name} info');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" />&nbsp;About this {$item._model.name}</a></li>
    {if $model_type == 'SM_ITEMCLASS_MT1_SUB_MODEL'}<li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/editItem?item_id={$parent_item.id}';"><img src="{$domain}Resources/Icons/package_small.png" alt="" />&nbsp;Back to {$parent_model.name}</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/lock_open.png" border="0" />&nbsp;Release for others to edit</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/approveItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Approve changes</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTodoItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Assign To-do</a></li>
    {if $default_metapage_id}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/preview?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/eye.png" border="0" />&nbsp;Preview it</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" />&nbsp;Publish it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/duplicateItem?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_white_copy.png" border="0" />&nbsp;Duplicate it</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/toggleItemArchived?item_id={$item.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" style="width:16px;height:16px" border="0" />&nbsp;{if $item.is_archived}Un-archive this {$item._model.name}{else}Archive this {$item._model.name}{/if}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item.itemclass_id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" />&nbsp;Finish editing for now</a></li>
  </ul>
  
  <ul class="actions-list">
    <li><b>{$item._model.name} Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$item._model.id}';" class="right-nav-link">Back to {$item._model.plural_name}</a></li>
    {if $model_type == 'SM_ITEMCLASS_MT1_SUB_MODEL'}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassMembers?class_id={$parent_model.id}';" class="right-nav-link">Back to {$parent_model.plural_name}</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$item._model.id}';" class="right-nav-link">New {$item._model.name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$item._model.id}';" class="right-nav-link">Create a new set of {$item._model.plural_name}</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$item._model.id}';" class="right-nav-link">Edit the properties of this model</a></li>
  </ul>

  <ul class="actions-list">
    <li><span style="color:#999">Recently edited {$item._model.plural_name|strtolower}</span></li>
    {foreach from=$recent_items item="recent_item"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
    {/foreach}
  </ul>
  
</div>
