<div id="modal-work-area">
  <h3>Define property <strong>{$property.name}</strong> for item '{$item.name}'</h3>
  
  <form action="{$domain}ajax:{$section}/updateItemsSelection" method="post" id="update-selected-items-form">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    <input type="hidden" name="property_id" value="{$property.id}" />
    
    <div class="instruction">Check the boxes next to the items you'd like to choose</div>
    
    <ul class="basic-list icons items">
      {foreach from=$options item="option"}
      <li><input type="checkbox" name="items[{$option.id}]" id="item_{$option.id}"{if in_array($option.id, $selected_ids)} checked="checked"{/if} /><label for="item_{$option.id}">{$option.name}</label></li>
      {/foreach}
    </ul>
    
    <div id="edit-form-layout">
      <div class="buttons-bar">
        <input type="button" value="Cancel" id="item-selection-cancel-button" />
        <input type="submit" name="action" value="Save" id="item-selection-save-button" />
      </div>
    </div>
    
  </form>
  
  <script type="text/javascript">
  
  {literal}
  
  (function(pid, iid){
    
    $('item-selection-save-button').observe('click', function(evt){
      evt.stop();
      $('update-selected-items-form').request({
          onComplete: function(){
              MODALS.hideViewer();
              new Ajax.Updater('choose-items-property-'+pid+'-summary', sm_domain+'ajax:datamanager/itemsSelectionPropertySummary', {
                  parameters: { item_id: iid, property_id: pid }
              });
          }
      });
    });
    
    $('item-selection-cancel-button').observe('click', function(evt){
      MODALS.hideViewer();
    });
    
  }){/literal}({$property.id}, {$item.id});
  
  </script>
  
</div>