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
    <h3>Elements used on page: {if $page.type == 'ITEMCLASS'}{$page.static_title} <span class="light">({$page.title})</span>{else}<span class="light">{$page.title}{if $is_tag_page && $tag.id} (tag '{$tag.label}'){/if}</span>{/if}</h3>
    {if $version == "draft"}
    <div class="instruction">Structure of elements as they are being rendered on the draft version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
    {else}
    <div class="instruction">Structure of elements as they are being rendered on the live version of this page. {help id="websitemanager:page_elements_tree"}What is this?{/help}</div>
    {/if}

    <form id="pageViewForm" method="get" action="">
      <input type="hidden" name="assetclass_id" id="item_id_input" value="" />
      <input type="hidden" name="page_id" value="{$page.webid}" />
      {if $item}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
      <input type="hidden" name="instance" value="" id="instance_name" />
    </form>

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
          {if (($page_template.status == "imported" && $page_template.filename) || strlen($page_template)) && $version == "draft"}
          <a href="{$domain}templates/editTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}&amp;from=pageAssets&amp;page_id={$page.webid}{if $page.type == 'ITEMCLASS' && $item.id}&amp;item_id={$item.id}{/if}" class="button" id="edit-page-template-button">Edit template</a>
          <script type="text/javascript">
            templateEditUrl = 'templates/editTemplateModal?template_type=SM_ASSETTYPE_MASTER_TEMPLATE&template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}&page_id={$page.webid}{if $page.type == 'ITEMCLASS' && $item.id}&item_id={$item.id}{/if}';
            {literal}
            $('edit-page-template-button').observe('click', function(e){
              e.stop();
              MODALS.load(templateEditUrl, 'Edit page template', true);
            });
            {/literal}
          </script>
          {/if}
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
  <li><b>Placeholder options</b></li>
  <li class="permanent-action" id="placeholder-add" style="display:none"><a href="#" onclick="elementTree.workWithItem('addPlaceholder');">Add this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('definePlaceholder');" class="right-nav-link"><i class="fa fa-pencil"></i> Choose file for this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('definePlaceholderWithNewFile');" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add a new file here</a></li>
  {if $item}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholder');" class="right-nav-link"><i class="fa fa-times"></i> Clear this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholderOnItemPage');" class="right-nav-link"><i class="fa fa-times-circle"></i> Clear or this {$item.model.name|strtolower} only</a></li>
  {else}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefinePlaceholder');" class="right-nav-link"><i class="fa fa-times"></i> Clear this placeholder</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editPlaceholder');" class="right-nav-link"><i class="fa fa-cog"></i> Edit this placeholder</a></li>
  {/if}
</ul>

<ul class="invisible-actions-list" id="container-specific-actions" style="display:none">
  <li><b>Container options</b></li>
  <li class="permanent-action" id="container-add" style="display:none"><a href="#" onclick="elementTree.workWithItem('addContainer');">Add this container</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineContainer');" class="right-nav-link"><i class="fa fa-file-code-o"></i> Choose layout template</a></li>
  {if $item}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainer');" class="right-nav-link"><i class="fa fa-times"></i> Clear container definition</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainerOnItemPage');" class="right-nav-link"><i class="fa fa-times-circle"></i> Clear for this {$item.model.name|strtolower} only</a></li>
  {else}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('undefineContainer');" class="right-nav-link"><i class="fa fa-times"></i> Clear container definition</a></li>
  {/if}
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editContainer');" class="right-nav-link"><i class="fa fa-cog"></i> Edit this container</a></li>
</ul>

<ul class="invisible-actions-list" id="list-specific-actions" style="display:none">
  <li><b>List options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineList');" class="right-nav-link"><i class="fa fa-pencil"></i> Define list parameters</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('clearList');" class="right-nav-link"><i class="fa fa-times"></i> Clear list parameters</a></li>
</ul>

<ul class="invisible-actions-list" id="attachment-specific-actions" style="display:none">
  <li><b>Attachment options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editAttachment');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit attachment settings</a></li>
</ul>

<ul class="invisible-actions-list" id="asset-specific-actions" style="display:none">
  <li><b>File options</b></li>
  <li class="permanent-action"><a href="#" onclick="return elementTree.workWithItem('editFile');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this file</a></li>
  <li class="permanent-action"><a href="#" onclick="return MODALS.load('assets/assetCommentStream?asset_id='+elementTree.lastItemId, 'File notes');" class="right-nav-link"><i class="fa fa-comments"></i> View notes on this file</a></li>
