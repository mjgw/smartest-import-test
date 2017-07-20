<div id="work-area-full">
  {load_interface file="edit_user_tabs.tpl"}
  <h3>Content associated with user <span class="light">{$user.full_name}</span></h3>
  
  <div class="layout-column width-3">
  {if $num_owned_pages > 1}
  <h4>Pages created ({$num_owned_pages} total)</h4>
  <ul class="basic-files-list" id="owned-pages-list" data-allshowing="false">
{foreach from=$owned_pages item="page" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="flaticon solid document-3"></i> {$page.title}{if $user.id == $_user.id} <a href="{$domain}websitemanager/openPage?page_id={$page.webid}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_owned_pages > 10}
  <a href="#show-all" id="show-all-owned-pages" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-owned-pages').observe('click', function(e){
    e.stop();
    if($('owned-pages-list').readAttribute('data-allshowing') == 'false'){
      $$('#owned-pages-list li').each(function(el){
        el.show();
      });
      $('owned-pages-list').writeAttribute('data-allshowing', 'true');
      $('show-all-owned-pages').update('Hide full list');
    }else{
      $$('#owned-pages-list li.all').each(function(el){
        el.hide();
      });
      $('owned-pages-list').writeAttribute('data-allshowing', 'false');
      $('show-all-owned-pages').update('Show all');
    }
  });
  {/literal}
  </script>
  {/if}
  <div class="v-spacer"></div>
  {/if}
  
  {if $num_held_pages > 1}
  <h4>Pages currently held ({$num_held_pages} total)</h4>
  <ul class="basic-files-list" id="held-pages-list" data-allshowing="false">
{foreach from=$held_pages item="page" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="flaticon solid document-3"></i> {$page.title}{if $user.id == $_user.id}  <a href="{$domain}websitemanager/openPage?page_id={$page.webid}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_held_pages > 10}
  <a href="#show-all" id="show-all-held-pages" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-held-pages').observe('click', function(e){
    e.stop();
    if($('held-pages-list').readAttribute('data-allshowing') == 'false'){
      $$('#held-pages-list li').each(function(el){
        el.show();
      });
      $('held-pages-list').writeAttribute('data-allshowing', 'true');
      $('show-all-held-pages').update('Hide full list');
    }else{
      $$('#held-pages-list li.all').each(function(el){
        el.hide();
      });
      $('held-pages-list').writeAttribute('data-allshowing', 'false');
      $('show-all-held-pages').update('Show all');
    }
  });
  {/literal}
  </script>
  {/if}
  {/if}
  </div>
  
  <div class="layout-column width-3">
  {if $num_owned_items > 1}
  <h4>Items created ({$num_owned_items} total)</h4>
  <ul class="basic-files-list" id="owned-items-list" data-allshowing="false">
{foreach from=$owned_items item="item" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="fa fa-cube"></i> {$item.title}{if $user.id == $_user.id}  <a href="{$domain}datamanager/openItem?item_id={$item.id}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_owned_items > 10}
  <a href="#show-all" id="show-all-owned-items" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-owned-items').observe('click', function(e){
    e.stop();
    if($('owned-items-list').readAttribute('data-allshowing') == 'false'){
      $$('#owned-items-list li').each(function(el){
        el.show();
      });
      $('owned-items-list').writeAttribute('data-allshowing', 'true');
      $('show-all-owned-items').update('Hide full list');
    }else{
      $$('#owned-items-list li.all').each(function(el){
        el.hide();
      });
      $('owned-items-list').writeAttribute('data-allshowing', 'false');
      $('show-all-owned-items').update('Show all');
    }
  });
  {/literal}
  </script>
  {/if}
   <div class="v-spacer"></div>
  {/if}
  
  {if $num_held_items > 1}
  <h4>Items currently held ({$num_held_items} total)</h4>
  <ul class="basic-files-list" id="held-items-list" data-allshowing="false">
{foreach from=$held_items item="item" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="fa fa-cube"></i> {$item.title}{if $user.id == $_user.id}  <a href="{$domain}datamanager/openItem?item_id={$item.id}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_held_items > 10}
  <a href="#show-all" id="show-all-held-items" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-held-items').observe('click', function(e){
    e.stop();
    if($('held-items-list').readAttribute('data-allshowing') == 'false'){
      $$('#held-items-list li').each(function(el){
        el.show();
      });
      $('held-items-list').writeAttribute('data-allshowing', 'true');
      $('show-all-held-items').update('Hide full list');
    }else{
      $$('#held-items-list li.all').each(function(el){
        el.hide();
      });
      $('held-items-list').writeAttribute('data-allshowing', 'false');
      $('show-all-held-items').update('Show all');
    }
  });
  {/literal}
  </script>
  {/if}
  {/if}
  </div>
  
  <div class="layout-column width-3">
  {if $num_owned_files > 1}
  <h4>Files created ({$num_owned_files} total)</h4>
  <ul class="basic-files-list" id="owned-files-list" data-allshowing="false">
{foreach from=$owned_files item="file" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="fa fa-{$file.fa_icon}"></i> {$file.label}{if $user.id == $_user.id}  <a href="{$domain}assets/editAsset?asset_id={$asset.id}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_owned_files > 10}
  <a href="#show-all" id="show-all-owned-files" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-owned-files').observe('click', function(e){
    e.stop();
    if($('owned-files-list').readAttribute('data-allshowing') == 'false'){
      $$('#owned-files-list li').each(function(el){
        el.show();
      });
      $('owned-files-list').writeAttribute('data-allshowing', 'true');
      $('show-all-owned-files').update('Hide full list');
    }else{
      $$('#owned-files-list li.all').each(function(el){
        el.hide();
      });
      $('owned-files-list').writeAttribute('data-allshowing', 'false');
      $('show-all-owned-files').update('Show all');
    }
  });
  {/literal}
  </script>
  <div class="v-spacer"></div>
  {/if}
  {/if}
  
  {if $num_owned_templates > 1}
  <h4>Templates created ({$num_owned_templates} total)</h4>
  <ul class="basic-files-list" id="owned-templates-list" data-allshowing="false">
{foreach from=$owned_templates item="template" key="num"}
    <li{if $num > 9} style="display:none" class="all"{/if}><i class="fa fa-file-code-o"></i> {$template.label}{if $user.id == $_user.id}  <a href="{$domain}templates/editTemplate?template={$template.id}" class="button small">Edit</a>{/if}</li>
{/foreach}
  </ul>
  {if $num_owned_templates > 10}
  <a href="#show-all" id="show-all-owned-templates" class="button">Show all</a>
  <script type="text/javascript">
  {literal}
  $('show-all-owned-templates').observe('click', function(e){
    e.stop();
    if($('owned-templates-list').readAttribute('data-allshowing') == 'false'){
      $$('#owned-templates-list li').each(function(el){
        el.show();
      });
      $('owned-templates-list').writeAttribute('data-allshowing', 'true');
      $('show-all-owned-templates').update('Hide full list');
    }else{
      $$('#owned-templates-list li.all').each(function(el){
        el.hide();
      });
      $('owned-templates-list').writeAttribute('data-allshowing', 'false');
      $('show-all-owned-templates').update('Show all');
    }
  });
  {/literal}
  </script>
  {/if}
  {/if}
  </div>
  
  <div class="breaker"></div>
  
</div>