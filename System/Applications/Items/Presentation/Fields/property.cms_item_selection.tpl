{if empty($value)}
  Save item first, then you will be able to choose items.
{else}
  <span id="choose-items-property-{$property.id}-summary">{$value.summary_75}</span>
  <a href="#choose-items" class="button" id="choose-items-property-{$property.id}-button">Choose items</a>
  {if strlen($property.hint)}<span class="form-hint">{$property.hint}</span>{/if}
{/if}

<script type="text/javascript">
{literal}
(function(pid, iid){
  $('choose-items-property-'+pid+'-button').observe('click', function(evt){
    evt.stop();
    MODALS.load('datamanager/chooseItems?item_id='+iid+'&property_id='+pid, 'Choose items');
  });
}){/literal}({$property.id}, {$item.id});
</script>