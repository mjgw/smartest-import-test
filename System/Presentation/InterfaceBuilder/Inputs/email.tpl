<input type="text" name="{$_input_data.name}" value="{$_input_data.value.html_escape}" id="{$_input_data.id}" />

<span id="{$_input_data.id}-email-invalid" style="{if strlen($_input_data.value) && $_input_data.value.valid}display:none{else}{/if}" class="feedback-bad"><i class="fa fa-times"> </i> Invalid email address</span>
<span id="{$_input_data.id}-email-ok" style="{if strlen($_input_data.value) && $_input_data.value.valid}{else}display:none{/if}" class="feedback-ok" title="This is a correctly formatted email address"><i class="fa fa-check"> </i></span>

<script type="text/javascript">
  {literal}(function(iid, emailRegex){
    var checkTimeOut;
    $(iid).observe('keydown', function(){
      if(checkTimeOut){
        clearTimeout(checkTimeOut);
      }
      checkTimeOut = setTimeout(function(){
        if($F(iid).charAt(1)){
          if(emailRegex.match($F(iid))){
            $(iid+'-email-ok').show();
            $(iid+'-email-invalid').hide();
          }else{
            $(iid+'-email-ok').hide();
            $(iid+'-email-invalid').show();
          }
        }else{
          $(iid+'-email-ok').hide();
          $(iid+'-email-invalid').hide();
        }
      }, 200);
    });
  }){/literal}('{$_input_data.id}', {$email_regex});
</script>