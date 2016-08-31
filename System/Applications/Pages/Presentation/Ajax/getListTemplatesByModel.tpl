{foreach from=$templates item="template"}
<option value="{$ct.url}">{$template.url}</option>
{/foreach}
{if $can_create_template}<option value="NEW">Create new template...</option>{/if}