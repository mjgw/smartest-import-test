<div id="work-area">
  
  <div class="special-box"><strong>Name field</strong>: <code style="font-size:1.2em">${$model.name|varname}.{$model.item_name_field_varname}</code> <em>or on meta-page</em> <code style="font-size:1.2em">$this.{$model.name|varname}.{$model.item_name_field_varname}</code></div>
  
    {foreach from=$properties item="property"}
    <div class="special-box"><strong>{$property.name}</strong>: <code style="font-size:1.2em">${$model.name|varname}.{$property.varname}</code> <em>or on meta-page</em> <code style="font-size:1.2em">$this.{$model.name|varname}.{$property.varname}</code> (<a href="http://wiki.smartestproject.org/Class:{$property.data_object_class}">{$property.data_object_class}</a> object)</div>
    {/foreach}
  
</div>