<div id="work-area">

<h3>Publish {if $item}meta-{/if}page</h3>

{if $show_site_disabled_warning}

<div class="warning"><strong>Warning</strong>: This site is not currently enabled. You can give this page the 'published' status, but it will not be available until the site is enabled.</div>

{/if}

<form action="{$domain}{$section}/publishPage" method="post">

<input type="hidden" name="page_id" value="{$page.webid}" />
{if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}

{if $allow_publish}


<div class="instruction">Definitely publish the page '{$page.title}'?</div>

{if $count > 0}

<div id="page-underfined-elements">
  
  <div class="special-box"><i class="fa fa-info-circle"></i> This page includes one or more elements that are not defined. <a href="#toggle-indefined-elements" class="button small" id="toggle-page-underfined-elements">Show</a></div>
  
  <div style="display:none" id="page-underfined-elements-holder">
  
{if $count == 1}

<p>The {$undefined_asset_classes[0].info.type} <strong>{$undefined_asset_classes[0].info.assetclass_name}</strong> is not defined.</p>
	
{elseif $count > 1}

<p>The following elements are not defined in the in the most recent version of this page. Publishing this page may result in an incomplete-looking page:</p>

<ul class="basic-list">

	{foreach from=$undefined_asset_classes item="undefinedAssetClass"}
	<li>{$undefinedAssetClass.info.type} <b>{$undefinedAssetClass.info.assetclass_name}</b>{if $undefinedAssetClass.info.instance != 'default'} (instance: {$undefinedAssetClass.info.instance}){/if}</li>
	{/foreach}

</ul>

{/if}{* number of undefined elements *}

  </div>
  
  <script type="text/javascript">
  {literal}
  $('toggle-page-underfined-elements').observe('click', function(e){
    e.stop();
    $('page-underfined-elements-holder').blindDown({duration:0.3});
    $('toggle-page-underfined-elements').fade({duration:0.3});
  });
  {/literal}
  </script>

{if !$is_homepage}
<div><input type="checkbox" name="clear_parent_from_cache" value="1" checked="checked" /> Refresh the parent of this page from the cache</div>
{/if}

</div>

{/if}{* has any undefined elements *}

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
      <option value="nothing">Skip publishing this itemspace</option>
  </select></p>
</div>
{/if}

{else}

<div class="warning">You can't publish this page at the moment.</warning>

{/if}{* whether this is an item page *}

<div class="buttons-bar">
  <input type="button" onclick="cancelForm();" value="Cancel" />
  {if $allow_publish}<input type="submit" value="Publish page" />{/if}
</div>
	
</div>

</form>