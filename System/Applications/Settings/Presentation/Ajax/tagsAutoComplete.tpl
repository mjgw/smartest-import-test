<ul>
{foreach from=$tags item="tag"}
  <li data-id="{$tag.id}" data-slug="{$tag.slug}" data-label="{$tag.label|escape:quotes|trim}" id="tag_{$tag.id}">{$tag.label}</li>
{/foreach}
  {if $allow_create}<li data-id="new-tag" data-label="{$new_tag_label}" id="create-new-tag"><em>Create new tag: '{$new_tag_label}'</em></li>{/if}
</ul>