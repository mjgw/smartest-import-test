<div id="work-area">
    
  {load_interface file="edit_filegroup_tabs.tpl"}
    
  <h3>Arrange file gallery contents: <span class="light">{$group.label}</span></h3>

{if count($assets)}

  <ul id="sortable-gallery-members" class="sortable-content">
  
{foreach from=$assets item="membership"}

    <li id="membership_{$membership.file.id}">
      
      <a href="#remove" style="position:absolute;top:5px;right:8px;font-size:2em;color:#999" id="membership-remove-{$membership.id}"><i class="fa fa-times-circle"></i></a>
      
      <p style="clear:both;margin:0 0 10px 0"><strong>File: {$membership.file.label}</strong> ({$membership.file.url}) <a href="{$domain}assets/editAsset?asset_id={$membership.file.id}"><i class="fa fa-pencil"></i></a> <a href="{$domain}assets/assetInfo?asset_id={$membership.file.id}"><i class="fa fa-info-circle"></i></a></p>
  
      <div class="asset-gallery-membership-file" style="float:left;width:150px">
        {if $membership.file.is_image}
          {$membership.file.image.constrain_150x150}
        {else}
          {if $membership.file.thumbnail_image.id}
          {$membership.file.thumbnail_image.image.constrain_180x150}
          {else}
          {$membership.file.icon_code}
          {/if}
        {/if}
      </div>
  
      <div class="asset-gallery-membership-file" style="float:right;width:400px">
        
        <div class="editable" id="membership-caption-{$membership.id}" style="min-height:1.3em">{$membership.caption}</div>
        
        <script type="text/javascript">
        
          new Ajax.InPlaceEditor('membership-caption-{$membership.id}', sm_domain+'ajax:assets/updateAssetGroupMembershipCaption', {ldelim}
            callback: function(form, value) {ldelim}
              return 'membership_id={$membership.id}&new_caption='+encodeURIComponent(value);
            {rdelim},
            highlightColor: '#ffffff',
            hoverClassName: 'editable-hover',
            savingClassName: 'editable-saving',
            rows: 4,
            cols: 50
            
          {rdelim});
          
          $('membership-remove-{$membership.id}').observe('click', function(event){ldelim}
            
            new Ajax.Request( sm_domain+'ajax:assets/deleteAssetGroupMembership', {ldelim}
              
              onSuccess: function(response) {ldelim}
                new Effect.Fade('membership_{$membership.file.id}');
              {rdelim},
              
              parameters: {ldelim}
                membership_id: {$membership.id}
              {rdelim}
              
            {rdelim});
            
            event.stop();
            
          {rdelim});
          
        </script>
      
      </div>
  
      <div class="breaker"></div>
  
    </li>

{/foreach}

  </ul>

<script type="text/javascript" src="/Resources/System/Javascript/scriptaculous/src/dragdrop.js"></script>
<script type="text/javascript">

var url = sm_domain+'ajax:assets/updateGalleryOrder';
var groupId = {$group.id};
{literal}
var IDs;
var IDs_string;

var itemsList = Sortable.create('sortable-gallery-members', {
      
      onUpdate: function(){
          
        IDs = Sortable.sequence('sortable-gallery-members');
        IDs_string = IDs.join(',');
        
        new Ajax.Request(url, {
          method: 'get',
          parameters: {group_id: groupId, new_order: IDs_string},
          onSuccess: function(transport) {
            
          }
        });
      },
      
//      handles:$$('#sortable-gallery-members li p a.handle'),
      
      constraint: false,
      scroll: window,
      scrollSensitivity: 35
      
  });
{/literal}
</script>
  
  {else}
  
  <div class="special-box">
    There are no files in this gallery yet. <a href="{$domain}assets/editAssetGroupContents?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="button">Add existing files</a> <a href="{$domain}smartest/file/new?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="button">Upload new files</a>
  </div>
  
  {/if}
  
</div>

<div id="actions-area">
  
  {if $request_parameters.from}
  <ul class="actions-list">
    <li><b>Workflow options</b></li>
    {if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}
    <li class="permanent-action"><a href="{$domain}datamanager/editItem?item_id={$workflow_item.id}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}"><i class="fa fa-check"></i> Return to editing {$workflow_item._model.name|lower}</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}
    <li class="permanent-action"><a href="#" onclick="cancelForm();"><i class="fa fa-check"></i> Return to page preview</a></li>
    {elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}
    <li class="permanent-action"><a href="{$domain}websitemanager/definePlaceholder?assetclass_id={$workflow_placeholder.name}&amp;page_id={$workflow_page.webid}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}"><i class="fa fa-check"></i> Return to placeholder</a></li>
    {/if}
  </ul>
  {/if}
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Group options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/file/new?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="right-nav-link"><i class="fa fa-plus-circle"></i> Upload a file into this group</a></li>
  	<li class="permanent-action"><a href="{$domain}assets/browseAssetGroup?group_id={$group.id}{if $workflow_item}&amp;item_id={$workflow_item.id}{/if}{if $workflow_page}&amp;page_id={$workflow_page.webid}{/if}{if $workflow_placeholder}&amp;placeholder_id={$workflow_placeholder.id}{/if}{if $workflow_type == 'SM_WORKFLOW_ITEM_EDIT'}&amp;from=editItem{elseif $workflow_type == 'SM_WORKFLOW_PAGE_PREVIEW'}&amp;from=pagePreview{elseif $workflow_type == 'SM_WORKFLOW_DEFINE_PLACEHOLDER'}&amp;from=definePlaceholder{/if}" class="right-nav-link"><i class="fa fa-search"></i> Browse this group</a></li>
  </ul>
  
</div>