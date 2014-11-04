<div id="work-area">

<h3 id="siteName">Edit Site Parameters</h3>

{* <div style="height:100px;border-radius:10px;-moz-border-radius:10px;background-color:#222;overflow:hidden">
  {foreach from=$site_images item="asset"}
  {$asset.image.height_33}
  {/foreach}
</div> *}

<form id="updateSiteDetails" name="updateSiteDetails" action="{$domain}{$section}/updateSiteDetails" method="POST" style="margin:0px" enctype="multipart/form-data">

<input type="hidden" name="site_id" value="{$site.id}">

<div id="edit-form-layout">

<div class="edit-form-row">
  <div class="form-section-label">Public title</div>
  <input type="text" name="site_name" value="{$site.name}"/><div class="form-hint">This will take effect on your pages the next time they are published</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Internal label</div>
  <input type="text" name="site_internal_label" value="{$site.internal_label}"/>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Page title format</div>
  <input type="text" name="site_title_format" value="{$site.title_format}" /><div class="form-hint">This will take effect on your pages the next time they are published</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Hostname</div>
  <input type="text" name="site_domain" value="{$site.domain}" /><div class="form-hint">Please be careful. The wrong value here will make your site temporarily inaccessible.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Temporarily disable site?</div>
  {boolean name="site_is_disabled" id="site-disabled" value=$site_disabled red="true"}
  <div class="form-hint">Temporarily takes your website offline and shows your 503 Not Available page, if you have one, or 'Site not enabled.' if you don't.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo</div>
  <select name="site_logo_image_asset_id">
    <option value="">None</option>
{foreach from=$logo_assets item="logo_asset"}
    <option value="{$logo_asset.id}"{if $site.logo_image_asset_id == $logo_asset.id} selected="selected"{/if}>{$logo_asset.label}</option>
{/foreach}
  </select><br />
</div>

<div class="edit-form-row">
  <div class="form-section-label"></div>
  <input type="file" name="site_logo" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Admin email</div>
  <input type="text" name="site_admin_email" value="{$site.admin_email}" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site ID</div>
  <code>{$site.unique_id}</code> {help id="desktop:install_ids" buttonize="true"}What&rsquo;s this?{/help}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site language</div>
  <select name="site_language">
  {foreach from=$_languages item="lang" key="langcode"}
    {if $langcode != "zxx"}<option value="{$langcode}"{if $site.language_code == $langcode} selected="selected"{/if}>{$lang.label}</option>{/if}
  {/foreach}
  </select>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Google Analytics Site ID</div>
  <input type="text" name="site_ga_id" value="{$site_ga_id}" />
  <div class="form-hint">Usually takes the form <em>UA-1234567-1</em></div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Responsive mode</div>
  <div style="float:left">
    <input type="checkbox" name="site_responsive_mode" value="1" id="site-responsive-mode" onchange="toggleFormAreaVisibilityBasedOnCheckbox('site-responsive-mode', 'site-responsive-options');"{if $site_responsive_mode} checked="checked"{/if} /> <label for="site-responsive-mode">Serve different resources to different platforms</label>
    <div id="site-responsive-options" style="display:{if $site_responsive_mode}block{else}none{/if}">
      <ul style="list-style-type:none;margin:0px;padding:0 0 0 18px">
        <li><input type="checkbox" name="site_responsive_distinguish_mobile" value="1" id="site-responsive-mode-mobile"{if $responsive_distinguish_mobiles} checked="checked"{/if} /> <label for="site-responsive-mode-mobile">Distinguish smartphones, iPod Touch, and other small mobile devices</label></li>
        <li><input type="checkbox" name="site_responsive_distinguish_tablet" value="1" id="site-responsive-mode-tablet"{if $responsive_distinguish_tablets} checked="checked"{/if} /> <label for="site-responsive-mode-tablet">Distinguish tablets and other larger mobile devices</label></li>
        <li><input type="checkbox" name="site_responsive_distinguish_oldpcs" value="1" id="site-responsive-mode-oldpcs"{if $responsive_distinguish_old_pcs} checked="checked"{/if} /> <label for="site-responsive-mode-oldpcs">Distinguish old or unsupported desktop browsers</label></li>
      </ul>
    </div>
  </div>
  <div class="breaker"></div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">EU cookie law compliance</div>
  {boolean name="site_eu_cookie_compliance" id="site-eu-cookie-compliance" value=$eu_cookie_compliance}
  <div class="form-hint">Provides easy compliance with EU Directive 2002/58/EC. Will take effect immediately</div>
</div>

<div class="breaker"></div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='{$domain}smartest'" />
  <input type="submit" name="action" value="Save Changes" />
</div>

</form>

</div>
 
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/users" class="right-nav-link"><img src="{$domain}Resources/Icons/user.png" border="0" alt=""> Users &amp; Permissions</a></li>
    <li class="permanent-action"><a href="{$domain}desktop/twitterSettings" class="right-nav-link"><img src="{$domain}Resources/Icons/cog.png" border="0" alt=""> Twitter settings</a></li>
  </ul>
</div>