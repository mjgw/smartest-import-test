<div id="work-area">
  <h3>{$property.name}</h3>
  Item: {$item.name} <a href="{$domain}datamanager/editItem?item_id={$item.id}" class="button small">Back to item</a><br />
  Property Type: {$property._type_info.label}<br />
  Property type code: <code>{$property._type_info.id}</code><br />
  Value ID: <code>{$ipv.id}</code><br />
  Value item ID: <code>{$ipv.item_id}</code><br /><br />
  Raw stored value: <code>{$raw_value}</code><br /><br />
  String value: <code>{$value}</code><br /><br />
  Will be saved as: <code>{$ipv.storable_format}</code><br /><br />
  Switch mode: <a href="{$domain}test:datamanager/ipv?item_id={$item.id}&amp;property_id={$property.id}&amp;mode=draft" class="button small">Draft</a> <a href="{$domain}test:datamanager/ipv?item_id={$item.id}&amp;property_id={$property.id}&amp;mode=live" class="button small">Live</a><br />
  <div style="text-align:left"><pre>{$output}</pre></div>
</div>

<div id="actions-area">
  <ul class="actions-list">
    <li><b>Test a property:</b></li>
{foreach from=$properties item="name" key="pid"}
    <li class="permanent-action"><a href="{$domain}test:datamanager/ipv?item_id={$item.id}&amp;property_id={$pid}&amp;mode={$mode}">{$name}</li>
{/foreach}
  </ul>
</div>