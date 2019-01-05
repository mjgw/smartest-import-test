<div id="work-area">

  <h3 id="page-name">Add New URL</h3>

  <form id="addUrl" name="addUrl" action="{$domain}{$section}/insertPageUrl" method="post" style="margin:0px">

  <input type="hidden" name="page_id" value="{$pageInfo.id}" />
  <input type="hidden" name="page_webid" value="{$pageInfo.webid}" />
  {if $is_item_page && $is_valid_item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

  <div id="edit-form-layout">

{if $is_item_page && $is_valid_item}
    <div class="modal-error" style="display:none" id="urls-error">*</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">{$item._model.name} {$item._model.infn|lower}</div>
      {$item.name}
    </div>
{/if}

    <div class="edit-form-row">
      <div class="form-section-label">New URL, not including leading '/'</div>
      <input type="text" name="page_url" value="" id="page-url-string" autocomplete="off" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label"><label for="forward_to_default">Redirect to default URL</label></div>
      <input type="checkbox" name="forward_to_default" id="forward_to_default" value="1" onchange="toggleFormAreaVisibilityBasedOnCheckbox('forward_to_default', 'show-redirect-type');" />
    </div>
    
    <div style="display:none;width:100%;clear:both;padding:0px" id="show-redirect-type">
      <div class="edit-form-row">
        <div class="form-section-label">Redirect type</div>
        <select name="url_redirect_type">
          <option value="301"{if $url.redirect_type == "301"} selected="selected"{/if}>301 Moved Permanently (SEO friendly - recommended)</option>
          <option value="302"{if $url.redirect_type == "302"} selected="selected"{/if}>302 Found (unspecified reason)</option>
          <option value="303"{if $url.redirect_type == "303"} selected="selected"{/if}>303 See other</option>
          <option value="307"{if $url.redirect_type == "307"} selected="selected"{/if}>307 Temporary redirect</option>
        </select>
      </div>
    </div>
    
{if $is_item_page && $is_valid_item}
    <div class="edit-form-row">
      <div class="form-section-label">Applies to</div>
      <select name="page_url_type" id="page-url-type">
        <option value="SINGLE_ITEM">This {$item._model.name|lower} only</option>
        <option value="ALL_ITEMS">All {$item._model.plural_name|lower}</option>
      </select>
    </div>
{/if}

    <div class="edit-form-row">
      <div class="buttons-bar">
        <img src="{$domain}Resources/System/Images/ajax-loader.gif" style="display:none" id="saver-gif" alt="" />
      	<input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
      	<input type="button" name="action" value="Save" id="save-button" />
      </div>
    </div>

  </div>

  </form>

{if $is_item_page}

{if $is_valid_item}
  <script type="text/javascript">
{literal}
  $('save-button').observe('click', function(evt){
    if($F('page-url-type') == 'ALL_ITEMS'){
      if(/(:|\$)(name|id|long_id)/.match($F('page-url-string'))){
        if($F('page-url-string').charAt(0)){
          saveNewPageUrl();
          $('urls-error').hide();
        }else{
          $('urls-error').update("The URL field cannot be blank");
          $('urls-error').appear({duration:0.3});
        }
      }else{
        evt.stop();
        $('urls-error').update("No matching URL parts. The new URL must contain ':name', ':id' or ':long_id' in order to enable each item to be recognised.");
      }
    }else{
      if($F('page-url-string').charAt(0)){
        saveNewPageUrl();
        $('urls-error').hide();
      }else{
        $('urls-error').update("The URL field cannot be blank");
        $('urls-error').appear({duration:0.3});
      }
    }
  });
{/literal}
  </script>
{/if}

{else}

  <script type="text/javascript">
{literal}
  $('save-button').observe('click', function(evt){
    if($F('page-url-string').charAt(0)){
      saveNewPageUrl();
      $('urls-error').hide();
    }else{
      $('urls-error').update("The URL field cannot be blank");
      $('urls-error').appear({duration:0.3});
    }
  });
{/literal}
  </script>

{/if}

</div>