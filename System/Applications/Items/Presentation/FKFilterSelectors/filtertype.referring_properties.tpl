<div class="edit-form-row">

  <div class="form-section-label">Choose a referring property:</div>
  
  {if count($foreign_key_filter_options)}
  <select name="foreign_key_filter">
    {foreach from=$foreign_key_filter_options item="option"}
    <option value="{$option.id}">{$option._model.plural_name} / {$option.name}</option>
    {/foreach}
  </select>
  {else}
  <span class="null-notice">There are no properties that refer to this model</span>
  <script type="text/javascript">
    {literal}
    document.observe('dom:loaded', function(){
      $('save-button').disabled = true;
      $('itemproperty_name').disabled = true;
    });
    {/literal}
  </script>
  {/if}

</div>