<div id="work-area">
  
  {load_interface file="edit_set_tabs.tpl"}
  
  <h3>Edit set order: <span class="light">{$set.label}</span></h3>
  
  {if count($items)}
  
  <div class="special-box">
    Show as <select name="col_count" id="col-count">
      <option value="1"{if $num_cols == "1"} selected="selected"{/if}>One column</option>
      <option value="2"{if $num_cols == "2"} selected="selected"{/if}>Two columns</option>
      <option value="3"{if $num_cols == "3"} selected="selected"{/if}>Three columns</option>
      <option value="4"{if $num_cols == "4"} selected="selected"{/if}>Four columns</option>
      <option value="5"{if $num_cols == "5"} selected="selected"{/if}>Five columns</option>
    </select>
  </div>
  
  <ul class="re-orderable-list div{$num_cols}" id="static-set-order" style="padding-top:10px">
    {foreach from=$items item="item" key="key"}
    <li id="item_{$item.id}">{$item.name}
      {* <div class="buttons">{if $key > 0}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=up"><img src="{$domain}Resources/Icons/arrow_up.png" alt="up" /></a>{/if}
      {if $key < count($items)-1}<a href="{$domain}sets/moveItemInStaticSet?set_id={$set.id}&amp;item_id={$item.id}&amp;direction=down"><img src="{$domain}Resources/Icons/arrow_down.png" alt="down" /></a>{/if}</div> *}
    </li>
    {/foreach}
  </ul>
  
  <script type="text/javascript">
    var currentColStyle = 'div{$num_cols}';
    {literal}
    $('col-count').observe('change', function(){
        $('static-set-order').removeClassName(currentColStyle);
        $('static-set-order').addClassName('div'+$('col-count').value);
        currentColStyle = 'div'+$('col-count').value;
        PREFS.setApplicationPreference('reorder_static_set_num_cols', $('col-count').value);
    });
    {/literal}
  </script>
  
  <div class="breaker"></div>
  
  <div class="buttons-bar">
    <input type="button" value="Drag items to change order" id="submit-ajax" disabled="disabled" />
    <input type="button" value="Done" onclick="cancelForm();">
  </div>
  
  <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
    
    <script type="text/javascript">
      var url = sm_domain+'ajax:sets/updateStaticSetOrder';
      var setId = {$set.id};
    {literal}
      var IDs;
      var IDs_string;
      
      // Position.includeScrollOffsets = true;
      var itemsList = Sortable.create('static-set-order', {
          
          onUpdate: function(){
            IDs = Sortable.sequence('static-set-order');
            IDs_string = IDs.join(',');
            $('submit-ajax').value = 'Save new order';
            $('submit-ajax').disabled=false;
          },
          
          constraint: false,
          scroll: window,
          scrollSensitivity: 35
          
      });
      
      $('submit-ajax').observe('click', function(){
          
          $('submit-ajax').disabled = true;
          $('submit-ajax').value = 'Updating...';
          
          new Ajax.Request(url, {
              method: 'post',
              parameters: {item_ids: IDs_string, set_id: setId},
              onSuccess: function(){
                  $('submit-ajax').value = 'Drag items to change order';
              }
          });
      });
      
     {/literal}
    </script>
  
  {else}
  <div class="warning">There are currently no items in this set. <a href="{$domain}{$section}/editSet?set_id={$set.id}">Click here</a> to add some.</div>
  {/if}
  
</div>

<div id="actions-area">
  
  <ul class="actions-list">
    <li><b>Options</b></li>
    {if $request_parameters.page_id && $request_parameters.from == 'preview'}
      <li class="permanent-action"><a href="#" onclick="window.location='{$domain}websitemanager/preview?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}'"><i class="fa fa-check"></i> Return page preview</a></li>
    {elseif $request_parameters.page_id && $request_parameters.from == 'fullPreview'}
      <li class="permanent-action"><a href="#" onclick="window.location='{$domain}website/renderEditableDraftPage?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}&amp;hide_newwin_link=true'"><i class="fa fa-check"></i> Return page preview</a></li>
	  {elseif $request_parameters.item_id && $request_parameters.from != 'preview' && $request_parameters.from != 'fullPreview'}
      <li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/editItem?item_id={$request_parameters.item_id}'"><i class="fa fa-check"></i> Return to editing item</a></li>
    {/if}
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/editSet?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}'"><i class="fa fa-pencil"></i> Edit set contents</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/previewSet?set_id={$set.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}'"><i class="fa fa-search"></i> Browse set contents</a></li>
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}{$section}/deleteSetConfirm?set_id={$set.id}'"><i class="fa fa-trash"></i> Delete this set</a></li>
		
  </ul>
  
</div>