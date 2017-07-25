{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{asset_group_select id=$property_id name=$name value=$value options=$property._options required=$property.required}

  <ul class="item_property_actions">
    {if is_numeric($item.id) || is_numeric($value.id)}
    {* <li style="display:{if is_numeric($item.id)}block{else}none{/if}"><a href="{$domain}sets/addSet?class_id={$property.foreign_key_filter}&amp;from=editItem&amp;itemproperty_id={$property.id}&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" title="Add a new set" id="new-set-button-{$property.id}"><img src="{$domain}Resources/Icons/add.png" alt="" /></a></li> *}
    <li style="display:{if is_numeric($value.id)}block{else}none{/if}" id="reorder-gallery-property-{$property.id}"><a href="{$domain}assets/arrangeAssetGallery?from=editItem&amp;group_id={$value.id}&amp;from=editItem&amp;item_id={$item.id}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}" title="Arrange this gallery" id="edit-gallery-button-{$property.id}"><i class="fa fa-random"></i></a></li>
    <script type="text/javascript">
    $('edit-gallery-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-gallery-property-tooltip-{$property.id}').update('Arrange the selected gallery');{literal}}{/literal});
    $('edit-gallery-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-gallery-property-tooltip-{$property.id}').update('');{literal}}{/literal});
    </script>
    {if is_numeric($item.id)}
    <li><a href="#create-new-gallery" id="add-gallery-button-{$property.id}"><i class="fa fa-plus-circle"></i></a></li>
    <script type="text/javascript">
    $('add-gallery-button-{$property.id}').observe('mouseover', function(){literal}{{/literal}$('file-gallery-property-tooltip-{$property.id}').update('Add a gallery here');{literal}}{/literal});
    $('add-gallery-button-{$property.id}').observe('mouseout', function(){literal}{{/literal}$('file-gallery-property-tooltip-{$property.id}').update('');{literal}}{/literal});
    $('add-gallery-button-{$property.id}').observe('click', function(e){literal}{{/literal}
    e.stop();
    MODALS.load('assets/createAssetGalleryForItemPropertyValue?property_id={$property.id}&item_id={$item.id}', 'Create a gallery');
    {literal}}{/literal});
    </script>
    {/if}
    {/if}
    <li style="padding-top:2px"><span class="form-hint" id="file-gallery-property-tooltip-{$property.id}"></span></li>
  </ul>


{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}