<div id="work-area">
  
  <h3>Edit variant order: <span class="light">{$parent_item.name}</span></h3>
  
  <ul class="re-orderable-list div1" id="item-order" style="padding-top:10px">
    {foreach from=$items item="sub_item" key="key"}
    <li id="item_{$sub_item.id}">{$sub_item.name}</li>
    {/foreach}
  </ul>
  
  <div class="buttons-bar">
    <input type="button" value="Drag values to change order" id="submit-ajax" disabled="disabled" />
    <input type="button" value="Done" onclick="window.location='{$domain}datamanager/getSubModelItems?item_id={$parent_item.id}&amp;sub_model_id={$sub_model.id}'" />
  </div>
  <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>

  <script type="text/javascript">

  var url = sm_domain+'ajax:datamanager/updateSubModelItemOrder';
  var parentItemId = '{$parent_item.id}';
  var subModelId = '{$sub_model.id}';
{literal}
  var IDs;
  var IDs_string;
  
  var itemsList = Sortable.create('item-order', {

        onChange: function(){
          IDs = Sortable.sequence('item-order');
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
              parameters: {value_ids: IDs_string, parent_item_id: parentItemId, sub_model_id: subModelId},
              onSuccess: function(){
                  $('submit-ajax').value = 'Drag values to change order';
              }
          });
          
      });

     {/literal}
      
  </script>
  
</div>