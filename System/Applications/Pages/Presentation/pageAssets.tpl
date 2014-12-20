<script type="text/javascript" type="text/javascript">

/* var selectedPage = null;
var selectedPageName = null;
var lastRow;
var lastRowColor; */
var treeNodes = new Array();
var pageWebId = '{$page.webid}';

{literal}

function viewDraftPage(parameters){

	var pageURL = sm_domain+'website/renderEditableDraftPage?page_id='+pageWebId;
	window.location=pageURL;

}

function viewLivePage(parameters){

	var pageURL = 'http://'+parameters.domain+'/website/renderPageFromId?page_id='+pageWebId{/literal}{if $item}+'&item_id={$item.id}'{/if};
	window.open(pageURL);

{literal}}{/literal}

</script>

<div id="work-area">

{load_interface file="edit_tabs.tpl"}

{if $allow_edit}

  {if $require_item_select}
    <h3>Page elements</h3>
    {load_interface file="choose_item.tpl"}
  {else}
    <h3>Elements used on page: {if $page.type == 'ITEMCLASS'}{$page.static_title} <span class="light">({$page.title})</span>{else}<span class="light">{$page.title}</span>{/if}</h3>
    {if $version == "draft"}
    <div class="instruction">Structure of elements as they are being rendered on the draft version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
    {else}
    <div class="instruction">Structure of elements as they are being rendered on the live version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
    {/if}

    <form id="pageViewForm" method="get" action="">
      <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
      <input type="hidden" name="page_id" value="{$page.webid}" />
      {if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
    </form>

    <script type="text/javascript">
    var elementTree = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'page-element');
    </script>

    {if $show_deleted_warning}
      <div class="warning">Warning: This page is currently in the trash.</div>
    {/if}

    {if $show_metapage_warning}
      <div class="warning">Warning: the meta-page you are editing is not the default meta-page for this item. Most automatically generated links will not link to this page. <a href="{$domain}{$section}/pageAssets?page_id={$default_metapage_webid}&amp;item_id={$item.id}"></a></div>
    {/if}

    <div class="special-box">
      <form id="viewSelect" action="{$domain}{$section}/pageAssets" method="get" style="margin:0px">
    
        <input type="hidden" name="page_id" value="{$page.webid}" />
        <input type="hidden" name="site_id" value="{$site_id}" />
        <input type="hidden" name="version" value="{$version}" />
        {if $page.type == 'ITEMCLASS' && $item.id}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
    
        <div class="special-box-key">Viewing mode:</div>
    
        <select name="version" onchange="document.getElementById('viewSelect').submit();">
          <option value="draft"{if $version == "draft"} selected="selected"{/if}>Draft</option>
          <option value="live"{if $version == "live"} selected="selected"{/if}>Live</option>
        </select>
    
        {*action_button text="Switch to live mode"}{$domain}websitemanager/pageAssets?page_id=jl02c1D042YTWF6w85TO9j808iW7wNjQ&amp;version=live{/action_button*}
        {*action_button text="Switch to draft mode"}{$domain}websitemanager/pageAssets?page_id=jl02c1D042YTWF6w85TO9j808iW7wNjQ&amp;version=draft{/action_button*}

      </form>

      <form id="templateSelect" action="{$domain}{$section}/setPageTemplate" method="get" style="margin:0px">

        <input type="hidden" name="page_id" value="{$page.webid}" />
        <input type="hidden" name="site_id" value="{$site_id}" />
        <input type="hidden" name="version" value="{$version}" />
  	  
    {if $version == "draft"}
        <span>
          <div class="special-box-key">Page Template:</div>
          <select name="template_name" onchange="$('templateSelect').submit();">
            <option value="">Not Selected</option>
    {foreach from=$templates item="t"}
            <option value="{$t.url}"{if $templateMenuField == $t.url} selected{/if}>{$t.url}</option>
    {/foreach}
          </select>
          {if (($page_template.status == "imported" && $page_template.filename) || strlen($page_template)) && $version == "draft"}<a href="{$domain}templates/editTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}" class="button">Edit</a>{/if}
        </span>
    {else}
        <div class="special-box-key">Page template: </div>{if strlen($templateMenuField)}<strong>{$templateMenuField}</strong>{else}<em style="color:#666">None yet specified</em>{/if}</span>
    {/if}

      </form>
    </div>

    {if $show_template_warning}
      <div class="warning">Warning: the page template you are currently using is not yet in the templates repository. <a href="{$domain}templates/importSingleTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={$templateMenuField}" class="button">Click here to import it</a></div>
    {/if}
    {load_interface file=$sub_template}
  {/if}

{else}

<h3>Page elements</h3>

{/if}

</div>

{if !$require_item_select}

<div id="actions-area">

<!--Navigation Bar-->

