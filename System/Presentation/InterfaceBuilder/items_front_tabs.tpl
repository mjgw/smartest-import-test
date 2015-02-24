<ul class="tabset" id="items-front-tabs">
    <li{if $method == "getItemClasses"} class="current"{/if}><a href="{$domain}smartest/models">Models</a></li>
    {if !empty($recent_items)}<li{if $method == "recentItems"} class="current"{/if}><a href="{$domain}datamanager/recentItems">Recent items</a></li>{/if}
    <li{if $method == "startPage"} class="current"{/if}><a href="{$domain}smartest/sets">Sets</a></li>
    {* <li{if $method == "itemPublishQueue"} class="current"{/if}><a href="{$domain}datamanager/itemPublishQueue">Publish queue</a></li> *}
</ul>

<script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
  
{* <script type="text/javascript">
  
/*     var url = sm_domain+'ajax:websitemanager/updatePageGroupOrder';
  
    var IDs;
    var IDs_string; */
    
    {literal}
    
    // Position.includeScrollOffsets = true;
    var pagesList = Sortable.create('items-front-tabs', {
        
        onUpdate: function(){
          /* IDs = Sortable.sequence('page-group-order');
          IDs_string = IDs.join(',');
          $('submit-ajax').value = 'Save new order';
          $('submit-ajax').disabled=false; */
        },
        
        constraint: false,
        scroll: false
        
    });
    
    {/literal}
    
</script> *}