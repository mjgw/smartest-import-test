<ul>
{foreach from=$assets item="asset"}
  <li id="assetOption-{$asset.id}" data-fullname="{$asset.label|summary:"80"}">{$asset.label|summary:"40"}<span class="informal"> {$asset.type_info.label}</span></li>
{foreachelse}
  <li id="assetOption-nothing">No matching files found.</li>
{/foreach}
</ul>