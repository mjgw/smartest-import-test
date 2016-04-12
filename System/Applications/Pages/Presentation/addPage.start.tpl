  <h3>Add a new page</h3>
  
  <ol class="stages-indicator">
    <li class="label">Stage:</li>
    <li class="current"><span class="stage-number">1</span> Enter basic page details</li>
    <li><span class="stage-number">2</span> Add metadata and content</li>
    <li><span class="stage-number">3</span> Check &amp; confirm</li>
  </ol>
  
  <form action="{$domain}smartest/page/new" method="post">
    
    {if $parent_page}<input type="hidden" name="page_parent" value="{$parent_page.id}" />{/if}
    <input type="hidden" name="stage" value="2">
    <input type="hidden" name="form_submitted" value="1" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Title</div>
  	  <input type="text" name="page_title" id="page_title" value="Untitled Page" />
  	  {literal}<script type="text/javascript">var titleChanged=false;$('page_title').observe('change', function(){titleChanged=true});</script>{/literal}
  	</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Type</div>
      <select name="page_type" id="page-type-selector">
        <option value="NORMAL" selected="selected">Regular Web-page</option>
        <option value="ITEMCLASS">Item Meta-page</option>
        {* <option value="TAG">Tag list-page</option> *}
      </select> {help id="websitemanager:metapages"}What are meta-pages?{/help}
    </div>
  	
    <div style="display:none" id="model-selector">
      <div class="edit-form-row">
  	    <div class="form-section-label">Select a Model</div>
  	    <select name="page_model">
  	      {foreach from=$models item="model"}
  	      <option value="{$model.id}">{$model.plural_name}</option>
  	      {/foreach}
  	    </select> {help id="datamanager:models"}What are models?{/help}
  	  </div>
    </div>
    
    <script type="text/javascript">
      $('page-type-selector').observe('change', function(e){ldelim}
        
        var element = Event.element(e);
        
        if(element.value == 'ITEMCLASS'){ldelim}
          $('model-selector').show();
        {rdelim}else{ldelim}
          $('model-selector').hide();
        {rdelim}
        
      {rdelim});
    </script>
    
    <div class="edit-form-row">
      <div class="form-section-label">Position in your site hierarchy</div>
  	  {if $parent_page}
  	  Child of page "{$parent_page.title}"
  	  {else}
  	  <select name="page_parent">
{foreach from=$parent_pages item="available_parent"}
        <option value="{$available_parent.info.id}">+{section name="dashes" loop=$available_parent.treeLevel}-{/section} Child of page "{$available_parent.info.title}"</option>
{/foreach}
  	  </select>
  	  {/if}
  	</div>
    
    <div class="edit-form-row">
      <div class="buttons-bar">
        <input type="submit" value="Next &gt;&gt;" />
      </div>
    </div>
    
  </form>