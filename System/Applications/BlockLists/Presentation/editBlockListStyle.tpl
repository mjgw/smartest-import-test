<div id="work-area">
  <h3>Edit blocklist style</h3>
  <div class="edit-form-row">
    <div class="form-section-label">Style name</div>
    <p class="editable" id="style-label">{$style.label}</p>
    <script type="text/javascript">
    new Ajax.InPlaceEditor('style-label', sm_domain+'ajax:blocklists/setBlockListStyleLabelFromInPlaceEditField', {ldelim}
      callback: function(form, value) {ldelim}
        return 'style_id={$style.id}&new_label='+encodeURIComponent(value);
      {rdelim},
      highlightColor: '#ffffff',
      hoverClassName: 'editable-hover',
      savingClassName: 'editable-saving'
    {rdelim});
    </script>
  </div>
  
  <div class="form-section-label-full">Block types</div>
  <ul>
    {foreach from=$style_templates item="template"}
    <li><a href="{$domain}templates/editTemplate?template={$template.id}">{$template.label}</a></li>
    {foreachelse}
    <li><em>There are no block types for this style yet.</em></li>
    {/foreach}
  </ul>
  <a class="button" href="{$domain}templates/startSingleTemplateImport?type=SM_ASSETTYPE_BLOCKLIST_TEMPLATE&amp;style_id={$style.id}">Add block template</a>
  <div class="v-spacer"></div>
  
  <div class="form-section-label-full">Stylesheets</div>
  <a class="button" href="">Add stylesheet</a>
  <div class="v-spacer"></div>
  
  <div class="form-section-label-full">Scripts</div>
  <a class="button" href="">Add script</a>
  <div class="v-spacer"></div>
  
</div>

<div id="actions-area">
  
</div>