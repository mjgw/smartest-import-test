<input type="text" name="{$_input_data.name}" value="{$_input_data.value}" data-lastvalue="{$_input_data.value}" id="{$_input_data.id}" class="sm-url-input" /><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="{$_input_data.id}-check-url-loader" style="margin-left:5px;display:none" />

<div class="form-hint">Don't forget the http:// or https://</div>
<div class="breaker"> </div>

<div class="edit-form-sub-row">
  <span id="{$_input_data.id}-url-ok" style="{if $_input_data.value.external_media_info.valid}{else}display:none{/if}" class="feedback-ok" title="Smartest recognises this URL and can display its content"><i class="fa fa-check-circle"> </i> <span id="{$_input_data.id}-url-service-label">{if $_input_data.value.external_media_info.valid}{$_input_data.value.external_media_info.data.label}{/if}</span></span>
  <span id="{$_input_data.id}-url-invalid" style="display:none" class="feedback-bad"><i class="fa fa-times"> </i> <span id="{$_input_data.id}-url-error-label"> </span></span>
  <div id="{$_input_data.id}-preview-box" style="margin-top:10px;max-width:400px;{if $_input_data.value.is_valid}{else}display:none{/if}">{$_input_data.value.preview_markup} </div>
</div>

<script type="text/javascript">
  
(function(inputId){ldelim}
{literal}

var isValid;

$(inputId).observe('keyup', function(evt){
  if($F(inputId).match(/^https?:\/\/\w+\.\w{2,}.*/) || $F(inputId) == ''){
    $(inputId).removeClassName('error');
    isValid = true;
  }
});

$(inputId).observe('blur', function(evt){
  
  if($F(inputId).match(/^https?:\/\/\w+\.\w{2,}.*/)){
    $(inputId).removeClassName('error');
    isValid = true;
  }else{
    if($F(inputId)){
      $(inputId).addClassName('error');
      isValid = false;
    }
  }
  
  if($F(inputId) != $(inputId).readAttribute('data-lastvalue')){
    
    $(inputId+'-check-url-loader').show();
    $(inputId+'-url-ok').hide();
    
    if(window.checkTimeOut){
      clearTimeout(window.checkTimeOut);
    }
    
    window.checkTimeOut = setTimeout(function(){
      
      new Ajax.Request(sm_domain+'ajax:assets/validateExternalResourceUrl', {
        
        parameters: {
          url: $F(inputId)
        },
        onSuccess: function(response) {
          
          $(inputId+'-preview-box').show();
          new Ajax.Updater(inputId+'-preview-box', sm_domain+'ajax:assets/urlPreview', {
            parameters: { url: $F(inputId) },
            onSuccess: function(response) {
              $(inputId+'-check-url-loader').hide();
            }
          });
          
          if(response.responseJSON.valid){
            $(inputId+'-url-service-label').update(response.responseJSON.data.label);
            $(inputId+'-url-ok').show();
            $(inputId+'-check-url-loader').hide();
          }else{
            $(inputId+'-check-url-loader').hide();
            // $(inputId+'-preview-box').hide();
          }
          
        }
      });
      
    }, 1500);
    
    $(inputId).writeAttribute('data-lastvalue', $F(inputId));
    
  }
    
});
  
{/literal}
{rdelim})('{$_input_data.id}');

</script>