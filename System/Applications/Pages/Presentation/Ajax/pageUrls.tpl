<table width="100%" style="border:1px solid #ccc;padding:2px;" cellpadding="0" cellspacing="0">
  
  {if $ishomepage == "true"}
	<tr style="background-color:#{cycle name="urls" values="ddd,fff"};height:20px">
	  <td>
		  <div style="display:inline" id="siteDomainField_0">
		    <strong>{if $page.is_published == "TRUE" && $site.is_enabled == 1}<a href="http://{$site.domain}{$domain}" target="_blank">{/if}http://{$site.domain}{$domain}{if $page.is_published == "TRUE" && $site.is_enabled == 1}</a>{/if}</strong> (default)</div></td>
	  <td style="width:32%">&nbsp;</td>
  </tr>
  {/if}
  
  {if count($page.urls)}
  
  {foreach from=$page.urls item=pageurl}
    {capture name="pageUrl" assign="pageUrl"}http://{$site.domain}{$domain}{$pageurl.url}{/capture}
  <tr style="background-color:#{cycle name="urls" values="ddd,fff"};height:20px">
    <td>
	    <div style="display:inline" id="siteDomainField_{$pageurl.id}">
	      {if $pageurl.is_default == 1}<strong>{/if}{if $page.is_published == "TRUE" && ($page.type == 'NORMAL' || ($page.type == 'ITEMCLASS' && $item.public == 'TRUE')) && $site.is_enabled == 1}<a href="{$pageUrl}" target="_blank">{$pageUrl|truncate:100:"..."}</a>{else}{$pageUrl|truncate:100:"..."}{/if}{if $pageurl.is_default == 1}</strong> (default){/if}</div></td>
    <td style="width:32%;text-align:right;padding:3px">
	    <a class="button small" href="#edit-url" data-urlid="{$pageurl.id}" onclick="MODALS.load('{$section}/editPageUrl?url_id={$pageurl.id}', 'Edit page URL');return false;">Edit</a>
	    {if !_b($ishomepage) && $pageurl.is_default != 1 && $pageurl.type != 'SM_PAGEURL_INTERNAL_FORWARD' && $pageurl.type != 'SM_PAGEURL_ITEM_FORWARD'}<a class="button small make_url_default" href="#make-default" data-urlid="{$pageurl.id}">Make default</a>{/if}
	    <a class="button small" href="#transfer-url" onclick="MODALS.load('{$section}/transferPageUrl?url_id={$pageurl.id}', 'Transfer page URL');return false;">Transfer</a>
	    <a class="button small delete_url" href="#delete-url" data-urlid="{$pageurl.id}" />Delete</a></td></tr> 
  {/foreach}
  
  {else}
    
  {/if}
  
  <tr style="background-color:#{cycle name="urls" values="ddd,fff"};height:20px">
      <td colspan="2">
        <div style="display:inline" id="siteDomainField">
        {if $link_urls && $page.is_published == "TRUE" && $site.is_enabled == 1}<a href="http://{$site.domain}{$domain}{$page.forced_fallback_url}" target="_blank">http://{$site.domain}{$domain}{$page.forced_fallback_url|truncate:50:"..."}</a>{else}http://{$site.domain}{$domain}{$page.forced_fallback_url|truncate:100:"..."}{/if}</div></td></tr>

  
</table>