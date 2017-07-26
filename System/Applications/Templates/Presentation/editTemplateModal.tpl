<div id="modal-work-area">
{if $show_editor}
<div class="instruction">You are editing template <strong><code>{$template.url}</code></strong>. <a href="{$domain}templates/editTemplate?template={if $template.status == "imported"}{$template.id}{else}{$template.url}&amp;asset_type={$template.type}{/if}{if $from}&amp;from={$from}{/if}{if $item}&amp;item_id={$item.id}{/if}{if $page}&amp;page_id={$page.webid}{/if}" class="button small" style="float:right">Open in full editor</a></div>
  <div class="v-spacer"></div>
  <form action="{$domain}ajax:templates/postBackTemplateEditorContentsFromModal" method="post" id="rich-text-updater-form">
    
    {if $template.status == "imported"}
    <input type="hidden" name="edit_type" value="imported" />
    <input type="hidden" name="template_id" value="{$template.id}" />
    {else}
    <input type="hidden" name="edit_type" value="unimported" />
    <input type="hidden" name="type" value="{$template.type}" />
    <input type="hidden" name="filename" value="{$template.url}" />
    {/if}
    
    <textarea name="template_content" style="width:100%;height:300px" id="template-editor-codemirror-{$random_nonce}">{$editor_contents}</textarea>
    
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" style="display:none;float:left" id="progress" />
      <span class="feedback-ok" style="display:none;float:left" id="saved-message"><i class="fa fa-thumbs-o-up"> </i> Template saved successfully</span>
      <span class="feedback-error" style="display:none;float:left" id="didnt-save-message"><i class="fa fa-thumbs-o-down"> </i> Template could not be saved</span>
      <input type="button" value="Cancel" id="cancel-texteditor-modal" />
      <input type="button" value="Save" id="save-texteditor-modal" />
      <input type="button" value="Save &amp; close" id="save-texteditor-modal-close" />
    </div>
    
  </form>
  <div>
    <script language="javascript" type="text/javascript">
// <![CDATA[
    var selectorName = "template-editor-codemirror-{$random_nonce}";
    var myCodeMirror;
    
    {literal}
    
    $('cancel-texteditor-modal').observe('click', function(){
      MODALS.hideViewer();
    });
    
    $('save-texteditor-modal-close').observe('click', function(){
      $('progress').show();
      $('saved-message').hide();
      myCodeMirror.save();
      $('rich-text-updater-form').request({
        onComplete: function(){
          $('progress').hide();
          MODALS.hideViewer();
          // display user message: saved
        }
      });
    });
    
    $('save-texteditor-modal').observe('click', function(){
      
      $('progress').show();
      $('saved-message').hide();
      myCodeMirror.save();
      $('rich-text-updater-form').request({
        onComplete: function(response){
          if(response.responseJSON.success){
            $('progress').hide();
            $('saved-message').show();
            setTimeout(function(){
              $('saved-message').fade({duration: 0.5});
            }, 1000);
          }else{
            $('progress').hide();
            $('didnt-save-message').show();
            setTimeout(function(){
              $('didnt-save-message').fade({duration: 0.5});
            }, 1000);
          }
        }
      });
    });
    
    myCodeMirror = CodeMirror.fromTextArea($(selectorName), {
      lineNumbers: true,
      mode: "htmlmixed",
      lineWrapping: false
    });
  
    {/literal}
// ]]>
    </script>
  </div>
{else}
<div class="warning">
  <p>{$message} <a class="button" href="javascript:MODALS.hideViewer();">Close editor</a></p>
</div>
{/if}
</div>