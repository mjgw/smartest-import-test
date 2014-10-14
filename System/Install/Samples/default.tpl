<!DOCTYPE html>

<html>

  <head>
  	<title><?sm:$this.page.formatted_title:?></title>
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  	<meta name="keywords" content="<?sm:$this.page.meta_keywords:?>" />
  	<meta name="description" content="<?sm:$this.page.meta_description:?>" />
  	<?sm:placeholder name="page_specific_javascript" editbutton="false":?>
  	<?sm:placeholder name="page_specific_stylesheet":?>
    <?sm:if $this.user_agent.is_mobile:?><meta name="viewport" content="width=device-width; initial-scale=1.0; minimum-scale = 0.8, maximum-scale = 2.0"/><?sm:/if:?>
  </head>

  <body>
    <p style="text-align:center;margin-top:60px">Edit or Replace Me (Presentation/Masters/%DEFAULTTEMPLATENAME%.tpl), Then Re-Publish Me!</p>
    <p style="text-align:center;margin-top:60px"><?sm:if $this.user_agent.is_tablet:?>
    You are viewing this page on a tablet.
    <?sm:elseif $this.user_agent.is_phone:?>
    You are viewing this page on a phone.
    <?sm:elseif $this.user_agent.is_desktop:?>
    You are viewing this page on a desktop computer.
    <?sm:/if:?></p>
    <?sm:container name="page_layout":?>
  </body>

</html>