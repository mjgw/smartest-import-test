<script type="text/javascript">
{literal}
  var templates = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
{/literal}
</script>

<div id="work-area">

{if $model.id}
<h3>{$model.name} templates</h3>

{if count($locations)}
  <div class="warning">
      <p>For smooth operation of the templates repository, the following locations need to be made writable:</p>
      <ul>
{foreach from=$locations item="l"}
        <li><code>{$l}</code></li>
{/foreach}        
      </ul>
  </div>
{/if}

{load_interface file="template_browse_tabs.tpl"}
<div class="instruction">Showing templates used to display information about {$model.plural_name|lower}</div>
{else}
<h3>Templates by model</h3>

{if count($locations)}
  <div class="warning">
      <p>For smooth operation of the templates repository, the following locations need to be made writable:</p>
      <ul>
{foreach from=$locations item="l"}
        <li><code>{$l}</code></li>
{/foreach}        
      </ul>
  </div>
{/if}

{load_interface file="template_browse_tabs.tpl"}
<div class="instruction">Showing templates that are not used to display information about any specific model</div>
{/if}

<div class="special-box">
  <form action="{$domain}smartest/templates/models" method="get" id="model-selector-form">
    Choose a model: <select name="model_id" id="model-selector">
      <option value="0"{if !$model.id} selected="selected"{/if}>Non-specific</option>
{foreach from=$models item="mdl"}
      <option value="{$mdl.id}"{if $mdl.id == $model.id} selected="selected"{/if}>{$mdl.plural_name}</option>
{/foreach}
    </select>
    {literal}<script type="text/javascript">$('model-selector').observe('change', function(){$('model-selector-form').submit()})</script>{/literal}
  </form>
</div>

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="{$type.template_type}" />
  <input type="hidden" name="asset_type" value="{$type.id}" />
  <input type="hidden" name="template" id="item_id_input"  value="" />
</form>

<div id="options-view-header">

  <div id="options-view-info">
    Found {$templates._count} template{if $templates._count != 1}s{/if}.
  </div>
  
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return templates.setView('list', 'list_by_type_view')" id="options-view-list-button" class="{if $list_style == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return templates.setView('grid', 'list_by_type_view')" id="options-view-grid-button" class="{if $list_style == "grid"}on{else}off{/if}"></a>
  </div>
  
  <div class="breaker"></div>
  
</div>

<ul class="options-{$list_style}" style="margin-top:0px" id="options_grid">
{foreach from=$templates item="template"}
<li>
  <a href="#" class="option" id="item_{$template.id}" onclick="return templates.setSelectedItem('{$template.id}', 'item');" ondblclick="window.location='{$domain}{$section}/editTemplate?asset_type={$template.type}&amp;template={$template.id}'">
    <i class="fa fa-file-code-o list"></i><img border="0" src="{$domain}Resources/System/Images/template.png" class="grid" />{$template.url}</a>
</li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="item-specific-actions" style="display:none">
    
  <li><b>Selected template:</b></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('editTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/pencil.png" border="0" alt="" /> Edit this template</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('templateInfo');" class="right-nav-link"><img src="{$domain}Resources/Icons/information.png" border="0" alt="" /> About this template</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('duplicateTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_edit.png" border="0" alt="" /> Duplicate this template</a></li>
	<li class="permanent-action"><a href="#" onclick="{literal}if(confirm('Really delete this template?')){ return templates.workWithItem('deleteTemplate'); }{/literal}" class="right-nav-link"><img src="{$domain}Resources/Icons/page_delete.png" border="0" alt="" /> Delete this template</a></li>
    <li class="permanent-action"><a href="#" onclick="return templates.workWithItem('downloadTemplate');" class="right-nav-link"><img src="{$domain}Resources/Icons/page_white_put.png" border="0" alt="" /> Download this template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template options</b></li>
	{if $dir_is_writable}<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addTemplate?type={$type.id}';" class="right-nav-link"><img src="{$domain}Resources/Icons/page_add.png" border="0" alt="" /> Add another {$type.label|lower}</a></li>{/if}
	<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/templates';" class="right-nav-link"><img src="{$domain}Resources/Icons/folder.png" border="0" alt="" style="width:16px;height:16px" /> Back to template types</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Recent templates</b></li>
  {foreach from=$recently_edited item="recent_template"}
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><img border="0" src="{$recent_template.small_icon}" /> {$recent_template.label|summary:"30"}</a></li>
  {/foreach}
</ul>

</div>