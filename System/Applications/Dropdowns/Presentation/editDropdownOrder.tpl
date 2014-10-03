<div id="work-area">
  
  {load_interface file="edit_dropdown_tabs.tpl"}
  <h3>Edit dropdown menu order: <span class="light">{$dropdown.label}</span></h3>
  
  <ul class="re-orderable-list div1" id="value-order" style="padding-top:10px">
    {foreach from=$dropdown.options item="option" key="key"}
    <li id="property_{$option.id}">{$option.label}</li>
    {/foreach}
  </ul>
  
  <div class="breaker"></div>

    <div class="buttons-bar"><input type="button" value="Drag values to change order" id="submit-ajax" disabled="disabled" /></div>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
  
    <script type="text/javascript">
  
    var url = sm_domain+'ajax:dropdowns/updateValuesOrder';
    var dropdownId = {$dropdown.id};
  {literal}
    var IDs;
    var IDs_string;
    
    var itemsList = Sortable.create('value-order', {

          onChange: function(){
            IDs = Sortable.sequence('value-order');
            IDs_string = IDs.join(',');
            $('submit-ajax').value = 'Save new order';
            $('submit-ajax').disabled=false;
          },

          constraint: false,
          scroll: window,
          scrollSensitivity: 35

      });
    
      $('submit-ajax').observe('click', function(){
            
        // alert(IDs_string);
            
            $('submit-ajax').disabled = true;
            $('submit-ajax').value = 'Updating...';

            new Ajax.Request(url, {
                method: 'post',
                parameters: {value_ids: IDs_string, dropdown_id: dropdownId},
                onSuccess: function(){
                    $('submit-ajax').value = 'Drag values to change order';
                }
            });
        });

       {/literal}
        
    </script>
  
</div>

<div id="actions-area">
{load_interface file="edit_dropdown_actions_area.tpl"}
</div>