{if $found_asset}
  <img src="{$asset.image.constrain_400x400.web_path}" alt="{$asset.label}" style="width:{$asset.image.constrain_200x200.width};height:{$asset.image.constrain_200x200.height}px" id="{$input_id}-thumbnail">
  <div class="image-picker-caption">{$asset.label} ({$asset.url}), {$asset.type_info.label}, {$asset.image.width}x{$asset.image.height}</div>
{else}
  <div class="image-picker-caption">No file is selected</div>
{/if}