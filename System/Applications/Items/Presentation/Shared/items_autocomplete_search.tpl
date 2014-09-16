  <div class="special-box">
      <form action="" method="get" id="items-search-form" onsubmit="return false">
        Search for an item: <input type="text" name="query" class="search" id="items-search-name" />
      </form>
      {literal}<script type="text/javascript">$('items-search-form').observe('submit', function(){return false;});</script>{/literal}
  </div>
  
  <div id="autocomplete_choices" class="autocomplete"></div>
  
  <script type="text/javascript">
    {literal}
    
    function getSelectionId(text, li) {
        var bits = li.id.split('-');
        window.location=sm_domain+'datamanager/openItem?item_id='+bits[1];
    }
    
    new Ajax.Autocompleter("items-search-name", "autocomplete_choices", "/ajax:datamanager/simpleItemTextSearch", {
        paramName: "query", 
        minChars: 2,
        delay: 50,
        width: 300,
        afterUpdateElement : getSelectionId
    });
    
    {/literal}
  </script>