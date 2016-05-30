<div id="work-area">

<h3 id="siteName">Edit CMS settings</h3>

<script type="text/javascript">
{literal}
var setEUCookieMode = function(state){
  if(state){
    Effect.BlindDown('eu-override-switch', {duration: 0.4});
  }else{
    Effect.BlindUp('eu-override-switch', {duration: 0.4});
  }
}
{/literal}
</script>

<form id="updateSiteDetails" action="{$domain}{$section}/updateCmsSettings" method="POST" enctype="multipart/form-data">

  <input type="hidden" name="site_id" value="{$site.id}">
  
  <div id="edit-form-layout">
    
    <div class="form-section-label-full">Backend preferences</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Default URL suffix for new pages</div>
      <select name="site_default_url_suffix" id="default-page-suffix-changer">
        <option{if $site_pageurl_default_suffix == "html"} selected="selected"{/if} value="html">.html</option>
        <option{if $site_pageurl_default_suffix == "php"} selected="selected"{/if} value="php">.php</option>
        <option{if $site_pageurl_default_suffix == "shtml"} selected="selected"{/if} value="shtml">.shtml</option>
        <option{if $site_pageurl_default_suffix == "_NONE"} selected="selected"{/if} value="_NONE">No suffix</option>
        <option{if $site_pageurl_default_suffix_custom} selected="selected"{/if} value="_CUSTOM">Custom (advanced)</option>
      </select>
      <input type="text" name="site_default_url_suffix_custom" value="{if $site_pageurl_default_suffix_custom}{$site_pageurl_default_suffix}{/if}" id="default-page-suffix-custom" style="display:{if $site_pageurl_default_suffix_custom}inline{else}none{/if}" />
      <div class="form-hint">Does not affect pages already created</div>
      <script type="text/javascript">
      {literal}
      $('default-page-suffix-changer').observe('change', function(){
          if($('default-page-suffix-changer').value == '_CUSTOM'){
              $('default-page-suffix-custom').show();
          }else{
              $('default-page-suffix-custom').hide();
          }
      });
      {/literal}
      </script>
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">Default text placeholder</div>
      <select name="site_default_text_placeholder_id">
        <option value="NONE">None</option>
{foreach from=$text_placeholders item="placeholder"}
        <option value="{$placeholder.id}"{if $placeholder.id == $primary_text_placeholder_id} selected="selected"{/if}>{$placeholder.label}</option>
{/foreach}
      </select>
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Default container for page layout</div>
      <select name="site_default_container_id">
        <option value="NONE">None</option>
{foreach from=$containers item="container"}
        <option value="{$container.id}"{if $container.id == $primary_container_id} selected="selected"{/if}>{$container.label}</option>
{/foreach}
      </select>
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">Default page preset when creating new pages</div>
      <select name="site_default_page_preset_id">
        <option value="NONE">None</option>
    {foreach from="$page_presets" item="preset"}
        <option value="{$preset.id}"{if $preset.id == $default_page_preset_id} selected="selected"{/if}>{$preset.name}</option>
    {/foreach}    
      </select>
    </div>
    
    <div class="form-section-label-full">Frontend options</div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Enable OEmbed?</div>
      {boolean name="site_oembed_enabled" id="site-oembed-enabled" value=$oembed_enabled}
      <div class="form-hint">OEmbed allows small previews to be built for pages on your site and embedded on other web pages</div>
      {* boolean name="site_oembed_enabled" id="site-oembed-enabled" value=$oembed_enabled changehook="setEUCookieMode" *}
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
      {boolean name="site_eu_cookie_compliance" id="site-eu-cookie-compliance" value=$eu_cookie_compliance changehook="setEUCookieMode"}
      <div class="form-hint">Provides easy compliance with EU Directive 2002/58/EC. Will take effect immediately {help id="desktop:eu_cookie_mode" buttonize="true"}What&rsquo;s this?{/help}</div> 
    </div>
    
    <div style="display:{if $eu_cookie_compliance}block{else}none{/if}" id="eu-override-switch">
      <div class="edit-form-row indent">
        <div class="form-section-label">Override for Google Analytics cookies</div>
        {boolean name="site_override_eu_cookie_compliance_ga" id="site-eu-cookie-compliance-override" value=$override_eu_cookie_compliance_ga}
        <div class="form-hint">If switched off, Google Analytics will only measure site visitors who give cookie permission, which adversely affects its accuracy</div>
      </div>
    </div>
    
    <div class="breaker"></div>

    <div class="buttons-bar">
      <input type="button" value="Cancel" onclick="window.location='{$domain}smartest/settings'" />
      <input type="submit" name="action" value="Save Changes" />
    </div>
    
  </div>
  
</form>

</div>