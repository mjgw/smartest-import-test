<div id="modal-work-area">
  <h3>File display parameters</h3>
  
  <div class="instruction">How should this {$asset_type.label} look on {$model.name} &quot;{$item.name}&quot;</div>
  
  <form action="{$domain}ipv:{$section}/updateAssetData" method="post" id="edit-asset-data-form">
  
  <input type="hidden" name="item_id" value="{$item.id}" />
  <input type="hidden" name="property_id" value="{$property.id}" />

  <div class="edit-form-row">
    <div class="form-section-label">Chosen File:</div>
    <b>{$asset.stringid}</b> ({if $asset_type.storage.type == 'file'}{$asset_type.storage.location}{/if}{$asset.url}) - {$asset_type.label}
  </div>

{foreach from=$params key="parameter_name" item="parameter"}
  <div class="edit-form-row">
    <div class="form-section-label">{$parameter_name}</div>
    <input type="text" name="params[{$parameter_name}]" style="width:250px" value="{$parameter.value}" />
  </div>
{/foreach}

  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" value="Cancel" id="cancel-button" />
      <input type="submit" value="Save changes" id="save-button" />
    </div>
  </div>
  
  </form>
  
  <script type="text/javascript">
  {literal}
    $('cancel-button').observe('click', function(e){
      e.stop();
      MODALS.hideViewer();
    });
    $('save-button').observe('click', function(e){
      e.stop();
      $('edit-asset-data-form').request({
        onComplete: function(){
          MODALS.hideViewer();
        }
      });
    });
  {/literal}
  </script>
  
</div>