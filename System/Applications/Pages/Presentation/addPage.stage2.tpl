  <h3>Unsaved Page: {$newPage.title}</h3>
  
  <div class="instruction">Step 2 of 3: Please fill out the details below</div>
  
  <form action="{$domain}smartest/page/new" method="post">
  
    <input type="hidden" name="page_parent" value="{$page_parent}" />
    <input type="hidden" name="stage" value="3">
    
    <div id="edit-form-layout">
        
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Please confirm the URL for your new page</div>
  	    http://{$siteInfo.domain}{$domain}<input type="text" name="page_url" id="page_url" style="width:420px" value="{if $suggested_url}{$suggested_url}{else}{$newPage.url}{/if}" />
  	    {if $newPage.type == "ITEMCLASS"}<input type="button" value="&lt;&lt; Item URL Name" onclick="addField('page_url', 'name');" />{/if}
  	    {if $newPage.type == "ITEMCLASS"}<input type="button" value="&lt;&lt; Item Short ID" onclick="addField('page_url', 'id');" />{/if}
  	    <div class="form-hint">You will be able to change this and add more URLs later</div>
  	  </div>
  	
  	{if $newPage.type == "TAG"}
  	
  	<div class="edit-form-row">
  	    <div class="form-section-label">Select a Tag</div>
  	    <select name="page_tag">
  	      {foreach from=$tags item="tag"}
  	      <option value="{$tag.id}"{if $newPage.dataset_id == $tag.id} selected="selected"{/if}>{$tag.label}</option>
  	      {/foreach}
  	    </select>
  	</div>
  	
  	{/if}
  	
  	<div class="edit-form-row">
      <div class="form-section-label">Cache as Static HTML</div>
      {boolean name="page_cache_as_html" id="page_cache_as_html" value=$newPage.cache_as_html}
    </div>
  	
    <div class="edit-form-row">
      <div class="form-section-label">Cache How Often?</div>
      <select name="page_cache_interval">
        <option value="PERMANENT"{if $newPage.cache_interval == 'PERMANENT'} selected="selected"{/if}>Stay Cached Until Re-Published</option>
        <option value="MONTHLY"{if $newPage.cache_interval == 'MONTHLY'} selected="selected"{/if}>Every Month</option>
        <option value="DAILY"{if $newPage.cache_interval == 'DAILY'} selected="selected"{/if}>Every Day</option>
        <option value="HOURLY"{if $newPage.cache_interval == 'HOURLY'} selected="selected"{/if}>Every Hour</option>
        <option value="MINUTE"{if $newPage.cache_interval == 'MINUTE'} selected="selected"{/if}>Every Minute</option>
        <option value="SECOND"{if $newPage.cache_interval == 'SECOND'} selected="selected"{/if}>Every Second</option>
      </select>
    </div>
  	
  	{if count($presets)}
  	  <div class="edit-form-row">
        
  	    <div class="form-section-label">Use page preset?</div>
  	    
        <select name="page_preset_id" id="preset-chooser">
  	      <option value="NONE">No preset</option>
  	      {foreach from=$presets item="preset"}
  	      <option value="{$preset.id}"{if $selected_preset_id == $preset.id} selected="selected"{/if}>{$preset.label}</option>
  	      {/foreach}
  	    </select>
        
        <script type="text/javascript">
        {literal}
        
        $('preset-chooser').observe('change', function(){
            
          // alert($F('preset-chooser'));
          
          // alert($('main-template-selection').visible());
          
          if($F('preset-chooser') == 'NONE'){
            if(!$('main-template-selection').visible()){
              new Effect.BlindDown('main-template-selection', {duration: 0.3});
            }
          }else{
            if($('main-template-selection').visible()){
              new Effect.BlindUp('main-template-selection', {duration: 0.3});
            }
          }
          
          // if(this.value){document.getElementById('page_draft_template').disabled=true;}else{document.getElementById('page_draft_template').disabled=false;}
          
        });
        
        {/literal}
        </script>
        
  	  </div>
  	{/if}
  	
    <div class="form-section-label-full">Meta data</div>
    
    <div id="main-template-selection"{if $hide_template_dropdown} style="display:none"{/if}>
  	  <div class="edit-form-row">
  	    <div class="form-section-label">Page master template</div>
        {if count($templates)}
  	    <select name="page_draft_template" id="page_draft_template">
  	      {foreach from=$templates item="template"}
  	      <option value="{$template.url}"{if $newPage.draft_template == $template.url} selected="selected"{/if}>{$template.url}</option>
  	      {/foreach}
  	    </select>
        {else}
        <em style="color:#999">There are no available page master templates. To populate this list, add files to Presentation/Masters/</em>
        <input type="hidden" name="page_draft_template" value="" />
        {/if}
  	  </div>
    </div>
  	
  	{if $newPage.type == 'NORMAL'}
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
        <textarea name="page_keywords" style="width:500px;height:100px">{$newPage.keywords}</textarea>
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