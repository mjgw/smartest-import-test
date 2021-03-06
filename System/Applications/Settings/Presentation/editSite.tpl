<div id="work-area">

<h3>Edit site settings</h3>

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
  <div class="form-section-label">Organization name</div>
  <input type="text" name="site_organisation_name" value="{$site_organisation}"/><div class="form-hint">Optional. Used for credits and attribution. This is the name of the entity your site is about, rather than the name of the site.</div>
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
  <div class="form-section-label">Admin email</div>
  {email_input name="site_admin_email" value=$site.admin_email id="site-admin-id"}
</div>

<div class="edit-form-row">
  <div class="form-section-label">Temporarily disable site?</div>
  {boolean name="site_is_disabled" id="site-disabled" value=$site_disabled red="true"}
  <div class="form-hint">Temporarily takes your website offline and shows your 503 Not Available page, if you have one, or 'Site not enabled.' if you don't.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Search type</div>
  <select name="site_search_type">
    <option value="BASIC"{if $search_type == 'BASIC' || !$allow_elastic_search} selected="selected"{/if}>Basic</option>
    <option value="ELASTICSEARCH"{if $search_type == 'ELASTICSEARCH'} selected="selected"{/if}{if !$allow_elastic_search} disabled="disabled"{/if}>Advanced</option>
  </select>
  <div class="form-hint">Basic search has no additional requirements; Advanced search requires ElasticSearch (PHP 5.6 and Java 8)</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Logo</div>
  <select name="site_logo_image_asset_id">
    <option value="">None</option>
{foreach from=$logo_assets item="logo_asset"}
    <option value="{$logo_asset.id}"{if $site.logo_image_asset_id == $logo_asset.id} selected="selected"{/if}>{$logo_asset.label}</option>
{/foreach}
  </select><br />
  <div class="form-section-label"></div>
  <input type="file" name="site_logo" />
</div>

<div class="edit-form-row">
  <div class="form-section-label">Favourite Icon</div>
  <select name="site_favicon_asset_id">
    <option value="">None</option>
{foreach from=$favicon_assets item="favicon_asset"}
    <option value="{$favicon_asset.id}"{if $site.favicon_id == $favicon_asset.id} selected="selected"{/if}>{$favicon_asset.label}</option>
{/foreach}
  </select>
  <div class="breaker"></div>
  <div class="form-hint">The favourite icon (or favicon) is a small icon that accompanies your site in people's web browsers and some search results.</div>
  <div class="form-hint warning-color" style="display:none" id="favicon-type-warning">The file you have selected is not the correct file type. The file must be in .ico format.</div>
  <div class="form-section-label"></div>
  <input type="file" name="site_favicon" id="site-favicon" />
  
  
  
  <script type="text/javascript">
  {literal}
  var suffixRegex = /\.ico/;
  
  $('site-favicon').observe('change', function(){
    if(suffixRegex.match($('site-favicon').value)){
      if($('favicon-type-warning').visible()){
        $('favicon-type-warning').fade({duration: 0.3});
      }
      if(!$('save-button').visible()){
        $('save-button').appear({duration: 0.3});
      }
    }else{
      $('favicon-type-warning').appear({duration: 0.3});
      $('save-button').fade({duration: 0.3});
    }
  });
  {/literal}
  </script>
  
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site ID</div>
  <code>{$site.unique_id}</code> <a href="http://{$site.domain}/smartest/heartbeat?site_code={$site.unique_id}" target="_blank" class="button small">Verify</a> {help id="desktop:install_ids" buttonize="true"}What&rsquo;s this?{/help}
  <div class="form-hint">This code allows Smartest installations to verify each other in a decentralised, user-controlled fashion. Give this code to people to enable them to request to syndicate your content.</div>
</div>

<div class="edit-form-row">
  <div class="form-section-label">Site language</div>
  <select name="site_language">
  {foreach from=$_languages item="lang" key="langcode"}
    {if $langcode != "zxx"}<option value="{$langcode}"{if $site.language_code == $langcode} selected="selected"{/if}>{$lang.label}</option>{/if}
  {/foreach}
  </select>
</div>

<div class="breaker"></div>

<div class="buttons-bar">
  <input type="button" value="Cancel" onclick="window.location='{$domain}smartest/settings'" />
  <input type="submit" name="action" value="Save Changes" id="save-button" />
</div>

</form>

</div>
 
</div>

<div id="actions-area">
  <ul class="actions-list" id="non-specific-actions">
    <li><b>Site Options</b></li>
    <li class="permanent-action"><a href="{$domain}smartest/users" class="right-nav-link"><i class="fa fa-users"></i> Users &amp; permissions</a></li>
  </ul>
</div>