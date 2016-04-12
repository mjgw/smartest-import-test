<ul>
{foreach from=$items item="item"}
  <li id="itemOption-{$item.id}" data-fullname="{$item.name|escape:quotes}">{$item.name|summary:"60"}<span class="informal"> {$item.model.name}</span></li>
{foreachelse}
  <li class="nothing-found" id="itemOption-nothing"><span class="informal">No items found. Try a different search.</span></li>
{/foreach}
</ul>