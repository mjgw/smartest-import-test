<div class="preference-pane" id="assets_draft" style="display:block">

{if !empty($elements_tree)}

<ul class="tree-parent-node-open" id="tree-root">
  <li class="page-element"><a class="option"><i class="flaticon solid document-3"></i></a> Current Page: {$page.title}</li>
  {defun name="menurecursion" list=$elements_tree}
    
    {capture name="foreach_name" assign="foreach_name"}list_{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    {capture name="foreach_id" assign="foreach_id"}{if $assetclass.info.assetclass_id}{$assetclass.info.assetclass_id}{else}0{/if}{/capture}
    
    {foreach from=$list item="assetclass" name=$foreach_name}
    
    <li {if $smarty.foreach.$foreach_name.last}class="last"{elseif $smarty.foreach.$foreach_name.first}class="first"{else}class="middle"{/if}>
    {if ($assetclass.info.defined == "PUBLISHED" || $assetclass.info.defined == "DRAFT") && in_array($assetclass.info.assetclass_type, array("SM_ASSETTYPE_JAVASCRIPT", "SM_ASSETTYPE_STYLESHEET", "SM_ASSETTYPE_RICH_TEXT", "SM_ASSETTYPE_PLAIN_TEXT", "SM_ASSETTYPE_SL_TEXT")) && $version == "draft"}<a href="{$domain}assets/editAsset?asset_id={$assetclass.info.asset_id}&amp;from=pageAssets" style="float:right;display:block;margin-right:5px;">Edit This File</a>{/if}
      {if !empty($assetclass.children)}
      <a href="{dud_link}" onclick="toggleParentNodeFromOpenState('{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}')"><img src="{$domain}Resources/System/Images/open.gif" alt="" border="0" id="toggle_{$foreach_id}_{$smarty.foreach.$foreach_name.iteration}" /></a>
      {else}
      <img src="{$domain}Resources/System/Images/blank.gif" alt="" border="0" />
      {/if}
      
      <a id="{$assetclass.info.type|lower}_{$assetclass.info.assetclass_name|escape:quotes}" class="option" href="#" onclick="{if $version == "draft"}return elementTree.setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.type|lower}');{else}return false;{/if}">		 
    {if $assetclass.info.exists == 'true'}
        
		{if $assetclass.info.defined == "PUBLISHED"}
		  {if $assetclass.info.type == 'attachment'}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/attach.png" />
		  {elseif $assetclass.info.type == 'asset'}
		    {if $assetclass.info.asset_type == "SM_ASSETTYPE_JPEG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_PNG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_GIF_IMAGE"}
	        <img src="{$domain}Resources/Icons/picture.png" style="border:0px" />
	      {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_PLAIN_TEXT"}
	        <img src="{$domain}Resources/Icons/page_white_text.png" style="border:0px" />
	      {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_RICH_TEXT"}
	        <img src="{$domain}Resources/Icons/style.png" style="border:0px" />
	      {else}
	        <img src="{$domain}Resources/Icons/page_white.png" style="border:0px" />
	      {/if}
		  {elseif $assetclass.info.type == 'template'}
  		  <img src="{$domain}Resources/Icons/page_white_code.png" style="border:0px" />
  		{elseif $assetclass.info.type == 'item'}
    		<img src="{$domain}Resources/Icons/package_small.png" style="border:0px;width:16px;height:16px" />
      {else}
		  <img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/published_{$assetclass.info.type|lower}.png" />
		  {/if}
		{elseif  $assetclass.info.defined == "DRAFT"}
		  {if $version == "draft"}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/System/Images/draftonly_{$assetclass.info.type|lower}.png" />
		  {else}
		    <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/System/Images/undefined_{$assetclass.info.type|lower}.png" />
		  {/if}
		{else}
		  <img border="0" style="width:16px;height:16px;" title="This {$assetclass.info.type} has not yet been defined" src="{$domain}Resources/System/Images/undefined_{$assetclass.info.type|lower}.png" />
		{/if}
	  
	  {if $assetclass.info.type != 'asset' && $assetclass.info.type != 'template'}
	  <b>{$assetclass.info.assetclass_name|end|escape:html}</b>
	  {/if}
	  
	  {if $assetclass.info.filename != ""}
	    {$assetclass.info.filename}
	  {else}
	    
	  {/if}
	  
	{else}
	
	{if $assetclass.info.type == "list"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/notexist_list.png" />
	{elseif $assetclass.info.type == "field"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/notexist_field.png" />
	{elseif $assetclass.info.type == "placeholder"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/notexist_placeholder.png" />
	{elseif $assetclass.info.type == "container"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/notexist_container.png" />
	{elseif $assetclass.info.type == "itemspace"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/System/Images/notexist_itemspace.png" />
	{elseif $assetclass.info.type == "attachment"}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/attach.png" />
	{else}
	<img border="0" style="width:16px;height:16px;" src="{$domain}Resources/Icons/notexist.gif" />
	{/if}
	
	<b>{$assetclass.info.assetclass_name}</b> Smartest needs more information about this new {$assetclass.info.type}.&nbsp;
	  {if $assetclass.info.type=='container'}
	    <a href="{$domain}{$section}/addContainer?name={$assetclass.info.assetclass_name}" class="button">Enter it now</a>
	  {elseif $assetclass.info.type=='placeholder'}
	    <a href="{$domain}{$section}/addPlaceholder?placeholder_name={$assetclass.info.assetclass_name}" class="button">Enter it now</a>
	  {elseif $assetclass.info.type=='list'}
	    <a href="{$domain}{$section}/addList?name={$assetclass.info.assetclass_name}" class="button">Enter it now</a>
	  {elseif $assetclass.info.type=='field'}
	    <a href="{$domain}metadata/addPageProperty?site_id={$site_id}&amp;name={$assetclass.info.assetclass_name}" class="button">Enter it now</a>
	  {elseif $assetclass.info.type=='itemspace'}
  	  <a href="{$domain}{$section}/addItemSpace?site_id={$site_id}&amp;name={$assetclass.info.assetclass_name}" class="button">Enter it now</a>
	  {/if}
	  
	{/if}
      </a>
      {if !empty($assetclass.children)}
      <ul class="tree-parent-node-open" id="{$foreach_name}_{$smarty.foreach.$foreach_name.iteration}">
        {fun name="menurecursion" list=$assetclass.children}
      </ul>
      {/if}
    </li>
    {/foreach}
    
  {/defun}
</ul>
{/if}
</div>