</ul>

<ul class="invisible-actions-list" id="template-specific-actions" style="display:none">
  <li><b>Template options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editTemplate');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this template</a></li>
</ul>

<ul class="invisible-actions-list" id="itemspace-specific-actions" style="display:none">
  <li><b>Itemspace options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('defineItemspace');" class="right-nav-link"><i class="fa fa-pencil"></i> Choose item</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editItemspace');" class="right-nav-link"><i class="fa fa-cog"></i> Edit itemspace settings</a></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('clearItemspaceDefinition');" class="right-nav-link"><i class="fa fa-times"></i> Clear itemspace</a></li>
</ul>

<ul class="invisible-actions-list" id="item-specific-actions" style="display:none">
  <li><b>Item options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('openItem');" class="right-nav-link"><i class="fa fa-cube"></i> Edit this item</a></li>
</ul>

<ul class="invisible-actions-list" id="blocklist-specific-actions" style="display:none">
  <li><b>Blocklist options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editBlocklist');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this blocklist</a></li>
</ul>

<ul class="invisible-actions-list" id="field-specific-actions" style="display:none">
  <li><b>Field Options</b></li>
  <li class="permanent-action"><a href="#" onclick="elementTree.workWithItem('editField');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit field value</a></li>
  <li class="permanent-action">
    <a href="#" onclick="elementTree.workWithItem('setLiveProperty', {ldelim}confirm: 'Are you sure you want to set the draft value as live?'{rdelim})" class="right-nav-link">
      <i class="fa fa-globe"></i> Publish this field</a></li>
  <li class="permanent-action">
    <a href="#" onclick="elementTree.workWithItem('undefinePageProperty', {ldelim}confirm: 'Are you sure you want to clear the value of this field?'{rdelim})" class="right-nav-link">
      <i class="fa fa-times"></i> Clear this field</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Page Options</b></li>
  {if $page_template.filename && $version == "draft"}<li class="permanent-action"><a href="{$domain}templates/editTemplate?asset_type=SM_ASSETTYPE_MASTER_TEMPLATE&amp;template={if $page_template.status == "imported"}{$page_template.id}{else}{$page_template.url}{/if}" value="Edit"><i class="fa fa-file-code-o"></i> Edit Page Template</a></li>{/if}
  {if $version == "draft"}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/publishPageConfirm?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}'" class="right-nav-link"><i class="fa fa-globe"></i> Publish this page</a></li>{/if}
  {if _b($page.is_published)}<li class="permanent-action"><a href="javascript:;" onclick="viewLivePage({ldelim}domain: '{$_site.domain}'{rdelim});" class="right-nav-link"><i class="fa fa-rocket"></i> See this page online</a></li>{/if}
  <li class="permanent-action"><a href="{$domain}{$section}/layoutPresetForm?page_id={$page.webid}{if $item}&amp;item_id={$item.id}{/if}" class="right-nav-link"><i class="fa fa-share-square-o"></i> Create preset from this page</a></li>
  {if $draftAsset.asset_id && $draftAsset.asset_id != $liveAsset.asset_id}<li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Are you sure you want to publish your changes right now?')){workWithItem('setLiveAsset');}{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt=""> Publish This Asset Class</a>{/if}
  <li class="permanent-action"><a href="{$domain}smartest/assets/types" class="right-nav-link"><i class="fa fa-file-image-o"></i> Browse Assets Library</a></li>
  <li class="permanent-action"><a href="{$domain}{$section}/closeCurrentPage" class="right-nav-link"><i class="fa fa-check"></i> Finish working with this page</a></li>
  {if $allow_release}<li class="permanent-action"><a href="{$domain}{$section}/releasePage?page_id={$page.webid}" class="right-nav-link"><i class="fa fa-unlock"></i> Release this page</a></li>{/if}
</ul>

{if $show_recent_items}
<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">{$model.name} meta-pages</span></li>
  {foreach from=$metapages item="metapage"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$metapage.webid}&amp;item_id={$item.id}'"><i class="flaticon solid document-3"></i> {$metapage.label}</a></li>
  {/foreach}
</ul>
{/if}

{if $show_recent_items}
<ul class="actions-list" id="non-specific-actions">
  <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
  {foreach from=$recent_items item="recent_item"}
  <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/pageAssets?page_id={$page.webid}&amp;item_id={$recent_item.id}'"><i class="fa fa-cube"></i> {$recent_item.label|summary:"28"}</a></li>
  {/foreach}
</ul>
{/if}

</div>

{/if}