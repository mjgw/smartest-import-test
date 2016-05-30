<div id="modal-work-area">

  <div id="existing-embeddable-media" style="">
    <ul class="file-thumbnails">
      <li class="add" id="add-image-li">
        <a href="#add" id="add-image-button"><i>+</i>Add a new file</a>
      </li>
{foreach from=$attachable_files item="file"}
      <li{if $file.thumbnail_image && $file.thumbnail_image.id} style="background-image:url({if $sm_user_agent.is_supported_browser}{$file.thumbnail_image.image.200x200.web_path}{else}{$file.thumbnail_image.image.100x100.web_path}{/if})"{/if}>
        <a href="#choose" data-assetid="{$file.id}" data-assetlabel="{$file.label|escape:"quotes"}" class="asset-selector thumbnail{if $file.id == $current_file_id} selected{/if}">
          <span class="file-info"><i class="fa fa-{$file.fa_icon}"> </i>{$file.label}</span>
        </a>
      </li>
{/foreach}  
    </ul>
  </div>

  <div id="create-embeddable-media" style="display:none">
    <ul class="apps small" style="padding:70px 0 0 100px">
      <li style="margin-right:40px" id="create-embeddable-media-from-url"><a href="" class="icon"><i class="fa fa-at"> </i></a><a href="" class="label">Create media from URL</a></li>
      <li id="create-embeddable-media-from-textarea"><a href="" class="icon"><i class="fa fa-copy"> </i></a><a href="" class="label">Copy and paste code from another site</a></li>
    </ul>
    <div class="breaker"> </div>
    <p style="text-align:center;padding-top:20px">Or <a class="button small" href="#back" id="create-intro-go-back">Go back</a></p>
  </div>
  
  <div id="create-embeddable-media-from-url-container" style="display:none">
    
    <div class="edit-form-row url">
      <div class="form-section-label">Enter URL</div>
      
      <input type="text" name="asset_url" style="width:520px" id="url-input" />
      <input type="hidden" name="oembed_service" value="" id="oembed-service-id" />
      <input type="hidden" name="asset_type" value="" id="asset-type" />
      
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="check-url-loader" style="display:none" />
      
      <div class="breaker"> </div>
      <div class="form-hint">Don't forget the http://</div>
      <div class="edit-form-sub-row">
        <span id="url-ok" style="display:none" class="feedback-ok"><i class="fa fa-check-circle"> </i> <span id="url-service-label"> </span></span>
        <span id="url-invalid" style="display:none" class="feedback-bad"><i class="fa fa-times"> </i> <span id="url-error-label"> </span></span>
      </div>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" name="asset_label" id="asset-label" value="Untitled embed" />
    </div>
    
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="url-form-submit-loader" style="display:none" />
      <input type="button" value="Cancel" id="create-embeddable-media-from-url-cancel" />
      <input type="button" value="Save" id="create-embeddable-media-from-url-save" disabled="disabled" />
    </div>
    
  </div>
  
  <div id="create-embeddable-media-from-textarea-container" style="display:none">
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" name="asset_label" id="asset-label-textarea" value="Untitled HTML Fragment" />
    </div>
    
    <div class="edit-form-row">
      <textarea name="asset_content" id="asset-content" style="width:300px;height:200px;border:1px solid #ccc"> </textarea>
    </div>
    
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="textarea-form-submit-loader" style="display:none" />
      <input type="button" value="Cancel" id="create-embeddable-media-from-textarea-cancel" />
      <input type="button" value="Save" id="create-embeddable-media-from-textarea-save" />
    </div>
    
  </div>

  <script type="text/javascript">
  {literal}
  
  var CodeMirrorShowing = false;
  
  $$('ul.file-thumbnails li a.asset-selector').each(function(a){
    a.observe('click', function(evt){
      evt.stop();
      var assetId = a.readAttribute('data-assetid');
      $('attached-file-id-embed').value = assetId;
      getAssetInfo(assetId);
      getNonImageAssetInfo(assetId);
      $('attachment-file-type').value = 'embed';
      $('embed-file-name').removeClassName('null-notice');
      $('embed-file-name').update(a.readAttribute('data-assetlabel'));
      MODALS.hideViewer();
    });
  });
  
  $('add-image-button').observe('click', function(evt){
    evt.stop();
    $('existing-embeddable-media').hide();
    $('create-embeddable-media').show();
    MODALS.updateTitle("Create embeddable file");
  });
  
  $('create-intro-go-back').observe('click', function(evt){
    evt.stop();
    MODALS.updateTitle("Select embeddable media");
    $('existing-embeddable-media').show();
    $('create-embeddable-media').hide();
  });
  
  $('create-embeddable-media-from-url').observe('click', function(evt){
    evt.stop();
    $('create-embeddable-media').hide();
    $('create-embeddable-media-from-url-container').show();
  });
  
  $('create-embeddable-media-from-textarea').observe('click', function(evt){
    evt.stop();
    $('create-embeddable-media').hide();
    $('create-embeddable-media-from-textarea-container').show();
    if(!CodeMirrorShowing){
      CM = CodeMirror.fromTextArea($('asset-content'), {
        lineNumbers: true,
        mode: "htmlmixed",
        lineWrapping: true,
        autofocus: true
      });
      CodeMirrorShowing = true;
    }
  });
  
  $('create-embeddable-media-from-url-cancel').observe('click', function(evt){
    evt.stop();
    $('create-embeddable-media').show();
    $('create-embeddable-media-from-url-container').hide();
  });
  
  $('create-embeddable-media-from-textarea-cancel').observe('click', function(evt){
    evt.stop();
    $('create-embeddable-media').show();
    $('create-embeddable-media-from-textarea-container').hide();
  });
  
  $('create-embeddable-media-from-url-save').observe('click', function(evt){
    evt.stop();
    $('url-form-submit-loader').show();
    new Ajax.Request(sm_domain+'ajax:assets/createEmbeddableAssetFromModal', {
      parameters: {
        asset_url: $F('url-input'),
        service_id: $F('oembed-service-id'),
        asset_type: $F('asset-type'),
        asset_label: $F('asset-label')
      },
      method: 'post',
      onSuccess: function(response){
        console.log(response.responseJSON);
        
        $('embed-file-name').update(response.responseJSON.label);
        $('attached-file-id-embed').value = response.responseJSON.id;
        $('embed-file-name').removeClassName('null-notice');
        $('attachment-file-type').value = 'embed';
        getAssetInfo(response.responseJSON.id);
        getNonImageAssetInfo(response.responseJSON.id);
        
        $('url-form-submit-loader').hide();
        MODALS.hideViewer();
        
      }
    });
  });
  
  $('create-embeddable-media-from-textarea-save').observe('click', function(evt){
    $('textarea-form-submit-loader').show();
    // console.log(CM.getValue());
    new Ajax.Request(sm_domain+'ajax:assets/createEmbeddableAssetFromModalTextArea', {
      
      parameters: {
        asset_contents: CM.getValue(),
        asset_type: 'SM_ASSETTYPE_HTML_FRAGMENT',
        asset_label: $F('asset-label-textarea')
      },
      
      method: 'post',
      
      onSuccess: function(response){
        console.log(response.responseJSON);
        
        $('embed-file-name').update(response.responseJSON.label);
        $('attached-file-id-embed').value = response.responseJSON.id;
        $('embed-file-name').removeClassName('null-notice');
        $('attachment-file-type').value = 'embed';
        getAssetInfo(response.responseJSON.id);
        getNonImageAssetInfo(response.responseJSON.id);
        
        $('textarea-form-submit-loader').hide();
        MODALS.hideViewer();
        
      }
    });
  });
  
  $('url-input').observe('keyup', function(evt){
    
    $('check-url-loader').show();
    $('url-ok').hide();
    $('url-invalid').hide();
    
    if(window.checkTimeOut){
      clearTimeout(window.checkTimeOut);
    }
    
    window.checkTimeOut = setTimeout(function(){
      
      new Ajax.Request(sm_domain+'ajax:assets/validateExternalResourceUrl', {
        
        parameters: {
          url: $F('url-input')
        },
        
        onSuccess: function(response) {
          
          if(response.responseJSON.valid){
            $('check-url-loader').hide();
            $('url-service-label').update(response.responseJSON.data.label);
            $('oembed-service-id').value = response.responseJSON.data.service_id;
            $('asset-type').value = response.responseJSON.data.type_code;
            $('create-embeddable-media-from-url-save').disabled = false;
            $('url-ok').show();
          }else{
            $('check-url-loader').hide();
            $('create-embeddable-media-from-url-save').disabled = true;
            $('oembed-service-id').value = '';
            $('asset-type').value = '';
            $('url-invalid').show();
            $('url-error-label').update(response.responseJSON.message);
          }
          
        }
      });
    }, 1000);
  });
  
  {/literal}
  </script>

</div>