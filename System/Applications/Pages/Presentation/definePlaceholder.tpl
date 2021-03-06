<script language="javascript">

var show_params_holder = false;

{literal}

var toggleParamsHolder = function(){
  if(show_params_holder){
    new Effect.BlindUp('params-holder', {duration: 0.6});
    show_params_holder = false;
    $('params-holder-toggle-link').innerHTML = "show";
  }else{
    new Effect.BlindDown('params-holder', {duration: 0.6});
    show_params_holder = true;
    $('params-holder-toggle-link').innerHTML = "hide";
  }
}

{/literal}
</script>

<div id="work-area">
  
  <h3>Choose a file for: <span class="light">{$placeholder.name}</span></h3>
  
  {if $require_choose_item}
  
  <div class="instruction">As this is a meta-page, you must choose an item to continue</div>
  
  <form id="item_chooser" method="get" action="{$domain}{$section}/definePlaceholder">
    
    <input type="hidden" name="assetclass_id" value="{$placeholder.name}" />
    <input type="hidden" name="page_id" value="{$page.webid}" />
    <input type="hidden" name="instance" id="instance" value="{$instance}" />
    
    <select name="item_id" onchange="$('item_chooser').submit()" style="width:300px">
      {foreach from=$items item="possible_item"}
        <option value="{$possible_item.id}">{$possible_item.name}</option>
      {/foreach}
    </select>
    <input type="submit" value="Continue" />
    
  </form>
  
  {else}
  
  <form id="file_chooser" method="get" action="{$domain}{$section}/definePlaceholder">
    
    <div class="edit-form-row">
      <div class="form-section-label">{if !$asset.id && ($placeholder.type == 'SM_ASSETCLASS_RICH_TEXT' || $placeholder.type == 'SM_ASSETCLASS_TEXT')}Placeholder text{else}Choose media for this placeholder{/if}</div>
      {if $only_accepts_images}
      
      {image_select id="chosen-asset-id" name="chosen_asset_id" for="placeholder" placeholder_id=$placeholder.id value=$asset onchange="$('file_chooser').submit()"}
      
      {else}
      
      <div id="placeholder-asset-select-{$placeholder.id}" style="display:{if !$asset.id && ($placeholder.type == 'SM_ASSETCLASS_RICH_TEXT' || $placeholder.type == 'SM_ASSETCLASS_TEXT')}none{else}block{/if}">

        <select name="chosen_asset_id" onchange="$('file_chooser').submit()">
          {if !$valid_definition}<option value="">None Selected</option>{/if}
          {foreach from=$assets item="available_asset"}
            <option value="{$available_asset.id}"{if $available_asset.id==$asset.id} selected="selected"{/if}>{if $available_asset.id==$live_asset_id}* {/if}{$available_asset.label}</option>
          {/foreach}
        </select>

      {if !$asset.id && ($placeholder.type == 'SM_ASSETCLASS_RICH_TEXT' || $placeholder.type == 'SM_ASSETCLASS_TEXT')} <a href="#cancel" class="button small" id="cancel-asset-text-select-{$placeholder.id}">Cancel</a>{/if}
      </div>
      
      {if !$asset.id && ($placeholder.type == 'SM_ASSETCLASS_RICH_TEXT' || $placeholder.type == 'SM_ASSETCLASS_TEXT')}
      
      <div class="edit-form-sub-row" style="margin-bottom:10px" id="asset-text-buttons-{$placeholder.id}">
        <div style="margin-bottom:5px">No text is currently in use for placeholder <code>'{$placeholder.name}'</code> on this page.</div>
        <a class="button" href="{$domain}assets/startNewFileCreationForPlaceholderDefinition?placeholder_id={$placeholder.id}&amp;page_id={$page.id}{if $show_item_options}&amp;item_id={$item.id}{/if}&amp;instance={$instance}" id="new-asset-button-{$placeholder.id}">Add new text</a>
        <a class="button" id="choose-asset-button-{$placeholder.id}" href="#choose-text">Choose existing text</a>
      </div>
  
      <script type="text/javascript">
      $('choose-asset-button-{$placeholder.id}').observe('click', function(e){ldelim}
        $('placeholder-asset-select-{$placeholder.id}').show();
        $('asset-text-buttons-{$placeholder.id}').hide();
        e.stop();
      {rdelim});
      $('cancel-asset-text-select-{$placeholder.id}').observe('click', function(e){ldelim}
        $('placeholder-asset-select-{$placeholder.id}').hide();
        $('asset-text-buttons-{$placeholder.id}').show();
        e.stop();
      {rdelim});
      </script>
  
      {/if}
      
      {* if !$valid_definition}<br /><a class="button small" style="margin-top:5px" href="{$domain}assets/startNewFileCreationForPlaceholderDefinition?placeholder_id={$placeholder.id}&amp;page_id={$page.id}{if $show_item_options}&amp;item_id={$item.id}{/if}&amp;instance={$instance}">{if $placeholder.type == 'SM_ASSETCLASS_RICH_TEXT'}Write new text{else}Upload a new file{/if}</a>{/if *}
      
      {/if}
      
    </div>
    
    <input type="hidden" name="assetclass_id" value="{$placeholder.name}" />
    <input type="hidden" name="page_id" value="{$page.webid}" />
    {if $show_item_options}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
    <input type="hidden" name="instance" id="instance" value="{$instance}" />
    
    </form>
    
    <form id="pageViewForm" method="post" action="{$domain}{$section}/updatePlaceholderDefinition">
    
      <input type="hidden" name="page_id" value="{$page.id}" />
      <input type="hidden" name="placeholder_id" value="{$placeholder.id}" />
      {if $show_item_options}<input type="hidden" name="item_id" value="{$item.id}" />{/if}
      <input type="hidden" name="instance" id="instance" value="{$instance}" />
    
    {if $valid_definition}
    
      <input type="hidden" name="asset_id" value="{$asset.id}" />
    
      <div class="edit-form-row">
        <div class="form-section-label">Chosen File:</div>
        {if $asset.is_binary_image && $placeholder.type != 'SM_ASSETCLASS_STATIC_IMAGE'}
        {$asset.image.constrain_150}
        <div class="breaker"></div>
        <div class="edit-form-sub-row">
          <b>{$asset.label}</b> <code>({if $asset_type.storage.type == 'file'}{$asset_type.storage.location}{/if}{$asset.url})</code> - {$asset_type.label} ({$asset.width} x {$asset.height} pixels)
        </div>
        {else}
        <b>{$asset.label}</b> <code>({if $asset_type.storage.type == 'file'}{$asset_type.storage.location}{/if}{$asset.url})</code> - {$asset_type.label}
        {/if}
      </div>
      
      {if $show_item_options}
      <div class="edit-form-row">
        <div class="form-section-label">Meta Page:</div>
        {$page.static_title}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">{$item.model.name}:</div>
        {$item.name}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Define placeholder on this meta-page for:</div>
        <select name="definition_scope">
          
          <option value="THIS">This {$item.model.name|strtolower} only</option>
          {if $item_uses_default}<option value="DEFAULT">All {$item.model.plural_name|strtolower} currently using the default definition</option>{/if}
          <option value="ALL">All {$item.model.plural_name|strtolower}{if $is_defined} (removes all other per-item definitions){/if}</option>
          
        </select>
      </div>
      {else}
      <div class="edit-form-row">
        <div class="form-section-label">Page:</div>
        {$page.title}
      </div>
      {/if}
      
      {if $placeholder.uses_group}
      <div class="edit-form-row">
        <div class="form-section-label">Choosing from file group:</div>
        {$placeholder.group.label} <a href="{$domain}assets/editAssetGroupContents?group_id={$placeholder.group.id}&amp;from=definePlaceholder&amp;placeholder_id={$placeholder.id}&amp;page_id={$page.id}{if $item}&amp;item_id={$item.id}{/if}" class="button small">Edit group contents</a>
      </div>
      {/if}
      
