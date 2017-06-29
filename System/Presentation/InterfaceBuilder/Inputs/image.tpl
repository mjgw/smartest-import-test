<div style="float:left">
  <div id="{$_input_data.id}-thumbnail-area">
  {if $value && $value.id}
    {if $_input_data.for == "user_profile_pic"}
    <div id="user-profile-form-profile-pic-holder" style="background-size:200px 200px;background-image:url({$value.image.400x400.web_path})"></div>
    {else}
    <img src="{$value.image.constrain_400x400.web_path}" alt="{$value.label}" style="width:{$value.image.constrain_200x200.width};height:{$value.image.constrain_200x200.height}px" id="{$_input_data.id}-thumbnail">
    {/if}
    <div class="image-picker-caption">{$value.label} ({$value.url}), {$value.type_info.label}, {$value.image.width}x{$value.image.height}</div>
  {else}
    <div class="image-picker-caption">No file is selected</div>
  {/if}
  </div>
  {if $_site.id}
  <a class="button" href="#select-image" id="{$_input_data.id}-button">Select image</a>
  <a class="button" href="#select-image" id="{$_input_data.id}-edit-metadata" style="{if $value && $value.id}{else}display:none{/if}">Edit image data</a>
  <a class="button" href="#clear-image" id="{$_input_data.id}-button-clear" style="{if $value && $value.id}{else}display:none{/if}">Clear</a>
  {else}
  <div class="image-picker-caption">Images can only be selected when you are working with a site.</div>
  {/if}
  <input type="hidden" name="{$_input_data.name}" id="{$_input_data.id}" value="{if $value && $value.id}{$value.id}{/if}" />
  
  {if $_site.id}
  <script type="text/javascript">
  {literal}
  if(typeof window.inputId == 'undefined'){
    var inputId;
  }
  {/literal}
  $('{$_input_data.id}-button').observe('click', function(e){ldelim}
    e.stop();
    MODALS.load('assets/miniImageBrowser?{if $_input_data.for}for={$_input_data.for}{if $_input_data.for == "ipv" && $_input_data.property_id}&property_id={$_input_data.property_id}{/if}{if $_input_data.for == "ipv" && $_input_data.item_id}&item_id={$_input_data.item_id}{/if}{if $_input_data.for == "placeholder" && $_input_data.placeholder_id}&placeholder_id={$_input_data.placeholder_id}{/if}{if $_input_data.for == "user_profile_pic" && $_input_data.user_id}&user_id={$_input_data.user_id}{/if}{/if}&input_id={$_input_data.id}&current_selection_id='+$F('{$_input_data.id}'), 'Image browser');
    {rdelim});
    
  $('{$_input_data.id}-button-clear').observe('click', function(e){ldelim}
    e.stop();
    // MODALS.load('assets/miniImageBrowser?{if $_input_data.for}for={$_input_data.for}{if $_input_data.for == "ipv" && $_input_data.property_id}&property_id={$_input_data.property_id}{/if}{if $_input_data.for == "ipv" && $_input_data.item_id}&item_id={$_input_data.item_id}{/if}{if $_input_data.for == "placeholder" && $_input_data.placeholder_id}&placeholder_id={$_input_data.placeholder_id}{/if}{if $_input_data.for == "user_profile_pic" && $_input_data.user_id}&user_id={$_input_data.user_id}{/if}{/if}&input_id={$_input_data.id}&current_selection_id='+$F('{$_input_data.id}'), 'Image browser');
    $('{$_input_data.id}').value = '';
    $('{$_input_data.id}-thumbnail-area').update('<div class="image-picker-caption">No file is selected</div>');
    $('{$_input_data.id}-edit-metadata').fade({ldelim}duration: 0.2{rdelim});
    $('{$_input_data.id}-button-clear').fade({ldelim}duration: 0.2{rdelim});
    $('{$_input_data.id}').fire('image:chosen', {ldelim}inputId: '{$_input_data.id}'{rdelim});
    {rdelim});
    
    $('{$_input_data.id}-edit-metadata').observe{literal}('click', function(){{/literal}
      MODALS.load('assets/editFileParametersModal?asset_id='+$F('{$_input_data.id}'), 'Edit image info');
    {literal}});{/literal}
    
  </script>
  {/if}
</div>

<div class="breaker"></div>

{if $_site.id}
<script type="text/javascript">
  $('{$_input_data.id}').observe('image:chosen', function(e){ldelim}
  
{if $_input_data.change_hook}{$_input_data.change_hook}$F('{$_input_data.id}'), '{$_input_data.id}');{/if}
{if $_input_data.change_js}{$_input_data.change_js}{/if}

  {rdelim});
</script>
{/if}