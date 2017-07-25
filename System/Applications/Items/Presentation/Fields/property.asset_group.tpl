{capture name="name" assign="name"}item[{$property.id}]{/capture}
{capture name="property_id" assign="property_id"}item_property_{$property.id}{/capture}

{asset_group_select id=$property_id name=$name value=$value options=$property._options required=$property.required}
{if is_numeric($value.id)}
 <a href="#edit-group" id="edit-group-button-{$property.id}" class="button">Edit file group</a>
 <script type="text/javascript">
 $('edit-group-button-{$property.id}').observe('click', function(e){ldelim}
     e.stop();
     window.location='{$domain}assets/editAssetGroupContents?from=editItem&group_id='+$('item_property_{$property.id}').value+'&item_id={$item.id}{if $request_parameters.page_id}&page_webid={$request_parameters.page_id}{/if}';
 {rdelim});
 </script>
{/if}

{if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}