{if !empty($params)}

<div class="v-spacer"></div>

<div class="special-box">
    
    <div class="heading">Instance parameters <a id="params-holder-toggle-link" href="javascript:toggleParamsHolder()" class="button">show</a></div>
    
    <div id="params-holder" style="display:none">
    {foreach from=$params key="parameter_name" item="parameter"}
      <div class="edit-form-row">
        <div class="form-section-label">{$asset_params[$parameter_name].label}</div>
        {if $asset_params[$parameter_name].datatype == 'SM_DATATYPE_BOOLEAN'}
          {capture name="name" assign="name"}params[{$parameter_name}]{/capture}
          {capture name="param_id" assign="param_id"}asset-parameter-{$parameter_name}{/capture}
          {boolean name=$name id=$param_id value=$parameter.value}
        {else}
          {if $asset_params[$parameter_name].has_options}
          <select name="params[{$parameter_name}]" id="render_parameter_{$parameter_name}">
            {if !$asset_params[$parameter_name].required}<option value=""></option>{/if}
          {foreach from=$asset_params[$parameter_name].options item="opt" key="key"}
            <option value="{$key}"{if $parameter.value == $key} selected="selected"{/if}>{$opt}</option>
          {/foreach}
          </select>
          {else}
          <input type="text" name="params[{$parameter_name}]" value="{$parameter.value}" style="width:250px" id="render_parameter_{$parameter_name}" />
          {/if}
        {/if}
        {if strlen($asset_params[$parameter_name].value) && $asset_params[$parameter_name].datatype != 'SM_DATATYPE_BOOLEAN' && !count($asset_params[$parameter_name].options)}
          Default: '<span id="param_{$parameter_name}_default_value">{$asset_params[$parameter_name].value}</span>' <a href="#apply" class="button applybutton" data-parameter="{$parameter_name}">Apply</a>
        {elseif strlen($asset_params[$parameter_name].value) && $asset_params[$parameter_name].has_options}
          Default: '<span id="param_{$parameter_name}_default_value">{$asset_params[$parameter_name].value}</span>' <a href="#apply" class="button applyselectvalue" data-parameter="{$parameter_name}" id="applyselectvalue_{$parameter_name}">Apply</a>
        {/if}
      </div>
    {/foreach}
    <div class="breaker"></div>
    <script type="text/javascript">
    {literal}
    
    $$('a.applybutton').each(function(btn){
      btn.observe('click', function(e){
        e.stop();
        $('render_parameter_'+btn.readAttribute('data-parameter')).value = $('param_'+btn.readAttribute('data-parameter')+'_default_value').innerHTML;
      });
    });
    
    $$('a.applyselectvalue').each(function(btn){
      btn.observe('click', function(e){
        e.stop();
        var v = new Smartest.UI.SelectMenu('render_parameter_'+btn.readAttribute('data-parameter'));
        v.setValue($('param_'+btn.readAttribute('data-parameter')+'_default_value').innerHTML);
      });
    });
    
    {/literal}
    </script>
    </div>

