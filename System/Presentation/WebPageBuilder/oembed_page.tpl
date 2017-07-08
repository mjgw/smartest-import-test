<!DOCTYPE html>

<html>
  <head>
    <title><?sm:$page.title:?></title>
    <meta charset="utf-8" />
    <meta name="generator" content="Smartest" />
    <link rel="stylesheet" href="<?sm:$request.domain:?>Resources/System/Stylesheets/sm_oembed_default.css" />
  </head>
  <body>
    
    <div id="container">
      
      <?sm:if $page._thumbnail.id:?>
      
      <div id="thumbnail" style="background-image:url(<?sm:$page._thumbnail.image.200x200.web_path:?>);<?sm:if $height:?>width:<?sm:$height:?>px; height:<?sm:$height:?>px<?sm:/if:?>"><?sm:link to=$page target="_top" with="":?></div>
      
      <div id="info" class="floating<?sm:if $height < 100:?> smaller<?sm:/if:?>">
        <h3><?sm:if $page.is_home_page:?><?sm:link to=$page with=$page.site.name target="_top":?><?sm:else:?><?sm:link to=$page target="_top":?><?sm:/if:?></h3>
        <h4><?sm:$site.organisation_name_safe:?></h4>
      </div>
      
      <?sm:else:?>
      
      <div id="info<?sm:if $height < 100:?> smaller<?sm:/if:?>">
        <h3><?sm:if $page.is_home_page:?><?sm:link to=$page with=$page.site.name target="_top":?><?sm:else:?><?sm:link to=$page target="_top":?><?sm:/if:?></h3>
        <h4><?sm:$site.organisation_name_safe:?></h4>
      </div>
      
      <?sm:/if:?>
    </div>
    
  </body>
</html>