<ul>
{foreach from=$items item="item"}
  <li id="itemOption-{$item.id}">{$item.name|summary:"58"}<span class="informal"> {$item.model.name}</span></li>
{/foreach}
</ul>