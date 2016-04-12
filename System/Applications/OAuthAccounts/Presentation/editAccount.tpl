<div id="work-area">
  <h3>Edit OAuth Client Account</h3>
  <form action="{$domain}oauth/updateAccount" method="post" id="">
    
    <input type="hidden" name="oauth_account_id" value="{$account.id}" />
    
    <div class="edit-form-row">
      <div class="form-section-label">Label</div>
      <input type="text" value="{$account.label}" name="oauth_service_label" />
    </div>
    
    <div class="edit-form-row">
      <div class="form-section-label">Service</div>
      {$account.service.label} (OAuth {$account.service.oauth_version})
    </div>
    
    {if strlen($account.info.username)}
    <div class="edit-form-row">
      <div class="form-section-label">User account</div>
      {$account.info.username}
    </div>
    {/if}
    
    {if empty($account.oauth_consumer_token) || empty($account.oauth_consumer_secret)}
    <div class="special-box">
      <p>To connect Smartest to {$account.service.label}, you will need a {if $account.service.oauth_version == "1.0"}consumer token{else}client ID{/if} and {if $account.service.oauth_version == "1.0"}consumer secret{else}client secret{/if}. You can either register this site as an "App" or "Client" (if you have not already done so) or use the default settings.</p>
      <div><a href="#use-defaults" class="button" id="use-default-oauth-client-id-button">Use default dettings</a> <a href="{$account.service.client_register_url}" target="_blank" class="button">Register custom app</a></div>
    </div>
    <script type="text/javascript">
    var platformID = '{$account.service.id}';
    {literal}
    $('use-default-oauth-client-id-button').observe('click', function(e){
      e.stop();
      $('primary-ajax-loader').show();
      new Ajax.Request(sm_domain+'ajax:oauth/getPlatformClientDefaults', {
        parameters: 'platform_id='+platformID,
        onSuccess: function(response){
          $('oauth-consumer-token').type = 'hidden';
          $('oauth-consumer-secret').type = 'hidden';
          $('oauth-consumer-token').value = response.responseJSON.ID;
          $('oauth-consumer-secret').value = response.responseJSON.secret;
          $('oauth-consumer-token-default-label').show();
          $('oauth-consumer-secret-default-label').show();
          $('primary-ajax-loader').hide();
        }
      });
    });
    {/literal}
    </script>
    {/if}
    
    <div class="edit-form-row">
      <div class="form-section-label">{if $account.service.oauth_version == "1.0"}Consumer Token{else}Client ID{/if}</div>
      {if ($account.service.oauth_version == "1.0" && strlen($account.service.default_consumer_key) && $account.oauth_consumer_token == $account.service.default_consumer_key) || ($account.service.oauth_version == "2.0" && strlen($account.service.default_client_id) && $account.oauth_consumer_token == $account.service.default_client_id)}
      <input type="hidden" name="oauth_consumer_token" value="{$account.oauth_consumer_token}" id="oauth-consumer-token"/>
      <span class="ui-info-inactive" id="oauth-consumer-token-default-label">Default {if $account.service.oauth_version == "1.0"}consumer token{else}client ID{/if} for Smartest on {$account.service.label}</span>
      {else}
      <input type="text" name="oauth_consumer_token" value="{$account.oauth_consumer_token}" id="oauth-consumer-token"/>
      <span class="ui-info-inactive" style="display:none" id="oauth-consumer-token-default-label">Default {if $account.service.oauth_version == "1.0"}consumer token{else}client ID{/if} for Smartest on {$account.service.label}</span>
      {/if}
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">{if $account.service.oauth_version == "1.0"}Consumer Secret{else}Client Secret{/if}</div>
      {if ($account.service.oauth_version == "1.0" && strlen($account.service.default_consumer_secret) && $account.oauth_consumer_secret == $account.service.default_consumer_secret) || ($account.service.oauth_version == "2.0" && strlen($account.service.default_client_secret) && $account.oauth_consumer_secret == $account.service.default_client_secret)}
      <input type="hidden" name="oauth_consumer_secret" value="{$account.oauth_consumer_secret}" id="oauth-consumer-secret"/>
      <span class="ui-info-inactive" id="oauth-consumer-token-default-label">Default {if $account.service.oauth_version == "1.0"}consumer secret{else}client secret{/if} for Smartest on {$account.service.label}</span>
      {else}
      <input type="password" name="oauth_consumer_secret" value="{$account.oauth_consumer_secret}" id="oauth-consumer-secret" />
      <span style="display:none" class="ui-info-inactive" id="oauth-consumer-secret-default-label">Default {if $account.service.oauth_version == "1.0"}consumer secret{else}client secret{/if} for Smartest on {$account.service.label}</span>
      {/if}
    </div>
    
    {if $account.service.oauth_version == "1.0"}
    
    <div class="edit-form-row">
      <div class="form-section-label">Access Token</div>
      <input type="text" name="oauth_access_token" value="{$account.oauth_access_token}"/>
    </div>

    <div class="edit-form-row">
      <div class="form-section-label">Access Token Secret</div>
      <input type="password" name="oauth_access_token_secret" value="{$account.oauth_access_token_secret}" />
    </div>
    
    {if $account.oauth_consumer_token && $account.oauth_consumer_secret}
      {if empty($account.oauth_access_token) || empty($account.oauth_access_token_secret)}
        <div class="special-box">To request valid access tokenss for this account, <a href="{$domain}oauth/prepareAccessTokenRequestProcess?account_id={$account.id}" class="button">click here</a></div>
      {else}
        <div class="special-box">To request new access tokens for this account, <a href="{$domain}oauth/prepareAccessTokenRequestProcess?account_id={$account.id}" class="button">click here</a></div>
      {/if}
    {/if}
    
    {elseif $account.service.oauth_version == "2.0"}
    
    <div class="edit-form-row">
      <div class="form-section-label">Access Token</div>
      <input type="text" name="oauth_access_token" value="{$account.oauth_access_token}"/>
    </div>
    
    {if $account.oauth_consumer_token && $account.oauth_consumer_secret}
      {if empty($account.oauth_access_token)}
        <div class="special-box">To request an access token for this account, <a href="{$domain}oauth/prepareAccessTokenRequestProcess?account_id={$account.id}" class="button">click here</a></div>
      {else}
        <div class="special-box">To request a new access token for this account, <a href="{$domain}oauth/prepareAccessTokenRequestProcess?account_id={$account.id}" class="button">click here</a></div>
      {/if}
    {/if}
    
    {/if}
    
    <div class="buttons-bar">
      <input type="submit" value="Save changes" />
    </div>
    
  </form>
</div>

<div id="actions-area">
  
</div>