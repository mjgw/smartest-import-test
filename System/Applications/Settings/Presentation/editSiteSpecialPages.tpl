<div id="work-area">
  
  <h3 id="siteName">Configure special pages</h3>
  
  <form id="updateSiteDetails" name="updateSiteDetails" action="{$domain}{$section}/updateSiteSpecialPages" method="POST" style="margin:0px" enctype="multipart/form-data">
  
  {* <div class="edit-form-row">
    <div class="form-section-label">Select Home Page (Advanced)</div>
    <select name="site_top_page">
      {foreach from=$pages item="page"}
        {if $page.info.id != $site.error_page_id}
        <option value="{$page.info.id}"{if $site.top_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
  </div> *}

  <div class="edit-form-row">
    <div class="form-section-label">Search results page</div>
    <select name="site_search_page">
      {foreach from=$pages item="page"}
        {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
        <option value="{$page.info.id}"{if $site.search_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
    <br /><div class="form-hint">This page will handle search queries made to http://{$site.domain}{$domain}search.</div>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">Tag page</div>
    <select name="site_tag_page">
      {foreach from=$pages item="page"}
        {if $page.info.id != $site.error_page_id && $page.info.id != $site.top_page_id}
        <option value="{$page.info.id}"{if $site.tag_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
    <br /><div class="form-hint">This page will be loaded when a tag is requested, eg: http://{$site.domain}{$domain}tag/elephants.html.</div>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">User profile page</div>
    <select name="site_user_page">
      {if !$site.user_page_id}<option value="NEW">Create a new page for this purpose</option>{/if}
      {foreach from=$pages item="page"}
        {if $page.info.id != $site.top_page_id}
        <option value="{$page.info.id}"{if $site.user_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
    <br /><div class="form-hint">This page will be loaded when a user profile is requested.</div>
  </div>

  <div class="edit-form-row">
    <div class="form-section-label">404 Not Found error page</div>
    <select name="site_error_page">
      {foreach from=$pages item="page"}
        {* Sergiy: Allow top page (why not?), I use this approach on my website already many years *}
        {* if $page.info.id != $site.top_page_id *}
        <option value="{$page.info.id}"{if $site.error_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {* /if *}
      {/foreach}
    </select>
    <br /><div class="form-hint">This page will be loaded when an unknown or unpublished page is requested, eg: http://{$site.domain}{$domain}kigsdfkjhg.</div>
  </div>
  
  <div class="edit-form-row">
    <div class="form-section-label">503 Not Available Page</div>
    <select name="site_holding_page">
      {if !$site.holding_page_id}<option value="NEW">Create a new page for this purpose</option>{/if}
      {foreach from=$pages item="page"}
        {if $page.info.id != $site.top_page_id}
        <option value="{$page.info.id}"{if $site.holding_page_id == $page.info.id} selected="selected"{/if}>+{section name="dashes" loop=$page.treeLevel}-{/section} {$page.info.title}</option>
        {/if}
      {/foreach}
    </select>
    <br /><div class="form-hint">This page will be loaded when the site is disabled, if it is published. Otherwise "site not enabled" is shown.</div>
  </div>
  
  <div class="breaker"></div>

  <div class="buttons-bar">
    <input type="button" value="Cancel" onclick="window.location='{$domain}smartest'" />
    <input type="submit" name="action" value="Save Changes" />
  </div>
  
  </form>
  
</div>