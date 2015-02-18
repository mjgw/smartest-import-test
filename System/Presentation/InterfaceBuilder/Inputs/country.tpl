<select name="{$_input_data.name}">
  {foreach from=$_input_data.options key="code" item="country"}
  <option value="{$code}"{if $_input_data.value.code == $code} selected="selected"{/if}>{$country}</option>
  {/foreach}
</select>