<script type="text/javascript">

  var suggestedSearchQuery = 'Elephants';
  var searchQueryFieldFocussed = false;
  
  {literal}
  
  document.observe('dom:loaded', function(){
    
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

<div class="instruction">This is your search page. It shows how your site will look when people submit a search.</div>
<div class="instruction">{$chooser_message}</div>

<form action="{$domain}{$continue_action}" method="get" id="user_chooser">
  <input type="hidden" name="page_id" value="{$page.webid}" />
  <div class="edit-form-row">
    <input type="text" name="search_query" value="Elephants" class="unfilled" id="search-query" />
  </div>
  <input type="submit" value="Continue" />
</form>