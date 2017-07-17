<div id="work-area">
<h3 id="definePageProperty">Define field: <span class="light">{$field.name}</span></h3>


<form id="defineProperty" name="defineProperty" action="{$domain}{$section}/updatePagePropertyValue" method="POST" style="margin:0px">
<input type="hidden" name="page_id" value="{$page_id}">
<input type="hidden" name="field_id" value="{$field.id}">

<div id="edit-form-layout">
    
    {if $field.is_sitewide}<div class="special-box">This field is global, meaning the value you enter on any page will be the same for all pages</div>{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Field value:</div>
      
      {if $field_type == 'SM_DATATYPE_DROPDOWN_MENU'}
      <select name="field_content">
        {foreach from=$options item="option"}
        <option value="{$option.value}"{if $option.value == $value.value} selected="selected"{/if}>{$option.label}</option>
        {/foreach}
      </select>
      
      {elseif $field_type == 'SM_DATATYPE_BOOLEAN'}
      {boolean name="field_content" id="field-value" value=$value}
      
      {elseif $field_type == 'SM_DATATYPE_INTERNAL_LINK'}
      {internal_link_select name="field_content" id="field-value" value=$value}
      
      {elseif $field_type == 'SM_DATATYPE_ASSET_GALLERY'}
      <select name="field_content">
        {foreach from=$options item="option"}
        <option value="{$option.id}"{if $option.id == $value} selected="selected"{/if}>{$option.label}</option>
        {/foreach}
      </select>
      
      {elseif $field_type == 'SM_DATATYPE_RGB_COLOR'}
      {color_input name="field_content" value=$value id="field-value"}
      
      {else}
      <input type="text" name="field_content" value="{$value}" />
      {/if}
    </div>
  
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();" />
      <input type="submit" name="action" value="Save field value" />
    </div>
  
</div>

</form>

</div>