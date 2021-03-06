<div id="work-area">
  
  {if $mode == 'items'}
  
    {if empty($items)} 
    
    <div class="special-box">You haven't created any {$model.plural_name|strtolower} on this website yet{if $model.id == $item.itemclass_id} (besides this one){/if}.</div>
    
    <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="buttons-bar">
          <input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
        </div>
      </div>
    </div>
    
    {else}
    
    <div class="instruction">Check the boxes next to the {$model.plural_name|strtolower} you'd like to link to this {$item.model.name|strtolower}.</div>
    
    <form action="{$domain}{$section}/updateRelatedItemConnections" method="post" id="update-item-related-items">
    
      <input type="hidden" name="item_id" value="{$item.id}" />
      <input type="hidden" name="model_id" value="{$model.id}" />
      
      <div class="special-box">Search: <input type="text" id="search-query" name="sq" /></div>
    
      <ul class="basic-list icons items" id="available-items-list">
      
        {foreach from=$items item="related_item"}
          {if $related_item.id == $item.id}<!--Skipped item "{$related_item.name}"-->{else}<li data-searchname="{$related_item.name|escape:"quotes"}"><input type="checkbox" name="items[{$related_item.id}]" id="item_{$related_item.id}"{if in_array($related_item.id, $related_ids)} checked="checked"{/if} /> <label for="item_{$related_item.id}">{$related_item.name.xmlentities}</label></li>{/if}
        {/foreach}
      
      </ul>
  
      <div id="edit-form-layout">
          <div class="buttons-bar">
            <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="items-saving-gif" style="display:none" />
            <input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
            <input type="button" value="Save" id="update-item-related-items-button" />
          </div>
      </div>
  
    </form>
    
    <script type="text/javascript">
    var model_id = {$model.id};
    var item_id = {$item.id};
    var update_div_id = 'related-items-{$model.id}-list-container';
  {literal}
    $('update-item-related-items-button').observe('click', function(){
      $('items-saving-gif').show();
      $('update-item-related-items').request({
        onComplete: function(){
          new Ajax.Updater(update_div_id, sm_domain+'ajax:datamanager/getRelatedItemsForItemByModel', {
            parameters: {'model_id': model_id, 'item_id': item_id},
          });
          MODALS.hideViewer();
        } 
      }); 
    });
    
    $('search-query').observe('keyup', function(kevt){
        
        if (kevt.keyCode == Event.KEY_RETURN){
            kevt.stop();
        }
        
        if($F('search-query').charAt(1)){ // One characters or more
            var reg = new RegExp($F('search-query'), 'i');
            $$('#available-items-list li').each(function(li){
                if(li.readAttribute('data-searchname').match(reg)){
                    li.show();
                }else{
                    li.hide();
                }
            });
        }else{
            $$('#available-items-list li').each(function(li){
                li.show();
            });

        }
    });
    
  {/literal}
    </script>
  
    {/if}
  
  {else}
  
  <div class="instruction">Check the boxes next to the pages you'd like to link to this one</div>
  
  <form action="{$domain}{$section}/updateRelatedPageConnections" method="post" id="update-page-related-items">
    
    <input type="hidden" name="item_id" value="{$item.id}" />
    
    <div class="special-box">Search: <input type="text" id="search-query" name="sq" /></div>
    
    <ul class="basic-list icons pages" id="available-pages-list">
      {foreach from=$pages item="relatable_page"}
      
      {if $relatable_page.type == 'NORMAL' && $relatable_page.id != $page.id}
      <li data-searchname="{$relatable_page.name|escape:"quotes"}"><input type="checkbox" name="pages[{$relatable_page.id}]" id="page_{$relatable_page.id}"{if in_array($relatable_page.id, $related_ids)} checked="checked"{/if} /><label for="page_{$relatable_page.id}">{$relatable_page.title|xmlentities}</label></li>
      {/if}
      {/foreach}
    </ul>
  
    <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="buttons-bar">
          <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="pages-saving-gif" style="display:none" />
          <input type="button" value="Cancel" onclick="MODALS.hideViewer();" />
          <input type="button" value="Save" id="update-item-related-pages-button" />
        </div>
      </div>
    </div>
  
  </form>
  
  <script type="text/javascript">
  var item_id = {$item.id};
{literal}
  $('update-item-related-pages-button').observe('click', function(){
    $('pages-saving-gif').show();
    $('update-page-related-items').request({
      onComplete: function(){
        new Ajax.Updater('related-pages-list-container', sm_domain+'ajax:datamanager/getRelatedPagesForItem', {
          parameters: {'item_id': item_id},
        });
        MODALS.hideViewer();
      } 
    }); 
  });
  
  $('search-query').observe('keyup', function(kevt){
      
      if (kevt.keyCode == Event.KEY_RETURN){
          kevt.stop();
      }
      
      if($F('search-query').charAt(1)){ // One characters or more
          var reg = new RegExp($F('search-query'), 'i');
          $$('#available-pages-list li').each(function(li){
              if(li.readAttribute('data-searchname').match(reg)){
                  li.show();
              }else{
                  li.hide();
              }
          });
      }else{
          $$('#available-pages-list li').each(function(li){
              li.show();
          });

      }
  });
  
{/literal}
  </script>
  
  {/if}
</div>