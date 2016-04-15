<div id="work-area">

  <script type="text/javascript">

    // Smartest.AjaxModalViewer.variables.responseTableLinks = {$link_urls.truefalse}
    
    var savePageUrlChanges = function(){ldelim}

      $('saver-gif').show();

      $('editUrl').request({ldelim}
        onComplete: function(){ldelim}
          // $('page-urls').update('');
          new Ajax.Updater('page-urls', '{$domain}ajax:websitemanager/pageUrls', {ldelim}
            parameters: {ldelim}page_id: '{$page.webid}'{if $item.id}, item_id: {$item.id}{/if}, responseTableLinks: {$link_urls.truefalse}{rdelim},
            onSuccess: function(response) {ldelim}
              setTimeout(addListeners, 30);
            {rdelim}
          {rdelim});
          MODALS.hideViewer();
        {rdelim}
      {rdelim});

      return true;

    {rdelim}
    
    var effectPageUrlTransfer = function(){ldelim}

      $('saver-gif').show();

      $('transferUrl').request({ldelim}
        onComplete: function(){ldelim}
          // $('page-urls').update('');
          new Ajax.Updater('page-urls', '{$domain}ajax:websitemanager/pageUrls', {ldelim}
            parameters: {ldelim}page_id: '{$page.webid}'{if $item.id}, item_id: {$item.id}{/if}, responseTableLinks: {$link_urls.truefalse}{rdelim},
            onSuccess: function(response) {ldelim}
              setTimeout(addListeners, 30);
            {rdelim}
          {rdelim});
          MODALS.hideViewer();
        {rdelim}
      {rdelim});

      return true;

    {rdelim}
    
    var saveNewPageUrl = function(){ldelim}

      $('saver-gif').show();

      $('addUrl').request({ldelim}
        onComplete: function(){ldelim}
          // $('page-urls').update('');
          new Ajax.Updater('page-urls', '{$domain}ajax:websitemanager/pageUrls', {ldelim}
            parameters: {ldelim}page_id: '{$page.webid}'{if $item.id}, item_id: {$item.id}{/if}, responseTableLinks: {$link_urls.truefalse}{rdelim},
            onSuccess: function(response) {ldelim}
              setTimeout(addListeners, 30);
            {rdelim}
          {rdelim});
          MODALS.hideViewer();
        {rdelim}
      {rdelim});

      return true;

    {rdelim}

  </script>

{if $allow_edit}
  
  {load_interface file="edit_tabs.tpl"}
  
  {if $require_item_select}
    <h3>Meta-Page Overview: <span id="page-name-in-h3" class="light">{$page.title}</span></h3>
    {load_interface file="choose_item.tpl"}
    {load_interface file="editMetaPageWithoutItem.tpl"}
  {else}
    {load_interface file="editPage.form.tpl"}
  {/if}

{else}

<div class="instruction">You can't currently edit this page</div>

{/if}

</div>

{if !$require_item_select}
<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    
    <li class="permanent-action"><a href="{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $page.type == "ITEMCLASS" && $page.item.id}&amp;item_id={$page.item.id}{/if}" class="right-nav-link"><i class="fa fa-globe"></i> Publish this page</a></li>
    {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$page.webid}" class="right-nav-link"><i class="fa fa-unlock"></i> Release this page</a></li>{/if}
    {if $allow_edit}<li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><i class="fa fa-check"></i> Finish working with this page</a></li>{/if}
  </ul>
</div>
{/if}