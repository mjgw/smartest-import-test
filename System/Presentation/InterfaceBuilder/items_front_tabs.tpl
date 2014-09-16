<ul class="tabset">
    <li{if $method == "getItemClasses"} class="current"{/if}><a href="{$domain}smartest/models">Models</a></li>
    {if !empty($recent_items)}<li{if $method == "recentItems"} class="current"{/if}><a href="{$domain}datamanager/recentItems">Recent items</a></li>{/if}
    <li{if $method == "startPage"} class="current"{/if}><a href="{$domain}smartest/sets">Sets</a></li>
</ul>