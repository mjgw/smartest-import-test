<script type="text/javascript">

var selectedAssetId = {if $attached_asset_id}{$attached_asset_id}{else}null{/if};
var currentSelectedType = {if $attached_asset.id && $attached_asset.is_binary_image}'image'{elseif $attached_asset.id && !$attached_asset.is_binary_image}'embed'{else}null{/if};
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
            $('embed-resize-options').fade({duration: 0.3});
        }
    }else{
        if($('image-resize-options').visible()){
            $('image-resize-options').fade({duration: 0.3});
            $('embed-resize-options').appear({duration: 0.3});
        }
    }
    
}

var getNonImageAssetInfo = function(asset_id){
    new Ajax.Request(sm_domain+'ajax:assets/getAssetInfoJsonForAttachmentForm?attached_file_id='+asset_id, {
        onSuccess: function(transport) {
            console.log(transport.responseJSON);
            selectedAssetInfo = transport.responseJSON;
            $('embed-default-width').update(selectedAssetInfo.width);
            if(selectedAssetInfo.width){
              $('embed-default-width-holder').show();
              if(selectedAssetInfo.width != $F('attached-embed-width')){
                $('apply-default-width-button').show();
              }else{
                $('apply-default-width-button').hide();
              }
            }else{
              $('embed-default-width-holder').hide();
            }
            /* updateSize($F('thumbnail_relative_size'));
            respondToAssetImageStatus(); */
        },
        asynchronous: false
    });
}

