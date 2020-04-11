<!DOCTYPE html>

<html>

  <head>
  	<title><?sm:$this.page.formatted_title:?></title>
    <meta property="og:title" content="<?sm:$this.page.formatted_title:?>" />
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  	<meta name="keywords" content="<?sm:$this.page.meta_keywords:?>" />
  	<meta name="description" content="<?sm:$this.page.meta_description:?>" />
    <meta name="og:description" content="<?sm:$this.page.meta_description:?>" />
    <?sm:site_favicon:?>
  	%CSSLINK%<?sm:placeholder name="page_specific_javascript" editbutton="false":?>
  	<?sm:placeholder name="page_specific_stylesheet":?>
    <?sm:if $this.user_agent.is_mobile:?><meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale = 0.8, maximum-scale = 2.0"/><?sm:/if:?>
  </head>

  <body>
    
    <div id="header-outer">
      <div id="header">
        <h1><?sm:$this.site.name:?></h1>
      </div>
    </div>
    
    <div id="navstripe">
      <div id="nav">
        <ul>
					<?sm:repeat from="pagegroup:main_nav" item="top_level_nav_page" assignhighlight="currentpage":?>
          <li><?sm:link to=$top_level_nav_page highlightpage=$currentpage highlightclass="current":?></li>
          <?sm:/repeat:?>
        </ul>
      </div>
    </div>
    
    <div id="container">
      
      <?sm:if $this.user_agent.is_tablet:?>
      <!--You are viewing this page on a tablet.-->
      <?sm:elseif $this.user_agent.is_phone:?>
      <!--You are viewing this page on a phone.-->
      <?sm:elseif $this.user_agent.is_desktop:?>
      <!--You are viewing this page on a desktop computer.-->
      <?sm:/if:?>
      
      <div class="breaker"></div>
        <p class="text">You have successfully created a new website in Smartest. You can update the design of this web page and updating the file:</p>     
        <p style="text-align:center;font-size:1.2em"><code>Presentation/Masters/%DEFAULTTEMPLATENAME%.tpl</code></p>
        <?sm:container name="page_layout":?>
      <div class="breaker"></div>
      
      <div id="footer">
        All content © <?sm:$this.site.organization_name:?> <?sm:$now.Y:?>, All rights reserved, except where noted.
      </div>
    
    </div>
    
  </body>

</html>