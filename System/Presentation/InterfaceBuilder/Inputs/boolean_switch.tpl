{if $sm_user_agent.is_supported_browser}
<div class="sm-boolean-switch {if $_input_data.value}on{else}off{/if}{if $_input_data.red} red{/if}" id="{$_input_data.id}-outer">
  <a href="#toggle-switch" id="{$_input_data.id}-link" class="sm-boolean-switch-inner">
    <span class="sm-boolean-switch-on-label">On</span><span class="sm-boolean-switch-target"></span><span class="sm-boolean-switch-off-label">Off</span>
  </a>
</div>
<input type="hidden" name="{$_input_data.name}" value="{if $_input_data.value}TRUE{else}FALSE{/if}" id="{$_input_data.id}-input" />

<script type="text/javascript">
  $('{$_input_data.id}-link').observe('click', function(e){ldelim}
    e.stop();
    var boolValue;
    if($('{$_input_data.id}-input').value == 'TRUE'){ldelim}
      boolValue = false;
      $('{$_input_data.id}-input').value = 'FALSE';
      $('{$_input_data.id}-outer').removeClassName('on');
      $('{$_input_data.id}-outer').addClassName('off');
      {if $_input_data.change_js}{$_input_data.change_js}{/if}
{if $_input_data.change_hook}{$_input_data.change_hook}boolValue, '{$_input_data.id}');
{/if}

    {rdelim}else{ldelim}
      boolValue = true;
      $('{$_input_data.id}-input').value = 'TRUE';
      $('{$_input_data.id}-outer').removeClassName('off');
      $('{$_input_data.id}-outer').addClassName('on');
      {if $_input_data.change_js}{$_input_data.change_js}{/if}
{if $_input_data.change_hook}{$_input_data.change_hook}boolValue, '{$_input_data.id}');
{/if}

    {rdelim};
    
    $('{$_input_data.id}-input').fire('switch:changed', {ldelim}inputId: '{$_input_data.id}-input', value: boolValue{rdelim});
    
  {rdelim});
</script>

{else}
{* Older browsers will probably not support this user input, so fall back to a clickable image. *}

<a href="#toggle-switch" id="{$_input_data.id}-link">{if $_input_data.value}<img src="{$domain}Resources/System/Images/bool-switch-on.png" id="{$_input_data.id}-img" alt="On" />{else}<img src="{$domain}Resources/System/Images/bool-switch-off.png" id="{$_input_data.id}-img" alt="Off" />{/if}</a>
<input type="hidden" name="{$_input_data.name}" value="{if $_input_data.value}TRUE{else}FALSE{/if}" id="{$_input_data.id}-input" />

<script type="text/javascript">

  $('{$_input_data.id}-link').observe('click', function(e){ldelim}
    e.stop();
    if($('{$_input_data.id}-input').value == 'TRUE'){ldelim}
      $('{$_input_data.id}-input').value = 'FALSE';
      $('{$_input_data.id}-img').src = '{$domain}Resources/System/Images/bool-switch-off.png';
      {if $_input_data.change_js}{$_input_data.change_js}{/if}
{if $_input_data.change_hook}{$_input_data.change_hook}false, '{$_input_data.id}');
{/if}
      
    {rdelim}else{ldelim}
      $('{$_input_data.id}-input').value = 'TRUE';
      $('{$_input_data.id}-img').src = '{$domain}Resources/System/Images/bool-switch-on.png';
      {if $_input_data.change_js}{$_input_data.change_js}{/if}
{if $_input_data.change_hook}{$_input_data.change_hook}true, '{$_input_data.id}');
{/if}

    {rdelim};
  {rdelim});
</script>

{/if}