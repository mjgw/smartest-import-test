  <div class="special-box">You are creating a Textile Markup file. {help id="assets:textile"}Click here{/help} to learn more about how these are formatted.</div>
  
  <div class="edit-form-row">
    <div class="form-section-label">Please enter your text here</div>
    <div class="breaker"></div>
    <div style="width:100%" id="text_window" class="textarea-holder">
      <textarea name="content" id="tpl_textArea" wrap="virtual" style="font-family:monospace;font-size:14px"></textarea>
      <span class="form-hint">Editor powered by CodeMirror</span>
    </div>
  </div>
  
  <script type="text/javascript">
  {literal}
  var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {
      mode: "textile",
      lineWrapping: true
    });
  {/literal}
  </script>