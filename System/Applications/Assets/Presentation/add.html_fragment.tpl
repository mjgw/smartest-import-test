  <div style="display:none;margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
    Upload file: <input type="file" name="new_file" /><br /><a href="javascript:hideUploader()">never mind</a>
  </div>
  
  <div style="width:100%" id="text_window" class="textarea-holder">
    <div class="textarea-holder">
      <textarea name="content" id="tpl_textArea" wrap="virtual"></textarea>
      <span class="form-hint">Editor powered by CodeMirror</span>
    </div>
  </div>

  <div id="uploader_link" class="special-box">or, alternatively, <a href="javascript:showUploader();">upload a file</a>.</div>
  
<script type="text/javascript">
{literal}
var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {
    lineNumbers: true,
    mode: "htmlmixed",
    lineWrapping: true
  });
{/literal}
</script>