<div id="work-area">

<h3><a href="{$domain}datamanager">Data Manager</a> > <a href="{$domain}modeltemplates/">Templates</a>  >  <a href="{$domain}modeltemplates/schemaDefinition?schema_id={$schema.schema_id}">{$schema.schema_name}</a> >  <a href="{$domain}modeltemplates/editSchemaVocabulary?schemadefinition_id={$vocabulary.schemadefinition_id}">{$vocabulary.vocabulary_name}</a> > Add  Attribute</h3>
<div class="instruction">Please enter the details.</div>

<form action="{$domain}{$section}/insertAttribute" method="post" id="pageViewForm">
<input type="hidden" name="vocabulary_id" value="{$vocabulary.vocabulary_id}" />
<input type="hidden" name="schema_id" value="{$schema.schema_id}" />
<input type="hidden" name="schemadefinition_id" value="{$vocabulary.schemadefinition_id}" />
<table style="width:750px;margin-left:10px;" border="0" cellspacing="2" cellpadding="1">   

<tr><td style="width:100px" valign="top">Attribute Name:</td>
<td><input type="text"  name="name"  /></td></tr>
<tr><td style="width:100px" valign="top">Value:</td>
<td><input type="text"  name="value"  /></td></tr>

<tr><td ></td>
<td>	<input type="submit" value="Add" />&nbsp;&nbsp;<input type="button" value="Cancel" onclick="window.location='{$domain}modeltemplates/schemaDefinition?schema_id={$schema.schema_id}';" />
</td>
</tr>
</table>
</form>

</div>