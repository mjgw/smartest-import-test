<div class="instruction">This is your author/user profile page. It shows how users will look when clicked from bylines.</div>
<div class="instruction">{$chooser_message}</div>

<form action="{$domain}{$continue_action}" method="get" id="user_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <select name="author_id" style="width:300px" onchange="$('user_chooser').submit()">
    {foreach from=$authors item="user"}
    <option value="{$user.id}">{$user.full_name}</option>
    {/foreach}
  </select>
  <input type="submit" value="Continue" />
</form>