  {* <div style="display:inline" id="editorButtons">
    <input type="button" onclick="insertLink();" value="Insert Link" disabled="disabled" style="float:right;margin-right:5px;" />
    <input type="button" onclick="insertImage();" value="Insert Image" disabled="disabled" style="float:right;margin-right:5px;" />
    <input type="button" onclick="insertAssetClass();" value="Insert Asset Class" disabled="disabled" style="float:right;margin-right:5px;" />
  </div> *}
  
<!--  <div class="edit-form-row">
    <div class="form-section-label">Name this file</div>
    <input type="text" name="string_id" />
  </div>
  
  <div style="display:none;margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
    Upload file: <input type="file" name="new_file" />
    <br /><a href="javascript:hideUploader()">never mind</a>
  </div> 
  
  <div class="edit-form-row">
    <div class="form-section-label">File Name</div>
    <label for="new_filename">Store this file as: </label>
    <code>{$new_asset_type_info.storage.location}</code><input type="text" name="new_filename" style="width:250px" /><code>.css</code>
  </div> -->
  
  <div style="width:100%" id="text_window" class="edit-form-row">
    <div class="form-section-label">Add your CSS here:</div>
    <div class="textarea-holder">
      <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
      <span class="form-hint">Editor powered by CodeMirror</span>
    </div>
  </div>
  
  <script type="text/javascript">

  var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {ldelim}
      lineNumbers: true,
      mode: "css",
      lineWrapping: true
    {rdelim});
  
  </script>