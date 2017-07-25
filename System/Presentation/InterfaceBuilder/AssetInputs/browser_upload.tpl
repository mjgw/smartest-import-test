{if $sm_user_agent.is_supported_browser}

<script type="text/javascript">
var suffixRegex = {if $file_suffix_regex}{$file_suffix_regex}{else}null{/if};
</script>

{if $post_max_size_warning}
<div class="warning">There is an issue in your PHP configration. Although upload_max_filesize has been configured to allow uploads of up to <strong>{$max_upload_size_in_megs} MB</strong>, the post_max_size directive in php.ini will still limit uploads to only <strong>{$post_max_size_in_megs} MB</strong>, and uploads will fail completely if they exceed this. To fix this, change the post_max_size directive to match upload_max_filesize.</div>
{/if}

<div class="error" style="display:none" id="upload-error">
  There was a problem with your file upload.
</div>
<div class="edit-form-row">
  <div class="form-section-label">Choose a file on your computer to upload</div>
  <input type="file" name="new_file" id="asset-file" />
  <div class="form-hint" style="color:#c30;display:none" id="asset-type-warning">The file you have selected is not the correct file type.</div>
  <div class="form-hint">Please do not upload files larger than <strong>{if $post_max_size_warning}{$post_max_size_in_megs}{else}{$max_upload_size_in_megs}{/if} MB</strong></div>
</div>
<div class="edit-form-row" id="upload-progress-bar-holder" style="display:none">
  <div class="form-section-label"> </div>
  <div class="progress-bar-outer" id="upload-progress-outer">
    <div class="progress-bar-inner" id="upload-progress-inner" style="width:0px;display:none"> </div>
  </div>
</div>

<script type="text/javascript">
var uploadUrl = sm_domain+'ajax:assets/uploadNewFileViaBrowserAjaxRequest';
{literal}
document.observe('dom:loaded', function(){
  
  $('asset-file').observe('change', function(){
    if(suffixRegex.match($('asset-file').value)){
      if($('asset-type-warning').visible()){
        $('asset-type-warning').fade({duration: 0.3});
      }
      if(!$('confirm-asset-create').visible()){
        $('confirm-asset-create').appear({duration: 0.3});
      }
    }else{
      $('asset-type-warning').appear({duration: 0.3});
      $('confirm-asset-create').fade({duration: 0.3});
    }
  });
  
  $('confirm-asset-create').observe('click', function(e){
    
    e.stop();
    var uploadInfo = {};
    uploadInfo.asset_label = $F('new-asset-name');
    
    $$('input.purpose_inputs').each(function(ipt){
      uploadInfo[ipt.name] = ipt.value;
    });
    
    if(($('new-asset-name').getValue() == itemNameFieldDefaultValue) || $('new-asset-name').getValue() == ''){
      return false;
    }else{
      
      var uploadComplete = function(evt) {
        $('buttons-bar').update('<input type="button" value="Continue" id="cancel-asset-create" onclick="finishTask();" />');
        new Effect.Fade('upload-progress-outer', {duration: 0.4});
      }
      
      var uploadFailed = function(evt) {
        $('buttons-bar').update('<input type="button" value="Go back" id="cancel-asset-create" onclick="cancelForm();" />');
        $('upload-error').show();
      }
      
      var uploadProgress = function(evt) {
        
        if (evt.lengthComputable) {
            var percentComplete = Math.round(evt.loaded * 100 / evt.total);
          
            if(!$('upload-progress-inner').visible()){
              $('upload-progress-inner').show();
            }
          
            var cssWidthValue = percentComplete.toString() + '%';
            $('upload-progress-inner').setStyle({width: cssWidthValue});
        }
        
      }
      
      var reader = new FileReader();
      var formdata = new FormData();
      var file = $('asset-file').files[0];
      var dataUrl;
    
      reader.readAsDataURL(file);
      
      formdata.append("new_file", file);
      
      $H(uploadInfo).each(function(iterator){
        formdata.append(iterator.key, iterator.value);
      });
      
      $H(uploadInfo).each(function(iterator){
        console.log(iterator.key+':'+iterator.value);
      });
      
      $('upload-progress-bar-holder').show();
      
      var xhr = new XMLHttpRequest;
      xhr.open('POST', uploadUrl, true);
      xhr.upload.addEventListener("progress", uploadProgress, false);
      xhr.addEventListener("load", uploadComplete, false);
      xhr.addEventListener("error", uploadFailed, false);
      xhr.send(formdata);
      
    }
  });
});
{/literal}
</script>
{else}

{if $post_max_size_warning}
<div class="warning">There is an issue in your PHP configration. Although upload_max_filesize has been configured to allow uploads of up to <strong>{$max_upload_size_in_megs} MB</strong>, the post_max_size directive in php.ini will still limit uploads to only <strong>{$post_max_size_in_megs} MB</strong>, and uploads will fail completely if they exceed this. To fix this, change the post_max_size directive to match upload_max_filesize.</div>
{/if}

<div style="margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
  <div class="edit-form-row">
    <div class="form-section-label">Choose a file on your computer to upload</div>
    <input type="file" name="new_file" id="asset-file" />
    <div class="form-hint" style="color:#c30;display:none" id="asset-type-warning">The file you have selected is not the correct file type.</div>
    <div class="form-hint">Please do not upload files larger than <strong>{if $post_max_size_warning}{$post_max_size_in_megs}{else}{$max_upload_size_in_megs}{/if} MB</strong></div>
    <div class="breaker"></div>
  </div>
</div>

<script type="text/javascript">
  
var suffixRegex = {if $file_suffix_regex}{$file_suffix_regex}{else}null{/if};

{literal}$('asset-file').observe('change', function(){
  if(suffixRegex.match($('asset-file').value)){
    if($('asset-type-warning').visible()){
      $('asset-type-warning').fade({duration: 0.3});
    }
    if(!$('confirm-asset-create').visible()){
      $('confirm-asset-create').appear({duration: 0.3});
    }
  }else{
    $('asset-type-warning').appear({duration: 0.3});
    $('confirm-asset-create').fade({duration: 0.3});
  }
});{/literal}
  
</script>

{/if}