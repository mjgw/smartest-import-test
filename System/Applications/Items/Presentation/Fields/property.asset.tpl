{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="input_id" assign="input_id"}item_property_{$property.id}{/capture}

{if $property.is_image_property && $sm_user_agent.is_supported_browser}
  {image_select id=$input_id name=$name value=$value for="ipv" property=$property item_id=$item.id}
    
    {if $value.id}
    <ul class="item_property_actions">
      
      {if is_array($value.type_info)}
        <li><a href="#edit-file-instance-params" id="edit-asset-button-{$property.id}" title="Edit this file"><i class="fa fa-pencil"></i></a>
        <script type="text/javascript">
        $('edit-asset-button-{$property.id}').observe('click', function(){literal}{{/literal}MODALS.load('assets/editFileParametersModal?asset_id='+$F('{$input_id}'), 'Image parameters');{literal}}{/literal});
        $('edit-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit the selected image\'s parameters');{literal}}{/literal});
        $('edit-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        </script></li>
      {/if}
    
      {if is_array($value.type_info) && count($value.type_info.param)}
        <li><a href="#edit-file-instance-params" id="edit-params-button-{$property.id}" title="Edit display parameters for this instance of this file"><i class="fa fa-sliders"></i></a>
          <script type="text/javascript">
          $('edit-params-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit display parameters for this instance of the image');{literal}}{/literal});
          $('edit-params-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
          
          $('edit-params-button-{$property.id}').observe('click', function(e){ldelim}e.stop();MODALS.load('{$section}/editAssetData?item_id={$item.id}&property_id={$property.id}{if $request_parameters.page_id}&page_id={$request_parameters.page_id}{/if}', 'Contextual file display parameters'){rdelim});
          
          </script></li>
      {/if}
      
      
      <li><a href="#file-notes" id="edit-file-notes-button-{$property.id}" title="View and make notes this file"><i class="fa fa-comments"></i></a>
        <script type="text/javascript">
        $('edit-file-notes-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('View and make notes this file');{literal}}{/literal});
        $('edit-file-notes-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        
        /* $('edit-file-notes-button-{$property.id}').observe('click', function(e){literal}{{/literal}MODALS.load('assets/assetCommentStream?asset_id='+$('{$input_id}').value, 'Notes on file '+$('{$_input_data.id}-thumbnail').alt); e.stop();{literal}}{/literal}); */
        
        (function(id, pid, label){ldelim}
        {literal}
        $('edit-file-notes-button-'+pid).observe('click', function(e){
          e.stop();
          MODALS.load('assets/assetCommentStream?asset_id='+id, 'Notes on file '+label);
        });
        {/literal}
        {rdelim})({$value.id}, {$property.id}, '{$value.label|addslashes}');
        
        </script></li>
        
        
        <li style="padding-top:2px"><span class="form-hint" id="file-property-tooltip-{$property.id}"></span></li>
        
    </ul>
    {/if}
    
{else}

<div id="itemproperty-asset-select-{$property.id}" style="display:{if !$value.id && ($property.foreign_key_filter == 'SM_ASSETTYPE_RICH_TEXT' || $property.foreign_key_filter == 'SM_ASSETCLASS_TEXT')}none{else}block{/if}">
{asset_select id=$input_id name=$name value=$value options=$property._options required=$property.required}
{if !$value.id && ($property.foreign_key_filter == 'SM_ASSETTYPE_RICH_TEXT' || $property.foreign_key_filter == 'SM_ASSETCLASS_TEXT')} <a href="#cancel" class="button small" id="cancel-asset-text-select-{$property.id}">Cancel</a>{/if}
</div>

<script type="text/javascript" src="{$domain}Resources/System/Javascript/tinymce4/tinymce.min.js"></script>

  {if !$value.id && ($property.foreign_key_filter == 'SM_ASSETTYPE_RICH_TEXT' || $property.foreign_key_filter == 'SM_ASSETCLASS_TEXT')}
  <div class="edit-form-sub-row" style="margin-bottom:10px" id="asset-text-buttons-{$property.id}">
    <div style="margin-bottom:5px">{if $item.id}This {$item.model.name|lower} has no text at present{else}This is a new item which does not yet have any text for this property.{/if}</div>
    {if $item.id}
    <a class="button" href="{$domain}assets/startNewFileCreationForItemPropertyValue?property_id={$property.id}&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" id="new-asset-button-{$property.id}">Add new text</a>
    {else}
    <a class="button" href="#new-text" id="new-asset-button-{$property.id}">Add new text</a> 
    {/if}
    <a class="button" id="choose-asset-button-{$property.id}" href="#choose-text">Choose existing text</a>
  </div>
  {if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}
  <script type="text/javascript">
  {if !$item.id}$('new-asset-button-{$property.id}').observe('click', function(e){ldelim}
    e.stop();
    $('next-action').setValue('createAsset');
    $('property-id').setValue({$property.id});
    document.fire('smartest:newItemFormSubmit');
  {rdelim});
  {/if}
  $('choose-asset-button-{$property.id}').observe('click', function(e){ldelim}
    $('itemproperty-asset-select-{$property.id}').show();
    $('asset-text-buttons-{$property.id}').hide();
    e.stop();
  {rdelim});
  $('cancel-asset-text-select-{$property.id}').observe('click', function(e){ldelim}
    $('itemproperty-asset-select-{$property.id}').hide();
    $('asset-text-buttons-{$property.id}').show();
    e.stop();
  {rdelim});
  </script>
  
  {else}
  {if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}
  <ul class="item_property_actions">
    
    {if $can_create_assets}
      {if $item.id}
        <li><a href="{$domain}assets/startNewFileCreationForItemPropertyValue?property_id={$property.id}&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" title="Use a new file instead" id="new-asset-button-{$property.id}"><i class="fa fa-plus-circle"></i></a>
          <script type="text/javascript">
          $('new-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Define this property with a new file');{literal}}{/literal});
          $('new-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
          </script></li>
      {else}
      {* This is a new item *}
       <li><a href="#save-item-create-file" title="Use a new file" id="new-asset-button-{$property.id}"><i class="fa fa-plus-circle"></i></a>
         <script type="text/javascript">
         $('new-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Define this property with a new file');{literal}}{/literal});
         $('new-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
         $('new-asset-button-{$property.id}').observe('click', function(e){literal}{{/literal}$('next-action').setValue('createAsset'); $('property-id').setValue({$property.id}); e.stop(); document.fire('smartest:newItemFormSubmit'); {literal}}{/literal});
         </script></li>
      {/if}
    {/if}
    
    {if $value.id && is_array($value.type_info) && isset($value.type_info.editable) && _b($value.type_info.editable)}
      <li><a href="#edit-file" id="edit-asset-button-{$property.id}" title="Edit this file"><i class="fa fa-pencil"></i></a>
      <script type="text/javascript">
      $('edit-asset-button-{$property.id}').observe('click', function(e){literal}{{/literal}
        e.stop();
        {if $property.foreign_key_filter == 'SM_ASSETTYPE_RICH_TEXT'}
        MODALS.load('assets/richTextEditorModal?asset_id={$value.id}&from=item_edit&item_id={$item.id}{if $request_parameters.page_id}&page_id={$request_parameters.page_id}{/if}', 'Edit rich text', true);
        {else}
        window.location='{$domain}assets/editAsset?from=item_edit&asset_id='+$('{$input_id}').value+'&item_id={$item.id}{if $request_parameters.page_id}&page_id={$request_parameters.page_id}{/if}';
        {/if}
      {literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit the selected file');{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
      </script></li>
    {elseif $value.id && is_array($value.type_info) && ((!isset($value.type_info.editable) || !_b($value.type_info.editable)) && count($value.type_info.param))}
      <li><a href="#edit-file-param" id="edit-asset-button-{$property.id}" title="Edit this file"><i class="fa fa-pencil"></i></a>
      <script type="text/javascript">
      $('edit-asset-button-{$property.id}').observe('click', function(e){literal}{{/literal}e.stop();window.location='{$domain}assets/editAsset?from=item_edit&asset_id='+$('{$input_id}').value+'&item_id={$item.id}{if $request_parameters.page_id}&page_id={$request_parameters.page_id}{/if}'{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit the selected file\'s display parameters');{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
      </script></li>
    {/if}
    
    {if $value.id && is_array($value.type_info) && count($value.type_info.param)}
      <li><a href="#edit-file-params" id="edit-params-button-{$property.id}" title="Edit display parameters for this instance of this file"><i class="fa fa-sliders"></i></a>
        <script type="text/javascript">
        $('edit-params-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit display parameters for this instance');{literal}}{/literal});
        $('edit-params-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        $('edit-params-button-{$property.id}').observe('click', function(e){ldelim}e.stop();MODALS.load('{$section}/editAssetData?item_id={$item.id}&property_id={$property.id}{if $request_parameters.page_id}&page_id={$request_parameters.page_id}{/if}', 'Contextual file display parameters'){rdelim});
        </script></li>
    {/if}
    
    {if $value.id}
      <li><a href="#file-notes" id="edit-file-notes-button-{$property.id}" title="View and make notes this file"><i class="fa fa-comments"></i></a>
        <script type="text/javascript">
        $('edit-file-notes-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('View and make notes this file');{literal}}{/literal});
        $('edit-file-notes-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
        
        (function(id, pid, label){ldelim}
        {literal}
        $('edit-file-notes-button-'+pid).observe('click', function(e){
          e.stop();
          MODALS.load('assets/assetCommentStream?asset_id='+id, 'Notes on file '+label);
        });
        {/literal}
        {rdelim})({$value.id}, {$property.id}, '{$value.label|addslashes}');
        
        </script></li>
    {/if}
    
    <li style="padding-top:2px"><span class="form-hint" id="file-property-tooltip-{$property.id}"></span></li>
    
  </ul>
  {/if}

{/if}