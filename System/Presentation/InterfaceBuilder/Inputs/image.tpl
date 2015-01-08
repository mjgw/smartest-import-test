<div style="float:left">
  <div id="{$_input_data.id}-thumbnail-area">
  {if $value && $value.id}
    <img src="{$value.image.constrain_400x400.web_path}" alt="{$value.label}" style="width:{$value.image.constrain_200x200.width};height:{$value.image.constrain_200x200.height}px" id="{$_input_data.id}-thumbnail">
    <div class="image-picker-caption">{$value.label} ({$value.url}), {$value.type_info.label}, {$value.image.width}x{$value.image.height}</div>
  {else}
    <div class="image-picker-caption">No file is selected</div>
  {/if}
  </div>
  <a class="button" href="#select-image" id="{$_input_data.id}-button">Select image</a>
  <input type="hidden" name="{$_input_data.name}" id="{$_input_data.id}" value="{if $value && $value.id}{$value.id}{/if}" />
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
  </script>
</div>
<div class="breaker"></div>