<div class="instruction">This is an object meta-page. it is only used for editing info about {$model.plural_name|lower}.</div>
<div class="instruction">{$chooser_message}</div>

{if count($items)}
<form action="{$domain}{$continue_action}" method="get" id="item_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <select name="item_id" style="width:300px" onchange="$('item_choooser').submit()">
    {foreach from=$items item="item"}
    <option value="{$item.id}">{$item.name}</option>
    {/foreach}
  </select>
  <input type="submit" value="Continue" />
</form>
{else}
<p>There are no {$model.plural_name|lower} yet. <a href="{$domain}datamanager/addItem?class_id={$model.id}">Click here</a> to create one.</p>
{/if}