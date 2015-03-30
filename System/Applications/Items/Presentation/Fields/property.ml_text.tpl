<textarea name="item[{$property.id}]" rows="3" cols="20" style="width:350px;height:80px" id="textarea-itemproperty-{$property.id}">{$value}</textarea>

{if strlen($property.hint)}<div class="form-hint">{$property.hint}</div>{/if}

<div class="form-hint"><span id="textarea-itemproperty-{$property.id}-charcount">{$value.charcount}</span> characters, <span id="textarea-itemproperty-{$property.id}-wordcount">{$value.wordcount}</span> words, <span id="textarea-itemproperty-{$property.id}-paracount">{$value.paracount}</span> paragraphs</div>

<script type="text/javascript">
$('textarea-itemproperty-{$property.id}').observe('keyup', function(){ldelim}
var theRootId = 'textarea-itemproperty-{$property.id}';
{literal}
  $(theRootId+'-wordcount').update($F(theRootId).split(/[\n\s]+/).size());
  $(theRootId+'-charcount').update($F(theRootId).length);
  $(theRootId+'-paracount').update($F(theRootId).split(/[\r\n]+/).filter(function(v){return v.match(/[\w]+/)}).size());
{/literal}
{rdelim});  
</script>