<script type="text/javascript">

var customAssetClassName = false;
var url = '{$domain}ajax:websitemanager/loadAssetGroupDropdownForNewPlaceholderForm';
var typesListUrl = '{$domain}ajax:websitemanager/getAssetTypesListForPlaceholderType';

{literal}

document.observe('dom:loaded', function(){

$('type-select').observe('change', function(){
  if($('type-select').value){
    $('primary-ajax-loader').show();
    new Ajax.Updater('placeholder-group-menu-space', url, {
      parameters: {placeholder_type: $('type-select').value},
      evalScripts: true
    });
    new Ajax.Updater('types-list', typesListUrl, {
      parameters: {placeholder_type: $('type-select').value},
      onSuccess: function(){
        $('primary-ajax-loader').hide();
      }
    });
    $('save-button').disabled = false;
  }else{
    $('save-button').disabled = true;
    $('types-list').update('Please choose a content type for this placeholder');
  }
});

$('new-placeholder-form').observe('submit', function(e){
    
    if($('type-select').value == ''){
        $('type-select').addClassName('error');
        e.stop();
    }
    
});

{/literal}

{if !$name}

{literal}

$('new-placeholder-form').observe('submit', function(e){
    
    if($('placeholder_name').value == 'Unnamed placeholder' || $('placeholder_name').value == 'unnamed_placeholder' || $('placeholder_name').value == ''){
        $('placeholder_name').addClassName('error');
        e.stop();
    }
    
});

{/literal}

{/if}

{literal}

});

{/literal}

</script>

<div id="work-area">

<h3>Add a new placeholder</h3>

<form action="{$domain}{$section}/insertPlaceholder" method="post" style="margin:0px" id="new-placeholder-form">
    
  {* <input type="hidden" name="placeholder_name" value="{$name}" />
  <input type="hidden" name="placeholder_type" value="{$selected_type}" />
  <input type="hidden" name="placeholder_label" value="{$label}" /> *}
  
  {if $name}
      <div class="edit-form-row">
        <div class="form-section-label">Name </div>
       {$name}<input type="hidden" name="placeholder_name" value="{$name}" />
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Template tag </div>
        <code>&lt;?sm:placeholder name="{$name}":?&gt;</code>
      </div>
  {else}
      <div class="edit-form-row">
        <div class="form-section-label">Name </div>
        <input type="text" name="placeholder_name" id="placeholder_name" value="unnamed_placeholder" class="unfilled" />
          <div class="form-hint">If you don't enter a tag name, one will be generated for you.</div>
      </div>
  {/if}

  <div class="edit-form-row">
    <div class="form-section-label">Label (optional):</div>
    <input type="text" name="placeholder_label" id="placeholder_label" value="{$label}" {if !$name}onkeyup="updateAssetClassName();"{/if} />
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Content type:</div>
      <select name="placeholder_type" id="type-select">
        <option value="">Choose...</option>
        {foreach from=$types item="type"}
        <option value="{$type.id}"{if $type.id == $selected_type || (!$selected_type && $suggested_type == $type.id)} selected="selected"{/if}>{$type.label}{if !$selected_type && $suggested_type == $type.id && $type_suggestion_automatic} (Automatically suggested){/if}</option>
        {/foreach}
      </select>
      <div class="form-hint"><span id="types-list">{if $type_suggestion_automatic}{$types_list}{else}Please choose a content type for this placeholder{/if}</span></div>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Limit to a file group?</div>
    <div id="placeholder-group-menu-space">
    {load_interface file="placeholder_groups_dropdown.tpl"}
    </div>
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" value="Save new placeholder" id="save-button"{if !$type_suggestion_automatic} disabled="disabled"{/if} />
    </div>
  </div>
</form>

</div>