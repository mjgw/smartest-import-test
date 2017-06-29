<div id="modal-work-area">
  
  <div class="instruction">Select or upload an image</div>
  <div class="warning" id="no-filereader-warning" style="display:none">To upload images here, you'll need an up-to-date browser that supports the FileReader and FormData APIs.</div>
  
  <ul class="file-thumbnails" id="image-list">
    {if $sm_user_agent.is_supported_browser}
    <li class="add" id="add-image-li">
      <a href="#add" id="add-image-button"><i>+</i>Upload a new image</a>
    </li>
    {/if}
  {foreach from=$assets item="asset"}
    <li style="background-image:url({if $sm_user_agent.is_supported_browser}{$asset.image.200x200.web_path}{else}{$asset.image.100x100.web_path}{/if})"><a href="#image-{$asset.id}" class="thumbnail {if $asset.id == $current_asset_id} selected{/if}" data-assetid="{$asset.id}"><span class="image-info">{$asset.label.xmlentities} ({$asset.image.width}x{$asset.image.height})</span></a></li>
  {/foreach}
  </ul>
  
  <div id="image-uploader" style="display:none">
    <form action="{$domain}ajax:assets/uploadNewImageFromMiniImageBrowser" method="post" enctype="multipart/form-data" id="new-image-upload-form">
      {if $for}
        <input type="hidden" name="for" id="for-input" value="{$for}" class="purpose_inputs" />
        {if $for == 'ipv' && $property_id}<input type="hidden" name="property_id" value="{$property_id}" class="purpose_inputs" />{/if}
        {if $for == 'ipv' && $item_id}<input type="hidden" name="item_id" value="{$item_id}" class="purpose_inputs" />{/if}
        {if $for == 'placeholder' && $placeholder_id}<input type="hidden" name="placeholder_id" value="{$placeholder_id}" class="purpose_inputs" />{/if}
        {if $for == 'user_profile_pic' && $user_id}<input type="hidden" name="user_id" value="{$user_id}" class="purpose_inputs" />{/if}
        {if $for == 'page_icon' && $page_id}<input type="hidden" name="page_id" value="{$page_id}" class="purpose_inputs" />{/if}
      {/if}
      <div class="form-section-label-full">Upload an image</div>
      <div class="edit-form-row">
        <div class="form-section-label">Give this image a label</div>
        <input type="text" name="asset_label" value="{$suggested_label}" id="asset-label" />
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">Credit, if this is a photo</div>
        <input type="text" name="asset_credit" value="" id="asset-credit" />
      </div>
      <div class="edit-form-row" id="choose-file-row">
        <div class="form-section-label">Choose an image file (JPEG, PNG or GIF)</div>
        <input type="file" name="asset_file" id="asset-file" />
      </div>
      <div class="v-spacer"> </div>
      <div class="progress-bar-outer" id="upload-progress-outer" style="display:none">
        <div class="progress-bar-inner" id="upload-progress-inner" style="width:0px;display:none"> </div>
      </div>
      <div class="buttons-bar">
        <a href="#upload" class="button" id="new-image-upload-cancel-button">Cancel</a>
        <a href="#upload" class="button" id="new-image-upload-button">Upload image</a>
      </div>
    </form>
  </div>
  
  <script type="text/javascript">// <![CDATA[
  
  var currentAssetId = {if $current_asset_id}{$current_asset_id}{else}null{/if};
  var purpose = '{$for}';
  inputId = '{$input_id}';
  
  {literal}
  $$('ul.file-thumbnails li a.thumbnail').each(function(clickedThumbnail){
    clickedThumbnail.observe('click', function(e){
      e.stop();
      // Update visual selected status
      $$('ul.file-thumbnails li a').each(function(l){
        l.removeClassName('selected');
      });
      clickedThumbnail.addClassName('selected');
      // Update hidden element
      $(inputId).value = clickedThumbnail.readAttribute('data-assetid');
      // Update form display with new thumbnail
      var url = sm_domain+'ajax:assets/getReplacementThumbnailForMiniImageBrowser';
      $(inputId+'-edit-metadata').appear({duration: 0.2});
      $(inputId+'-button-clear').appear({duration: 0.2});
      new Ajax.Updater(inputId+'-thumbnail-area', url, {
        parameters: {
          asset_id: clickedThumbnail.readAttribute('data-assetid'),
          input_id: inputId,
          'for': purpose
        }
      });
      $(inputId).fire('image:chosen', {inputId: inputId});
      // Close modal
      MODALS.hideViewer();
    });
  });
  {/literal}
  
  {if $sm_user_agent.is_supported_browser}
  {literal}
  if($('add-image-button')){
    $('add-image-button').observe('click', function(e){
      e.stop();
      $('image-list').hide();
      $('image-uploader').show();
      $('asset-label').focus();
      MODALS.updateScroller();
    });
  }
  {/literal}
  {/if}
  
  {literal}
  
  var startUpload = function(e){
    // e is an Event object
    e.stop();
    
    if($F('asset-label').length && $F('asset-file').length){
      
      var reader = new FileReader();
      var formdata = new FormData();
      var file = $('asset-file').files[0];
      var dataUrl;
    
      reader.readAsDataURL(file);
    
      formdata.append("asset_file", file);
      formdata.append("asset_label", $F('asset-label'));
      formdata.append("asset_credit", $F('asset-credit'));
    
      $$('input.purpose_inputs').each(function(ipt){
        formdata.append(ipt.name, ipt.value);
      });
    
      var uploadComplete = function(evt) {
        /* This event is raised when the server send back a response */
        var jsonResponse = JSON.parse(evt.target.responseText);
        var modalURL = 'assets/miniImageBrowser?input_id='+inputId;
      
        if(jsonResponse.for){
          modalURL += '&for='+jsonResponse.for;
        }
      
        if(jsonResponse.placeholder_id){
          modalURL += '&placeholder_id='+jsonResponse.placeholder_id;
        }
      
        if(jsonResponse.property_id){
          modalURL += '&property_id='+jsonResponse.property_id;
        }
      
        if(jsonResponse.user_id){
          modalURL += '&user_id='+jsonResponse.user_id;
        }
      
        modalURL += '&current_selection_id='+jsonResponse.asset_id;
      
        $(inputId).value = jsonResponse.asset_id;
      
        new Ajax.Updater(inputId+'-thumbnail-area', sm_domain+'ajax:assets/getReplacementThumbnailForMiniImageBrowser', {
          parameters: {
            asset_id: jsonResponse.asset_id,
            input_id: inputId,
            'for': purpose
          }
        });
        
        $(inputId).fire('image:chosen', {inputId: inputId});
        $(inputId+'-edit-metadata').appear({duration: 0.2});
        $(inputId+'-button-clear').appear({duration: 0.2});
        MODALS.hideViewer();

      }
    
      var uploadProgress = function (evt) {
      
          if (evt.lengthComputable) {
          
              var percentComplete = Math.round(evt.loaded * 100 / evt.total);
            
              if(!$('upload-progress-inner').visible()){
                $('upload-progress-inner').show();
              }
            
              var cssWidthValue = percentComplete.toString() + '%';
            
              $('upload-progress-inner').setStyle({width: cssWidthValue});
            
          }
      }
    
      // show progress bar
      $('upload-progress-outer').show();
    
      // Create XMLHttpRequest and upload file
      var xhr = new XMLHttpRequest;
      xhr.open('POST', $('new-image-upload-form').action, true);
      xhr.upload.addEventListener("progress", uploadProgress, false);
      xhr.addEventListener("load", uploadComplete, false);
      xhr.send(formdata);
      
    }else{
      
      if(!$F('asset-label').length){
        
        $('asset-label').addClassName('error');
        
        $('asset-label').observe('keyup', function(){
          if($F('asset-label').length){
            $('asset-label').removeClassName('error');
          }
        });
        
      }
      
    }
    
  }
  
  $('new-image-upload-cancel-button').observe('click', function(e){
    e.stop();
    $('image-list').show();
    $('image-uploader').hide();
    MODALS.updateScroller();
  });
  
  $('new-image-upload-form').observe('keypress', function(e){
    if(e.keyCode == 13){
      e.stop(e);
      startUpload();
    }
  });
  
  $('new-image-upload-button').observe('click', startUpload);
  
  {/literal}
  
  // ]]>
  </script>
  
</div>