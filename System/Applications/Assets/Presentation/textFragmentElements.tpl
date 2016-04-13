<div id="work-area">
    
  {load_interface file="edit_asset_tabs.tpl"}
  <h3>Attachments</h3>
{if count($attachments)}  
  <div class="special-box">The following attachment tags were recognized in this text:</div>
  
  {foreach from=$attachments item="attachment"}
  <div style="padding:8px;background-color:#{cycle values="ddd,fff"};min-height:100px">
    
    {if $attachment.asset.id && $attachment.asset.is_binary_image}<img src="{$attachment.asset.image.constrain_400x200.web_path}" alt="" style="float:right;max-width:200px;max-height:100px" />{/if}
    
    <strong>Name in text:</strong>&nbsp;<code>{$attachment.name}</code><br />
    <strong>Attached file:</strong>&nbsp;{if $attachment.asset.id}<code>{$attachment.asset.url}</code>{else}<em style="color:#999">None yet</em>{/if}<br />
    {if $attachment.allow_resize && $attachment.asset.is_binary_image}<strong>Resizing: </strong>{$attachment.thumbnail_relative_size}% ({$attachment.thumbnail.width}x{$attachment.thumbnail.height} pixels)<br />{/if}
    <strong>Caption:</strong>&nbsp;{$attachment.caption}<br />
    <strong>Align:</strong>&nbsp;{$attachment.alignment}
    
    <div style="margin-top:10px">
      <a href="{$domain}{$section}/defineAttachment?attachment={$attachment.name}&amp;asset_id={$asset.id}{if $request_parameters.item_id}&amp;item_id={$request_parameters.item_id}{/if}{if $request_parameters.from}&amp;from={$request_parameters.from}{/if}{if $request_parameters.page_id}&amp;page_id={$request_parameters.page_id}{/if}{if $request_parameters.author_id}&amp;author_id={$request_parameters.author_id}{/if}{if $request_parameters.search_query}&amp;search_query={$request_parameters.search_query}{/if}{if $request_parameters.tag}&amp;tag={$request_parameters.tag}{/if}" class="button">{if $attachment.asset.id}Edit...{else}Attach file...{/if}</a>
    </div>
    
  </div>
  {/foreach}
{else}
    <div class="special-box">There are no attachment tags in this text yet. <a href="{$domain}{$section}/editTextFragmentSource?asset_id={$asset.id}" class="button">Click here</a> to add some.</div>
{/if}

<div class="buttons-bar"><input type="button" id="done-button" value="{$_l10n_global_strings.system_wide_buttons.done}" /><script type="text/javascript">{literal}$('done-button').observe('click', cancelForm);{/literal}</script></div>

</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Options</b></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/previewAsset?asset_id={$asset.id}'"><img src="{$domain}Resources/Icons/page_lightning.png" alt=""/> Preview this file</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/getAssetTypeMembers?asset_type={$asset_type.id}'"><img src="{$domain}Resources/Icons/folder_old.png" alt=""/> View all {$asset_type.label} files</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editAsset?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/pencil.png" alt=""/> Edit in rich-text editor</a></li>
    <li class="permanent-action"><a href="{dud_link}" onclick="window.location='{$domain}{$section}/editTextFragmentSource?assettype_code={$asset_type.id}&amp;asset_id={$asset.id}{if $smarty.get.from}&amp;from={$smarty.get.from}{/if}'"><img src="{$domain}Resources/Icons/page_edit.png" alt=""/> Edit file source</a></li>
  </ul>
</div>