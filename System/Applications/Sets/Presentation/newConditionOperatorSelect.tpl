{if $aspect == "_SMARTEST_ITEM_PIECE_ITSELF"}
<select name="new_condition_operator" id="new-condition-operator">
  <option value="8">Is Tagged With</option>
  <option value="9">Is Not Tagged With</option>
</select>
<script type="text/javascript">newConditionMaker.setOperator(8);</script>
{elseif $aspect == "_SMARTEST_ITEM_ID"}
<select name="new_condition_operator" id="new-condition-operator">
  <option value="1">Does Not Equal</option>
  <option value="6">Is Greater Than</option>
  <option value="7">Is Less Than</option>
</select>
<script type="text/javascript">newConditionMaker.setOperator('1');</script>
{elseif $aspect == "_SMARTEST_ITEM_NUM_HITS" || $aspect == "_SMARTEST_ITEM_NUM_COMMENTS"}
<select name="new_condition_operator" id="new-condition-operator">
  <option value="0">Equals</option>
  <option value="1">Does Not Equal</option>
  <option value="6">Is Greater Than</option>
  <option value="7">Is Less Than</option>
</select>
<script type="text/javascript">newConditionMaker.setOperator('0');</script>
{elseif $aspect == "_SMARTEST_ITEM_NAME"}
<select name="new_condition_operator" id="new-condition-operator">
  <option value="1">Does Not Equal</option>
  <option value="2">Contains</option>
  <option value="3">Does Not Contain</option>
  <option value="4">Starts With</option>
  <option value="5">Ends With</option>
</select>
<script type="text/javascript">newConditionMaker.setOperator('1');</script>
{else}
<select name="new_condition_operator" id="new-condition-operator">
  {if $aspect != '_SMARTEST_ITEM_ID'}<option value="0">Equals</option>{/if}
  <option value="1">Does not equal</option>
  <option value="2">Contains</option>
  <option value="3">Does not contain</option>
  <option value="4">Starts with</option>
  <option value="5">Ends with</option>
  {if $ordinary_property_available}
  {if $property._type_info.chronological && $property.datatype != 'SM_DATATYPE_CALENDAR_EVENT'}
  <option value="7">Is before</option>
  <option value="6">Is after</option>
  <option value="22" data-hidevalueinput="true">Is in the past</option>
  <option value="23" data-hidevalueinput="true">Is in the future</option>
  {elseif $property.datatype == 'SM_DATATYPE_NUMERIC'}
  <option value="6">Is greater than</option>
  <option value="7">Is less than</option>
  {/if}
  {else}
  {if $aspect != '_SMARTEST_ITEM_NAME'}<option value="6">Is Greater Than</option>{/if}
  {if $aspect != '_SMARTEST_ITEM_NAME'}<option value="7">Is Less Than</option>{/if}
  {/if}
  <option value="128" data-hidevalueinput="true">Has value</option>
</select>
<script type="text/javascript">newConditionMaker.setOperator('0');</script>
{/if}

<script type="text/javascript">
  {literal}
  $('new-condition-operator').observe('change', function(){
    
    var option = $$('#new-condition-operator option')[$('new-condition-operator').selectedIndex];
    
    if(option.readAttribute('data-hidevalueinput') == 'true'){
      $('new-condition-value-input').hide();
    }else{
      $('new-condition-value-input').show();
    }
    
    newConditionMaker.setOperator();
    
  });
  {/literal}
</script>