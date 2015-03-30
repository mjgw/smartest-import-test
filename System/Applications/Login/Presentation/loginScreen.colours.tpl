<body class="{$start_colour} slowchange">

    <script type="text/javascript">
       
      var colourindex = {$colour_index};
       
    {literal}
      
      var bgcolors = ['blue', 'magenta', 'orange', 'red'];
      
      document.observe('hash:changed', function(){
        
        var hash = document.location.hash.substring(1);
        var messageId = 'message-'+hash;
        
        if($(messageId)){
          
          $$('p.login-message.notify').each(function(p){
            p.hide();
          });
          
          $(messageId).appear();
          
          /* if(hash == 'badauth'){
            new Effect.Shake('login', {delay: 0.25, duration:0.5, distance: 135});
          } */
          
        }
        
      });
      
      var loginSubmit = function(){
        
        new Effect.Opacity('username-holder',{
          duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.01 });
        
        new Effect.Opacity('password-holder',{
          duration: 0.1, transition: Effect.Transitions.linear, from: 1.0, to: 0.01 });
          
        $('footer').fade({duration: 0.150});
        
        var timeout0 = window.setTimeout(function(){
          new Effect.BlindUp('loginform_container', { duration: 0.6 });
        }, 170);
        
        var timeout1 = window.setTimeout(function(){
          new Effect.BlindDown('login-message-holder', { duration: 0.5 });
        }, 600);
        
        var timeout2 = window.setTimeout(function(){
          new Effect.Puff('login', {duration:0.5});
          $('loginform').submit();
        }, 2000);
        
      }
      
      document.observe('dom:loaded', function(){
        
        if(colourindex > 2){
          var newcolourindex = 0;
        }else{
          var newcolourindex = colourindex+1;
        }
        // alert(bgcolors[newcolourindex]);
        $$('body')[0].removeClassName(bgcolors[colourindex]);
        $$('body')[0].addClassName(bgcolors[newcolourindex]);
        colourindex = newcolourindex;
        
        setInterval(function(){
          if(colourindex > 2){
            var newcolourindex = 0;
          }else{
            var newcolourindex = colourindex+1;
          }
          $$('body')[0].removeClassName(bgcolors[colourindex]);
          $$('body')[0].addClassName(bgcolors[newcolourindex]);
          colourindex = newcolourindex;
        }, 8500);
        
        $('loginform').observe('keypress', function(e){
          
          if(e.keyCode == 13){
            
            loginSubmit();
            
          }
          
        });
        
        $('submit-button').observe('click', function(e){
          
          loginSubmit();
          e.stop();
          
        });
        
        $('logo').observe('click', function(){
          
          window.open('http://sma.rte.st/?ref=login');
          
        });
        
      });
      
    {/literal}
    </script>

<div id="login">

  <div id="login-inner">
	
    <img src="{$domain}Resources/System/Images/smartest-login-logo.png" alt="Smartest" border="0" id="logo" style="width:162px;height:50px" />
  
	  <div id="login-message-holder" style="display:none;">
      <p class="login-message">Please wait...</p>
    </div>

    <div id="loginform_container">

      <p class="login-message notify" id="message-logout" style="display:none">You have been safely logged out of Smartest.</p>
      <p class="login-message notify" id="message-badauth" style="display:none">The username or password you provided were wrong.</p>
      <p class="login-message notify" id="message-session" style="display:none">Your session has timed out. Please log back into Smartest</p>
      <p class="login-message notify" id="message-welcome" style="display:none">Welcome to Smartest. Submit the username and password you just chose to log in.</p>
      <p class="login-message notify" id="message-unauthorized" style="display:none">The user you are logged in as is not authorized to access the Smartest backend.</p>
      <p class="login-message notify" id="message-reauth" style="display:none">You need to re-authenticate using this login form.</p>

      <form name="loginform" id="loginform" action="{$domain}smartest/login/check" method="post">

        <p id="username-holder">
          <label>
            Username<br />
            <input type="text" name="user" id="username" value="" size="20" tabindex="1" class="textInput" />
          </label>
        </p>
        
        <p id="password-holder">
          <label>
            Password<br />
            <input type="password" name="passwd" id="password" value="" size="20" tabindex="2" class="textInput" />
          </label>
        </p>

        <input type="hidden" name="from" value="{$from}" />
        <input type="hidden" name="refer" value="{$refer}" />
        <input type="hidden" name="service" value="smartest" />

        <p class="submit">
          <a href="#" id="submit-button">Log in</a>
        </p>

      </form>
  
    </div>

  </div>

</div>

<p id="footer">Smartest is © VSC Creative Ltd. {$now.Y}</p>

</body>