{if $sm_user_agent.is_supported_browser}
<div class="error" style="display:none" id="upload-error">
  There was a problem with your file upload.
</div>
<div class="edit-form-row">
  <div class="form-section-label">Choose a file on your computer to upload</div>
  <input type="file" name="new_file" id="asset-file" /><div class="form-hint">Please do not upload files larger than <strong>{$max_upload_size_in_megs} MB</strong></div>
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
  $('confirm-asset-create').observe('click', function(e){
    e.stop();
    var uploadInfo = {};
    uploadInfo.asset_label = $F('new-asset-name');
    $$('input.purpose_inputs').each(function(ipt){
      uploadInfo[ipt.name] = ipt.value;
    });
    // console.log(uploadInfo);
    if(($('new-asset-name').getValue() == itemNameFieldDefaultValue) || $('new-asset-name').getValue() == ''){
      return false;
    }else{
      
      var uploadComplete = function(evt) {
        $('buttons-bar').update('<input type="button" value="Done" id="cancel-asset-create" onclick="cancelForm();" />');
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
      
      // console.log(formdata);
      
      $('upload-progress-bar-holder').show();
      
      var xhr = new XMLHttpRequest;
      xhr.open('POST', uploadUrl, true);
      xhr.upload.addEventListener("progress", uploadProgress, false);
      xhr.addEventListener("load", uploadComplete, false);
      xhr.addEventListener("error", uploadFailed, false);
      xhr.send(formdata);
      
      // alert('proceed with upload');
    }
  });
});
{/literal}
</script>
{else}
<div style="margin-top:8px;margin-bottom:8px" id="uploader" class="special-box">
  Upload file: <input type="file" name="new_file" /><div class="form-hint">Please do not upload files larger than <strong>{$max_upload_size_in_megs} MB</strong></div>
</div>
{/if}