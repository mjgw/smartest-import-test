{day_input name=$_input_data.name value=$value id=$_input_data.property_id property=$property}

{if $property.id == $item.model.default_date_property_id && $_user.can_publish_items}
<div class="edit-form-sub-row" style="margin:8px 0;display:{if ($item.public == 'FALSE' || $item.public == 'SCHED') && $value.unix > $now.unix}block{else}none{/if}" id="item-scheduled-{$item.id}-{$property.id}" data-status="{$item.public}">
  <input type="checkbox" name="item_publish_scheduled" value="1"{if $item.public == 'SCHED'} checked="checked"{/if} id="item-scheduled-checkbox-{$item.id}-{$property.id}" />
  <label class="hint" for="item-scheduled-checkbox-{$item.id}-{$property.id}">Schedule this {$item.model.name|lower} to be published on this date</label>
</div>

<script type="text/javascript">
  $('item_property_{$property.id}-datefields').observe('date:changed', function(e){ldelim}
    var now = {$now.unix}*1000;
    var id = 'item-scheduled-{$item.id}-{$property.id}';
    {literal}
    var u = new Date(e.memo.date.year+'.'+e.memo.date.month+'.'+e.memo.date.day).getTime();
    if(u > now && $(id).readAttribute('data-status') != 'TRUE'){
      if(!$(id).visible()){
        Effect.BlindDown(id, {duration: 0.3});
      }
    }else{
      if($(id).visible()){
        Effect.BlindUp(id, {duration: 0.3});
      }
    }
    {/literal}
  {rdelim});
</script>
{/if}

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}