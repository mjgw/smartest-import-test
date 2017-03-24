<!DOCTYPE html>

<html>

  <head>
  	<title><?sm:$this.page.formatted_title:?></title>
    <meta property="og:title" content="<?sm:$this.page.formatted_title:?>" />
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  	<meta name="keywords" content="<?sm:$this.page.meta_keywords:?>" />
  	<meta name="description" content="<?sm:$this.page.meta_description:?>" />
    <meta name="og:description" content="<?sm:$this.page.meta_description:?>" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <?sm:site_favicon:?>
  	<?sm:placeholder name="page_specific_javascript" editbutton="false":?>
  	<?sm:placeholder name="page_specific_stylesheet":?>
    <?sm:if $this.user_agent.is_mobile:?><meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale = 0.8, maximum-scale = 2.0"/><?sm:/if:?>
  </head>

  <body>
    
    <div id="container">
    
      <p style="text-align:center;margin-top:60px">Edit or Replace Me (Presentation/Masters/%DEFAULTTEMPLATENAME%.tpl), Then Re-Publish Me!</p>
      
      <?sm:if $this.user_agent.is_tablet:?>
      <!--You are viewing this page on a tablet.-->
      <?sm:elseif $this.user_agent.is_phone:?>
      <!--You are viewing this page on a phone.-->
      <?sm:elseif $this.user_agent.is_desktop:?>
      <!--You are viewing this page on a desktop computer.-->
      <?sm:/if:?>
      
      <?sm:container name="page_layout":?>
      
      <div id="footer">
        <p>All content © <?sm:$this.site.organization_name:?> <?sm:$now.Y:?>, All rights reserved, except where noted. </p>
      </div>
    
    </div>
    
  </body>

</html>