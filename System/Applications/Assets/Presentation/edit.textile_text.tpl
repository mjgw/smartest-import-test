<script type="text/javascript">
{literal}

var CM;

var makeAttachment = function(){
  addAttachmentTag(prompt('Please enter a name for the attachment'));  
}

var addAttachmentTag = function(attachmentName){  
  CM.replaceSelection("\n"+'{attach:'+attachmentName.toVarName()+'}'+"\n");
}

{/literal}
</script>

<form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

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
    
    <div class="special-box">You are editing a Textile Markup file. {help id="assets:textile"}Click here{/help} to learn more about how these are formatted.</div>
    
    <a class="button" href="#add-attachment" onclick="makeAttachment(); return false;">Add an attachment</a>
    
    <div class="breaker" style="height:10px"></div>
    
    <div id="text_window" class="textarea-holder">
        <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0;font-family:monospace;font-size:14px">{$textfragment_content}</textarea>
        <span class="form-hint">Editor powered by CodeMirror</span>
    </div>
        
    <div class="buttons-bar">
        {save_buttons}
    </div>
    
</form>

<script type="text/javascript">
{literal}
CM = CodeMirror.fromTextArea($('tpl_textArea'), {
    mode: "textile",
    lineWrapping: true
  });
{/literal}
</script>

<div class="breaker"></div>