<div id="work-area">
<h3>Tags</h3>

{* <div class="special-box"><a href="{$domain}smartest/settings" class="button"><i class="fa fa-chevron-circle-left"></i>Click here</a> to return to settings.</div> *}

<div class="instruction">Tags exist across all your sites. Some tags may not make sense for certain sites, but they can be ignored.</div>

{if count($tags)}

<ul class="checkbox-array-list" id="tags-list">
{foreach from=$tags item="tag" key="key"}
  <li data-tagid="{$tag.id}" data-taglabel="{$tag.label}"{if $tag.featured} class="featured"{/if}><label>{$tag.label}<a href="#tag-info" data-tag="{$tag.name}" class="tag-info tag-icon-button"><i class="fa fa-info-circle"></i></a>{if $allow_delete_tags} <a href="#tag-delete" data-tag="{$tag.name}" class="tag-delete tag-icon-button"><i class="fa fa-times"></i></a>{/if}{if $allow_edit_tags} <a href="{$domain}settings/editTag?tag_id={$tag.id}" data-tag="{$tag.name}" class="tag-edit tag-icon-button"><i class="fa fa-pencil"></i></a>{/if}<a href="#featured" class="tag-feature-toggle tag-icon-button"><i class="fa fa-star{if !$tag.featured}-o{/if}" ></i></a></label></li>
{/foreach}
</ul>

<script type="text/javascript">
{literal}
$$('#tags-list li label a.tag-info').each(function(tagLink){
  tagLink.observe('click', function(evt){
    // console.log(tagLink.up(1));
    evt.stop();
    var tagLabel = tagLink.up(1).readAttribute('data-taglabel');
    MODALS.load('settings/getTaggedObjects?tag='+tagLink.readAttribute('data-tag'), 'Objects tagged with &lsquo;'+tagLabel+'&rsquo;');
  });
});
{/literal}
</script>

<script type="text/javascript">
{literal}
$$('#tags-list li label a.tag-delete').each(function(tagLink){
  tagLink.observe('click', function(evt){
    // console.log(tagLink.up(1));
    evt.stop();
    
    var tagLabel = tagLink.up(1).readAttribute('data-taglabel');
    var tagID = tagLink.up(1).readAttribute('data-tagid');
    
    if(confirm('Really delete tag "'+tagLabel+'"?')){
      new Ajax.Request(sm_domain+'ajax:settings/deleteTagById', {
        
        parameters: 'tag_id='+tagID,
        onSuccess: function(response) {
          // hide tag
          /* li.fade({duration: 0.3, afterfinish: function(){li.remove()}});
          $('no-tags-notice').appear({duration: 0.3}); */
          tagLink.up(1).fade();
        }
      
      });
      
    }
    // MODALS.load('settings/getTaggedObjects?tag='+tagLink.readAttribute('data-tag'), 'Objects tagged with &lsquo;'+tagLabel+'&rsquo;');
  });
});

{/literal}
</script>

<script type="text/javascript">
{literal}
$$('#tags-list li label a.tag-feature-toggle').each(function(tagLink){
  tagLink.observe('click', function(evt){
    
    evt.stop();
    
    var tagLabel = tagLink.up(1).readAttribute('data-taglabel');
    var tagId = tagLink.up(1).readAttribute('data-tagid');
    
    if(tagLink.up(1).hasClassName('featured')){
      
      // is featured - remove featured status
      tagLink.up(1).removeClassName('featured');
      tagLink.down('i').removeClassName('fa-star');
      tagLink.down('i').addClassName('fa-star-o');
      new Ajax.Request(sm_domain+'ajax:settings/toggleTagFeatured', {
        
        parameters: 'featured=false&tag_id='+tagId,
        onSuccess: function(response) {
          
        }
      
      });
      
    }else{
      
      // is not featured
      tagLink.up(1).addClassName('featured');
      tagLink.down('i').removeClassName('fa-star-o');
      tagLink.down('i').addClassName('fa-star');
      
      new Ajax.Request(sm_domain+'ajax:settings/toggleTagFeatured', {
        
        parameters: 'featured=true&tag_id='+tagId,
        onSuccess: function(response) {
          
        }
      
      });
      
    }
    
    // MODALS.load('settings/getTaggedObjects?tag='+tagLink.readAttribute('data-tag'), 'Objects tagged with &lsquo;'+tagLabel+'&rsquo;');
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
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/addTag'"><i class="fa fa-tag"></i> Add tag</a></li>    
  </ul>
</div>