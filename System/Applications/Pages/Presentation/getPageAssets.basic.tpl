<ul class="options-grid" id="page-elements-grid">
{foreach from=$elements_list item="assetclass"}
  <li>
    <a href="#" class="option" id="{$assetclass.info.type|lower}_{$assetclass.info.assetclass_name|escape:quotes}" onclick="{if $version == "draft"}return elementTree.setSelectedItem('{$assetclass.info.assetclass_name|escape:quotes}', '{$assetclass.info.type|lower}');{else}return false;{/if}">
  		{if $assetclass.info.exists == 'true'}
        {if $assetclass.info.defined == "PUBLISHED"}
  		    {if $assetclass.info.type == 'attachment'}
  		    <img src="{$domain}Resources/Icons/attach.png" alt="" />
  		    {elseif $assetclass.info.type == 'asset'}
  		      {if $assetclass.info.asset_type == "SM_ASSETTYPE_JPEG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_PNG_IMAGE" || $assetclass.info.asset_type == "SM_ASSETTYPE_GIF_IMAGE"}
  	          <img src="{$domain}Resources/Icons/picture.png" alt="" />
  	        {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_PLAIN_TEXT"}
  	          <img src="{$domain}Resources/Icons/page_white_text.png" alt="" />
  	        {elseif $assetclass.info.asset_type == "SM_ASSETTYPE_RICH_TEXT"}
  	          <img src="{$domain}Resources/System/Images/rich_text_file.png" alt="" />
  	        {else}
  	          <img src="{$domain}Resources/Icons/page_white.png" alt="" />
  	        {/if}
  		    {elseif $assetclass.info.type == 'template'}
    	  	  <img src="{$domain}Resources/Icons/page_white_code.png" alt="" />
    	  	{elseif $assetclass.info.type == 'item'}
        		<img src="{$domain}Resources/Icons/package_small.png" alt="" />
          {else}
  		    <img src="{$domain}Resources/System/Images/published_{$assetclass.info.type|lower}.png" alt="" />
  		    {/if}
  		  {elseif  $assetclass.info.defined == "DRAFT"}
  		    {if $version == "draft"}
  		      <img title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/System/Images/draftonly_{$assetclass.info.type|lower}.png" alt="" />
  		    {else}
  		      <img title="This {$assetclass.info.type} is only defined in the draft version of the page" src="{$domain}Resources/System/Images/undefined_{$assetclass.info.type|lower}.png" alt="" />
  		    {/if}
  		  {else}
  		    <img title="This {$assetclass.info.type} has not yet been defined" src="{$domain}Resources/System/Images/undefined_{$assetclass.info.type|lower}.png" alt="" />
  		  {/if}
  	    {if $assetclass.info.type == 'asset' && $assetclass.info.type == 'template'}
  	    {$assetclass.info.filename}
        {else}
          {if $assetclass.info.assetclass_label}
          {$assetclass.info.assetclass_label}
          {else}
          {$assetclass.info.assetclass_name}
          {/if}
  	    {/if}
        
     	{else}
	
     	  {if $assetclass.info.type == "list"}
     	  <img src="{$domain}Resources/Icons/notexist_list.gif" alt="" />
     	  {elseif $assetclass.info.type == "field"}
     	  <img src="{$domain}Resources/Icons/notexist_field.gif" alt="" />
      	{elseif $assetclass.info.type == "placeholder"}
      	<img src="{$domain}Resources/System/Images/notexist_placeholder.png" alt="" />
      	{elseif $assetclass.info.type == "container"}
      	<img src="{$domain}Resources/System/Images/notexist_container.png" alt="" />
      	{elseif $assetclass.info.type == "itemspace"}
      	<img src="{$domain}Resources/System/Images/notexist_itemspace.png" alt="" />
     	  {elseif $assetclass.info.type == "attachment"}
     	  <img src="{$domain}Resources/Icons/attach.png" alt="" />
     	  {else}
     	  <img src="{$domain}Resources/Icons/notexist.gif" alt="" />
     	  {/if}
        
        {$assetclass.info.assetclass_name}
      
      {/if}
      
      
    </a>
  </li>
{/foreach}
</ul>