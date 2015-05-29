<div class="work-area">
  
  <form action="{$domain}{$section}/updateAsset" method="post" id="modal-file-params-editor-form" enctype="multipart/form-data">
  
    <input type="hidden" name="asset_id" value="{$asset.id}" />
  
    <div id="edit-form-layout">
    
      {foreach from=$asset._editor_parameters key="parameter_name" item="parameter"}
      <div class="edit-form-row">
        <div class="form-section-label">{$parameter.label}</div>
        {if $parameter.datatype == 'SM_DATATYPE_BOOLEAN'}
        {capture name="name" assign="name"}params[{$parameter_name}]{/capture}
        {capture name="param_id" assign="param_id"}asset-parameter-{$parameter_name}{/capture}
        {boolean name=$name id=$param_id value=$parameter.value}
        {else}
        {if $parameter.has_options}
        <select name="params[{$parameter_name}]">
          {if !$parameter.required}<option value=""></option>{/if}
        {foreach from=$parameter.options item="opt" key="key"}
          <option value="{$key}"{if $parameter.value == $key} selected="selected"{/if}>{$opt}</option>
        {/foreach}
        </select>
        {else}
        <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" />
        {/if}
        {/if}
      </div>
      {/foreach}
  
      <div class="edit-form-row">
        <div class="buttons-bar">
          <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="modal-ajax-loader" style="display:none" />
          <input type="button" onclick="MODALS.hideViewer();" value="Cancel" />
          <input type="submit" id="modal-file-params-editor-saver" value="Save" />
        </div>
      </div>
  
    </div>
  
  </form>
  
  <script type="text/javascript">
  
  {literal}
  
  var saveFileParamValues = function(){
    
    $('modal-ajax-loader').show();
    $('modal-file-params-editor-form').request({
      onSuccess: function(){
        $('modal-ajax-loader').hide();
        MODALS.hideViewer();
      }
    });
    
  };
  
  $('modal-file-params-editor-saver').observe('click', function(e){
    e.stop();
    saveFileParamValues();
  });
  
  $('modal-file-params-editor-form').observe('keypress', function(e){
    if(e.keyCode == 13){
      e.stop();
      saveFileParamValues();
    }
  });
  
  {/literal}
  
  </script>
  
</div>