<div id="work-area">

{load_interface file="template_edit_tabs.tpl"}

<h3>{$interface_title}</h3>

{if $show_form}

{if $is_editable}<form action="{$domain}{$section}/updateTemplate" method="post" name="newTemplate" enctype="multipart/form-data">{/if}
  
  {if $template.status == "imported"}
  <input type="hidden" name="edit_type" value="imported" />
  <input type="hidden" name="template_id" value="{$template.id}" />
  {else}
  <input type="hidden" name="edit_type" value="unimported" />
  <input type="hidden" name="type" value="{$template.type}" />
  <input type="hidden" name="filename" value="{$template.url}" />
  {/if}
  
  <div class="special-box"><strong>Template</strong>: <code>{$template.storage_location}</code><strong><code>{$template.url}</code></strong></div>
  {if !$file_is_writable}
    <div class="warning">This file is not currently writable by the web server, so it cannot be edited directly in Smartest.</div>
  {elseif !$dir_is_writable}
    <div class="warning">The directory where this file is stored is not currently writable by the web server, so this file cannot be edited directly in Smartest.</div>
  {/if}
  
  {if $template.status == 'unimported'}
  <div class="warning">
    This template has not been imported into Smartest's template repository yet. To import it into this website, <a href="{$domain}templates/importSingleTemplate?type={$template.type}&amp;asset_type={$template.type}&amp;template={$template.url}" class="button">click here</a>
  </div>
  {/if}
  
  {if $model}
  <div class="special-box">
    This template is paired with the <strong>{$model.plural_name|strtolower}</strong> model <a href="#" onclick="return MODALS.load('datamanager/modelInfo?class_id={$model.id}', 'Model info')"><i class="fa fa-info-circle"></i></a> <a href="#" onclick="return MODALS.load('datamanager/showItemClassTemplateAccess?class_id={$model.id}', '{$model.plural_name} template data')"><i class="fa fa-file-code-o"></i></a>. {help id="templates:data_in_templates"}What does this mean?{/help}
  </div>
  {else}
  
  {if $suggested_models._count}
    {if $suggested_models._count > 1}
  <div class="special-box"><p>It looks like you're using data in this template that is specific to one or more of your models. Would you like to associate it with one of those models?</p>
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Suggested models</b></li>
    {foreach from=$suggested_models item="model"}
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/pairTemplateWithModelOneClick?template_id={$template.id}&amp;model_id={$model.id}'"><img border="0" src="/Resources/Icons/package_small.png" /> {$model.plural_name}</a></li>
    {/foreach}
  </ul>
  <p>Or, you can just <a href="{$domain}{$section}/hideTemplateModelPairingMessage?template_id={$template.id}">hide this message</a>.</p></div>
    {else}
  <div class="special-box"><p>It looks like you're using data in this template that is specific to the <strong>{$suggested_models._first.plural_name|lower}</strong> model.</p><p>You can confirm this by <a href="{$domain}{$section}/pairTemplateWithModelOneClick?template_id={$template.id}&amp;model_id={$suggested_models._first.id}" class="button small">clicking here</a>, which will help Smartest show you the most relevant options when choosing templates, or you can hide this message by <a href="{$domain}{$section}/hideTemplateModelPairingMessage?template_id={$template.id}" class="button small">clicking here</a>.</p></div>
    {/if}
  {/if}
  
  {/if}
  
  <div style="width:100%" id="editTMPL" class="textarea-holder">
    <textarea name="template_content" id="tpl_textArea" style="display:block">{$template_content}</textarea>
    <div style="height:14px"><span class="form-hint">Editor powered by CodeMirror</span></div>
  </div>
  
  <div class="buttons-bar">
    {if $is_editable}
    {save_buttons}
    {else}
    <input type="button" onclick="cancelForm();" value="Cancel" />
    {/if}
  </div>
  
{if $is_editable}</form>{/if}

{/if}


<script type="text/javascript">
{literal}
var myCodeMirror = CodeMirror.fromTextArea($('tpl_textArea'), {
    lineNumbers: true,
    mode: "htmlmixed",
    lineWrapping: true
  });
{/literal}
</script>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><strong>Options</strong></li>
    {if $is_convertable}<li class="permanent-action"><a href="javascript:nothing()" onclick="window.location='{$domain}{$section}/convertTemplateType?template_id={$template.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/wrench_orange.png" border="0" alt="" /> Convert to another type</a></li>{/if}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/listByType?type={$template.type}'"><img src="{$domain}Resources/Icons/page_white_stack.png" border="0" alt="" /> See {$type_info.label|lower}s</a></li>
    {if $model.id}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/templates/models?model_id={$model.id}'"><img src="{$domain}Resources/Icons/page_white_stack.png" border="0" alt="" /> See {$model.name|lower} templates</a></li>{/if}
  </ul>
  

  <ul class="actions-list" id="stylesheet-actions">
{if $template.type == 'SM_ASSETTYPE_MASTER_TEMPLATE' && !empty($stylesheets)}
    <li><strong>Stylesheets in this template</strong></li>
{foreach from=$stylesheets item="stylesheet"}
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/editAsset?asset_id={$stylesheet.id}'"><i class="fa fa-file-o"></i> {$stylesheet.label}</a></li>
{/foreach}
{elseif count($tokens)}
    <li><strong>Stylesheets for this template</strong></li>
    <li><img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" /> Scanning template...</li>
{/if}
  </ul>

{if $template.type != 'SM_ASSETTYPE_MASTER_TEMPLATE' && count($tokens)}
<script type="text/javascript">
  {literal}(function(id, type){
    new Ajax.Updater('stylesheet-actions', sm_domain+'ajax:templates/relevantTemplates?asset_type='+type+'&template='+id, {
      onSuccess: function(response){
        if(!response.responseText.charAt(1)){
          $('stylesheet-actions').hide();
        }
      }
    });
  })({/literal}{if $template.status == "imported"}{$template.id}{else}'{$template.url}'{/if}, '{$template.type}');
</script>
{elseif $template.status == 'unimported' && $template.type == 'SM_ASSETTYPE_MASTER_TEMPLATE' && count($tokens)}
<script type="text/javascript">
  {literal}(function(id, type){
    new Ajax.Updater('stylesheet-actions', sm_domain+'ajax:templates/relevantTemplates?asset_type='+type+'&template='+id, {
      onSuccess: function(response){
        if(!response.responseText.charAt(1)){
          $('stylesheet-actions').hide();
        }
      }
    });
  })({/literal}'{$template.url}', '{$template.type}');
</script>
{/if}

{if !empty($recently_edited)}
<ul class="actions-list" id="non-specific-actions">
  <li><b>Recently edited</b></li>
  {foreach from=$recently_edited item="recent_template"}
	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_template.action_url}'"><img border="0" src="{$recent_template.small_icon}" /> {$recent_template.label|summary:"30"}</a></li>
  {/foreach}
</ul>
{/if}
  
</div>