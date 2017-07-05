{if empty($groups)}
  <span class="form-hint">No groups currently exist that exlusively contain files that accepted by this placeholder type{if $final_type} <a href="{$domain}assets/newAssetGroup?filter_type={$final_type}" class="button small">create one</a>{/if}</span>
  <input type="hidden" name="placeholder_filegroup" value="NONE" />
{else}
  <select name="placeholder_filegroup">
    <option value="NONE">Do not limit - Allow all files of the correct types</option>
    {foreach from=$groups item="group"}
    <option value="{$group.id}">{$group.label}</option>
    {/foreach}
  </select>
{/if}