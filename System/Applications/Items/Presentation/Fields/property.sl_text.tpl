{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{text_input name=$_input_data.name value=$value id=$_input_data.id}<div class="form-hint">{if strlen($property.hint)}{$property.hint}{/if} Max 255 characters</div>

<div id="autocomplete_choices_{$property.id}" class="autocomplete" style="display:none"></div>

<script type="text/javascript">
var pid_ = 
new Ajax.Autocompleter("{$_input_data.id}", "autocomplete_choices_{$property.id}", "/ajax:datamanager/getTextIpvAutoSuggestValues", {literal}{
    paramName: "str", 
    minChars: 2,
    delay: 50,
    width: 300,
    {/literal}parameters: 'property_id={$property.id}',{literal}
});

{/literal}

</script>