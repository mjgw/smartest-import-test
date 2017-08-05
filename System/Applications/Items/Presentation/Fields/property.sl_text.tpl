{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{text_input name=$_input_data.name value=$value id=$_input_data.id}<div class="form-hint">{if strlen($property.hint)}{$property.hint}{/if} Max 255 characters</div>

<div id="autocomplete_choices_{$property.id}" class="autocomplete" style="display:none"></div>
<script type="text/javascript">
{literal}(function(pid, iid){
  new Ajax.Autocompleter(iid, "autocomplete_choices_"+pid, sm_domain+"ajax:datamanager/getTextIpvAutoSuggestValues", {
    paramName: "str", 
    minChars: 2,
    delay: 50,
    width: 300,
    parameters: 'property_id='+pid,
  });
}){/literal}({$property.id}, '{$_input_data.id}');
</script>