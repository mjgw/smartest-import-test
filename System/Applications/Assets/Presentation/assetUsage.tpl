<div id="work-area">
  
  {load_interface file="edit_asset_tabs.tpl"}
  <h3>File usage: <span class="light">&ldquo;{$asset.label}&rdquo;</span></h3>
  
  {if count($usages)}
  <ul class="objects-list">
  {foreach from=$usages item="usage"}
  <li class="{cycle values="odd,even"}">
    {$usage.nice_type}:
    {if $usage.type == "SM_ASSETUSAGETYPE_PLACEHOLDER"}
    Placeholder <strong>{$usage.placeholder.name}</strong> on page &lsquo;<strong>{$usage.page.title}</strong>&rsquo; <a href="{$usage.page.action_url}" class="button small">Edit page</a>
    {elseif $usage.type == "SM_ASSETUSAGETYPE_ITEMPROPERTY"}
    Property <strong>{$usage.itemproperty.name}</strong> of {$usage.item.model.name|strtolower} item &lsquo;<strong>{$usage.item}</strong>&rsquo; <a href="{$usage.item.action_url}" class="button small">Edit {$usage.item.model.name|strtolower}</a>
    {elseif $usage.type == "SM_ASSETUSAGETYPE_ATTACHMENT"}
    Attachment <strong>{$usage.attachment.instance_name}</strong> of host file &lsquo;<strong>{$usage.host_file.label}</strong>&rsquo; <a href="{$domain}assets/defineAttachment?attachment={$usage.attachment.instance_name}&amp;asset_id={$usage.host_file.id}" class="button small">Edit attachment</a> <a href="{$domain}assets/editTextFragmentSource?asset_id={$usage.host_file.id}" class="button small">Edit host file</a>
    {/if}
  </li>
  {/foreach} 
  </ul>
  {else}
  <div class="special-box">
    This file does not appear to have been used anywhere yet.
  </div>
  {/if}
      
  
  
</div>