<script type="text/javascript">
{literal}

var CM;

var makeAttachment = function(){
  addAttachmentTag(prompt('Please enter a name for the attachment'));  
}

var addAttachmentTag = function(attachmentName){  
  CM.replaceSelection("\n"+'<?sm:attachment name="'+attachmentName.toVarName()+'":?>'+"\n");
}
  
/* var insertAtCaret = function (areaId, text) {
    var txtarea = document.getElementById(areaId);
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
        "ff" : (document.selection ? "ie" : false ) );
    if (br == "ie") { 
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        strPos = range.text.length;
    }
    else if (br == "ff") strPos = txtarea.selectionStart;

    var front = (txtarea.value).substring(0,strPos);  
    var back = (txtarea.value).substring(strPos,txtarea.value.length); 
    txtarea.value=front+text+back;
    strPos = strPos + text.length;
    if (br == "ie") { 
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart ('character', -txtarea.value.length);
        range.moveStart ('character', strPos);
        range.moveEnd ('character', 0);
        range.select();
    }
    else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
} */
  
{/literal}
</script>

<div id="work-area">
  
  {load_interface file="edit_asset_tabs.tpl"}
  <h3>Edit Text File Source</h3>
  <form action="{$domain}{$section}/updateAsset" method="post" name="newHtml" enctype="multipart/form-data">

    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <input type="hidden" name="asset_type" value="{$asset.type}" />
    <input type="hidden" name="filter_markup" value="0" />

      {foreach from=$asset.type_info.param item="parameter"}
      <div class="edit-form-row">
        <div class="form-section-label">{$parameter.name}</div>
        <input type="text" name="params[{$parameter.name}]" value="{$parameter.value}" style="width:250px" />
      </div>
      {/foreach}

      <div class="instruction">File you are editing: <strong>{$asset.label}</strong></div>
      
      <div class="special-box">Are you trying to include images in this text as attachments? {help id="assets:attachments"}read this guide{/help}</div>
      
      <a class="button" href="#add-attachment" onclick="makeAttachment(); return false;">Add an attachment</a>
      
      <div class="breaker" style="height:10px"></div>
      
      <div class="textarea-holder" style="width:100%">
          <textarea name="asset_content" id="tpl_textArea" wrap="virtual" style="width:100%;padding:0">{$textfragment_content}</textarea>
          <span class="form-hint">Editor powered by CodeMirror</span>
      </div>
        
      <div class="buttons-bar">
        {* <input type="submit" value="Save Changes" />
        <input type="button" onclick="cancelForm();" value="Cancel" /> *}
        <input type="hidden" name="editor" value="source" />
        {save_buttons}
      </div>

  </form>
  
  <script type="text/javascript">
  {literal}
  CM = CodeMirror.fromTextArea($('tpl_textArea'), {
      lineNumbers: true,
      mode: "htmlmixed",
      lineWrapping: true
    });
  {/literal}
  </script>
  
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} assets</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/assetInfo?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/information.png" alt=""/> About this file</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit in Rich Text Editor</a></li>
    {if $show_attachments}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/textFragmentElements?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/attach.png" alt=""/> Edit File Attachments</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/previewAsset?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview This File</a></li>
    {if $show_publish}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishTextAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Publish This Text</a></li>{/if}
  </ul>
</div>