<div id="work-area">
  
  {load_interface file="model_list_tabs.tpl"}
  
  <h3>Edit {$model.name|lower} model settings</h3>
  
  {if $can_edit_model}<form action="{$domain}{$section}/updateModel" method="post">{/if}
    
    <input type="hidden" name="class_id" value="{$model.id}" />
    
    <div class="edit-form-layout">
      
      <div class="edit-form-row">
        <div class="form-section-label">Model plural name</div>
        {if $allow_plural_name_edit}<input type="text" name="itemclass_plural_name" value="{$model.plural_name}" />{else}{$model.plural_name}{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Build-in 'name' field value</div>
        <div style="float:left">
          <select name="itemclass_name_origin">
            <option value="manual"{if $model.item_name_field_origin == 'manual'} selected="selected"{/if}>Manual input (default)</option>
            {if $name_origin_properties_available}<option value="derive"{if $model.item_name_field_origin == 'derive'} selected="selected"{/if}>Derive from other properties</option>{/if}
            <option value="method"{if $model.item_name_field_origin == 'method'} selected="selected"{/if}>Custom getName() method</option>
          </select>
          <div id="itemclass-name-derive-format-holder" style="display:{if $model.item_name_field_origin == 'derive'}block{else}none{/if}">
            <input type="text" name="itemclass_name_derive_format" value="{$model.item_name_field_derive_format}" />
            <br /><span class="form-hint">A combination of the following properties by name: {$name_origin_var_names_joined}</span>
          </div>
        </div>
        <div class="breaker"></div>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Build-in 'name' field title</div>
        {if $allow_infn_edit}<input type="text" name="itemclass_item_name_field_name" value="{$model.item_name_field_name}" /><div class="form-hint">Usually something like 'name', 'title', 'headline', 'label'</div>{else}{$model.item_name_field_name}{/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Shared on all sites</div>
        
        {if $can_edit_model}
        
        <input type="checkbox" name="itemclass_shared" id="itemclass-shared" value="1"{if $shared} checked="checked"{/if}{if !$allow_sharing_toggle} disabled="disabled"{/if} />
        
            {if $shared}
              {if $allow_sharing_toggle}
                <label for="itemclass-shared">Uncheck the box to make only this site able to store and display {$model.plural_name|lower}</label>
              {else}
                <span class="form-hint">
                {if $is_movable}
                  This model must be shared because it is already in use in more than one website.
                {else}
                  This model cannot be unshared because file permissions do not allow the model's class file to be moved.
                {/if}
                </span>
              {/if}
            {else}
              {if $allow_sharing_toggle}
                <label for="itemclass-shared">Check the box to make all sites able to store and display {$model.plural_name|lower}</label>
              {else}
                <span class="form-hint">
                  {if $is_movable}
                  This model cannot be shared because other models with conflicting or identical names exist on other sites.
                  {else}
                  This model cannot be shared because file permissions do not allow the model's class file to be moved.
                  {/if}
                </span>
              {/if}
            {/if}
            
            {if !$is_movable}
              <div class="warning">
                The following files or directories must be writable by the web server before you can {if $shared}unshare this model{else}share this model with other sites{/if}:<br />
                {foreach from=$unwritable_files item="unwritable_file"}
                <div><code>{$unwritable_file}</code></div>
                {/foreach}
              </div>
            {/if}
            
            {else}
            
            {if $shared}Yes{else}No{/if}
            
            {/if}
            
      </div>
      
      {if $allow_main_site_switch}
      <div class="edit-form-row" id="">
        <div class="form-section-label">Main site</div>
        {if $can_edit_model}
        <select name="itemclass_site_id">
          {foreach from=$sites item="s"}
          <option value="{$s.id}"{if $current_site_id_id==$s.id} selected="selected"{/if}>{$s.name}</option>
          {/foreach}
        </select><span class="form-hint">The model's main site is the one that can use it if the model is not shared.</span>
        {else}
          {foreach from=$sites item="s"}
            {if $current_site_id_id==$s.id}{$s.name}{/if}
          {/foreach}
        {/if}
      </div>
      {/if}
      
      {if count($metapages)}
      <div class="edit-form-row">
        {if !$model.default_metapage_id}<div class="warning">{/if}
        <div class="form-section-label">Default meta-page</div>
        {if $can_edit_model}
        <select name="itemclass_default_metapage_id">
          <option value="NONE">No default</option>
          {foreach from=$metapages item="page"}
          <option value="{$page.id}"{if $model.default_metapage_id == $page.id} selected="selected"{/if}>{$page.title}</option>
          {/foreach}
        </select>
        {if !$model.default_metapage_id}</div>{/if}
        {else}
        {foreach from=$metapages item="page"}
          {if $model.default_metapage_id == $page.id}{$page.title}{/if}
          {/foreach}
        {/if}
      </div>
      {/if}
      
      {if count($available_primary_properties)}
      <div class="edit-form-row">
          <div class="form-section-label">Primary property</div>
          {if $can_edit_model}
          <select name="itemclass_primary_property_id">
            <option value="NONE">None</option>
            {foreach from=$available_primary_properties item="property"}
            <option value="{$property.id}"{if $model.primary_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
            {/foreach}
          </select>
          {else}
          {if $model.primary_property_id}
          {foreach from=$available_primary_properties item="property"}
            {if $model.primary_property_id == $property.id}{$property.name}{/if}
          {/foreach}
          {else}
          None
          {/if}
          {/if}
        </div>
      {/if}
      
      <div class="edit-form-row">
        <div class="form-section-label">Default description property</div>
        {if $can_edit_model}
        <select name="itemclass_default_description_property_id">
          {if !$model.default_description_property_id}<option value="0"></option>{/if}
          {foreach from=$description_properties item="property"}
          <option value="{$property.id}"{if $model.default_description_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
          {/foreach}
        </select>
        {else}
        {if $model.default_description_property_id}
          {foreach from=$description_properties item="property"}
            {if $model.default_description_property_id == $property.id}{$property.name}{/if}
          {/foreach}
        {else}
          None
        {/if}
        {/if}
      </div>
      
      {if $model.type == 'SM_ITEMCLASS_MT1_SUB_MODEL' || $model.type == 'SM_ITEMCLASS_MTM_SUB_MODEL'}
      
      <script type="text/javascript">
      {literal}
      var toggleDefaultSortControl = function(value){
        
        if(value){
          Effect.BlindUp('sort-property-row', {duration: 0.3});
        }else{
          Effect.BlindDown('sort-property-row', {duration: 0.3});
        }
        
      }
      {/literal}  
      </script>
      
      <div class="edit-form-row">
        <div class="form-section-label">Manual sorting</div>{if $model.manual_order}{/if}
        {boolean name="itemclass_manual_ordering" id="itemclass-manual-ordering" value=$model.manual_order changehook="toggleDefaultSortControl"}
      </div>
      
      {/if}
      
      <div id="sort-property-row" style="display:{if $model.type == 'SM_ITEMCLASS_MODEL' || !$model.manual_order}block{else}none{/if}">
        <div class="edit-form-row">
          <div class="form-section-label">Default sort property</div>
          {if $can_edit_model}
          <select name="itemclass_default_sort_property_id">
            <option value="0">{$model.name} {$model.item_name_field_name}</option>
            {foreach from=$sort_properties item="property"}
            <option value="{$property.id}"{if $model.default_sort_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
            {/foreach}
          </select>
          {else}
          {if $model.default_sort_property_id}
            {foreach from=$sort_properties item="property"}
              {if $model.default_sort_property_id == $property.id}{$property.name}{/if}
            {/foreach}
          {else}
            None
          {/if}
          {/if}
        </div>
        
        <div class="edit-form-row">
          <div class="form-section-label">Default sort direction</div>
          {if $can_edit_model}
          <select name="itemclass_default_sort_direction">
            <option value="ASC"{if $model.default_sort_property_dir == 'ASC'} selected="selected"{/if}>Ascending</option>
            <option value="DESC"{if $model.default_sort_property_dir == 'DESC'} selected="selected"{/if}>Descending</option>
          </select>
          <div class="form-hint">This is used only when the default sort property above is used.</div>
          {else}
            {if $model.default_sort_property_dir == 'DESC'}Descending{else}Ascending{/if}
          {/if}
        </div>
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Default date property</div>
        {if $can_edit_model}
        <select name="itemclass_default_date_property_id">
          {if !$model.default_date_property_id}<option value="0"></option>{/if}
          {foreach from=$date_properties item="property"}
          <option value="{$property.id}"{if $model.default_date_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
          {/foreach}
        </select>
        {else}
        {if $model.default_date_property_id}
          {foreach from=$date_properties item="property"}
            {if $model.default_date_property_id == $property.id}{$property.name}{/if}
          {/foreach}
        {else}
          None
        {/if}
        {/if}
      </div>
      
      {if $model.default_date_property_id}
      <div class="edit-form-row">
        <div class="form-section-label">Enable scheduling</div>
        {if $can_edit_model}
        {boolean name="itemclass_publish_items_after_date" id="itemclass-auto-publish" value=$model.auto_publish}
        <div class="form-hint">Allow items to be scheduled when date specified in default date property passes</div>
        {else}
        
        {/if}
      </div>
      {/if}
      
      <div class="edit-form-row">
        <div class="form-section-label">Default thumbnail property</div>
        {if $can_edit_model}
        <select name="itemclass_default_thumbnail_property_id">
          <option value="0">None</option>
          {foreach from=$thumbnail_properties item="property"}
          <option value="{$property.id}"{if $model.default_thumbnail_property_id == $property.id} selected="selected"{/if}>{$property.name}</option>
          {/foreach}
        </select>
        {else}
        {if $model.default_thumbnail_property_id}
          {foreach from=$thumbnail_properties item="property"}
            {if $model.default_thumbnail_property_id == $property.id}{$property.name}{/if}
          {/foreach}
        {else}
          None
        {/if}
        {/if}
      </div>
      
      <div class="edit-form-row">
        <div class="form-section-label">Private model</div>
        {boolean name="itemclass_is_hidden" id="itemclass-is-hidden" value=$model.is_hidden}
      </div>
            
      <div class="edit-form-row">
        <div class="form-section-label">Long ID format for new items</div>
        {if $can_edit_model}
        <select name="itemclass_long_id_format" id="long-id-format-changer">
          <option{if $model.long_id_format == "_STD"} selected="selected"{/if} value="_STD">Standard 32-char mixed random</option>
          <option{if $model.long_id_format == "_UUID"} selected="selected"{/if} value="_UUID">UUID (ISO/IEC 9834-8:2012)</option>
          <option{if $model.long_id_format == "NNNNNNNNNNNNNNNN"} selected="selected"{/if} value="NNNNNNNNNNNNNNNN">16 digits</option>
          <option{if $model.long_id_format == "NNNNNNNN"} selected="selected"{/if} value="NNNNNNNN">8 digits</option>
          <option{if $model.long_id_format == "my-NNNNNNNNNNNN"} selected="selected"{/if} value="my-NNNNNNNNNNNN">MMYY-12 digits</option>
          <option{if $model.long_id_format == "my-NNNNNNNN"} selected="selected"{/if} value="my-NNNNNNNN">MMYY-8 digits</option>
          <option{if $model.long_id_format == "CCCCCC"} selected="selected"{/if} value="CCCCCC">Standard record locator (6 digits or uppercase letters)</option>
          <option{if $model.long_id_format_custom} selected="selected"{/if} value="_CUSTOM">Custom (advanced)</option>
        </select>
        <div class="edit-form-sub-row" id="long-id-custom-format" style="display:{if $model.long_id_format_custom}block{else}none{/if}">
          <input type="text" name="itemclass_long_id_custom_format" value="{if $model.long_id_format_custom}{$model.long_id_format}{/if}" />
        </div>
        <div class="form-hint">Does not affect items already created</div>
        <script type="text/javascript">
        {literal}
        $('long-id-format-changer').observe('change', function(){
            if($('long-id-format-changer').value == '_CUSTOM'){
                $('long-id-custom-format').show();
            }else{
                $('long-id-custom-format').hide();
            }
        });
        {/literal}
        </script>
        {else}
          {if $model.long_id_format == "_STD"}Standard 32-char mixed random{/if}
          {if $model.long_id_format == "_UUID"}UUID (ISO/IEC 9834-8:2012){/if}
          {if $model.long_id_format == "NNNNNNNNNNNNNNNN"}16 digits{/if}
          {if $model.long_id_format == "NNNNNNNN"}8 digits{/if}
          {if $model.long_id_format == "my-NNNNNNNNNNNN"}MMYY-12 digits{/if}
          {if $model.long_id_format == "my-NNNNNNNN"}MMYY-8 digits{/if}
          {if $model.long_id_format == "CCCCCC"}Standard record locator (6 digits or uppercase letters){/if}
          {if $model.long_id_format_custom}Custom{/if}
        {/if}
      </div>
      
      {* <div class="edit-form-row">
          <div class="form-section-label">Color</div>
          {if $can_edit_model}
          <input type="text" class="color" name="itemclass_color" value="{$model.color}" />
          {else}
          <span style="color:#{$model.color.hex};font-weight:bold">#{$model.color.hex}</span>
          {/if}
        </div> *}
      
        <div class="edit-form-row">
          <div class="form-section-label">Static sets to which new items should automatically be added</div>
          <div><span id="model-set-auto-add-ajax-field">{load_interface file="model_auto_sets_info.tpl"}</span> {if $can_edit_model}<a href="#edit-automatic-sets" id="edit-automatic-sets-link" class="button">Edit</a>{/if}</div>
          <script type="text/javascript">
            
            $('edit-automatic-sets-link').observe('click', function(e){ldelim}
                MODALS.load('{$section}/editModelAutomaticSets?model_id={$model.id}', 'Choose static sets');
                e.stop();
            {rdelim});

          </script>
        </div>
      
      {if $can_edit_model}<div class="edit-form-row">
        <div class="buttons-bar">
          {save_buttons}
        </div>
      </div>{/if}
      
    </div>
    
  {if $can_edit_model}</form>{/if}
  
</div>

<div id="actions-area">
    
    <ul class="actions-list" id="non-specific-actions">
      <li><b>Model Options</b></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="MODALS.load('datamanager/modelInfo?class_id={$model.id}', 'Model info');"><i class="fa fa-info-circle"></i> Model info</a></li>
      {if $allow_create_new}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addItem?class_id={$model.id}'"><i class="fa fa-plus-circle"></i> Add a new {$model.name}</a></li>{/if}
      {if $can_edit_properties}<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getItemClassProperties?class_id={$model.id}'"><i class="fa fa-sliders"></i> Edit model properties</a></li>{/if}
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/releaseUserHeldItems?class_id={$model.id}'"><i class="fa fa-unlock"></i> Release all {$model.plural_name}</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/addSet?class_id={$model.id}'"><i class="fa fa-plus-square-o"></i> Create a new set from this model</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}sets/getItemClassSets?class_id={$model.id}'"><i class="fa fa-folder-open"></i> View data sets for this model</a></li>
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}datamanager/exportModelKit?class_id={$model.id}'"><i class="fa fa-external-link"></i> Export this model as kit</a></li>
    {* <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/importData?class_id={$itemBaseValues.itemclass_id}';"><img border="0" src="{$domain}Resources/Icons/page_code.png" /> Import data from CSV</a></li> *}
    </ul>

{if count($recent_items)}
    <ul class="actions-list" id="non-specific-actions">
      <li><span style="color:#999">Recently edited {$model.plural_name|strtolower}</span></li>
      {foreach from=$recent_items item="recent_item"}
      <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$recent_item.action_url}'"><i class="fa fa-cube"></i> {$recent_item.label|summary:"28"}</a></li>
      {/foreach}
    </ul>
{/if}

</div>