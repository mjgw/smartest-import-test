<div id="modal-work-area">
  <form action="" method="post" id="create-gallery-form">
    
    <input type="hidden" name="item_id" value="{$item.id}" id="create-gallery-item-id" />
    <input type="hidden" name="property_id" value="{$property.id}" id="create-gallery-property-id" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Name this gallery</div>
      <input type="text" name="asset_gallery_label" value="{$start_name}" id="asset-gallery-label" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Which files can go in this gallery?</div>
      <select name="asset_gallery_type" id="asset-gallery-type">
    
        <option value="ALL">Any gallery-compatible file</option>
    
        <optgroup label="Placeholder types">
{foreach from=$gallery_placeholder_types item="type"}
          <option value="P:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
        </optgroup>

        <optgroup label="Specific file types">
{foreach from=$gallery_asset_types item="type"}
          <option value="A:{$type.id}"{if $filter_type == $type.id} selected="selected"{/if}>{$type.label}</option>
{/foreach}
        </optgroup>
    
        <optgroup label="Existing file groups">
{foreach from=$gallery_groups item="group"}
          <option value="G:{$group.id}">Files from '{$group.label}'</option>
{foreachelse}
          <option value="" disabled="disabled">No matching file groups</option>
{/foreach}
        </optgroup>
    
      </select>
    </div>
    <div class="buttons-bar">
      <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" style="display:none" id="gallery-creation-loader" />
      <input type="button" value="Cancel" id="cancel-new-gallery-button" />
      <input type="button" value="Save" id="save-new-gallery-button" />
    </div>
  </form>
  
  <script type="text/javascript">
  var reorderButtonId = 'reorder-gallery-property-{$property.id}';
  {literal}
  $('cancel-new-gallery-button').observe('click', function(e){
    e.stop();
    MODALS.hideViewer();
  });
  $('save-new-gallery-button').observe('click', function(e){
    e.stop();
    $('gallery-creation-loader').show();
    new Ajax.Request(sm_domain+'ajax:assets/insertGalleryForIpv', {
      method:'post',
      parameters: $('create-gallery-form').serialize(true),
      onSuccess: function(response){
        // console.log(response.responseJSON);
        var opt = new Element('option', { value: response.responseJSON.id, selected: 'selected' }).update(response.responseJSON.label);;
        $('item_property_165').appendChild(opt);
        $(reorderButtonId).show();
        $('gallery-creation-loader').hide();
        MODALS.hideViewer();
      }
    });
  });
  {/literal}
  </script>
  
</div>