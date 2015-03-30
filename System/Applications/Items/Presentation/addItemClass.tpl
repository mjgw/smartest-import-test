<script language="javascript">

var parentModelProperties = {$first_model_property_varnames_json};

{literal}
var setPlural = true;
var setParentModelPropertyName = true;

var suggestPluralName = function (){
	if(setPlural == true){
		$('plural').value = $('modelname').value+"s";
	}
	
}

var suggestParentModelPropertyName = function(){
	if(setParentModelPropertyName == true){
		$('itemclass-parent-model-property-name').value = ($('modelname').value+"s").toVarName();
    checkParentModelPropertyName();
	}
}

function turnOffAutoPlural(){
	setPlural = false;
}

var parentModelPropertyNameUpdate = function(){
  setParentModelPropertyName = false;
  checkParentModelPropertyName();
}

var checkParentModelPropertyName = function(){
  if(parentModelProperties.indexOf($F('itemclass-parent-model-property-name').toVarName()) != -1){
    $('itemclass-parent-model-property-name').addClassName('error');
    $('itemclass-parent-model-property-name-hint').update("The value '"+$F('itemclass-parent-model-property-name')+"' is not allowed because it is already a property name of the "+$('model-'+$F('main-model-select')+'-option').innerHTML+" model.");
  }else{
    $('itemclass-parent-model-property-name').removeClassName('error');
    $('itemclass-parent-model-property-name-hint').update('');
  }
}

{/literal}
</script>

<div id="work-area">

<h3>Build a new model</h3>

{if $permissions_issue}
<div class="warning">
  <p>The following directories need to be writeable before you can save models.</p>
  <ul class="location-list">
{if !$site_om_dir_is_writable}<li><i class="fa fa-folder"></i> <code>{$site_om_dir}</code></li>{/if}
{if !$central_om_dir_is_writable}<li><i class="fa fa-folder"></i> <code>{$central_om_dir}</code></li>{/if}
{if !$cache_om_dir_is_writable}<li><i class="fa fa-folder"></i> <code>{$cache_om_dir}</code></li>{/if}
  </ul>
  {help id="desktop:permissions"}Tell me more{/help}
</div>
{/if}

<div class="special-box">Unsure about what "models" are? {help id="datamanager:models"}click here{/help} before you go any further.</div>

<form name="searchform" onsubmit="return liveSearchSubmit()" method="post" action="{$domain}{$section}/insertItemClass">
<input type="hidden" name="stage" value="2" />
    
<div class="edit-form-row">
  <div class="form-section-label">Model Name:</div>
  <input id="modelname" onkeyup="suggestPluralName(); suggestParentModelPropertyName();" type="text" name="itemclass_name" style="width:200px" /><span class="form-hint">ie "Article", "Car", "Person"</span>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Model Plural Name:</div>
  <input id="plural" onkeyup="turnOffAutoPlural()" type="text" name="itemclass_plural_name" style="width:200px" /><span class="form-hint">ie "Articles", "Cars", "People"</span>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Role</div>
  <select name="itemclass_role" id="model-role-select">
    <option value="freestanding">Freestanding model</option>
    <option value="constituent">Constituent items for another model</option>
  </select>
  <div class="form-hint">Leave this as it is if you are unsure. You can rename this field later.</div>
</div>

<div id="constituent-model-options" style="display:none">
  <div class="edit-form-row">
    <div class="form-section-label">Parent model</div>
    <select name="itemclass_parent_model" id="main-model-select">
{foreach from=$models item="parent_model"}
      <option value="{$parent_model.id}" id="model-{$parent_model.id}-option" data-varname="{$parent_model.varname}">{$parent_model.plural_name}</option>
{/foreach}
    </select>
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Relationship</div>
    <select name="itemclass_parent_model_rel" id="main-model-relationship-select">
      <option value="mt1">Many-to-one</option>
      <option value="mtm">Many-to-many</option>
    </select>
  </div>
  <div class="edit-form-row">
    <div class="form-section-label">Parent model property name</div>
    <input type="text" name="itemclass_parent_model_property_name" value="" id="itemclass-parent-model-property-name" onkeyup="parentModelPropertyNameUpdate()" />
    <div class="form-hint" id="itemclass-parent-model-property-name-hint"></div>
  </div>
