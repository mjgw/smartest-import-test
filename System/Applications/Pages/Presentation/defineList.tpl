{literal}

<script language="javascript">

function adjustListOptions(currentListValue){
    // alert(currentListValue);
    
    if(currentListValue == 'SM_LIST_SIMPLE'){
        $('articulated_options').blindUp({duration:0.5});
        $('simple_options').blindDown({duration:0.5, delay:0.5});
    }
    
    if(currentListValue == 'SM_LIST_ARTICULATED'){
        $('simple_options').blindUp({duration:0.5});
        $('articulated_options').blindDown({duration:0.5, delay:0.5});
    }
    
    
}

</script>

{/literal}

<div id="work-area">

<h3>Define list</h3>

<div class="text" style="margin-bottom:10px">Choose a data set and templates to use in {$list_name}</div>

<form id="editForm" method="post" action="{$domain}{$section}/saveList">
  
  <input type="hidden" name="page_id" value="{$page.id}" />
  <input type="hidden" name="list_name" value="{$list_name}" />

  <div class="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Title</div>
      <input type="text" name="list_title" value="{$list.title}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Thematic image</div>
      {image_select name="list_header_image_id" id="list-header-image-id" value=$list.draft_header_image_id}
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Maximum length</div>
      <input type="text" name="list_maximum_length" value="{$list.maximum_length}" style="width:50px" />
      <div class="form-hint">Limit the list to how many items? Zero or no value means unlimited.</div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">List from</div>
      <select name="list_type" id="list-source-selector">
        <option value="SM_LIST_SIMPLE"{if $list.type == "SM_LIST_SIMPLE"} selected="selected"{/if}>Items in a set</option>
        <option value="SM_LIST_TAG"{if $list.type == "SM_LIST_TAG"} selected="selected"{/if}>Items from a tag</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Which items?</div>
      <select name="list_member_filter_id" id="list-model-selector">
{foreach from=$models item="model"}
        <option value="{$model.id}"{if $chosen_model.id == $model.id} selected="true"{/if}>{$model.plural_name}</option>
{/foreach}
      </select>
    </div>
    
    <div class="edit-form-row" id="list-set-selector-container"{if $list.type == 'SM_LIST_TAG'} style="display:none"{/if}>
      <div class="form-section-label">Data set</div>
      <select name="dataset_id" id="dataset-selector" onchange="">
        {foreach from=$sets item="set"}
        <option value="{$set.id}"{if $list.type == "SM_LIST_SIMPLE" && $list.draft_set_id == $set.id && strlen($set.id)} selected="selected"{/if} data-modelid="{$set.itemclass_id}">{$set.name} ({$set.type|lower})</option>
        {/foreach}
      </select><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="set-loader-img" style="display:none" />
    </div>
    
    <div class="edit-form-row" id="list-tag-selector-container"{if $list.type != 'SM_LIST_TAG'} style="display:none"{/if}>
      <div class="form-section-label">Tag</div>
      <select name="tag_id" onchange="">
        {foreach from=$tags item="tag"}
        <option value="{$tag.id}"{if $list.type == "SM_LIST_TAG" && $list.draft_set_id == $tag.id && strlen($tag.id)} selected="selected"{/if}>{$tag.label}</option>
        {/foreach}
      </select>
    </div>
    
    <!--<div class="edit-form-row" id="list-secondary-tag-selector-container"{if $list.type != 'SM_LIST_TAG'} style="display:none"{/if}>
      <div class="form-section-label">Tag</div>
      <select name="dataset_id" onchange="">
        {foreach from=$tags item="tag"}
        <option value="{$set.id}"{if $list.draft_set_id == $tag.id && strlen($tag.id)} selected="selected"{/if}>{$tag.label}</option>
        {/foreach}
      </select>
    </div>-->
          
    <div class="edit-form-row">
      <div class="form-section-label">Template</div>
      <select name="art_main_template" id="template-selector">
        {foreach from=$list_templates item="ct"}
        <option value="{$ct.url}" {if $main_template == $ct.url} selected="selected"{/if}>{$ct.url}</option>
        {/foreach}
        {if $can_create_template}<option value="NEW">Create new template...</option>{/if}
      </select><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="template-loader-img" style="display:none" />
    </div>
    
    <input type="hidden" name="dataset_type" value="SM_LIST_SIMPLE" />
    
    <div class="edit-form-row">
       <div class="buttons-bar">
         <input type="button" onclick="cancelForm();" value="Cancel">
         <input type="submit" value="Save" />
       </div>
    </div>

</div>

</form>

<script type="text/javascript">
{literal}  

$('list-source-selector').observe('change', function(){
  if($F('list-source-selector') == 'SM_LIST_SIMPLE'){
    $('list-set-selector-container').show();
    $('list-tag-selector-container').hide();
    // $('list-secondary-tag-selector-container').hide();
  }else{
    $('list-set-selector-container').hide();
    $('list-tag-selector-container').show();
    // $('list-secondary-tag-selector-container').show();
  }
});

$('list-model-selector').observe('change', function(){
  $('set-loader-img').show();
  $('template-loader-img').show();
  new Ajax.Updater('dataset-selector', sm_domain+'ajax:sets/listSetsByModelIdAsDropdownOptions', {
    parameters: {class_id: $F('list-model-selector')},
    onSuccess: function(){
      $('set-loader-img').hide();
    }
  });
  new Ajax.Updater('template-selector', sm_domain+'ajax:websitemanager/getListTemplatesByModel', {
    parameters: {model_id: $F('list-model-selector')},
    onSuccess: function(){
      $('template-loader-img').hide();
    }
  });
});

{/literal}  
</script>

</div>


<div id="actions-area">

<ul class="actions-list" id="non-specific-actions">
  <li><b>Options</b></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/sets'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_picture.png" border="0" alt=""> View data sets</a></li>
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/templates/SM_ASSETTYPE_COMPOUND_LIST_TEMPLATE'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Browse list templates</a></li>
</ul>

</div>