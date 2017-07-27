<script language="javascript">
{literal}

function showUploader(){
	/* document.getElementById('tplUploader').style.display = 'block';
	document.getElementById('tplUploadShowButton').style.display = 'none';
	document.getElementById('editTMPL').style.display = 'none'; */
	$('editTMPL').blindUp({duration: 0.3, scaleMode: 'contents'});
	$('tplUploader').blindDown({duration: 0.3, delay:0.4, scaleMode: 'contents'});
	document.getElementById('add_type').value = 'UPLOAD';
}

function hideUploader(){
	/* document.getElementById('tplUploader').style.display = 'none';
	document.getElementById('tplUploadShowButton').style.display = 'block';
	document.getElementById('editTMPL').style.display = 'block'; */
	$('tplUploader').blindUp({duration: 0.3, scaleMode: 'contents'});
	$('editTMPL').blindDown({duration: 0.3, delay:0.4, scaleMode: 'contents'});
	document.getElementById('add_type').value = 'DIRECT';
}

{/literal}
</script>

<div id="work-area">

  <ul class="tabset">
    <li{if $method == "startSingleTemplateImport"} class="current"{/if}><a href="{$domain}templates/startSingleTemplateImport?{if $type_specified}type={$template_type.id}&amp;{/if}{if $add_to_group}add_to_group_id={$add_to_group.id}&amp;{/if}{if $blocklist_style}style_id={$blocklist_style.id}&amp;{/if}">Import template</a></li>
    <li{if $method == "addTemplate"} class="current"{/if}><a href="{$domain}templates/addTemplate?{if $type_specified}type={$template_type.id}&amp;{/if}{if $add_to_group}add_to_group_id={$add_to_group.id}&amp;{/if}{if $blocklist_style}style_id={$blocklist_style.id}&amp;{/if}">Create template</a></li>
  </ul>

<h3>{$interface_title}</h3>

{if $allow_save}

<form action="{$domain}{$section}/saveNewTemplate" method="post" name="newTemplate" enctype="multipart/form-data">

{if $add_to_group}
  <div class="special-box">This template will be added to group <strong>{$add_to_group.label}</strong></div>
  <input type="hidden" name="add_to_group_id" id="add_to_group_id" value="{$add_to_group.id}" />
{/if}

  {if $blocklist_style}<input type="hidden" name="blocklist_style_id" value="{$blocklist_style.id}" />{/if}
  
  <input type="hidden" name="add_type" id="add_type" value="DIRECT" />
  
  {* if $type_specified}
  <input type="hidden" name="template_type" value="{$template_type.id}" />
  {else *}
  <div class="special-box">
    Template type: <select name="template_type">
{foreach from=$types item="new_template_type"}
      <option value="{$new_template_type.id}"{if !$new_template_type.storage.writable} disabled="disabled"{else}{if $new_template_type.id == $template_type.id} selected="selected"{/if}{/if}>{$new_template_type.label}{if !$new_template_type.storage.writable} (directory not writable){/if}</option>
{/foreach}
    </select>
  </div>
  {* /if *}
  
  <div id="editTMPL">
  
    <div class="edit-form-row">
      <div class="form-section-label">Template name: </div>
      <input type="text" name="template_name" value="{$default_name}" />.tpl<br />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Template contents: </div>
      <div style="width:100%" class="textarea-holder">
        <textarea name="template_content" id="tpl_textArea" wrap="virtual">&lt;!-- Created by {$_user.full_name} --&gt;</textarea>
      </div>
    </div>
  
    <div id="tplUploadShowButton">or, alternatively, <a href="javascript:showUploader();" class="button small">upload a file</a>.</div>
  
  </div>
  
  <div style="display:none" class="special-box" id="tplUploader">
    
    <label for="template_upload">Upload file: </label>
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <input type="file" id="template_upload" name="template_upload" />
    <br />
    <a href="javascript:hideUploader()" class="button small">Never Mind</a>
    
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Share with other sites? </div>
    <input type="checkbox" name="template_shared" value="1" id="share-checkbox" />&nbsp;<label for="share-checkbox">Click the box to make this template available to other websites hosted in this Smartest install</label>
  </div>
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="cancelForm();">
      <input type="submit" value="Save new template" />
    </div>
  </div>

</form>

<script type="text/javascript">
{literal}
var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {
    lineNumbers: true,
    mode: "htmlmixed",
    lineWrapping: true
  });
{/literal}
</script>

{else}

<div class="warning">
  {if $type_specified}
  The directory <code>{$path}</code> is not writable by the web server, so new templates cannot currently be created via Smartest. You may either upload them to the same place via FTP/SFTP, or speak to your server administrator to fix this.
  {else}
  None of the directories where Smartest stores templates are writable by the web server, so new templates cannot currently be created via Smartest. You may either upload them to the same place via FTP/SFTP, or speak to your server administrator to fix this.
  {/if}
</div>

<div class="edit-form-row">
  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="cancelForm();" />
  </div>
</div>

{/if}

</div>