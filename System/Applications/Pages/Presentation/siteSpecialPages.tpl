<script type="text/javascript">

  var suggestedNonExistentPage = '{$unknown_url}';
  var suggestedSearchQuery = 'Elephants';
  var urlFieldFocussed = false;
  var searchQueryFieldFocussed = false;
  
  {literal}
  
  document.observe('dom:loaded', function(){
    
    $('requested-page').observe('focus', function(){
      if($F('requested-page') == suggestedNonExistentPage || $F('requested-page') == ''){
        $('requested-page').removeClassName('unfilled');
        $('requested-page').setValue('');
      }
      urlFieldFocussed = true;
    });
    
    $('requested-page').observe('blur', function(){
      if($F('requested-page') == suggestedNonExistentPage || $F('requested-page') == ''){
        $('requested-page').addClassName('unfilled');
        $('requested-page').setValue(suggestedNonExistentPage);
      }
      urlFieldFocussed = false;
    });
    
    $('search-query').observe('focus', function(){
      if($F('search-query') == suggestedSearchQuery || $F('search-query') == ''){
        $('search-query').removeClassName('unfilled');
        $('search-query').setValue('');
      }
      searchQueryFieldFocussed = true;
    });
    
    $('search-query').observe('blur', function(){
      if($F('search-query') == suggestedSearchQuery || $F('search-query') == ''){
        $('search-query').addClassName('unfilled');
        $('search-query').setValue(suggestedSearchQuery);
      }
      searchQueryFieldFocussed = false;
    });
    
  });
  
  {/literal}
  
</script>


<div id="work-area">

{load_interface file="cms_elements_tabs.tpl"}

  <h3>Special pages</h3>
  
  <div class="special-box">
    <h4>"404 Not Found" page</h4>
    {if $site.error_page_id}
    <form action="{$domain}websitemanager/preview" method="get" id="preview-error-page-form">
      <input type="hidden" name="page_id" value="{$error_page.webid}" />
      <div class="edit-form-row">
        /<input type="text" name="requested_page" id="requested-page" value="{$unknown_url}" class="unfilled" /><span class="form-hint">The unknown URL being requested</span>
      </div>
      <a class="button" href="#edit" id="preview-error-page">Preview &amp; edit</a>
    </form>
    <script type="text/javascript">
    {literal}
    $('preview-error-page').observe('click', function(e){
      e.stop();
      $('preview-error-page-form').submit();
    });
    {/literal}
    </script>
    {else}
    <p><em>It would appear that your website does not have a 404 page set</em></p>
    {/if}
  </div>
  
  <div class="special-box">
    <h4>"503 Not Available" page</h4>
    {if $site.holding_page_id}
    <a class="button" href="{$domain}websitemanager/preview?page_id={$holding_page.webid}">Preview &amp; edit</a><span class="form-hint">This page is shown when your site is disabled in site settings.</span>
    {else}
    <p><em>It would appear that your website does not have a holding page set</em></p>
    {/if}
  </div>
  
  <div class="special-box">
    <h4>Search page</h4>
    {if $site.search_page_id}
    <form action="{$domain}websitemanager/preview" method="get" id="preview-search-page-form">
      <input type="hidden" name="page_id" value="{$search_page.webid}" />
      <div class="edit-form-row">
        <input type="text" name="search_query" id="search-query" style="width:300px" value="Elephants" class="unfilled" /><span class="form-hint">The words being searched for</span>
      </div>
      <a class="button" href="#edit" id="preview-search-page">Test the search page</a>
    </form>
    <script type="text/javascript">
    {literal}
    $('preview-search-page').observe('click', function(e){
      e.stop();
      $('preview-search-page-form').submit();
    });
    {/literal}
    </script>
    {else}
    <p><em>It would appear that your website does not have a search page set</em></p>
    {/if}
  </div>
  
  {if !empty($tags)}
  <div class="special-box">
    <h4>Tagged content list page</h4>
    {if $site.tag_page_id}
    <form action="{$domain}websitemanager/preview" method="get" id="preview-tag-page-form">
      <input type="hidden" name="page_id" value="{$tag_page.webid}" />
      <div class="edit-form-row">
        <select name="tag" class="unfilled">
{foreach from=$tags item="tag"}
          <option value="{$tag.name}">{$tag.label}</option>
{/foreach}
        </select>
      </div>
      <a class="button" href="#edit" id="preview-tag-page">Preview &amp; edit</a>
      <script type="text/javascript">
      {literal}
      $('preview-tag-page').observe('click', function(e){
        e.stop();
        $('preview-tag-page-form').submit();
      });
      {/literal}
      </script>
    </form>
    {else}
    <p><em>It would appear that your website does not have a tag page set</em></p>
    {/if}
  </div>
  {/if}
  
  <div class="special-box">
    <h4>Author&rsquo;s list page</h4>
    <form action="{$domain}websitemanager/preview" method="get" id="preview-user-page-form">
      <input type="hidden" name="page_id" value="{$author_page.webid}" />
    <div class="edit-form-row">
      <select name="author_id" class="unfilled">
{foreach from=$authors item="author"}
        <option value="{$author.id}">{$author.full_name}</option>
{/foreach}
      </select>
      <span class="form-hint">The page that lists all content for which the author is credited</span>
    </div>
    <a class="button" href="#edit" id="preview-author-page">Preview &amp; edit</a>
    <script type="text/javascript">
    {literal}
    $('preview-author-page').observe('click', function(e){
      e.stop();
      $('preview-user-page-form').submit();
    });
    {/literal}
    </script>
  </form>
  </div>
  
  <div><a class="button" href="{$domain}settings/editSiteSpecialPages">Configure special pages</a></div>
  
</div>