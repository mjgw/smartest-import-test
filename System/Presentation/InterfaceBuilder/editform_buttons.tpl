  <input type="button" value="{$_cancel_message}" onclick="cancelForm();" id="form-cancel-button" />
  <input type="submit" value="{$_continue_message}" onclick="$('sm-form-submit-action').value='continue';return true;" id="form-save-button" />
  {if $_publish_action}<input type="submit" value="{$_publish_message}" onclick="$('sm-form-submit-action').value='publish';return true;" id="form-save-publish-button" />{/if}
  <input type="submit" value="{$_quit_message}" onclick="$('sm-form-submit-action').value='quit';return true;" id="form-save-quit-button" />
  
  <input type="hidden" name="_submit_action" id="sm-form-submit-action" value="quit" />
  <input type="hidden" name="_referring_action" value="{$_referring_action}" />
  {if $_publish_action}<input type="hidden" name="_publish_action" value="{$_publish_action}" />{/if}