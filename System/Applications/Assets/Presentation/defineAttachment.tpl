<script type="text/javascript">

var selectedAssetId = {if $attached_asset_id}{$attached_asset_id}{else}null{/if};
{literal}
var selectedAssetInfo = {};

function toggleResizeImageOption(flag){
    if(flag){
        new Effect.BlindDown('thumbnail_size_selector', {duration:0.4});
    }else{
        new Effect.BlindUp('thumbnail_size_selector', {duration:0.4});
    }
}

function updateSize(value){
    if(selectedAssetInfo.width && selectedAssetInfo.height){
        $('file-size').update(Math.ceil(value/100*selectedAssetInfo.width)+'x'+Math.ceil(value/100*selectedAssetInfo.height)+' pixels');
    }else{
        $('file-size').update('');
    }
}

function respondToAssetImageStatus(){
    
    if(selectedAssetInfo.is_image){
        if(!$('image-resize-options').visible()){
            $('image-resize-options').appear({duration: 0.3});
        }
    }else{
        if($('image-resize-options').visible()){
            $('image-resize-options').fade({duration: 0.3});
        }
    }
    
}

function getAssetInfo(asset_id){
    new Ajax.Request(sm_domain+'ajax:assets/getAssetInfoJsonForAttachmentForm?attached_file_id='+asset_id, {
        onSuccess: function(transport) {
            console.log(transport.responseJSON);
            selectedAssetInfo = transport.responseJSON;
            updateSize($F('thumbnail_relative_size'));
            respondToAssetImageStatus();
        },
        asynchronous: false
    });
}

document.observe('dom:loaded', function(){
    
    if(selectedAssetId){
        getAssetInfo(selectedAssetId);
    }
  
    $('asset-selector').observe('change', function(){
        if($F('asset-selector')){
            getAssetInfo($F('asset-selector'));
        }else{
            $('image-resize-options').fade({duration: 0.3});
        }
    });
    
});

{/literal}
</script>

<div id="work-area">
  <h3>Define Attachment</h3>
  <form action="{$domain}{$section}/updateAttachmentDefinition" method="post" id="attachment-form">
  <div id="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Attachment Name</div>
      <code style="display:inline-block;padding-top:3px;font-size:14px">{$attachment_name}</code><input type="hidden" name="attachment_name" value="{$attachment_name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Text File</div>
      <img src="{$asset.small_icon}" alt="" /> {$asset}<input type="hidden" name="textfragment_id" value="{$textfragment_id}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Attached File</div>
      <select name="attached_file_id" id="asset-selector">
        <option value="">No file attached</option>
        {foreach from=$files item="file"}
        <option value="{$file.id}"{if $file.id == $attached_asset_id} selected="selected"{/if}>{$file.stringid} ({$file.url})</option>
        {/foreach}
      </select>
    </div>
    
    <div id="image-resize-options">
    
      <div class="edit-form-row">
        <div class="form-section-label">Resize</div>
        {boolean name="attached_file_resize" id="attached-file-resize" value=$resize changehook="toggleResizeImageOption"}
        <!--<input type="checkbox" name="attached_file_zoom" value="TRUE" id="attached_file_zoom"{if $zoom} checked="checked"{/if} onchange="toggleZoomImageOption()" />&nbsp;<label for="attached_file_zoom">Zoom from thumbnail file</label>-->
      </div>
    
      <div id="thumbnail_size_selector">
        <div class="edit-form-row">
          <div class="form-section-label">Relative size:</div>
          {slider name="thumbnail_relative_size" value=$relative_size min="10" max="90" value_unit="%" slidehook="updateSize"} <span id="file-size" style="padding-left:10px"></span>
        </div>
        <div class="edit-form-row">
          <div class="form-section-label">Zoom resized images?</div>
          {boolean name="attached_file_zoom" id="attached-file-zoom" value=$zoom}
          <!--<input type="checkbox" name="attached_file_zoom" value="TRUE" id="attached_file_zoom"{if $zoom} checked="checked"{/if} onchange="toggleZoomImageOption()" />&nbsp;<label for="attached_file_zoom">Zoom from thumbnail file</label>-->
          <span id="highslide-credit" class="form-hint" style="display:block;margin-left:194px">(Powered by <a href="http://www.highslide.com/" target="_blank">Highslide</a>. License terms apply. Please {help id="assets:highslide"}click here{/help} for more information. )</span>
          <div class="breaker"></div>
        </div>
      </div>
    
      {if $resize}{else}<script type="text/javascript">$('thumbnail_size_selector').hide();</script>{/if}
    
    </div>
    {if !$attached_asset.is_image}<script type="text/javascript">$('image-resize-options').hide();</script>{/if}

    <div class="edit-form-row">
      <div class="form-section-label">Position</div>
      <select name="attached_file_alignment">
        <option value="left"{if $alignment == "left"} selected="selected"{/if}>On the Left</option>
        <option value="right"{if $alignment == "right"} selected="selected"{/if}>On the Right</option>
        <option value="center"{if $alignment == "center"} selected="selected"{/if}>In the Center (Non-floating only)</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Caption</div>
      <textarea name="attached_file_caption" style="width:370px;height:60px">{$caption}</textarea>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Caption Alignment</div>
      <select name="attached_file_caption_alignment">
        <option value="left"{if $caption_alignment == "left"} selected="selected"{/if}>From Left</option>
        <option value="right"{if $caption_alignment == "right"} selected="selected"{/if}>From Right</option>
        <option value="center"{if $caption_alignment == "center"} selected="selected"{/if}>Centered</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Float within the text</div>
      {boolean name="attached_file_float" id="attached-file-float" value=$float}
      <!--<input type="checkbox" name="attached_file_float" value="TRUE"{if $float} checked="checked"{/if} id="attached_file_float" />&nbsp;<label for="attached_file_float">Float within the text.</label>-->
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Show a 1px grey border</div>
      {boolean name="attached_file_border" id="attached-file-border" value=$border}
      <!--<input type="checkbox" name="attached_file_border" value="TRUE"{if $border} checked="checked"{/if} id="attached_file_border" />&nbsp;<label for="attached_file_border">Show a 1px grey border.</label>-->
    </div>
    
    <div class="buttons-bar">
      <input type="submit" value="Save" />
      <input type="button" value="Cancel" onclick="cancelForm();" />
    </div>
    
  </div>
  
  </form>
</div>