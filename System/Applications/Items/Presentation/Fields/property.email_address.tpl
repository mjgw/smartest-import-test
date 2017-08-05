{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{email_input name=$_input_data.name value=$value id=$property_id}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}

<div id="autocomplete_choices_{$property.id}" class="autocomplete" style="display:none"></div>
<script type="text/javascript">
{literal}(function(pid, iid){
  new Ajax.Autocompleter(iid, "autocomplete_choices_"+pid, sm_domain+"ajax:datamanager/getTextIpvAutoSuggestValues", {
    paramName: "str", 
    minChars: 2,
    delay: 50,
    width: 300,
    parameters: 'type=email&property_id='+pid,
  });
}){/literal}({$property.id}, '{$_input_data.id}');
</script>