var getAssetInfo = function(asset_id){
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

var imageSelected = function(){
  // alert('called: '+$F('asset-selector-img'));
  $('attachment-file-type').value = 'image';
  getAssetInfo($F('asset-selector-img'));
}

document.observe('dom:loaded', function(){
    
    if(selectedAssetId){
        getAssetInfo(selectedAssetId);
    }
  
    /* $('asset-selector-embed').observe('change', function(){
        if($F('asset-selector-embed')){
            // getAssetInfo($F('asset-selector-embed'));
            getNonImageAssetInfo($F('asset-selector-embed'));
            $('attachment-file-type').value = 'embed';
        }
    }); */
    
    $('attachment-filetype-selector-image').observe('click', function(evt){
      evt.stop();
      $('image-selector').show();
      $('embed-selector').hide();
      $('attachment-filetype-selector-image').addClassName('selected');
      $('attachment-filetype-selector-embed').removeClassName('selected');
      getAssetInfo($F('asset-selector-img'));
      $('attachment-filetype-selector-clear').appear({duration: 0.3});
      $('embed-resize-options').blindUp({duration: 0.25});
      $('image-resize-options').blindDown({duration: 0.25});
      if($F('asset-selector-img')){
        $('attachment-file-type').value = 'image';
      }
    });
    
    $('attachment-filetype-selector-embed').observe('click', function(evt){
      
      evt.stop();
      
      $('image-selector').hide();
      $('embed-selector').show();
      $('attachment-filetype-selector-embed').addClassName('selected');
      $('attachment-filetype-selector-image').removeClassName('selected');
      $('image-resize-options').blindUp({duration: 0.3});
      $('embed-resize-options').blindDown({duration: 0.25});
      $('attachment-filetype-selector-clear').appear({duration: 0.3});
      
      if($F('asset-selector-embed')){
        $('attachment-file-type').value = 'embed';
      }
      
    });
    
    $('attachment-filetype-selector-clear').observe('click', function(evt){
      evt.stop();
      $('image-selector').hide();
      $('embed-selector').hide();
      $('attachment-filetype-selector-embed').removeClassName('selected');
      $('attachment-filetype-selector-image').removeClassName('selected');
      $('image-resize-options').fade({duration: 0.25});
      $('embed-resize-options').fade({duration: 0.25});
      $('attachment-filetype-selector-clear').fade({duration: 0.3});
      $('attachment-file-type').value = 'none';
    });
    
    $('apply-default-width-button').observe('click', function(evt){
      evt.stop();
      $('attached-embed-width').value = $('embed-default-width').innerHTML;
      $('apply-default-width-button').fade({duration: 0.3});
    });
    
    $('attached-embed-width').observe('blur', function(){
      if($('embed-default-width').innerHTML && ($('attached-embed-width').value != $('embed-default-width').innerHTML)){
        $('apply-default-width-button').appear({duration: 0.3});
      }
    });
    
    $('embed-selector-button').observe('click', function(e){
      e.stop();
      MODALS.load('assets/nonImageAttachmentChooser', 'Select embeddable media');
    });
    
});

{/literal}
</script>

<div id="work-area">
  
  <h3>Edit text attachment</h3>
  
  <form action="{$domain}{$section}/updateAttachmentDefinition" method="post" id="attachment-form">
  <div id="edit-form-layout">
    
    <div class="edit-form-row">
      <div class="form-section-label">Attachment name</div>
      <code style="display:inline-block;padding-top:3px;font-size:14px">{$attachment_name}</code><input type="hidden" name="attachment_name" value="{$attachment_name}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Text file containing attachment</div>
      <img src="{$asset.small_icon}" alt="" /> {$asset}<input type="hidden" name="textfragment_id" value="{$textfragment_id}" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Attached media</div>
      
      <ul class="round-buttons-list">
        <li><a id="attachment-filetype-selector-image" href="#image"{if $attached_asset.id && $attached_asset.is_binary_image} class="selected"{/if}><i class="fa fa-image"></i></a></li>
        <li><a id="attachment-filetype-selector-embed" href="#embed"{if $attached_asset.id && !$attached_asset.is_binary_image} class="selected"{/if}><i class="fa fa-code"></i></a></li>
        <li><a id="attachment-filetype-selector-clear" href="#clear"{if !$attached_asset.id} style="display:none"{/if}><i class="fa fa-times"></i></a></li>
      </ul>
      
      <input type="hidden" name="attachment_file_type" id="attachment-file-type" value="{if $attached_asset.id && $attached_asset.is_binary_image}image{elseif $attached_asset.id && !$attached_asset.is_binary_image}embed{/if}" />
      
      <div class="edit-form-sub-row">
        
        <div id="image-selector" {if !$attached_asset.id || !$attached_asset.is_binary_image} style="display:none"{/if}>
          {if $attached_asset.id && $attached_asset.is_binary_image}
          {image_select name="attached_file_id_img" id="asset-selector-img" changehook="imageSelected" value=$attached_asset}
          {else}
          {image_select name="attached_file_id_img" id="asset-selector-img" changehook="imageSelected"}
          {/if}
        </div>
        
        <div id="embed-selector" {if !$attached_asset.id || $attached_asset.is_binary_image} style="display:none"{/if}>
          <!--<select name="attached_file_id_embed" id="asset-selector-embed">
            <option value="">No file attached</option>
            {foreach from=$non_image_files item="file"}
            <option value="{$file.id}"{if $file.id == $attached_asset_id} selected="selected"{/if}>{$file.stringid} ({$file.url})</option>
            {/foreach}
          </select>-->
          <input type="hidden" name="attached_file_id_embed" value="{$attached_asset.id}" id="attached-file-id-embed" />
          <div class="item-chooser-input" id="embed-file-indicator-holder">
            <span id="embed-file-indicator" style="margin-right:10px">
              <i class="fa fa-file-o"></i> <span id="embed-file-name" class="{if !$attached_asset.id || $attached_asset.is_binary_image}null-notice{/if}">{if !$attached_asset.id || $attached_asset.is_binary_image}No embed file selected{else}{$attached_asset.label}{/if}</span>
            </span>
            <a href="#select" id="embed-selector-button" class="button">Select</a>
          </div>
        </div>
      
      </div>
      
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
          {slider name="thumbnail_relative_size" value=$relative_size min="5" max="90" value_unit="%" slidehook="updateSize"} <span id="file-size" style="padding-left:10px"></span>
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

    <div id="embed-resize-options">
      
      <div class="edit-form-row">
        <div class="form-section-label">Width</div>
        <input type="text" name="attached_embed_width" id="attached-embed-width" value="{$manual_width}" style="width:80px" />
        <span id="embed-default-width-holder"{if !$attached_asset_default_width} style="display:none"{/if}>Default: <span id="embed-default-width">{$attached_asset_default_width}</span> <a class="button small" href="#apply-default-width" id="apply-default-width-button"{if $attached_asset_default_width == $manual_width} style="display:none"{/if}>Apply</a></span>
      </div>
      
    </div>
    
    {if !$attached_asset.is_binary_image || !$attached_asset.id}<script type="text/javascript">$('image-resize-options').hide();</script>{/if}
    {if $attached_asset.is_binary_image || !$attached_asset.id}<script type="text/javascript">$('embed-resize-options').hide();</script>{/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">Position</div>
      <a class="attachment-position-selector{if $alignment == "left" && $float} selected{/if}" href="#float-left" id="attachment-float-left" data-float="true" data-align="left"></a>
      <a class="attachment-position-selector{if $alignment == "right" && $float} selected{/if}" href="#float-right" id="attachment-float-right" data-float="true" data-align="right"></a>
      <a class="attachment-position-selector{if $alignment == "center"} selected{/if}" href="#align-center" id="attachment-align-center" data-float="false" data-align="center"></a>
      <a class="attachment-position-selector{if $alignment == "left" && !$float} selected{/if}" href="#align-left" id="attachment-align-left" data-float="false" data-align="left"></a>
      <a class="attachment-position-selector{if $alignment == "right" && !$float} selected{/if}" href="#align-right" id="attachment-align-right" data-float="false" data-align="right"></a>
      
      {makebool value=$float assign="floatbool"}
      
      <input type="hidden" name="attached_file_alignment" id="attached-file-alignment" value="{$alignment}" />
      <input type="hidden" name="attached_file_float" id="attached-file-float" value="{$floatbool.truefalse}" />
      
      <script type="text/javascript">
      {literal}
      $$('a.attachment-position-selector').each(function(a){
        a.observe('click', function(e){
          e.stop();
          $$('a.attachment-position-selector').each(function(aa){
            aa.removeClassName('selected');
          });
          a.addClassName('selected');
          $('attached-file-float').value = a.readAttribute('data-float');
          $('attached-file-alignment').value = a.readAttribute('data-align');
        });
      });
      {/literal}
      </script>
      
    </div>
    
    {* <div class="edit-form-row">
      <div class="form-section-label">Position</div>
      <select name="attached_file_alignment">
        <option value="left"{if $alignment == "left"} selected="selected"{/if}>On the left</option>
        <option value="right"{if $alignment == "right"} selected="selected"{/if}>On the right</option>
        <option value="center"{if $alignment == "center"} selected="selected"{/if}>In the center (non-floating only)</option>
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Float within the text</div>
      {boolean name="attached_file_float" id="attached-file-float" value=$float}
      <!--<input type="checkbox" name="attached_file_float" value="TRUE"{if $float} checked="checked"{/if} id="attached_file_float" />&nbsp;<label for="attached_file_float">Float within the text.</label>-->
    </div> *}
    
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