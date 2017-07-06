<script type="text/javascript">

  /* function savePageUrlChanges(){ldelim}
    
    $('saver-gif').show();

    $('editUrl').request({ldelim}
      onComplete: function(){ldelim}
        // $('page-urls').update('');
        new Ajax.Updater('page-urls', '{$domain}ajax:websitemanager/pageUrls', {ldelim}
          parameters: {ldelim}page_id: '{$page.webid}'{if $item.id}, item_id: {$item.id}{/if}{rdelim}
        {rdelim});
        MODALS.hideViewer();
      {rdelim}
    {rdelim});

    return true;

  {rdelim} */

</script>

<h3 id="pageName">Page Details: <span class="light">{$page.static_title}{if $is_tag_page && $tag.id} (tag '{$tag.label}'){/if}</span></h3>

<form id="getForm" method="get" action="">
  <input type="hidden" name="page_id" value="{$page.id}">
  <input type="hidden" name="page_webid" value="{$page.webid}">
  <input type="hidden" name="current_url" value="{$pageurl.pageurl_url}">
</form>

<div class="instruction">Edit page meta information.</div>

{if $show_deleted_warning}
  <div class="warning" id="page-trash-warning">Warning: This page is currently in the trash. <a class="button" id="restore-page-button" href="#restore">Restore</a></div>
  <script type="text/javascript">
  var pageId = {$page.id};
  {literal}
  $('restore-page-button').observe('click', function(evt){
    evt.stop();
    new Ajax.Request(sm_domain+'ajax:websitemanager/restoreTrashedPage', {
      parameters: 'page_id='+pageId,
      onSuccess: function(response){
        $('page-trash-warning').fade({duration: 0.4});
      }
    });
  });
  {/literal}
  </script>
{/if}

<form id="updatePage" name="updatePage" action="{$domain}smartest/page/update" method="post" style="margin:0px">
  
  <input type="hidden" name="page_id" value="{$page.webid}">
  <input type="hidden" name="page_webid" value="">

