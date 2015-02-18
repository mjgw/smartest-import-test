<select name="{$_input_data.name}">
  {foreach from=$_input_data.options item="license"}
  <option value="{$license.shortname}"{if $_input_data.value.shortname == $license.shortname} selected="selected"{/if}>{$license.label}</option>
  {/foreach}
</select>