<div id="top-bar-message-container">
{foreach from=$sm_messages item="message" name="messages" key="i"}
  <div class="ui-message {$message.type}-msg{if $message.is_sticky} sticky{else} not-sticky{/if}" id="message-{$i}">
    <div class="ui-message-inner">
      {if $message.is_sticky}<a href="#dismiss-message" class="dismiss" id="message-dismiss-{$i}"> </a>{/if}
      <p>{$message.content}</p>
    </div>
    <script type="text/javascript">
    {if $message.is_sticky}
    $('message-dismiss-{$i}').observe('click', function(e){ldelim}
      e.stop();
      new Effect.SlideUp('message-{$i}', {ldelim}duration: 0.35, transition: Effect.Transitions.sinoidal{rdelim});
    {rdelim});
    {/if}
    $('message-{$i}').observe('click', function(e){ldelim}
      e.stop();
      new Effect.SlideUp('message-{$i}', {ldelim}duration: 0.35, transition: Effect.Transitions.sinoidal{rdelim});
    {rdelim});
    </script>
  </div>
{/foreach}
</div>

{if count($sm_messages)}

<script type="text/javascript">
{literal}
var baseSeconds = 4;
var msgIndex = 0;

$$('div.ui-message.not-sticky').each(function(msgDiv){
  setTimeout(function(){
    if(msgDiv.getStyle('display') == 'block'){
      msgDiv.slideUp({duration: 0.35, transition: Effect.Transitions.sinoidal})
    }
  }, (baseSeconds+msgIndex*1.6)*1000);
  msgIndex++;
});

{/literal}
</script>

{/if}

<div id="smartest-frame">
    
  <div id="top-strip">
    <a href="#about" style="" id="top-left-logo" title="{$_l10n_global_strings.main_nav.about}"></a>
    <script type="text/javascript">
      {literal}
      $('top-left-logo').observe('click', function(e){
        e.stop();
        MODALS.load('desktop/aboutSmartest', 'About Smartest');
      });
      {/literal}
    </script>
  </div>
  
  <div id="user-info">
    {if $show_left_nav_options}{$_l10n_global_strings.top_bar.signed_in_before_site_name} <strong>{$_site.internal_label}</strong>{else}{$_l10n_global_strings.top_bar.signed_in_without_site_name}{/if} {$_l10n_global_strings.top_bar.signed_in_after_site_name} <strong>{$_user.firstname} {$_user.lastname}</strong>&nbsp;&nbsp;{if $show_left_nav_options || $show_create_button}<a href="#create" id="sm-button-create" title="Create something new" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/profile" id="sm-button-profile" title="Edit your user account" class="sm-top-bar-button">&nbsp;</a>{if $show_left_nav_options && ($_user.num_allowed_sites > 1 || $_user_allow_site_create)}<a href="{$domain}smartest/close" id="sm-button-close" title="Close this site" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/logout" id="sm-button-exit" title="Sign out" class="sm-top-bar-button">&nbsp;</a>&nbsp;&nbsp;
  </div>
  
  <div id="primary-ajax-loader" style="display:none">
    {if $sm_user_agent.is_supported_browser}
    <div class="sm-loading-main left" id="primary-ajax-loader-element">
      <div class="loader small">
        <svg class="sm-circular-loader" viewBox="25 25 50 50" >
          <circle class="loader-path" cx="50" cy="50" r="20" fill="none" stroke="#777777" stroke-width="6" />
        </svg>
      </div>
    </div>
    {else}
    <img src="{$domain}Resources/System/Images/ajax-loader.gif" alt="" id="primary-ajax-loader-element" />
    {/if}
  </div>
    
{include file=$sm_navigation}

{include file=$sm_interface}

</div>

{* <div id="smartest-favourites" class="closed">
  <ul>
    <li><a href="#">This is a favourite</a></li>
    <li><a href="#">This is another favourite</a></li>
  </ul>
</div> *}

{if $show_left_nav_options || $show_create_button}
<script type="text/javascript">
{literal}
  $('sm-button-create').observe('click', function(e){
      Smartest.createNew();
      e.stop();
  });
{/literal}
</script>
{/if}