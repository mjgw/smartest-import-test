<div id="work-area">
  <h3>Edit gallery membership</h3>
  <form action="{$domain}assets/updateAssetGalleryMembership" method="post">
    <input type="hidden" name="membership_id" value="{$membership.id}" />
    <input type="hidden" name="group_id" value="{$gallery.id}" />
    <div id="edit-form-layout">
      <div class="edit-form-row">
        <div class="form-section-label">Gallery</div>
        {$gallery.label}
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">File</div>
        {$membership.asset.label} <a href="{$domain}assets/editAsset?asset_id={$membership.asset.id}" class="button small">Edit{if $membership.asset.type_info.editable}{else} parameters{/if}</a>
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">Thumbnail image</div>
{*        <select name="membership_thumbnail_image_id">
          <option value="0"{if !$membership.thumbnail_asset_id} selected="selected"{/if}>No thumbnail</option>
{foreach from=$thumbnails item="thumbnail_image"}
          <option value="{$thumbnail_image.id}"{if $thumbnail_image.id == $membership.thumbnail_asset_id} selected="selected"{/if}>{$thumbnail_image.label}</option>
{/foreach}
        </select> *}
        {image_select id="membership-thumbnail-gallery-id" name="membership_thumbnail_image_id" value=$membership.thumbnail_asset}
        <div class="form-hint">This file is an image that can represent files the context of this gallery ({$gallery.label}) specifically.</div>
      </div>
      <div class="edit-form-row">
        <div class="form-section-label">Caption</div>
        <textarea name="membership_caption" style="width:400px;height:50px">{$membership.caption}</textarea>
        <div class="form-hint">Choose a caption here to be used alongside this file in this gallery.</div>
      </div>
      <div class="buttons-bar">
        <input type="submit" value="Save" />
      </div>
    </div>
  </form>
</div>