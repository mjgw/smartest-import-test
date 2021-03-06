{if $allow_save}<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">{/if}

    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <input type="hidden" name="asset_type" value="{$asset.type}" />
    
    <div class="special-box">
      <span class="heading">Language</span>
      <select name="asset_language">
        <option value="">{$lang.label}</option>
    {foreach from=$_languages item="lang" key="langcode"}
        <option value="{$langcode}"{if $asset.language == $langcode} selected="selected"{/if}>{$lang.label}</option>
    {/foreach}
      </select>
    </div>
    
    {foreach from=$asset.default_parameters key="parameter_name" item="parameter_value"}
    <div class="edit-form-row">
      <div class="form-section-label">{$parameter_name}</div>
      <input type="text" name="params[{$parameter_name}]" value="{$parameter_value}" style="width:250px" />
    </div>
    {/foreach}
    
    <div id="textarea-holder" style="width:100%">
      <div class="textarea-holder">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$textfragment_content}</textarea>
        <span class="form-hint">Editor powered by CodeMirror</span>
      </div>
      <div class="buttons-bar">
        {if $allow_save}
        {save_buttons}
        {else}
        <input type="button" onclick="cancelForm();" value="Cancel" />
        {/if}
      </div>
    <div>
      
    <script type="text/javascript">
    {literal}
    var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {
        lineNumbers: true,
        mode: "xml",
        lineWrapping: true
      });
    {/literal}
    </script>
        
{if $allow_save}</form>{/if}