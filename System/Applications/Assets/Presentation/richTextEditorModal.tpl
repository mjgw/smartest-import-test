<div id="modal-work-area">
{if $show_editor}
<div class="instruction">When you are finished, click "Save &amp; close" below. <a href="{$domain}assets/editAsset?asset_id={$asset.id}{if isset($from)}&amp;from=edit_item{if isset($item)}&amp;item_id={$item.id}{/if}{if isset($page)}&amp;page_id={$page.webid}{/if}{/if}" class="button small" style="float:right">Open in full editor</a></div>
  <form action="{$domain}ajax:assets/postBackTextEditorContentsFromModal" method="post" id="rich-text-updater-form">
    <input type="hidden" name="asset_id" value="{$asset.id}" />
    <textarea name="asset_content" style="width:100%;height:250px" id="asset-editor-tinymce-{$random_nonce}"> {$editor_contents}</textarea>
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" style="display:none;float:left" id="progress" />
      <span class="feedback-ok" style="display:none;float:left" id="saved-message"><i class="fa fa-thumbs-o-up"> </i> Text saved successfully</span>
      <span class="feedback-error" style="display:none;float:left" id="didnt-save-message"><i class="fa fa-thumbs-o-up"> </i> Text could not be saved</span>
      <input type="button" value="Cancel" id="cancel-texteditor-modal" />
      <input type="button" value="Save" id="save-texteditor-modal" />
      <input type="button" value="Save &amp; close" id="save-texteditor-modal-close" />
    </div>
  </form>
  <div>
    <script language="javascript" type="text/javascript">
// <!--
    var selectorName = "#asset-editor-tinymce-{$random_nonce}";
    {literal}
    
    $('cancel-texteditor-modal').observe('click', function(){
      MODALS.hideViewer();
    });
    
    $('save-texteditor-modal-close').observe('click', function(){
      $('progress').show();
      $('saved-message').hide();
      // $('rich-text-updater-form').submit();
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
      // $('rich-text-updater-form').submit();
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
    
    tinymce.init({
        selector: selectorName,
        menubar: false,
        plugins: [
            "advlist autolink lists charmap print preview anchor",
            "searchreplace visualblocks code fullscreen",
            "media table contextmenu paste link wordcount noneditable"
        ],
        
        style_formats: [
            {title: 'Headers', items: [
                {title: 'Header 2', block: 'h2'},
                {title: 'Header 3', block: 'h3'},
                {title: 'Header 4', block: 'h4'},
                {title: 'Header 5', block: 'h5'},
                {title: 'Header 6', block: 'h6'}
            ]},

            {title: 'Blocks', items: [
                {title: 'Paragraph', block: 'p'},
                {title: 'div', block: 'div'},
                {title: 'pre', block: 'pre'}
            ]},

            {title: 'Containers', items: [
                {title: 'section', block: 'section', wrapper: true, merge_siblings: false},
                {title: 'article', block: 'article', wrapper: true, merge_siblings: false},
                {title: 'blockquote', block: 'blockquote', wrapper: true},
                {title: 'aside', block: 'aside', wrapper: true},
                {title: 'figure', block: 'figure', wrapper: true}
            ]}
        ],
        
        protect: [
            /\<xsl\:[^>]+\>/g,  // Protect <xsl:...>
            /<\?sm:.*?:\?>/g  // Protect php code
        ],
    
        paste_word_valid_elements: "b,strong,i,em,h1,h2,h3,h4,p",
        toolbar: "styleselect | bold italic | link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        },
        relative_urls : false,
        document_base_url : sm_domain,
        skin: "smartest"

    });
  
    {/literal}
// -->
    </script>
  </div>
{else}
<div class="warning">
  <p>{$message}</p>
</div>
{/if}
</div>