<ul class="tabset">
  {if $model.type == 'SM_ITEMCLASS_MODEL'}
    <li{if $method == "getItemClassMembers"} class="current"{/if}><a href="{$domain}datamanager/getItemClassMembers?class_id={$model.id}">All {$model.plural_name|lower}</a></li>
    <li{if $method == "getItemClassSets"} class="current"{/if}><a href="{$domain}sets/getItemClassSets?class_id={$model.id}">Sets of {$model.plural_name|lower}</a></li>
  {/if}  
    {if $has_metapages}<li{if $method == "getItemClassComments"} class="current"{/if}><a href="{$domain}datamanager/getItemClassComments?class_id={$model.id}">Comments</a></li>{/if}
    <li{if $method == "editModel"} class="current"{/if}><a href="{$domain}datamanager/editModel?class_id={$model.id}">{if $can_edit_properties}Edit model{else}Model attributes{/if}</a></li>
    {if $can_edit_properties}<li{if $method == "getItemClassProperties"} class="current"{/if}><a href="{$domain}datamanager/getItemClassProperties?class_id={$model.id}">Model properties</a></li>{/if}
    {if $can_edit_properties && $model._properties._count > 1}<li{if $method == "editItemClassPropertyOrder"} class="current"{/if}><a href="{$domain}datamanager/editItemClassPropertyOrder?class_id={$model.id}">Edit property order</a></li>{/if}
</ul>