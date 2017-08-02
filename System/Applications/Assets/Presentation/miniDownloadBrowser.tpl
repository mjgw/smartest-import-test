<div id="modal-work-area">
  
  <div id="file-selection-area">
  
    <h3>Choose a downloadable file</h3>
  
    <form action="{$domain}ajax:{$section}/updateFilesSelection" method="post" id="update-selected-files-form">
    
      <input type="hidden" name="item_id" value="{$item.id}" />
      <input type="hidden" name="property_id" value="{$property.id}" />
    
      <div class="instruction">Check the boxes next to the files you'd like to choose</div>
    
      <div class="special-box">Search: <input type="text" id="search-query" name="sq" /></div>
    
      <ul class="basic-list icons files" id="available-files-list">
        {foreach from=$assets item="option"}
        <li data-searchname="{$option.name|escape:"quotes"}">{$option.label}</li>
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
  
  <script type="text/javascript">// <![CDATA[
  
  {literal}
  
  (function(iid){
    
    $('search-query').observe('keyup', function(kevt){
        if (kevt.keyCode == Event.KEY_RETURN){
            kevt.stop();
        }
        
        if($F('search-query').charAt(1)){ // Two characters or more
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
    
  }){/literal}('{$input_id}');
  
  // ]]>
  </script>
  
</div>