</div>

{* <div class="edit-form-row">
  <div class="form-section-label">Use name field</div>
  <input id="name-field-visible" type="checkbox" name="itemclass_name_field_visible" checked="checked" value="1" /><label for="name-field-visible">Give this model a built-in "name" field</label><span class="form-hint">Leave this as it is if you are unsure. You can rename this field later.</span>
</div> *}

{if $central_om_dir_is_writable}
<div class="edit-form-row">
  <div class="form-section-label">Shared</div>
  <input id="shared" type="checkbox" name="itemclass_shared" checked="checked" value="1"{if !$site_om_dir_is_writable} disabled="disabled"{/if} /><label for="shared">Make this model available to all sites</label> {help id="desktop:multisite"}What does this mean?{/help}
</div>
{elseif $site_om_dir_is_writable && !$central_om_dir_is_writable}
<div class="edit-form-row">
  <div class="form-section-label">Shared</div>
  <input id="shared" type="checkbox" name="itemclass_shared" value="1" disabled="disabled" /><label for="shared">Make this model available to all sites</label> {help id="desktop:multisite"}What does this mean?{/help}
</div>
{/if}

<div class="special-box">
  
  <input type="checkbox" name="create_meta_page" id="create-meta-page" value="1" onchange="toggleFormAreaVisibilityBasedOnCheckbox('create-meta-page', 'extra-form-options');"{if $cmp} checked="checked"{/if} /><label for="create-meta-page">Create meta-page now for this model</label>
  
  <div style="display:{if $cmp}block{else}none{/if}" id="extra-form-options">
    <div class="edit-form-row">
      <div class="form-section-label">Meta-page template</div>
      <select name="meta_page_template">
        {foreach from=$templates item="template"}
        <option value"{$template.url}">{$template.url}</option>
        {/foreach}
      </select>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Meta-page parent</div>
      <select name="meta_page_parent">
        {foreach from=$pages item="page"}
          <option value="{$page.info.id}"{if $pageInfo.parent == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/foreach}
      </select>
    </div>
  </div>
  
</div>
    
{* <div class="edit-form-row">
  <div class="form-section-label">Model Template (optional)</div>
      <select name="itemclass_schema_id" style="width:180px">
        <option value="">None (Custom Model)</option>  
        {foreach from=$content.schemas key=key item=item }
          <option value="{$item.schema_id}">{$item.schema_name}</option>  
        {/foreach}
      </select>
</div> *}

{if $permissions_issue}
<div class="buttons-bar"><input type="button" onclick="cancelForm();" value="Cancel" /></div>
{else}
<div class="buttons-bar"><input type="submit" value="Next &gt;&gt;" /></div>
{/if}

</form>

</div>

<script type="text/javascript">
{literal}
  
  $('model-role-select').observe('change', function(e){
    if($F('model-role-select') == 'constituent'){
      $('constituent-model-options').blindDown({duration: 0.3});
    }else{
      $('constituent-model-options').blindUp({duration: 0.3});
    }
  });
  
  $('main-model-select').observe('change', function(e){
    new Ajax.Request('/ajax:datamanager/getModelPropertyVarnames?model_id='+$F('main-model-select'), {
      onSuccess: function(response){
        parentModelProperties = response.responseJSON;
        checkParentModelPropertyName();
        if(setParentModelPropertyName){
          var modelVarName = $('model-'+$F('main-model-select')+'-option').readAttribute('data-varname');
          var modelPluralName = $('model-'+$F('main-model-select')+'-option').innerHTML;
          // $('itemclass-parent-model-property-name').value = modelPluralName;
          suggestParentModelPropertyName();
        }
      }
    });
  });
  
{/literal}
</script>