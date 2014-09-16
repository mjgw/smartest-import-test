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
    <a href="{$domain}smartest/about" style="" id="top-left-logo"></a>
  </div>
  
  <div id="user-info">
    {if $show_left_nav_options}{$_l10n_global_strings.top_bar.signed_in_before_site_name} <strong>{$_site.internal_label}</strong>{else}{$_l10n_global_strings.top_bar.signed_in_without_site_name}{/if} {$_l10n_global_strings.top_bar.signed_in_after_site_name} <strong>{$_user.firstname} {$_user.lastname}</strong>&nbsp;&nbsp;{if $show_left_nav_options || $show_create_button}<a href="#create" id="sm-button-create" title="Create something new" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/profile" id="sm-button-profile" title="Edit your user account" class="sm-top-bar-button">&nbsp;</a>{if $show_left_nav_options && ($_user.num_allowed_sites > 1 || $_user_allow_site_create)}<a href="{$domain}smartest/close" id="sm-button-close" title="Close this site" class="sm-top-bar-button">&nbsp;</a>{/if}<a href="{$domain}smartest/logout" id="sm-button-exit" title="Sign out" class="sm-top-bar-button">&nbsp;</a>&nbsp;&nbsp;
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