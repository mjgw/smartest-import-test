<div id="smartest-cookie-alert">
  <p>We would like to place cookies on your machine to help make this website better. <a href="#cookie-warning-dismiss" id="sm-cookie-warning-dismiss">I understand.</a></p>
</div>

<script type="text/javascript">

document.getElementById('sm-cookie-warning-dismiss').addEventListener('click', function(event){
    
    event.preventDefault();
    document.getElementById('smartest-cookie-alert').style.display = 'none';
    
		var date = new Date();
    var days = 90;
		date.setTime(date.getTime()+(days*24*60*60*1000));
    
    document.cookie = "SMARTEST_COOKIE_CONSENT=1; expires="+date.toGMTString()+"; path=/";
    
}, false);

</script>

<!--To customise this markup, place your own template in either Sites/SITE_DIR/Presentation/Special/eu_cookie_warning.tpl (Site specific, checked first) or Presentation/Special/eu_cookie_warning.tpl (all sites)-->

