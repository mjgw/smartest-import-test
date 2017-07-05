<div id="work-area">
  
  <h3>Get permission to access your {$account.service.label} account</h3>
  
  
  
  <div class="instruction">
  <p>This screen is where Smartest sends you to {$account.service.label} to log in and authorize Smartest to use your account. This permission can be revoked at any time, and at no point will Smartest ever have your password for {$account.service.label}.</p>
  {if $account.info.username}
  <p>You will need to be logged into the {$account.service.label} account <strong>{$account.service.username_prefix}{$account.info.username}</strong> before proceeding.</p>
  {else}
  <p>You must already be logged into the {$account.service.label} account you want to authorise Smartest's access to.</p>
  {/if}
  </div>
  
  {if $service.token_request_url == 'dynamic'}
  <a class="button" href="{$domain}smartest/oauth_accounts">Cancel</a>
  <a class="button" href="{$authorize_uri}">Request permission from {$service.label}</a>
  {else}
  <form action="{$service.token_request_url}" method="{$authorize_method}">
    <input type="hidden" name="client_id" value="{$account.oauth_consumer_token}">
    <input type="hidden" name="redirect_uri" value="{$redirect_uri}">
    <input type="hidden" name="response_type" value="code">
    {if $service.shortname == 'instagram'}<input type="hidden" name="scope" value="public_content">{/if}
    {if $service.shortname == 'medium'}<input type="hidden" name="scope" value="basicProfile,publishPost">{/if}
    <input type="hidden" name="state" value="{$final_uri}?scid={$account.id}">
    <a class="button" href="{$domain}smartest/oauth_accounts">Cancel</a>
    <input type="submit" value="Request permission from {$service.label}" />
  </form>
  {/if}
  
</div>