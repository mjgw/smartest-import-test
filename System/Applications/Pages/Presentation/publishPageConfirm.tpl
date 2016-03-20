<div id="work-area">

<h3>Publish {if $item}Meta-{/if}Page</h3>

{if $show_site_disabled_warning}

<div class="warning"><strong>Warning</strong>: This site is not currently enabled. You can give this page the 'published' status, but it will not be available until the site is enabled.</div>

{/if}

<form action="{$domain}{$section}/publishPage" method="post">

<input type="hidden" name="page_id" value="{$page_id}" />
{if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

{if $allow_publish}

{if $count < 1}

<div class="instruction">Are you sure you want to publish this page?</div>

{elseif $count == 1}

<div class="warning">The {$undefined_asset_classes[0].info.type} <strong>{$undefined_asset_classes[0].info.assetclass_name}</strong> is not defined.</div>
	
{elseif $count > 1}

<div class="warning">The following elements are not defined in the in the most recent version of this page. Publishing this page will cause To be empty:</div>

<ul class="basic-list">

	{foreach from=$undefined_asset_classes item="undefinedAssetClass"}
	<li>{$undefinedAssetClass.info.type} <b>{$undefinedAssetClass.info.assetclass_name}</b></li>
	{/foreach}

</ul>

{/if}{* number of undefined elements *}

{if $item}

<div class="edit-form-row">
  {if $item.is_published}
  <div class="form-section-label">Would you like to re-publish this {$item._model.name|strtolower}?</div>
  <select name="publish_item">
    <option value="IGNORE">No, I'll do that manually. Just update page elements.</option>
    <option value="PUBLISH" selected="selected">Yes, re-publish the {$item._model.name|strtolower} '{$item.name}'</option>
  </select>
  {else}
  <div class="form-section-label">This {$item._model.name|strtolower} is currently not published. Would you like to publish it?</div>
  <select name="publish_item">
    <option value="IGNORE" selected="selected">No, I'll do that manually. Just update page elements.</option>
    <option value="PUBLISH">Yes, publish the {$item._model.name|strtolower} '{$item.name}'</option>
  </select>
  {/if}{* whether the item is published *}
</div>

{/if}{* whether this is an item page *}

{if $show_itemspace_publish_warning}
<div class="special-box">
{if count($itemspaces) > 1}
  <p>The following item spaces are defined with items that are not yet published:</p>
  <ul>
{foreach from=$itemspaces item="itemspacedef"}
    <li><strong>{$itemspacedef.itemspace.label}</strong> - chosen item: {$itemspacedef.draft_item.name}</li>
{/foreach}
  </ul>
{else}
{foreach from=$itemspaces item="itemspacedef"}
<p>The item space <strong>{$itemspacedef.itemspace.label}</strong> contains an item that is not published: &quot;{$itemspacedef.draft_item.name}&quot;</p>
{/foreach}
{/if}
  <p>What would you like to do?</p>
  <p><select name="itemspace_action">
      <option value="publish">Publish the items and update the page</option>
      <option value="nothing">Do nothing</option>
  </select></p>
</div>
{/if}

{else}

<div class="instruction">You can't publish this page at the moment</div>

{/if}{* whether this is an item page *}

<div class="buttons-bar">
  <input type="button" onclick="cancelForm();" value="Cancel" />
  {if $allow_publish}<input type="submit" value="Publish" />{/if}
</div>
	
</div>

</form>