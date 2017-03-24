<div id="work-area">
    
  {load_interface file="edit_filegroup_tabs.tpl"}
    
  <h3>Arrange file gallery contents: <span class="light">{$group.label}</span></h3>

{if count($assets)}

  <ul id="sortable-gallery-members" style="margin:0px;padding:0px;list-style-type:none">
  
{foreach from=$assets item="membership"}

    <li style="padding:10px;border:1px solid #ccc;border-radius:6px;margin-bottom:8px;background-color:#fff;cursor:move;position:relative" id="membership_{$membership.file.id}">
      
      <a href="#remove" style="position:absolute;top:10px;right:10px;font-size:2em;color:#999" id="membership-remove-{$membership.id}"><i class="fa fa-times-circle"></i></a>
      
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
    There are no files in this gallery yet. <a href="{$domain}assets/editAssetGroupContents?group_id={$group.id}" class="button">Add files</a>
  </div>
  
  {/if}
  
</div>

<div id="actions-area">
  
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Gallery options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}smartest/file/new?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/add.png" border="0" alt="" /> Upload a file into this gallery</a></li>
  	<li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}assets/browseAssetGroup?group_id={$group.id}'" class="right-nav-link"><img src="{$domain}Resources/Icons/folder_magnify.png" border="0" alt="" style="width:16px;height:16px" /> Browse this group</a></li>
  </ul>
  
  {if $request_parameters.from}
  <ul class="actions-list">
    <li><b>Workflow options</b></li>
    {if $request_parameters.item_id}
    <li class="permanent-action"><a href="#" onclick="window.location='{$domain}datamanager/editItem?item_id={$request_parameters.item_id}'"><img border="0" src="{$domain}Resources/Icons/tick.png"> Return to editing item</a></li>
    {/if}
    {if $request_parameters.from == 'pagePreview'}
    <li class="permanent-action"><a href="#" onclick="cancelForm();"><img border="0" src="{$domain}Resources/Icons/tick.png"> Return to page preview</a></li>
    {/if}
  </ul>
  {/if}
  
</div>