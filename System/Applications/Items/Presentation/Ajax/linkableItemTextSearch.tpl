<ul>
{foreach from=$items item="item"}
  <li id="itemOption-{$item.id}" data-fullname="{$item.name|escape:quotes}">{$item.name|summary:"60"}<span class="informal"> {$item.model.name}</span></li>
{/foreach}
</ul>