<div id="edit-form-layout">
  
  <div class="edit-form-row">
    <div class="form-section-label">Type</div>
    {if $page.type == "ITEMCLASS"}Meta-page {help id="websitemanager:metapages"}What are meta-pages?{/help}{else}Regular web-page{/if}
  </div>
  
  {if $page.type == "ITEMCLASS"}
  {* <div class="edit-form-row">
    <div class="form-section-label">Data Set</div>
    &quot;{$page.set_name}&quot;
  </div> *}
  
  <div class="edit-form-row">
    <div class="form-section-label">Model</div>
    {$page.model.plural_name} <a href="{$domain}smartest/items/{$page.model.varname}" class="button small"> view all</a> {help id="datamanager:models"}What is a model?{/help}
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Title</div>
    	<input type="text" name="page_title" value="{$page.static_title}" />
    	{if $page.type == "ITEMCLASS"}
    	  <div class="form-hint">This is only visible to the public if you check "Always use page name" below.</div>
    	{else}
    	  {if !$page.title}<div>You must have a title! </div>{/if}
    	{/if}
  </div>
  
  {if $page.type == "ITEMCLASS"}
  <div class="edit-form-row">
    <div class="form-section-label">Always use page name</div>
    <input type="checkbox" name="page_force_static_title" id="page_force_static_title" value="true"{if $page.force_static_title=='1'} checked="checked"{/if} />
    <label for="page_force_static_title">{if $page.force_static_title=='1'}Un-tick this box to make this meta-page have the title of the {$page.model.name|lower} that is being displayed.{else}Tick this box to make sure this meta-page keeps the title above, instead of the {$page.model.name|lower} that is being displayed.{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Short Name</div>
    {if $allow_edit_page_name}<input type="text" name="page_name" value="{$page.name}" /><div class="form-hint">Numbers, lowercase letters and hyphens only, please</div>{else}{$page.name}{/if}
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Numeric ID</div>
    <span style="font-size:1.2em"><code>{$page.id}</code></span>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Long ID</div>
    <span style="font-size:1.2em"><code>{$page.webid}</code></span>
  </div>
  
  <div class="edit-form-row">
      <div class="form-section-label">Link code</div>
      <code>{$page.link_code}</code>
    </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Status</div>
      <div style="display:inline" class="text">
      {if $page.is_published == "TRUE"}
        <div style="display:inline" class="text">
    	    <strong>Live</strong> - Last published: {convert_timestamp format="h:i a, l jS F, Y" time=$page.last_published}{if $page.last_built}; Last built: {convert_timestamp format="h:i a, l jS F, Y" time=$page.last_built}{/if}{if $page.type == "NORMAL"}{if $page.modified}; Last modified: {$page.modified}{/if}{/if}
    	  </div>
        <div class="edit-form-sub-row">
          <a class="button" href="{$domain}{$section}/unPublishPage?page_id={$page.webid}">Un-publish</a>
          <a class="button" href="{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $page.type == "ITEMCLASS" && $page.item.id}&amp;item_id={$page.item.id}{/if}">Re-publish</a>
        </div>
    	{else}
        <div style="display:inline" class="text">
    	  {if $page.last_published == 0 }
    	  	<strong>Never Published</strong>
    	  {else}
    	    <strong>Not Published</strong>
    	  {/if}
        {if $page.type == "NORMAL" && $page.modified_raw} - Last modified: {$page.modified}{/if}
        </div>
        <div class="edit-form-sub-row">
          <a class="button" href="{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $page.type == "ITEMCLASS" && $page.item.id}&amp;item_id={$page.item.id}{/if}">Publish</a>
          {if $is_special_page}<p>This is a special page. Not being published means a part of your site's functionality may be unavailable to users.</p>{/if}
        </div>
    	{/if}</div>
  </div>
  
  {if !$is_special_page}
  
  {if $page.type == "NORMAL"}
  <div class="edit-form-row">
    <div class="form-section-label">Section</div>
    <input type="checkbox" name="page_is_section" id="page_is_section" value="true"{if $page.is_section=='1'} checked="checked"{/if} />
    <label for="page_is_section">{if $page.is_section=='1'}Un-tick this box to make this page no longer a section{else}Tick this box to make this page a section{/if}</label>
  </div>
  {/if}
  
  <div class="edit-form-row">
    <div class="form-section-label">Cache as Static HTML</div>
    {boolean name="page_cache_as_html" id="page-cache-as-html" value=$page.cache_as_html}
  </div>
  
  {if $page.cache_as_html == "TRUE"}
  <div class="edit-form-row">
    <div class="form-section-label">Cache How Often?</div>
    	<select name="page_cache_interval" style="width:300px">
    	  <option value="PERMANENT"{if $page.cache_interval=='PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
    	  <option value="MONTHLY"{if $page.cache_interval=='MONTHLY'} selected="selected"{/if}>Every Month</option>
    	  <option value="DAILY"{if $page.cache_interval=='DAILY'} selected="selected"{/if}>Every Day</option>
    	  <option value="HOURLY"{if $page.cache_interval=='HOURLY'} selected="selected"{/if}>Every Hour</option>
    	  <option value="MINUTE"{if $page.cache_interval=='MINUTE'} selected="selected"{/if}>Every Minute</option>
    	  <option value="SECOND"{if $page.cache_interval=='SECOND'} selected="selected"{/if}>Every Second</option>
    	</select>
  </div>
  {/if}
  
  
  
  {if $page.id > 0}
  <div class="edit-form-row">
    
		<div class="form-section-label-full" id="urls">Page URL(s)</div>
		
		<div class="special-box">
		
		<div id="page-urls">
		  
  	  <!--<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  	  
    	  {if $ishomepage == "true"}
      	<tr style="background-color:#{cycle values="ddd,fff"};height:20px">
      	  <td>
      		  <div style="display:inline" id="siteDomainField_0">
      		    <strong>{if $page.is_published == "TRUE" && $site.is_enabled == 1}<a href="http://{$site.domain}{$domain}" target="_blank">{/if}http://{$site.domain}{$domain}{if $page.is_published == "TRUE"}</a>{/if}</strong> (default)</div></td>
      	  <td style="width:32%">&nbsp;</td>
        </tr>
        {/if}
      
    	  {if count($page.urls)}
  	  
    	  {foreach from=$page.urls item=pageurl}
    	    {capture name="pageUrl" assign="pageUrl"}http://{$site.domain}{$domain}{$pageurl.url}{/capture}
    	  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
    	    <td>
    		    <div style="display:inline" id="siteDomainField_{$pageurl.id}">
    		      {if $pageurl.is_default == 1}<strong>{/if}{if $page.is_published == "TRUE" && ($page.type == 'NORMAL' || ($page.type == 'ITEMCLASS' && $item.public == 'TRUE')) && $site.is_enabled == 1}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}{if $pageurl.is_default == 1}</strong> (default){/if}</div></td>
    	    <td style="width:32%;text-align:right;padding:3px">
    		    <a class="button small" href="#edit-url" data-urlid="{$pageurl.id}" onclick="MODALS.load('{$section}/editPageUrl?url_id={$pageurl.id}', 'Edit page URL');return false;">Edit</a>
    		    {if !_b($ishomepage) && $pageurl.is_default != 1 && $pageurl.type != 'SM_PAGEURL_INTERNAL_FORWARD' && $pageurl.type != 'SM_PAGEURL_ITEM_FORWARD'}<a class="button small make_url_default" href="#make-default" data-urlid="{$pageurl.id}">Make default</a>{/if}
    		    <a class="button small" href="#transfer-url" onclick="MODALS.load('{$section}/transferPageUrl?url_id={$pageurl.id}', 'Transfer page URL');return false;">Transfer</a>
    		    <a class="button small delete_url" href="#delete-url" data-urlid="{$pageurl.id}" />Delete</a></td></tr> 
        {/foreach}
      
  	    {else}
  	    
    	  {/if}
  	  
    	  <tr style="background-color:#{cycle values="ddd,fff"};height:20px">
            <td colspan="2">
              <div style="display:inline" id="siteDomainField">
              {if $page.is_published == "TRUE" && $site.is_enabled == 1}<a href="http://{$site.domain}{$domain}{$page.forced_fallback_url}" target="_blank">http://{$site.domain}{$domain}{$page.forced_fallback_url|truncate:50:"..."}</a>{else}http://{$site.domain}{$domain}{$page.forced_fallback_url|truncate:100:"..."}{/if}</div></td>
      	  </tr>

    	</table>-->
        
      {load_interface file="Ajax/pageUrls.tpl"}
    	
  	</div>
  	
    <script type="text/javascript">
      
    var pageId = {$page.id};
    var pageWebId = '{$page.webid}';
    {if $item.id}var pageId = {$item.id};{/if}
    var deleteUrl = '{$domain}{$section}/deletePageUrl';
    var makeDefaultUrl = '{$domain}{$section}/setPageDefaultUrl';
    var updaterUrl = '{$domain}ajax:websitemanager/pageUrls';
    
    {literal}
    
    var addListeners = function(){
      
      // alert('adding listeners');
      
      // console.log($$('a.make_url_default').length);
      
      $$('a.delete_url').each(function(el){
        el.observe('click', function(e){
          e.stop();
          if(confirm('Are you sure you want to delete this URL?')){
            var urlId = el.readAttribute('data-urlid');
            $('urls-updating-gif').show();
            new Ajax.Request(deleteUrl, {
              parameters: {
                page_id: pageWebId,
                url: urlId
              },
              onComplete: function(){
                new Ajax.Updater('page-urls', updaterUrl, {
                  parameters: {page_id: pageId{/literal}{if $item.id}, item_id: pageId{/if}, responseTableLinks: {$link_urls.truefalse}{literal}},
                  onSuccess: function(response) {
                    setTimeout(addListeners, 30);
                  }
                });
                $('urls-updating-gif').hide();
              }
            });
          }
        });
      });
    
      $$('a.make_url_default').each(function(el){
        // console.log($(el));
        $(el).observe('click', function(e){
          // console.log('clicked');
          e.stop();
          // if(confirm('Are you sure you want to delete this URL?')){
            var urlId = el.readAttribute('data-urlid');
            $('urls-updating-gif').show();
            new Ajax.Request(makeDefaultUrl, {
              parameters: {
                page_id: pageWebId,
                url: urlId
              },
              onComplete: function(){
                new Ajax.Updater('page-urls', updaterUrl, {
                  parameters: {page_id: pageId{/literal}{if $item.id}, item_id: pageId{/if}, responseTableLinks: {$link_urls.truefalse}{literal}},
                  onSuccess: function(response) {
                    setTimeout(addListeners, 50);
                  }
                });
                $('urls-updating-gif').hide();
              }
            });
          // }
        });
      });
    
    }
    
    {/literal}
    
    addListeners();
      
    </script>
    
    <div class="v-spacer" style="height:10px"></div>
    <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none;float:right" id="urls-updating-gif" alt="" />
  	<a href="#add-url" class="button" id="new-url-button">{if count($page.urls) || $ishomepage == "true"}Add another url{else}Give this page a nicer URL{/if}</a><br />

    <script type="text/javascript">
    
    var newUrlUrl = '{$section}/addPageUrl?page_id={$page.webid}{if $page.type != "NORMAL"}&item_id={$item.id}{/if}';
    
    {literal}
    $('new-url-button').observe('click', function(e){
      e.stop();
      MODALS.load(newUrlUrl, 'Add page URL');
    });
    {/literal}
    
    </script>
    
  	</div>
	
  </div>
  
    {/if}
  
  {/if}
  
  
  
  {if $page.id == $site.holding_page_id || !$is_special_page}
    {if $page.type == "NORMAL"}
    <div class="edit-form-row">
      <div class="form-section-label">Page thumbnail image</div>
      {image_select for="page_icon" page_id=$page.id name="page_icon_image_id" id="page_icon_image_input" value=$page.thumbnail_image}
      <!--<select name="page_icon_image_id">
        <option value="">None</option>
        {foreach from=$available_icons item="icon"}
        <option value="{$icon.id}"{if $page.thumbnail_image.id == $icon.id} selected="selected"{/if}>{$icon.label} ({$icon.url})</option>
        {/foreach}
      </select>-->
      <div class="form-hint">This image can be used when referring to a page from another page.</div>
    </div>
    {/if}
  {/if}
  
  
  
  {if !$is_special_page}
  
    {if $page.type == "NORMAL"}
    <div class="edit-form-row">
      <div class="form-section-label">Tags</div>
      <div class="edit-form-sub-row">
        <ul class="checkbox-array-list" id="page-tags-list">
  {foreach from=$page_tags item="tag"}
          <li data-tagid="{$tag.id}"><label>{$tag.label} <a href="#remove-tag" class="tag-icon-button delete-tag"><i class="fa fa-times"></i></a></label></li>
  {/foreach}
        </ul>
        <span class="null-notice" id="no-tags-notice"{if count($page.tags)} style="display:none"{/if}>No tags attached to this page</span>
        <div class="v-spacer half"></div>
        <input type="text" name="page_add_tag" value="Add a tag..." id="page-add-tag-textbox" class="unfilled" />
        <div class="autocomplete" id="tags-autocomplete"></div>
      </div>
    
      <script type="text/javascript">
    
      var pageId = {$page.id};
    
      {literal}
    
      var tagsInUse = {};
    
      var removeTagFromClick = function(evt){
      
        evt.stop();
        var a = Event.element(evt);
        var li = a.up(2);
        var tagId = li.readAttribute('data-tagid');
      
        if(tagsInUse.hasOwnProperty('tag_'+tagId)){
        
          // remove tag by ID
          new Ajax.Request(sm_domain+'ajax:websitemanager/unTagPage', {
        
            parameters: 'tag_id='+tagId+'&page_id='+pageId,
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
    
      $$('#page-tags-list li').each(function(li){
        var tkey = 'tag_'+li.readAttribute('data-tagid');
        tagsInUse[tkey] = true;
      });
    
      $$('#page-tags-list li label a.tag-icon-button.delete-tag').each(function(a){
        a.observe('click', removeTagFromClick);
      });
    
      $('page-add-tag-textbox').observe('focus', function(){
          if(($('page-add-tag-textbox').getValue() == 'Add a tag...') || $('page-add-tag-textbox').getValue() == ''){
              $('page-add-tag-textbox').removeClassName('unfilled');
              $('page-add-tag-textbox').setValue('');
          }
      });
    
      $('page-add-tag-textbox').observe('blur', function(){
          if(($('page-add-tag-textbox').getValue() == 'Add a tag...') || $('page-add-tag-textbox').getValue() == ''){
              $('page-add-tag-textbox').addClassName('unfilled');
              $('page-add-tag-textbox').setValue('Add a tag...');
          }
      });
    
      new Ajax.Autocompleter('page-add-tag-textbox', "tags-autocomplete", sm_domain+"ajax:settings/tagsAutoComplete", {
      
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
              
                new Ajax.Request(sm_domain+'ajax:websitemanager/tagPage', {
          
                  parameters: 'tag_id='+newTag.id+'&page_id='+pageId,
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
          
                    $('page-tags-list').appendChild(tag_li);
          
                    if($('no-tags-notice').visible()){
                      $('no-tags-notice').hide();
                    }
            
                    var tkey = 'tag_'+newTag.id;
                    tagsInUse[tkey] = true;
              
                    $('page-add-tag-textbox').value = "";
                    $('page-add-tag-textbox').blur();
          
                  }
           
                });
              
              }
            
            })
          
          }else{
          
            $('page-add-tag-textbox').value = "";
            $('page-add-tag-textbox').blur();
          
            if(tagsInUse.hasOwnProperty('tag_'+tagId)){
          
              // That tag is already in use here
          
            }else{
          
              new Ajax.Request(sm_domain+'ajax:websitemanager/tagPage', {
          
                parameters: 'tag_id='+tagName+'&page_id='+pageId,
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
            
                  $('page-tags-list').appendChild(tag_li);
            
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
    {else}
    
    <div class="v-spacer"></div>
    
    {/if}
  
  {if !$ishomepage}
  <div class="edit-form-row">
    <div class="form-section-label">Parent Page</div>
    {$page.parent}
    <select name="page_parent">
      {foreach from=$parent_pages item="p_page"}
        {if $p_page.id != $page.id}
        <option value="{$p_page.info.id}"{if $page.parent.id == $p_page.info.id} selected="selected"{/if}>+
          {* for $foo=1 to 3}
              <li>{$foo}</li>
          {/for *}
          {section name="dashes" loop=$p_page.treeLevel}-{/section} {$p_page.info.title}</option>
        {/if}
      {/foreach}
    </select>
  </div>
  
    {if $show_parent_meta_page_property_control}
  <div class="edit-form-row">
    <div class="form-section-label">Parent meta-page item source</div>
    {if $parent_mpp_control_type == 'dropdown'}
    <select name="page_parent_data_source">
      {if $show_self_option}<option value="_SELF"{if $parent_data_source_property_id == '_SELF'} selected="selected"{/if}>This {$model.name}  ({$item.name})</option>{/if}
      {foreach from=$parent_meta_page_property_options item="parent_meta_page_property"}
      <option value="{$parent_meta_page_property.id}"{if $parent_data_source_property_id == $parent_meta_page_property.id} selected="selected"{/if}>Property: {$parent_meta_page_property.name} ({$parent_meta_page_property.selected_item_name})</option>
      {/foreach}
    </select>
    {elseif $parent_mpp_control_type == 'text'}
      {if $parent_meta_page_property == '_SELF'}
      Parent meta-page will use the same items as this page.
      {else}
      <input type="hidden" name="page_parent_data_source" value="{$parent_meta_page_property.id}" />
      Property: {$parent_meta_page_property.name} ({$parent_meta_page_property.selected_item_name})
      {/if}
    {/if}
  </div>
  
    {/if}{* show parent meta page property control *}
  
  {/if}{* if the page is not the homepage *}
  
  
  
  {if $page.type == 'NORMAL'}
  
  <div class="edit-form-row">
    <div class="form-section-label">Search terms</div>
      <textarea name="page_search_field" style="width:500px;height:60px">{$page.search_field}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Page Description</div>
      <textarea name="page_description" style="width:500px;height:60px">{$page.description}</textarea>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px">{$page.meta_description}</textarea>
  </div>
    
  <div class="edit-form-row">
      <div class="form-section-label">Meta Keywords</div>
      <textarea name="page_keywords" style="width:500px;height:100px">{$page.keywords}</textarea>
    </div>
    
  {/if}{* if the page is not a meta page *}
  
  {elseif $page.id == $site.error_page_id}
  
  <div class="edit-form-row">
    <div class="form-section-label">Meta Description</div>
      <textarea name="page_meta_description" style="width:500px;height:60px">{$page.meta_description}</textarea>
  </div>
  
  {/if}{* if the page is not a special page *}
    
    <div class="buttons-bar">
      {url_for assign="publish_action"}@publish_page?page_id={$page.webid}{if $item}page_id={$item.id}{/if}{/url_for}
      {save_buttons publish_action=$publish_action}
    </div>
  
</div>

</form>