<ul class="invisible-actions-list" id="placeholder-specific-actions" style="display:none">
  <li><b>Placeholder Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('definePlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('definePlaceholderWithNewFile');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_add.png" border="0" alt=""> Add a new file here</a></li>
  {if $item}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholderOnItemPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear or this {$item.model.name|strtolower} only</a></li>
  {else}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editPlaceholder');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this placeholder</a></li>
  {/if}
</ul>

<ul class="invisible-actions-list" id="container-specific-actions" style="display:none">
  <li><b>Container Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineContainer');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define this container</a></li>
  {if $item}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainer');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear container definition</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainerOnItemPage');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear for this {$item.model.name|strtolower} only</a></li>
  {else}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainer');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear container definition</a></li>
  {/if}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editContainer');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit this container</a></li>
</ul>

<ul class="invisible-actions-list" id="list-specific-actions" style="display:none">
  <li><b>List Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineList');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define List Parameters</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('clearList');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear List Parameters</a></li>
</ul>

<ul class="invisible-actions-list" id="attachment-specific-actions" style="display:none">
  <li><b>Attachment Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editAttachment');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit Attachment Settings</a></li>
</ul>

<ul class="invisible-actions-list" id="asset-specific-actions" style="display:none">
  <li><b>File Options</b></li>
  <li class="permanent-action"><a href="#" onclick="return elementTree.workWithItem('editFile');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit this file</a></li>
  <li class="permanent-action"><a href="#" onclick="return MODALS.load('assets/assetCommentStream?asset_id='+elementTree.lastItemId, 'File notes');" class="right-nav-link"><img src="{$domain}Resources/Icons/note.png" border="0" alt=""> View notes on this File</a></li>
</ul>

<ul class="invisible-actions-list" id="template-specific-actions" style="display:none">
  <li><b>Template Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Edit This Template</a></li>
</ul>

<ul class="invisible-actions-list" id="itemspace-specific-actions" style="display:none">
  <li><b>Itemspace Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineItemspace');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define This Itemspace</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editItemspace');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit This Itemspace</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('clearItemspaceDefinition');" class="right-nav-link"><img src="{$domain}Resources/Icons/cross.png" border="0" alt=""> Clear This Itemspace</a></li>
</ul>

<ul class="invisible-actions-list" id="item-specific-actions" style="display:none">
  <li><b>Item Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('openItem');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt=""> Edit This Item</a></li>
</ul>

<ul class="invisible-actions-list" id="field-specific-actions" style="display:none">
  <li><b>Field Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editField');" class="right-nav-link"><img src="{$domain}Resources/Icons/layout_edit.png" border="0" alt=""> Define This Field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="elementTree.workWithItem('setLiveProperty', {ldelim}confirm: 'Are you sure you want to set the draft value as live?'{rdelim})" class="right-nav-link">
      <img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="elementTree.workWithItem('undefinePageProperty', {ldelim}confirm: 'Are you sure you want to undefine this field?'{rdelim})" class="right-nav-link">
      <img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Undefine this field</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  {if $page_template.filename && $version == "draft"}<li class="permanent-action"><a href="{$domain}templates/editTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}" value="Edit"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Edit Page Template</a></li>{/if}
  {if $version == "draft"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}'" class="right-nav-link"><img src="{$domain}Resources/Icons/page_lightning.png" border="0" alt=""> Publish this page</a></li>{/if}
  <li class="permanent-action"><a href="javascript:;" onclick="viewLivePage({ldelim}domain: '{$_site.domain}'{rdelim});" class="right-nav-link"><img src="{$domain}Resources/Icons/page_go.png" border="0" alt=""> Go to this page</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/layoutPresetForm?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt=""> Create preset from this page</a></li>
  {if $draftAsset.asset_id && $draftAsset.asset_id != $liveAsset.asset_id}<li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to publish your changes right now?')){workWithItem('setLiveAsset');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Publish This Asset Class</a>{/if}
  <li class="permanent-action"><a href="{$domain}smartest/assets/types" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt=""> Browse Assets Library</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Finish working with this page</a></li>
  {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$page.webid}" class="right-nav-link"><img src="{$domain}Resources/Icons/tick.png" border="0" alt=""> Release this page</a></li>{/if}
</ul>

{if $show_recent_items}
<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">{$model.name} meta-pages</span></li>
  {foreach from=$metapages item="metapage"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$metapage.webid}&amp;item_id={$item.id}'"><img border="0" src="{$metapage.small_icon}" /> {$metapage.label}</a></li>
  {/foreach}
</ul>
{/if}

{if $show_recent_items}
<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$page.webid}&amp;item_id={$recent_item.id}'"><img border="0" src="{$recent_item.small_icon}" /> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>
{/if}

</div>

{/if}