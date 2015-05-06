Automatic property - {$value._count} object{if $value._count != 1}s{/if} retrieved: {$value} {if $value._count > 0}<a href="#more" id="auto-fk-show-items-{$property.id}" class="button small">See items</a>{/if}

{if $value._count > 0}
<div class="breaker"></div>
<div id="auto-fk-ipv-items-{$property.id}-container" style="display:none">
  <ul class="auto-fk-ipv-items" id="auto-fk-ipv-items-{$property.id}">
    {foreach from=$value item="foreign_key_item"}
      <li><i class="fa fa-cube"></i> {$foreign_key_item.name} <a class="button small" href="#{$foreign_key_item.slug}" data-itemid="{$foreign_key_item.id}" data-url="{$foreign_key_item.action_url}">Edit</a></li>  
    {/foreach}
  </ul>
</div>

<script>
(function(propertyId){ldelim}
{literal}

var listLinkSelector = '#auto-fk-ipv-items-'+propertyId+' li a';
var moreLinkId = 'auto-fk-show-items-'+propertyId;

$(moreLinkId).observe('click', function(e){
  e.stop();
  Effect.BlindDown('auto-fk-ipv-items-'+propertyId+'-container', {duration:0.3});
  Effect.Fade(moreLinkId, {duration:0.3});
});

$$(listLinkSelector).each(function(a){
  a.observe('click', function(e){
    e.stop();
    window.location = a.readAttribute('data-url');
  });
});

{/literal}
{rdelim})('{$property.id}');
</script>
{/if}