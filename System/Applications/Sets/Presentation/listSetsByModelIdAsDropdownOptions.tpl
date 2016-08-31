{foreach from=$sets item="set"}
<option value="{$set.id}">{$set.label}</option>
{foreachelse}
<option value="ALL">All {$model.plural_name|lower}</option>
{/foreach}