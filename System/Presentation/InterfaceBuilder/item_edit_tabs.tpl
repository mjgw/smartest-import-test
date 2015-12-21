{* Loaded when we are viewing a meta-page (and thus most tabs refer to the item) *}
<ul class="tabset">
    {if $request_parameters.page_id && $page_is_editable}<li{if $method == "editPage"} class="current"{/if}><a href="{$domain}websitemanager/editPage?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Page Overview</a></li>{/if}
    <li{if $method == "editItem"} class="current"{/if}><a href="{$domain}datamanager/editItem?item_id={$request_parameters.item_id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Edit {$item._model.name}</a></li>
{foreach from=$item._model.mt1_sub_models item="sub_model"}
    <li{if $method == "getSubModelItems" && $request_parameters.sub_model_id == $sub_model.id} class="current"{/if}><a href="{$domain}datamanager/getSubModelItems?item_id={$request_parameters.item_id}&amp;sub_model_id={$sub_model.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">{$sub_model.plural_name}</a></li>
{/foreach}
    {if $request_parameters.page_id && $page_is_editable}<li{if $method == "pageAssets"} class="current"{/if}><a href="{$domain}websitemanager/pageAssets?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Page Elements Tree</a></li>{/if}
    {if $request_parameters.page_id}<li{if $method == "preview"} class="current"{/if}><a href="{$domain}websitemanager/preview?page_id={$request_parameters.page_id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Preview</a></li>{/if}
    {* <li{if $method == "itemTags"} class="current"{/if}><a href="{if $request_parameters.item_id}{$domain}datamanager/itemTags?item_id={$request_parameters.item_id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{else}{$domain}websitemanager/pageTags?page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Tags</a></li> *}
    <li{if $method == "relatedContent"} class="current"{/if}>{if $request_parameters.item_id}<a href="{$domain}datamanager/relatedContent?&amp;item_id={$request_parameters.item_id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Related Content</a>{else}{if $page_is_editable}<a href="{$domain}websitemanager/relatedContent?page_id={$request_parameters.page_id}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Related Content</a>{/if}{/if}</li>
    <li{if $method == "authors"} class="current"{/if}><a href="{$domain}datamanager/authors?item_id={$request_parameters.item_id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Authors</a></li>
    <li{if $method == "itemComments"} class="current"{/if}><a href="{$domain}datamanager/itemComments?item_id={$request_parameters.item_id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}">Comments</a></li>
</ul>