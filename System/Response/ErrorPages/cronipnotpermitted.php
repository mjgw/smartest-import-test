<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Host name not allowed for admin</title>
    <link rel="stylesheet" href="/Resources/System/Stylesheets/sm_error.css" />
    <meta charset="UTF-8" />
  </head>
  <body>
    <div id="fixed-width">
      <h1>Automation remote IP not allowed</h1>
      <p>Smartest automation cannot be triggered from the IP address <strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong>.</p>
      <p class="technical">Technical info: For security reasons, only certain IP addresses can be used to trigger this Smartest installation's automation. If you have access to the Smartest file system, please see Configuration/cron_ips.yml, otherwise contact your system administrator.</p>
    </div>
  </body>
</html>