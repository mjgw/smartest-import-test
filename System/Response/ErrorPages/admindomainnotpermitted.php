<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Host name not allowed for admin</title>
    <link rel="stylesheet" href="/Resources/System/Stylesheets/sm_error.css" />
    <meta charset="UTF-8" />
  </head>
  <body>
    <div id="fixed-width">
      <h1>Host name not allowed</h1>
      <p>Smartest cannot be administered via the hostname <strong><?php echo $_SERVER['HTTP_HOST']; ?></strong>.</p>
      <p class="technical">Technical info: For security reasons, only certain hostnames can be used to administer this Smartest installation. If you have access to the Smartest file system, please see Configuration/admin_domains.yml, otherwise contact your system administrator.</p>
    </div>
  </body>
</html>