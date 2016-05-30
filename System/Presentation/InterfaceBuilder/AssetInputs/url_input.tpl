<div class="edit-form-row">
    <div class="form-section-label">Enter URL</div>
    <input type="text" name="asset_url" style="width:520px" id="url-input" /> <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="check-url-loader" style="display:none" />
    <div class="form-hint">Don't forget the http://</div>
    {if $type_code == 'SM_ASSETTYPE_OEMBED_URL'}
    <div class="edit-form-sub-row">
      <span id="url-ok" style="display:none" class="feedback-ok"><i class="fa fa-check-circle"></i> <span id="url-service-label"></span></span>
      <span id="url-invalid" style="display:none" class="feedback-bad"><i class="fa fa-times"></i> <span id="url-error-label"></span></span>
    </div>
    {/if}
    {* <br /><input type="checkbox" name="check_url" id="check-url" value="1" /><label for="check-url">Check this URL</label> *}
</div>

<div class="edit-form-row">
    <div class="form-section-label">Attempt thumbnail?</div>
    <input type="checkbox" name="retrieve_thumbnail" id="retrieve-thumbnail" value="1" checked="" /><label for="retrieve-thumbnail"> Try to create thumbnail using Open Graph</label>
</div>

{if $type_code == 'SM_ASSETTYPE_OEMBED_URL'}
<script type="text/javascript">
{literal}
document.observe('dom:loaded', function(){
  $('confirm-asset-create').hide();
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
            $('url-ok').show();
            $('confirm-asset-create').show();
          }else{
            $('check-url-loader').hide();
            $('url-invalid').show();
            $('url-error-label').update(response.responseJSON.message);
          }
          
        }
      });
    }, 1500);
  });
});
// http://www.scribd.com/doc/110799637/Synthesis-of-Knowledge-Effects-of-Fire-and-Thinning-Treatments-on-Understory-Vegetation-in-Dry-U-S-Forests
{/literal}
</script>
{/if}