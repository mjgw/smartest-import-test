<script type="text/javascript">
{literal}
  var templates = new Smartest.UI.OptionSet('pageViewForm', 'item_id_input', 'option', 'options_grid');
{/literal}
</script>

<div id="work-area">

<h3>{$type.label}s</h3>

<div class="instruction">{$type.description}</div>

{if !$dir_is_writable}
<div class="warning">The directory <code>{$type.storage.location}</code> needs to be writable before you can add more of these files via Smartest.</div>
{/if}

<form id="pageViewForm" method="get" action="">
  <input type="hidden" name="type" value="{$type.template_type}" />
  <input type="hidden" name="asset_type" value="{$type.id}" />
  <input type="hidden" name="template" id="item_id_input"  value="" />
</form>

<div id="options-view-header">

  <div id="options-view-info">
    Found {$count} {$type.label|lower}{if $count != 1}s{/if}.
  </div>
  
  <div id="options-view-chooser">
    <a href="#list-view" onclick="return templates.setView('list', 'list_by_type_view')" id="options-view-list-button" class="{if $list_style == "list"}on{else}off{/if}"></a>
    <a href="#grid-view" onclick="return templates.setView('grid', 'list_by_type_view')" id="options-view-grid-button" class="{if $list_style == "grid"}on{else}off{/if}"></a>
  </div>
  
  <div class="breaker"></div>
  
</div>

<ul class="options-{$list_style}" style="margin-top:0px" id="options_grid">
  <li class="add">
    <a href="{$domain}{$section}/addTemplate?type={$type.id}" class="add"><i>+</i>Add a new {$type.label|lower}</a>
  </li>
{foreach from=$templates item="template"}
  <li>
    <a href="#" class="option" id="{if $template.status == 'imported'}imported-template{else}unimported-template{/if}_{if $template.status == 'imported'}{$template.id}{else}{$template.url}{/if}" onclick="return templates.setSelectedItem('{if $template.status == 'imported'}{$template.id}{else}{$template.url}{/if}', '{if $template.status == 'imported'}imported-template{else}unimported-template{/if}');" ondblclick="window.location='{$domain}{$section}/editTemplate?asset_type={$template.type}&amp;template={if $template.status == 'imported'}{$template.id}{else}{$template.url}{/if}'">
      <i class="fa fa-file-code-o list"></i><img border="0" src="{$domain}Resources/System/Images/{if $template.status == 'imported'}template{else}mystery_page{/if}.png" class="grid" /><span class="label">{$template.url}</span></a>
  </li>
{/foreach}
</ul>

</div>

<div id="actions-area">

<ul class="actions-list" id="imported-template-specific-actions" style="display:none">
    
  <li><b>Selected template:</b></li>
	<li class="permanent-action"><a href="javascript:templates.workWithItem('editTemplate');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit this template</a></li>
	<li class="permanent-action"><a href="javascript:templates.workWithItem('templateInfo');" class="right-nav-link"><i class="fa fa-info-circle"></i> About this template</a></li>
	<li class="permanent-action"><a href="javascript:templates.workWithItem('duplicateTemplate');" class="right-nav-link"><i class="fa fa-clipboard"></i> Duplicate this template</a></li>
	<li class="permanent-action"><a href="javascript:{literal}if(confirm('Really delete this template?')){ templates.workWithItem('deleteTemplate'); }{/literal}" class="right-nav-link"><i class="fa fa-times-circle"></i> Delete this template</a></li>
	<li class="permanent-action"><a href="javascript:templates.workWithItem('downloadTemplate');" class="right-nav-link"><i class="fa fa-download"></i> Download this template</a></li>
</ul>

<ul class="actions-list" id="unimported-template-specific-actions" style="display:none">
    
  <li><b>Unimported template:</b></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('importSingleTemplate');" class="right-nav-link"><i class="fa fa-sign-in"></i> Import this template</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('editTemplate');" class="right-nav-link"><i class="fa fa-pencil"></i> Edit as-is</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('deleteTemplate');" class="right-nav-link"><i class="fa fa-times-circle"></i> Delete this template</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('duplicateTemplate');" class="right-nav-link"><i class="fa fa-clipboard"></i> Duplicate this template</a></li>
	<li class="permanent-action"><a href="#" onclick="return templates.workWithItem('downloadTemplate');" class="right-nav-link"><i class="fa fa-download"></i> Download this template</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Template options</b></li>
	{if $dir_is_writable}<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/addTemplate?type={$type.id}';" class="right-nav-link"><i class="fa fa-plus-circle"></i> Add another {$type.label|lower}</a></li>{/if}
	<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}smartest/templates';" class="right-nav-link"><i class="fa fa-folder-open"></i> Back to template types</a></li>
</ul>

<ul class="actions-list" id="non-specific-actions">
  <li><b>Recent {$type.label|strtolower}s</b></li>
  {foreach from=$recently_edited item="recent_template"}
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><i class="fa fa-file-code-o"></i> {$recent_template.label|summary:"30"}</a></li>
  {/foreach}
</ul>

</div>