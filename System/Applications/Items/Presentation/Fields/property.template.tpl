{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="input_id" assign="input_id"}item_property_{$property.id}{/capture}

{asset_select id=$input_id name=$_input_data.name value=$value options=$property._options required=$property.required}
{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}

{if $value.id}
  <ul class="item_property_actions">
    
    {if is_array($value.type_info)}
      <!--<input type="button" onclick="window.location='{$domain}assets/editAsset?from=item_edit&amp;asset_id='+$('item_property_').value" value="Edit &gt;&gt;" />-->
      <li><a href="javascript:;" id="edit-asset-button-{$property.id}" title="Edit this file"><i class="fa fa-pencil"></i></a>
      <script type="text/javascript">
      $('edit-asset-button-{$property.id}').observe('click', function(){literal}{{/literal}
        // window.location='{$domain}templates/editTemplate?from=item_edit&template='+$('{$input_id}').value
        MODALS.load('templates/editTemplateModal?template='+$F('{$input_id}')+'&type=SM_ASSETTYPE_SINGLE_ITEM_TEMPLATE&from=editItem&item_id={$item.id}', 'Edit template', true);
      {literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('Edit the selected template');{literal}}{/literal});
      $('edit-asset-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-property-tooltip-{$property.id}').update('');{literal}}{/literal});
      </script></li>
    {/if}
    
    <li style="padding-top:6px"><span class="form-hint" id="file-property-tooltip-{$property.id}"></span></li>
    
  </ul>
{/if}