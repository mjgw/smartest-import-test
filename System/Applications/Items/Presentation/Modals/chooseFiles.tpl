<div id="modal-work-area">
  
  <div id="file-selection-area">
  
    <h3>Define property <strong>{$property.name}</strong> for item '{$item.name}'</h3>
  
    <form action="{$domain}ajax:{$section}/updateFilesSelection" method="post" id="update-selected-files-form">
    
      <input type="hidden" name="item_id" value="{$item.id}" />
      <input type="hidden" name="property_id" value="{$property.id}" />
    
      <div class="instruction">Check the boxes next to the files you'd like to choose</div>
    
      <div class="special-box">Search: <input type="text" id="search-query" name="sq" /></div>
    
      <ul class="basic-list icons files" id="available-files-list">
        {foreach from=$options item="option"}
        <li data-searchname="{$option.name|escape:"quotes"}"><input type="checkbox" name="files[{$option.id}]" id="item_{$option.id}"{if in_array($option.id, $selected_ids)} checked="checked"{/if} /><label for="item_{$option.id}">{$option.name}</label></li>
        {/foreach}
      </ul>
    
      <div id="edit-form-layout">
        <div class="buttons-bar">
          <input type="button" value="Cancel" id="file-selection-cancel-button" />
          <input type="button" value="Add file" id="file-selection-addfile-button" style="display:none" />
          <input type="submit" name="action" value="Save changes" id="file-selection-save-button" />
        </div>
      </div>
    
    </form>
  
  </div>
  
  <div id="file-addition-area" style="display:none">
    
    <h3>Add {$item.model.name|lower}</h3>
    
    <div class="buttons-bar">
      <input type="button" value="Cancel" id="file-addition-cancel-button" />
      <input type="button" value="Save {$item.model.name|lower}" id="file-addition-save-button" />
    </div>
    
  </div>
  
  <script type="text/javascript">
  
  {literal}
  
  (function(pid, iid, mid){
    
    $('file-selection-save-button').observe('click', function(evt){
      evt.stop();
      $('primary-ajax-loader').show();
      $('update-selected-files-form').request({
          onComplete: function(){
            new Ajax.Updater('choose-assets-property-'+pid+'-summary', sm_domain+'ajax:datamanager/filesSelectionPropertySummary', {
                parameters: { item_id: iid, property_id: pid },
                onSuccess: function(){
                  $('primary-ajax-loader').hide();
                  MODALS.hideViewer();
                }
              });
          }
      });
    });
    
    $('file-selection-addfile-button').observe('click', function(){
        // MODALS.load('datamanager/addNewFileToAssetSelection?class_id='+mid+'&amp;item_id='+iid+'&amp;property_id='+pid, "Add file");
    });
    
    $('file-selection-cancel-button').observe('click', function(evt){
      MODALS.hideViewer();
    });
    
    $('search-query').observe('keyup', function(kevt){
        
        if (kevt.keyCode == Event.KEY_RETURN){
            kevt.stop();
        }
        
        if($F('search-query').charAt(1)){ // One characters or more
            var reg = new RegExp($F('search-query'), 'i');
            $$('#available-files-list li').each(function(li){
                if(li.readAttribute('data-searchname').match(reg)){
                    li.show();
                }else{
                    li.hide();
                }
            });
        }else{
            $$('#available-files-list li').each(function(li){
                li.show();
            });

        }
    });
    
  }){/literal}({$property.id}, {$item.id}, {$item.model.id});
  
  </script>
  
</div>