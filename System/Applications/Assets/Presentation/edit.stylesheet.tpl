{if $allow_save}<form action="{$domain}{$section}/updateAsset" method="post" name="newCss" enctype="multipart/form-data">{/if}
  
  <input type="hidden" name="asset_type" value="{$asset.type}" />
  <input type="hidden" name="asset_id" value="{$asset.id}" />
  
  <div class="special-box">
    <span class="heading">Language</span>
    <select name="asset_language">
      <option value="">{$lang.label}</option>
  {foreach from=$_languages item="lang" key="langcode"}
      <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
  {/foreach}
    </select>
  </div>
    
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
    <div class="form-section-label">File contents</div>
    <div class="textarea-holder">
      <textarea name="asset_content" id="tpl_textArea" wrap="virtual" >{$textfragment_content}</textarea>
      <span class="form-hint">Editor powered by CodeMirror</span>
    </div>
  </div>
  
  <div class="buttons-bar">
    {if $allow_save}
    {save_buttons}
    {else}
    <input type="button" onclick="cancelForm();" value="Cancel" />
    {/if}
  </div>
  
  <script type="text/javascript">

  var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {ldelim}
      lineNumbers: true,
      mode: "css",
  {if !$allow_save}    readOnly: true,
  {/if}
      lineWrapping: true
    {rdelim});
  
  </script>
  
{if $allow_save}</form>{/if}