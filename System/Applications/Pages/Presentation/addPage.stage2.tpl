  <h3>Unsaved Page: {$newPage.title}</h3>
  
  <ol class="stages-indicator">
    <li class="label">Stage: </li>
    <li><span class="stage-number">1</span> Enter basic page details</li>
    <li class="current"><span class="stage-number">2</span> Add metadata and content</li>
    <li><span class="stage-number">3</span> Check &amp; confirm</li>
  </ol>
  
  <form action="{$domain}smartest/page/new" method="post">
  
    <input type="hidden" name="page_parent" value="{$page_parent}" />
    <input type="hidden" name="form_submitted" value="1" />
    <input type="hidden" name="stage" value="3">
    
    <div id="edit-form-layout">
        
    <div class="form-section-label-full">Accessing your page</div>
    
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Please confirm the URL for your new page</div>
  	    http://{$siteInfo.domain}{$domain}<input type="text" name="page_url" id="page_url" style="width:420px" value="{if $suggested_url}{$suggested_url}{else}{$newPage.url}{/if}" />
        {if $newPage.type == "ITEMCLASS"}
        <div class="edit-form-sub-row">
  	      <input type="button" value="Insert item URL name variable" onclick="addField('page_url', 'name');" />
  	      <input type="button" value="Insert item short ID variable" onclick="addField('page_url', 'id');" />
          <input type="button" value="Insert item long ID variable" onclick="addField('page_url', 'long_id');" />
        </div>
        {/if}
  	    <div class="form-hint">You will be able to change this and add more URLs later</div>
  	  </div>
  	
    <div class="form-section-label-full">Content &amp; layout</div>
    
  	{if count($presets)}
  	  <div class="edit-form-row">
        
  	    <div class="form-section-label">Build from page preset?</div>
  	    
        <select name="page_preset_id" id="preset-chooser">
  	      <option value="NONE">No preset</option>
  	      {foreach from=$presets item="preset"}
  	      <option value="{$preset.id}"{if $selected_preset_id == $preset.id} selected="selected"{/if}>{$preset.label}</option>
  	      {/foreach}
  	    </select>
        
        <script type="text/javascript">
        {literal}
        
        $('preset-chooser').observe('change', function(){
            
          if($F('preset-chooser') == 'NONE'){
            if(!$('main-template-selection').visible()){
              new Effect.BlindDown('main-template-selection', {duration: 0.3});
            }
          }else{
            if($('main-template-selection').visible()){
              new Effect.BlindUp('main-template-selection', {duration: 0.3});
            }
          }
        
        {/literal}
        
        {if $primary_container_known}
        
        // alert($F('preset-chooser'));
        var primaryContainerId = '{$primary_container_id}';
        
        {literal}
        
        if($F('preset-chooser') == 'NONE'){
          
          if(!$('page-layout-template-selector').visible()){
            new Effect.BlindDown('page-layout-template-selector', {duration: 0.3});
          }
          
        }else{
          
          $('primary-ajax-loader').show();
          var checkPresetForLayoutContainerTemplate = new Ajax.Request(sm_domain+'ajax:websitemanager/checkPresetDefinitionForContainerId', {
            
            parameters: 'preset_id='+$F('preset-chooser')+'&container_id='+primaryContainerId,
            onSuccess: function(response){
              if(response.responseJSON.success){
                if(response.responseJSON.has_definition){
                  if($('page-layout-template-selector').visible()){
                    new Effect.BlindUp('page-layout-template-selector', {duration: 0.3});
                  }
                }else{
                  if(!$('page-layout-template-selector').visible()){
                    new Effect.BlindDown('page-layout-template-selector', {duration: 0.3});
                  }
                }
                $('primary-ajax-loader').hide();
              }
            }
          });
        
        }
        
        {/literal}
        
        {/if}
        
        {if $primary_text_placeholder_known}
        
        var primaryTextPlaceholderId = '{$primary_text_placeholder_id}';
        {literal}
        var checkPresetForMainText = new Ajax.Request(sm_domain+'ajax:websitemanager/checkPresetDefinitionForTextAssetId', {
          parameters: 'preset_id='+$F('preset-chooser')+'&text_placeholder_id='+primaryTextPlaceholderId,
          onSuccess: function(response){
            console.log(response.responseJSON);
            if(response.responseJSON.success){
              if(response.responseJSON.has_definition){
                // $('page-text-contents-holder').hide();
                // $('preset-text-asset-info').show();
              }else{
                // $('page-text-contents-holder').show();
                // $('preset-text-asset-info').hide();
              }
            }
          }
        });
        {/literal}
        
        {/if}
        
        {literal}
          
        });
        
        {/literal}
        
        </script>
        
  	  </div>
  	{/if}
    
    <div id="main-template-selection" style="clear:both;{if $hide_template_dropdown}display:none{/if}">
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Page master template</div>
        {if count($templates)}
  	    <select name="page_draft_template" id="page_draft_template">
  	      {foreach from=$templates item="template"}
  	      <option value="{$template.url}"{if $newPage.draft_template == $template.url} selected="selected"{/if}>{$template.label} ({$template.url})</option>
  	      {/foreach}
  	    </select>
        {else}
        <em style="color:#999">There are no available page master templates. To populate this list, add files to Presentation/Masters/</em>
        <input type="hidden" name="page_draft_template" value="" />
        {/if}
  	  </div>
    </div>
    
    {if $primary_container_known}
    <div id="page-layout-template-selector" style="clear:both;{if !$show_template_selector}display:none{/if}">
      <div class="edit-form-row">
        <div class="form-section-label">Page layout template</div>
        <select name="layout_template_id">
{foreach from=$layout_templates item="template"}
          <option value="{$template.id}"{if $template.id == $selected_layout_template_id} selected="selected"{/if}>{$template.label} ({$template.url})</option>
{/foreach}
        </select>
      </div>
    </div>
    {/if}
    
    {if $primary_text_placeholder_known}
    <div id="page-main-text-selector" style="clear:both;{if !$show_main_text_input}display:none{/if}">
      <div class="form-section-label">Page text</div>
      <div style="{if $show_main_text_input}display:block{else}display:none{/if}" id="page-text-contents-holder" class="edit-form-sub-row">
        
        <div id="page-text-contents-container">
          <textarea name="page_text_contents" id="page-text-contents" style="height:200px">{$text_editor_content}</textarea>
        </div>
        
        <span style="display:none" class="ui-info-inactive" id="page-text-contents-enter-later-label">To be entered later <a class="button" href="#enter-text-now" id="enter-text-now">Enter now</a></span>
        <div class="v-spacer half"></div>
        <a href="#enter-text-later" class="button" id="page-text-contents-enter-later">Enter text later</a>
        <input type="hidden" name="save_textarea_contents" value="1" id="page-text-contents-save" />
        
        <script src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>
        <script language="javascript" type="text/javascript">
        
        {literal}
        
        $('page-text-contents-enter-later').observe('click', function(e){
          e.stop();
          $('page-text-contents-container').hide();
          $('page-text-contents-enter-later').hide();
          $('page-text-contents-enter-later-label').show();
          $('page-text-contents-save').value = '';
        });
        
        $('enter-text-now').observe('click', function(e){
          e.stop();
          $('page-text-contents-container').show();
          $('page-text-contents-enter-later').show();
          $('page-text-contents-enter-later-label').hide();
          $('page-text-contents-save').value = '1';
        });
        
        tinymce.init({
            selector: "#page-text-contents",
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
            toolbar: "styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent"

        });
  
        {/literal}

        </script>
      </div>
      <span id="preset-text-asset-info" style="{if $show_main_text_input}display:none{else}display:inline{/if}">Text asset info</span>
    </div>
    {/if}
  	
    <div class="form-section-label-full">Caching</div>
    
  	<div class="edit-form-row">
      <div class="form-section-label">Cache this page</div>
      {boolean name="page_cache_as_html" id="page_cache_as_html" value=$newPage.cache_as_html changehook="toggleCache"}
      <script type="text/javascript">
      {literal}
      var toggleCache = function(state){
        if(state){
          $('cache-rebuild-freq-holder').blindDown({duration: 0.3});
        }else{
          $('cache-rebuild-freq-holder').blindUp({duration: 0.3});
        }
      }
      {/literal}
      </script>
    </div>
  	
    <div id="cache-rebuild-freq-holder">
      <div class="edit-form-row">
        <div class="form-section-label">Rebuild cached page how often?</div>
        <select name="page_cache_interval">
          <option value="PERMANENT"{if $newPage.cache_interval == 'PERMANENT'} selected="selected"{/if}>Stay in pages cache until re-published</option>
          <option value="MONTHLY"{if $newPage.cache_interval == 'MONTHLY'} selected="selected"{/if}>Monthly</option>
          <option value="DAILY"{if $newPage.cache_interval == 'DAILY'} selected="selected"{/if}>Daily</option>
          <option value="HOURLY"{if $newPage.cache_interval == 'HOURLY' || !strlen($newPage.cache_interval)} selected="selected"{/if}>Every hour</option>
          <option value="MINUTE"{if $newPage.cache_interval == 'MINUTE'} selected="selected"{/if}>Every minute</option>
          <option value="SECOND"{if $newPage.cache_interval == 'SECOND'} selected="selected"{/if}>Every second</option>
        </select>
      </div>
    </div>
    
  	{if $newPage.type == 'NORMAL'}
    
      <div class="form-section-label-full">Meta data</div>
      
    	<div class="edit-form-row">
        <div class="form-section-label">Search terms</div>
        <textarea name="page_search_field" style="width:500px;height:60px">{$newPage.search_field}</textarea>
      </div>
  	
    	<div class="edit-form-row">
        <div class="form-section-label">Page Description</div>
        <textarea name="page_description" style="width:500px;height:60px">{$newPage.meta_description}</textarea>
      </div>
  	
    	<div class="edit-form-row">
        <div class="form-section-label">Meta Description</div>
        <textarea name="page_meta_description" style="width:500px;height:60px">{$newPage.meta_description}</textarea>
      </div>
    
      <div class="edit-form-row">
        <div class="form-section-label">Meta Keywords</div>
        <textarea name="page_keywords" style="width:500px;height:60px">{$newPage.keywords}</textarea>
      </div>
    {/if}
    
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Start over" onclick="window.location='{$domain}websitemanager/addPage?page_id={$newPage.parent.webid}'" />
          <input type="submit" value="Next &gt;&gt;" />
        </div>
      </div>
      
    </div>
  </form>