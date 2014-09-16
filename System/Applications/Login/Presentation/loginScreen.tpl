<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title>Smartest Web Platform&trade; | Log In</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
    <link rel="stylesheet" href="{$domain}Resources/System/Stylesheets/sm_login.css" />
    <link rel="icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="{$domain}Resources/System/Images/favicon.ico" type="image/x-icon">
    
    <style type="text/css">
      /* {literal}div#login{{/literal}
        background-image:url('{$domain}Resources/System/Images/login_form_bg_hgrad.gif');
        background-repeat:repeat-x;
      {literal}}{/literal} */
      
    </style>
    
    <script type="text/javascript" language="javascript">

       var sm_domain = '{$domain}';
       var sm_section = '{$section}';
       var sm_method = '{$method}';
       var sm_user_agent = {$sm_user_agent_json};
       
    </script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/lib/prototype.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/scriptaculous/src/effects.js"></script>
    <script type="text/javascript" src="{$domain}Resources/System/Javascript/nakajima/event_hash_changed.js"></script>
    <script type="text/javascript">
    {literal}
    
      document.observe('hash:changed', function(){
        
        var hash = document.location.hash.substring(1);
        var messageId = 'message-'+hash;
        
        if($(messageId)){
          
          $$('p.login-message.notify').each(function(p){
            p.hide();
          });
          
          $(messageId).appear();
          
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
          $('loginform').submit();
        }, 2000);
        
      }
      
      document.observe('dom:loaded', function(){
        
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

</head>

<body>

<div id="login">

  <div id="login-inner">
	{if $is_msie}
    <img src="{$domain}Resources/System/Images/login_logo.png" alt="Smartest" border="0" id="logo" />
  {else}
		<svg width="150" height="45" id="logo">
			<g>
	
					<image overflow="visible" opacity="0.6" width="46" height="46" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAC8AAAAvCAYAAABzJ5OsAAAACXBIWXMAAAsSAAALEgHS3X78AAAA
			GXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABQNJREFUeNrsWYty0kAUJQ9SpEBt
			a7U+6/9/lVb7UNtCaSE8kqy7M+c6x+smJECtOmbmTIE07Ll3z30trdZffAV/IA/zN5APKjiYOobE
			j0hcENJrAxR1DAgeibgjHCkI+VyhKDMg/s3xJB5267YtdoA2DHJEFxZz/F3gOa8B8QNKQhvAMnFk
			n1j0LXp47bhkFlOLe8AQfpFQ/ADBJwRDpW25InjbET8ABjBoaXFrcQ2iWZV04i0HH2s4BkLymsH7
			J0T+2OIQBs1wz0A6KaSTbVvz4k0OPue9hLSc0Bo5kXD39iz2LY4snuOzFPcn2IEE3yvx8JN04g29
			Hargc17rQse7eJ9gwQU8u8QzjvxTGODQgd6dx4f4nh3avWAbmudUF2MBITwgUnv4LIHXUgSheLcP
			ucj/duCEKe6x8XPsnNnE8+ztBAv2sPgByDzD333ca2NhR2oMA3IYfEgGiMSmeD/A8/cgn8EJP3Yg
			XtPjCRYfYHHR7TH+CqEuyGdEfoL3CZ7fh6dlJ1M863CDZ6aQXcHej9fQeBseH4D0S4vXFq9A/gi7
			0MP/RfB0CuIpyEeQxS6kJ6REghI3HQpcTslmHc+3sYDzzAuLtxYnMOAFPDkgDYcgvwsPiwQC8naA
			QOaA5CwWeupFY9lEijx7/jX0PoBH27RoQSl0oYpPjs+mSI8jizvKPJkvWDeVTQ9efgYcIHC7nm02
			6rU0XTOS0xiV9avFhcU3GDLBruTrkNc5nVNjn8D65MoqBapFjZcE75i8PUSAfiNcI9vMVJtQK2A5
			w4jHdxXxHpFuK41yR5iBxB1IOXJXeH0DDIERDLujTJPV9bwu/Zwa91V+7nk03lL9TI6tn4LcF4sz
			i3Py8IjqgGSlOZ7LtNfLyAeeYtSl1HhMaVEHaOTJCjIZLcnzV9D1ZxjC+VyasSXFhqkTsJq4yGQP
			RF1meWPxDtnlSJX2sGImLeDBObUKrPl71UUWq8bBMs8L8T51fkJc8BLS4WIUrJig9PgXUlxkhNzX
			ReorKhnRdigVHqMQvUcxegviLJlkBXn2vHSXM0qVc9J3LeI+8jyiPUWfIhX0PRE/rEncp/2Cevsl
			EfcFZ6sJea6g7PUTSEWmnv6KIK269C6knuxSi3xYoxj1EZTSn/OQUIe4L3vJ0NLF6x1PfWg1IV/n
			cCii4Tqs6XHjgZZP7skujcibii2dIAfPVFCZFYsZT5qU1mBEhWmiBo5aBkQlVTX2DNM8UMfK+1Vn
			jjkVqDEK1CUK1CeqsrcwLKNs05h84Dl/iUjjEX0WeOALTCZ+AdIfYcAlKiyPe6ZpttFGmJIvCUsO
			lbTnC2p7pd29AGmHU3j9CvdnnoGkEfmyvFx4BoIyuXAzNodHb0DcEf4A8kw8VSnSrEPeF2hFyclt
			4VmMiS9UF/kJxE/RUYrOUzVsrO35MgNyCryMqmPmMSanTHULkmcklc+YlkbUiDUmvoq8zwAhvVBY
			EoS46PwcpE+pBR7RkLEW8VWTlFEjnPF4ltvaIR15xDBkhGxy6kmJi6Yab3rQygYYNVhwXy7H0nKA
			JMfVY0jkEhiiIC1W/eqxDfI+AwrVEUrFvKHTAznim4DwNQ0cjSvptn6TKjsd7lCj1cG9AkQnNEjP
			qs5hHpp82bm8dIsJjYOGeqS5yk4bE9/018CgYrwLPGm22DRAt/1Tpq8fClSLUVT9KPaY5Mu+K/AQ
			Na3/1z9yfRdgAFXMTzJ1HXyGAAAAAElFTkSuQmCC" transform="matrix(1 0 0 1 -4.4148 1.2871)">
				</image>
				<g>
		
						<linearGradient id="SVGID_1_" gradientUnits="userSpaceOnUse" x1="30.6467" y1="40.8584" x2="18.6228" y2="55.188" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#80B0FF"/>
						<stop  offset="1" style="stop-color:#4670B5"/>
					</linearGradient>
					<path fill="url(#SVGID_1_)" d="M16.882,15.747c-5.1,2.401-9.892,5.79-13.952,10.353c2.851,3.824,8.502,8.426,15.003,11.25
						c2.9-4.574,6.376-8.551,10.501-11.852C22.809,22.123,19.842,18.812,16.882,15.747z"/>
		
						<linearGradient id="SVGID_2_" gradientUnits="userSpaceOnUse" x1="27.8088" y1="50.2207" x2="23.3314" y2="57.9758" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#4A7BC2"/>
						<stop  offset="1" style="stop-color:#2C5BAB"/>
					</linearGradient>
					<path fill="url(#SVGID_2_)" d="M18.083,25.123c1.476-1.375,2.922-2.66,4.397-3.768c-2.274-1.929-4.014-3.812-5.75-5.609
						c-1.811,0.852-3.582,1.832-5.296,2.942C13.083,20.991,15.552,23.534,18.083,25.123z"/>
		
						<linearGradient id="SVGID_3_" gradientUnits="userSpaceOnUse" x1="30.7429" y1="53.6279" x2="25.0742" y2="63.4464" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#EB5593"/>
						<stop  offset="1" style="stop-color:#CD00DF"/>
					</linearGradient>
					<path fill="url(#SVGID_3_)" d="M20.982,9.321c-3.55,1.25-7.551,3.5-10.551,5.751c1.45,2.65,4.575,6.226,7.801,8.251
						c3.302-3.076,6.452-5.701,10.126-6.901C24.909,14.471,22.333,11.421,20.982,9.321z"/>
		
						<linearGradient id="SVGID_4_" gradientUnits="userSpaceOnUse" x1="35.343" y1="57.3525" x2="33.6959" y2="60.585" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#BF4578"/>
						<stop  offset="1" style="stop-color:#C41B99"/>
					</linearGradient>
					<path fill="url(#SVGID_4_)" d="M25.502,13.885c-0.641,0.349-1.283,0.72-1.944,1.112c0.761,0.929,1.688,1.716,2.688,2.373
						c0.831-0.438,1.682-0.81,2.563-1.098C27.592,15.583,26.485,14.757,25.502,13.885z"/>
		
						<linearGradient id="SVGID_5_" gradientUnits="userSpaceOnUse" x1="38.6462" y1="58.3398" x2="36.0102" y2="63.5134" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#F79B45"/>
						<stop  offset="1" style="stop-color:#F06D0F"/>
					</linearGradient>
					<path fill="url(#SVGID_5_)" d="M30.285,10.12c-2.602,0.801-4.776,1.951-7.177,3.376c1.351,1.65,3.226,2.851,5.102,3.676
						c1.649-1.05,4.926-2.9,6.676-3.3C33.185,12.872,31.235,11.321,30.285,10.12z"/>
		
						<linearGradient id="SVGID_6_" gradientUnits="userSpaceOnUse" x1="38.4065" y1="66.0195" x2="37.4766" y2="68.5743" gradientTransform="matrix(1 0 0 -1 -8.3599 74.5547)">
						<stop  offset="0" style="stop-color:#F7636D"/>
						<stop  offset="1" style="stop-color:#DB3D1A"/>
					</linearGradient>
					<path fill="url(#SVGID_6_)" d="M28.535,9.071c1.149-0.65,2.449-1.101,3.75-1.351c-0.7-0.7-1.051-1.25-1.351-2.05
						c-1.25,0.15-2.9,0.65-4.051,1.15C27.334,7.72,27.884,8.62,28.535,9.071z"/>
				</g>
			</g>
			<g>
	
					<image overflow="visible" opacity="0.85" width="108" height="33" xlink:href="data:image/png;base64,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" transform="matrix(1 0 0 1 30.6702 6.3721)">
				</image>
				<g>
					<path fill="#FFFFFF" d="M45.535,18.637c-0.111,0.22-0.166,0.33-0.331,0.33c-0.192,0-1.349-0.743-2.779-0.743
						c-1.101,0-2.037,0.55-2.037,1.816c0,3.027,6.11,2.174,6.11,6.467c0,2.587-2.037,3.908-4.403,3.908
						c-1.321,0-4.183-0.771-4.183-1.321c0-0.137,0.055-0.22,0.109-0.33l0.386-0.853c0.083-0.193,0.166-0.248,0.247-0.248
						c0.248,0,1.679,0.963,3.551,0.963c1.184,0,2.202-0.66,2.202-1.981c0-2.861-6.109-2.229-6.109-6.385
						c0-2.229,1.734-3.825,4.155-3.825c0.936,0,2.531,0.44,3.248,0.826c0.22,0.11,0.302,0.22,0.302,0.358
						c0,0.083-0.083,0.165-0.11,0.275L45.535,18.637z"/>
					<path fill="#FFFFFF" d="M49.418,20.26c0-0.633-0.028-1.293-0.165-1.926c-0.138-0.633-0.357-0.798-0.357-0.936
						c0-0.055,0.027-0.11,0.138-0.193l1.184-0.661c0.055-0.027,0.165-0.11,0.248-0.11c0.413,0,0.715,1.431,0.743,1.624
						c0.715-0.66,2.422-1.624,3.578-1.624c1.789,0,2.559,0.55,2.917,1.651c0.825-0.605,2.312-1.651,3.633-1.651
						c3.055,0,3.274,2.146,3.274,4.128V29.7c0,0.221-0.109,0.331-0.331,0.331h-1.431c-0.22,0-0.33-0.11-0.33-0.331v-9.192
						c0-1.183-0.303-2.229-1.762-2.229c-1.321,0-2.146,0.743-2.697,1.128V29.7c0,0.221-0.109,0.331-0.33,0.331h-1.431
						c-0.22,0-0.331-0.11-0.331-0.331v-9.192c0-1.183-0.303-2.229-1.789-2.229c-1.239,0-2.201,0.771-2.669,1.156V29.7
						c0,0.221-0.11,0.331-0.331,0.331h-1.431c-0.22,0-0.33-0.11-0.33-0.331V20.26z"/>
					<path fill="#FFFFFF" d="M76.976,25.682c0,1.734,0.083,1.982,0.331,2.751c0.11,0.33,0.495,0.854,0.495,1.019
						c0,0.11-0.137,0.166-0.248,0.221l-1.074,0.66c-0.108,0.056-0.165,0.083-0.22,0.083c-0.412,0-0.908-1.458-1.045-1.844
						c-0.33,0.303-1.679,1.844-3.963,1.844c-2.173,0-3.578-1.432-3.578-3.881c0-4.651,5.504-4.733,7.211-4.788v-0.991
						c0-1.596-0.496-2.477-2.45-2.477c-1.844,0-3.108,0.881-3.356,0.881c-0.083,0-0.166-0.055-0.193-0.138l-0.357-0.963
						c-0.055-0.138-0.083-0.22-0.083-0.357c0-0.468,2.532-1.266,4.292-1.266c3.166,0,4.239,1.542,4.239,3.853V25.682z M74.885,23.453
						c-1.211,0.028-5.146,0.028-5.146,2.917c0,1.101,0.605,2.339,2.01,2.339c1.431,0,2.696-1.32,3.137-1.707V23.453z"/>
					<path fill="#FFFFFF" d="M83.086,29.7c0,0.221-0.11,0.331-0.33,0.331h-1.432c-0.221,0-0.33-0.11-0.33-0.331v-9.44
						c0-0.495-0.083-1.403-0.469-2.091c-0.22-0.385-0.303-0.606-0.303-0.688c0.028-0.165,0.138-0.248,0.193-0.275l1.018-0.633
						c0.11-0.083,0.22-0.138,0.275-0.138c0.496,0,0.963,1.707,1.074,2.312c0.716-0.77,1.705-2.312,3.303-2.312
						c0.633,0,1.348,0.221,1.348,0.468c0,0.055-0.055,0.138-0.083,0.22l-0.605,1.404c-0.027,0.055-0.056,0.193-0.165,0.193
						c-0.139,0-0.386-0.248-0.908-0.248c-0.963,0-1.926,1.431-2.587,2.146L83.086,29.7L83.086,29.7z"/>
					<path fill="#FFFFFF" d="M97.956,29.012c0.026,0.11,0.055,0.193,0.055,0.276c0,0.356-2.063,1.128-3.549,1.128
						c-1.185,0-2.174-0.439-2.587-0.963c-0.798-0.854-0.798-2.092-0.798-3.109v-7.816h-2.064c-0.193,0-0.193-0.192-0.193-0.33v-1.045
						c0-0.193,0-0.331,0.193-0.331h2.064V13.05c0-0.165,0.109-0.275,0.303-0.33l1.458-0.413c0.083,0,0.137-0.028,0.192-0.028
						c0.138,0,0.138,0.138,0.138,0.303v4.238h4.073c0.164,0,0.303,0.027,0.303,0.193c0,0.055-0.028,0.109-0.028,0.192l-0.356,1.073
						c-0.056,0.138-0.193,0.248-0.357,0.248h-3.633v7.816c0,1.102,0,2.229,1.65,2.229c1.293,0,2.395-0.742,2.643-0.742
						c0.109,0,0.191,0.083,0.191,0.165L97.956,29.012z"/>
					<path fill="#FFFFFF" d="M109.202,28.764c0.026,0.084,0.083,0.139,0.083,0.221c0,0.303-2.312,1.431-4.349,1.431
						c-3.66-0.028-5.807-2.808-5.807-6.99c0-4.045,2.036-6.99,5.449-6.99c4.596,0,5.009,4.458,5.009,6.825c0,0.385,0,0.605-0.303,0.605
						h-7.898c0,1.678,0.66,4.705,3.797,4.705c1.789,0,3.22-1.127,3.385-1.127c0.082,0,0.11,0.082,0.165,0.164L109.202,28.764z
						 M107.33,22.16c0-1.431-0.468-4.018-2.862-4.018c-2.532,0-3.082,2.642-3.082,4.018H107.33z"/>
					<path fill="#FFFFFF" d="M119.27,18.637c-0.109,0.22-0.166,0.33-0.331,0.33c-0.192,0-1.349-0.743-2.779-0.743
						c-1.102,0-2.036,0.55-2.036,1.816c0,3.027,6.108,2.174,6.108,6.467c0,2.587-2.036,3.908-4.402,3.908
						c-1.321,0-4.184-0.771-4.184-1.321c0-0.137,0.056-0.22,0.11-0.33l0.385-0.853c0.083-0.193,0.165-0.248,0.248-0.248
						c0.248,0,1.678,0.963,3.55,0.963c1.184,0,2.201-0.66,2.201-1.981c0-2.861-6.109-2.229-6.109-6.385
						c0-2.229,1.734-3.825,4.156-3.825c0.937,0,2.532,0.44,3.248,0.826c0.22,0.11,0.302,0.22,0.302,0.358
						c0,0.083-0.083,0.165-0.109,0.275L119.27,18.637z"/>
					<path fill="#FFFFFF" d="M131.1,29.012c0.028,0.11,0.056,0.193,0.056,0.276c0,0.356-2.063,1.128-3.55,1.128
						c-1.183,0-2.174-0.439-2.587-0.963c-0.798-0.854-0.798-2.092-0.798-3.109v-7.816h-2.063c-0.192,0-0.192-0.192-0.192-0.33v-1.045
						c0-0.193,0-0.331,0.192-0.331h2.063V13.05c0-0.165,0.11-0.275,0.303-0.33l1.458-0.413c0.082,0,0.138-0.028,0.192-0.028
						c0.137,0,0.137,0.138,0.137,0.303v4.238h4.073c0.166,0,0.303,0.027,0.303,0.193c0,0.055-0.027,0.109-0.027,0.192l-0.357,1.073
						c-0.055,0.138-0.192,0.248-0.357,0.248h-3.633v7.816c0,1.102,0,2.229,1.651,2.229c1.294,0,2.395-0.742,2.642-0.742
						c0.11,0,0.193,0.083,0.193,0.165L131.1,29.012z"/>
				</g>
			</g>
		</svg>
	{/if}
	
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
            Username:<br />
            <input type="text" name="user" id="username" value="" size="20" tabindex="1" class="textInput" />
          </label>
        </p>
        
        <p id="password-holder">
          <label>
            Password:<br />
            <input type="password" name="passwd" id="password" value="" size="20" tabindex="2" class="textInput" />
          </label>
        </p>

        <input type="hidden" name="from" value="{$from}" />
        <input type="hidden" name="refer" value="{$refer}" />
        <input type="hidden" name="service" value="smartest" />

        <p class="submit">
          <a href="#" id="submit-button"><img src="{$domain}Resources/System/Images/login_button.png" alt="Log In" /></a>
        </p>

      </form>
  
    </div>

  </div>

</div>

<p id="footer">© VSC Creative Ltd. {$now.Y}</p>

{if $sm_user_agent.platform == "Windows" && $sm_user_agent.appName == "Explorer" && $sm_user_agent.appVersionInteger < 7}
<script language="javascript" src="{$domain}Resources/System/Javascript/supersleight/supersleight.js"></script>
<script language="javascript">supersleight.init();</script>
{/if}

</body>
</html>
