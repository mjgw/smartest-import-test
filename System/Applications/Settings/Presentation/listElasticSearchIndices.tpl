<div id="work-area">
  <h3>ElasticSearch</h3>
  <p>Status: <span class="status-{$status}">{$status}</span></p>
  
  {if $status == 'green'}
  
  {if !$site_has_index}<p>This site does not yet have an index. <a href="#bulid-index" class="button">Build one</a></p>.{/if}
  
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Size</th>
        <th>Num documents</th>
        <th>Num shards</th>
        <th>Operations</th>
      </tr>
    </thead>
    <tbody>
{foreach from=$indices item="index"}
      <tr>
        <td><code>{$index.name}</code></td>
        <td>{$index.size_formatted}</td>
        <td>{$index.docs_info.num_docs}</td>
        <td>{$index.shards_info.num_shards}</td>
        <td><a href="#rebuild" class="button">Rebuild</a>{if $index.name != $site_main_index_name} <a href="{$domain}settings/deleteElasticSearchIndex?index_name={$index.name}" class="button">Delete</a>{/if}</td>
      </tr>
{/foreach}
    </tbody>
  </table>
  {else}
  <p>{$status_message}</p>
  {/if}
</div>

<div id="actions-area">
  
</div>