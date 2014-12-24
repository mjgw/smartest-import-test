<form action="{$domain}{$section}/updateAsset" method="post" enctype="multipart/form-data">
  
  <input type="hidden" name="asset_id" value="{$asset.id}" />
  
  <div id="edit-form-layout">
    
    {foreach from=$asset._editor_parameters key="parameter_name" item="parameter"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter.label}</div>
      {if $parameter.datatype == 'SM_DATATYPE_BOOLEAN'}
      {capture name="name" assign="name"}params[{$parameter_name}]{/capture}
      {capture name="param_id" assign="param_id"}asset-parameter-{$parameter_name}{/capture}
      {boolean name=$name id=$param_id value=$parameter.value}
      {else}
      {if $parameter.has_options}
      <select name="params[{$parameter_name}]">
        {if !$parameter.required}<option value=""></option>{/if}
      {foreach from=$parameter.options item="opt" key="key"}
        <option value="{$key}"{if $parameter.value == $key} selected="selected"{/if}>{$opt}</option>
      {/foreach}
      </select>
      {else}
      <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" />
      {/if}
      {/if}
    </div>
    {/foreach}
  
    <div class="edit-form-row">
      <div class="buttons-bar">
        {* <input type="submit" value="Save Changes" />
        <input type="button" onclick="cancelForm();" value="Done" /> *}
        {save_buttons}
      </div>
    </div>
  
  </div>
  
</form>