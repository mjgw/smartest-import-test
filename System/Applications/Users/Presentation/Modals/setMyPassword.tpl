<div id="work-area">
  <div class="special-box">Use this form to change your password. Passwords must be at least eight characters long.</div>
  <form action="{$domain}users/updateMyPassword" method="post" id="update-password-form">
    <div class="edit-form-row">
      <div class="form-section-label">Enter it once</div>
      <input type="password" name="password_1" id="password-1" />
      <div class="edit-form-sub-row" id="password-length-warning" style="display:none"><span class="feedback-bad">This password is not long enough</span></div>
    </div>
    <div class="edit-form-row">
      <div class="form-section-label">Enter it again</div>
      <input type="password" name="password_2" id="password-2" />
      <div class="edit-form-sub-row" id="password-match-warning" style="display:none"><span class="feedback-bad">The password fields do no match</span></div>
    </div>
    <div class="buttons-bar">
      <input type="button" value="Cancel" id="password-cancel-button" />
      <input type="submit" value="Update password" id="password-save-button" disabled="disabled" />
    </div>
  </form>
  <script type="text/javascript">// <![CDATA[
  {literal}
  var checkMatchTimeout, checkLengthTimeout;
    $('password-save-button').observe('click', function(e){
      e.stop();
      $('primary-ajax-loader').show();
      $('update-password-form').request({
        onSuccess: function(){
          $('primary-ajax-loader').hide();
          MODALS.hideViewer();
        }
      });
    });
    $('password-cancel-button').observe('click', function(e){
      e.stop();
      MODALS.hideViewer();
    });
    var checkPasswordsMatch = function(){
      if($F('password-1').length > 7){
        if($F('password-1') == $F('password-2')){
          $('password-match-warning').hide();
          $('password-save-button').disabled = false;
        }else{
          $('password-match-warning').show();
          $('password-save-button').disabled = true;
        }
      }else{
        $('password-save-button').disabled = true;
      }
    }
    var checkPasswordLength = function(){
      if($F('password-1').length > 7){
        $('password-length-warning').hide();
      }else{
        $('password-length-warning').show();
      }
    }
    
    $('password-1').observe('keyup', function(){
      if(checkLengthTimeout){
        clearTimeout(checkLengthTimeout);
      }
      checkLengthTimeout = setTimeout(checkPasswordLength, 400);
    });
    
    $('password-2').observe('keyup', function(){
      if(checkMatchTimeout){
        clearTimeout(checkMatchTimeout);
      }
      checkMatchTimeout = setTimeout(checkPasswordsMatch, 400);
    });
    
  {/literal}
  // ]]>
  </script>
</div>