<div id="work-area">

  <h3>Tagged Items: {$tag.label}</h3>
  
  {if !$items._empty}
  <h4>Items</h4>
  <ul class="basic-list">
    {foreach from=$items item="item"}
    <li style="list-style-image:url('{$item.small_icon}')"> <a href="{$item.action_url}">{$item.name}</a><span> - {$item.model.name}</span></li>
    {/foreach}
  </ul>
  {/if}
  
  {if !$assets._empty}
  <h4>Files</h4>
  <ul class="basic-list">
    {foreach from=$assets item="asset"}
    <li style="list-style-image:url('{$asset.small_icon}')"> <a href="{$asset.action_url}">{$asset.label}</a><span> - {$asset.type_info.label}</span></li>
    {/foreach}
  </ul>
  {/if}
  
  {if !$pages._empty}
  <h4>Pages</h4>
  <ul class="basic-list">
    {foreach from=$pages item="page"}
    <li style="list-style-image:url('{$page.small_icon}')"> <a href="{$page.action_url}">{$page.title}</a></li>
    {/foreach}
  </ul>
  {/if}
  
  {if !$users._empty}
  <h4>Users</h4>
  <ul class="basic-list">
    {foreach from=$users item="user"}
    <li style="list-style-image:url('{$user.small_icon}')"> <a href="{$user.action_url}">{$user.full_name}</a></li>
    {/foreach}
  </ul>
  {/if}
  
  {if $items._empty && $assets._empty && $pages._empty && $users._empty}
  <div class="special-box">This tag is not connected to any items, files, pages or users yet.</div>
  {/if}

</div>