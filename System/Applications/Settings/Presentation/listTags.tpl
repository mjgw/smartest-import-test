<div id="work-area">
<h3>Tags</h3>

<div class="special-box"><a href="{$domain}smartest/settings" class="button"><i class="fa fa-chevron-circle-left"></i>Click here</a> to return to settings.</div>

<div class="instruction">Tags exist across all your sites. Some lags may not make sense for certain sites, but they can be ignored.</div>

{if count($tags)}

{foreach from=$tags item="tag" key="key"}
<a class="tag" href="{$domain}smartest/tagged/{$tag.name}" data-tag="{$tag.name}">{$tag.label}</a>
{/foreach}

<script type="text/javascript">
{literal}
$$('a.tag').each(function(tagLink){
  tagLink.observe('click', function(evt){
    evt.stop();
    MODALS.load('settings/getTaggedObjects?tag='+tagLink.readAttribute('data-tag'), 'Objects tagged with &lsquo;'+tagLink.innerHTML+'&rsquo;');
  });
});
{/literal}
</script>

{else}

<div class="special-box">It looks as though there aren't any tags yet. You can get started by <a href="{$domain}{$section}/addTag" class="button">adding a tag</a>.</div>

{/if}

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Tags Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTag'"><img src="{$domain}Resources/Icons/tag_blue.png" />Add Tag</a></li>    
  </ul>
</div>