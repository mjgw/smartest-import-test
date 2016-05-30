<!DOCTYPE html>

<html>
  <head>
    <title><?sm:$item.name:?></title>
    <meta charset="utf-8" />
    <meta name="generator" content="Smartest" />
    <link rel="stylesheet" href="<?sm:$request.domain:?>Resources/System/Stylesheets/sm_oembed_default.css" />
  </head>
  <body>
    
    <div id="container">
      <?sm:if $item._thumbnail.id:?>
      <div id="thumbnail" style="background-image:url(<?sm:$item._thumbnail.image.square.web_path:?>);<?sm:if $height:?>width:<?sm:$height:?>px; height:<?sm:$height:?>px<?sm:/if:?>"><?sm:if $item._is_linkable:?><?sm:link to=$item target="_top" with="" id="thumbnail-link":?><?sm:/if:?></div>
      <div id="info" class="floating<?sm:if $height < 100:?> small<?sm:/if:?>">
        <h3><?sm:if $item._is_linkable:?><?sm:link to=$item target="_top":?><?sm:else:?><?sm:$item.name:?><?sm:/if:?></h3>
        <h4><?sm:$site.organisation_name_safe:?></h4>
      </div>
      <script type="text/javascript">
      var h = '<?sm:$height:?>';
      h = h*1;
      document.getElementById('thumbnail').style.width = h+'px';
      document.getElementById('thumbnail').style.height = h+'px';
      document.getElementById('thumbnail-link').style.width = h+'px';
      document.getElementById('thumbnail-link').style.height = h+'px';
      document.getElementById('thumbnail-link').style.maxWidth = 'initial';
      document.getElementById('thumbnail-link').style.maxHeight = 'initial';
      document.getElementById('info').style.maxWidth = 'calc(100% - '+h+'px)';
      </script>
      <?sm:else:?>
      <div id="info"<?sm:if $height < 100:?> class="smaller""<?sm:/if:?>>
        <h3><?sm:if $item._is_linkable:?><?sm:link to=$item target="_top":?><?sm:else:?><?sm:$item.name:?><?sm:/if:?></h3>
        <h4><?sm:$site.organisation_name_safe:?></h4>
      </div>
      <?sm:/if:?>
    </div>
    
  </body>
</html>