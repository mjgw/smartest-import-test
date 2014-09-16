<div id="work-area">
  
  {load_interface file="edit_tabs.tpl"}
  
  <h3>Related to this page</h3>
  
  {if $require_item_select}
  
    {load_interface file="choose_item.tpl"}
  
  {else}
  
    <h4>Other pages</h4>
    
    {if empty($related_pages)}
      <i>No other pages are linked to this page.</i><br /><br />
    {else}
    <ul>
      {foreach from=$related_pages item="related_page"}
      <li>{$related_page.title}</li>
      {/foreach}
    </ul>
    {/if}
  
    <a href="{$domain}{$section}/editRelatedContent?page_id={$page.webid}" class="button">Edit</a><br /><br />
  
    {foreach from=$models item="model"}
    
    <h4>{$model.plural_name}</h4>
    
    {if $model._related_items._empty}
      <i>No {$model.plural_name|strtolower} are linked to this page.</i><br /><br />
    {else}
      <ul>
        {foreach from=$model._related_items item="related_item"}
        <li>{$related_item.name}</li>
        {/foreach}
      </ul>
    {/if}
    
    <a href="{$domain}{$section}/editRelatedContent?page_id={$page.webid}&amp;model_id={$model.id}" class="button">Edit</a><br /><br />
    
    {/foreach}
  
  {/if}
  
</div>

<div id="actions-area">
  
</div>