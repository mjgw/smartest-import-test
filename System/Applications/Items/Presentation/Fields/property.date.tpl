{day_input name=$_input_data.name value=$value id=$_input_data.property_id property=$property}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}