</div>
{/if}

{/if}
  
  <div class="edit-form-row">
    <div class="buttons-bar">
      <input type="button" onclick="cancelForm();" value="Cancel" />
      {if $valid_definition}<input type="submit" value="Save Changes" />{/if}
    </div>
  </div>
  
  </form>
  
  {/if}
  
</div>

<div id="actions-area">

  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{$domain}assets/startNewFileCreationForPlaceholderDefinition?placeholder_id={$placeholder.id}&amp;page_id={$page.id}{if $show_item_options}&amp;item_id={$item.id}{/if}&amp;instance={$instance}" class="right-nav-link"><i class="fa fa-plus-circle"></i> Create a new file for this page</a></li>
    <li class="permanent-action"><a href="{$domain}{$section}/editPlaceholder?placeholder_id={$placeholder.id}" class="right-nav-link"><i class="fa fa-pencil"></i> Edit placeholder</a></li>
{if $item}
    <li class="permanent-action"><a href="{$domain}websitemanager/undefinePlaceholder?page_id={$page.id}&amp;assetclass_id={$placeholder.name}&amp;instance={$instance}" class="right-nav-link"><i class="fa fa-times"></i> Clear this placeholder for all {$item.model.plural_name|strtolower}</a></li>
    <li class="permanent-action"><a href="{$domain}websitemanager/undefinePlaceholderOnItemPage?page_id={$page.id}&amp;assetclass_id={$placeholder.name}&amp;item_id={$item.id}"  class="right-nav-link"><i class="fa fa-times-circle"></i> Clear for this {$item.model.name|strtolower} only</a></li>
{else}
    <li class="permanent-action"><a href="{$domain}websitemanager/undefinePlaceholder?page_id={$page.id}&amp;assetclass_id={$placeholder.name}&amp;instance={$instance}" class="right-nav-link"><i class="fa fa-times"></i> Clear this placeholder</a></li>
{/if}
  </ul>
  
</div>