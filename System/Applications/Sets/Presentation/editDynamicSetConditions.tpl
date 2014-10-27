<script language="javascript">{literal}

function updateSetConditionsFormFromOperator(condition, value){
  
  if(condition=='new'){
    
    $('add_new_condition').checked=true;
    
    var no_property = 'no-property-input-new-condition';
    var choose_property = 'property-input-new-condition';
    
  }else{
    var no_property = 'no-property-input-'+condition;
    var choose_property = 'property-select-input-'+condition;
  }
  
  if(value == 8 || value == 9){
    $(no_property).style.display='inline';
    $(choose_property).style.display='none';
  }else{
    $(no_property).style.display='none';
    $(choose_property).style.display='inline';
  }
  
}

{/literal}
</script>

<style type="text/css">
{literal}
  #new-condition-value-input select{
    width:250px;
  }
{/literal}
</style>

<div id="work-area">
  
  {load_interface file="edit_set_tabs.tpl"}
  
          <h3>Dynamic set conditions</h3>

    		  <script type="text/javascript" src="{$domain}Resources/System/Javascript/smartest/dynamic-set-builder.js"></script>
    		  
    		  <script type="text/javascript">
    		    var newConditionMaker = new Smartest.DynamicSetBuilder;
    		  </script>
              
              <form id="pageViewForm" method="post" action="{$domain}{$section}/updateDynamicSetConditions">
                  
                  <input type="hidden" name="set_id" value="{$set.id}" />
              
    		  <table id="rules-list">

  {if empty($conditions)}			
            <tr><td colspan="4"><div>There are no conditions for this data set yet</div></td></tr>
  {else}
            <tr><td colspan="4"><div>Retrieve all {$model.plural_name} where:</div></td></tr>
  {/if}

  {foreach from=$conditions item="rule" }
            <tr id="rule-tr-{$rule.itemproperty_id}">            
              <td>
                <span id="no-property-input-{$rule.id}" style="{if $rule.itemproperty_id == '_SMARTEST_ITEM_TAGGED'}display:inline{else}display:none{/if}">The {$model.name}
  					    <input value="_SMARTEST_ITEM_TAGGED" name="conditions[{$rule.id}][property_id]" type="hidden" /></span>
    					  <select name="conditions[{$rule.id}][property_id]" id="property-select-input-{$rule.id}" style="{if $rule.itemproperty_id == '_SMARTEST_ITEM_TAGGED'}display:none{else}display:inline{/if}">
    						  <option value="_SMARTEST_ITEM_NAME" {if $rule.itemproperty_id == "_SMARTEST_ITEM_NAME"} selected{/if}>{$model.name} {$model.item_name_field_name}</option>
    						  <option value="_SMARTEST_ITEM_ID" {if $rule.itemproperty_id == "_SMARTEST_ITEM_ID"} selected{/if}>{$model.name} ID</option>
    					    {foreach from=$properties item="property"}
    						  <option value="{$property.id}" {if $property.id == $rule.itemproperty_id} selected{/if}>{$property.name}</option>
                  {/foreach}
    						  <option value="_SMARTEST_ITEM_NUM_HITS" {if $rule.itemproperty_id == "_SMARTEST_ITEM_NUM_HITS"} selected{/if}>Number of hits</option>
    						  <option value="_SMARTEST_ITEM_NUM_COMMENTS" {if $rule.itemproperty_id == "_SMARTEST_ITEM_NUM_COMMENTS"} selected{/if}>Number of comments</option>
    					  </select>
  					  </td>
  					  <td>
    					  <select name="conditions[{$rule.id}][operator]" onchange="updateSetConditionsFormFromOperator('{$rule.id}', this.value)">
    						  {if $rule.itemproperty_id != "_SMARTEST_ITEM_ID"}<option value="0" {if $rule.operator == "0"} selected="selected" {/if}>Equals</option>{/if}
    						  <option value="1" {if $rule.operator == "1"} selected="selected" {/if}>Does not equal</option>
    						  <option value="2" {if $rule.operator == "2"} selected="selected" {/if}>Contains</option>
    						  <option value="3" {if $rule.operator == "3"} selected="selected" {/if}>Does not contain</option>
    						  <option value="4" {if $rule.operator == "4"} selected="selected" {/if}>Starts with</option>
    						  <option value="5" {if $rule.operator == "5"} selected="selected" {/if}>Ends with</option>
{if $rule.type_info.chronological}
    						  <option value="6" {if $rule.operator == "6"} selected="selected" {/if}>Is after</option>
    						  <option value="7" {if $rule.operator == "7"} selected="selected" {/if}>Is before</option>
                  <option value="22" {if $rule.operator == "22"} selected="selected" {/if}>Is in the past</option>
                  <option value="23" {if $rule.operator == "23"} selected="selected" {/if}>Is in the future</option>
{else}
    						  <option value="6" {if $rule.operator == "6"} selected="selected" {/if}>Greater than</option>
    						  <option value="7" {if $rule.operator == "7"} selected="selected" {/if}>Less than</option>
{/if}
    						  <option value="8" {if $rule.operator == "8"} selected="selected" {/if}>Is tagged with</option>
    						  <option value="9" {if $rule.operator == "9"} selected="selected" {/if}>Is not tagged with</option>
    					  </select>
    					</td>
  						<td>
{if !is_numeric($rule.itemproperty_id) || $rule.operator < 20}
                <input type="text" value="{$rule.value}" name="conditions[{$rule.id}][value]" />
{/if}
              </td>
              <td><a class="button" href="#remove" onclick="window.location='{$domain}{$section}/removeConditionFromSet?condition_id={$rule.id}'">Remove</a></td>

          </tr>
  {/foreach}

        <!--Add new condition?-->
        <tr id="add-new-condition-checkbox-holder">
  			  <td colspan="4"><div>Add a new Condition:</div></td>
        </tr>

        <!--New condition form-->
  			<tr id="add-new-condition">
  			  <td>

  				  <select name="new_condition_property_id" id="new-condition-aspect">
    				  <option value="IGNORE">Choose...</option>
    				  <option value="_SMARTEST_ITEM_PIECE_ITSELF" id="nc_name">The {$model.name} itself</option>
    				  <option value="_SMARTEST_ITEM_NAME" id="nc_name">{$model.name} {$model.item_name_field_name}</option>
    				  <option value="_SMARTEST_ITEM_ID" id="nc_id">{$model.name} ID</option>
              {foreach from=$properties item="property"}<option value="{$property.id}">{$property.name}</option>{/foreach}
  					  <option value="_SMARTEST_ITEM_NUM_HITS">Number of hits</option>
  					  <option value="_SMARTEST_ITEM_NUM_COMMENTS">Number of comments</option>
    		    </select>

    		    <script type="text/javascript">
    		      {literal}
    		      $('new-condition-aspect').observe('change', function(){
    		        newConditionMaker.setAspect();
    		      });
    		      {/literal}
    		    </script>

    		  </td>

    			<td>
    			  <div id="new-condition-operator-input"><span style="display:none;color:#999" id="operator-loading-text">loading...</span></div>
    			</td>

    			<td>
    			  <div id="new-condition-value-input"><span style="display:none;color:#999" id="value-loading-text">loading...</span></div>
    			</td>

    			<td></td>

        </tr>
      </table>
      
      <div class="edit-form-row">
        <div class="buttons-bar">
          {* <input type="button" value="Cancel" />
          <input type="submit" value="Save Changes" /> *}
          {save_buttons}
        </div>
      </div>
      
    </form>
      
</div>

<div id="actions-area"></div>