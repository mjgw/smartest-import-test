<div class="work-area">
{if $found_image}
<img src="{$image.constrain_1050x632.web_path}" alt="previewed image" style="width:{$image.constrain_525x316.width}px;height:{$image.constrain_525x316.height}px;display:block" />
{else}
<em>The image file you requested could not be found</em>
{/if}
</div>