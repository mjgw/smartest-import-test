{if empty($value)}
  Save item first, then you will be able to choose files.
{else}
  <span id="choose-assets-property-{$property.id}-summary">{$value.summary}</span>
  <a href="{$domain}ipv:{$section}/chooseFiles?item_id={$item.id}&amp;property_id={$property.id}" class="button" id="choose-items-property-{$property.id}-button">Choose files</a>
{/if}

{if strlen($property.hint)}&nbsp;<div class="form-hint">{$property.hint}</div>{/if}

<script type="text/javascript">

{literal}
(function(pid, iid){
  $('choose-items-property-'+pid+'-button').observe('click', function(evt){
    // evt.stop();
    // MODALS.load('datamanager/chooseItems?item_id='+iid+'&property_id='+pid, 'Choose files');
  });
}){/literal}({$property.id}, {$item.id});

</script>