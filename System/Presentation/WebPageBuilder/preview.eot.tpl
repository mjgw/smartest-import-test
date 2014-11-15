<style type="text/css">
@font-face {
    font-family: "sm-eot-preview";
    src: url('<?sm:$domain:?>Resources/Fonts/<?sm:$asset_info.url:?>'); /* IE9 Compat Modes */
    src: url('<?sm:$domain:?>Resources/Fonts/<?sm:$asset_info.url:?>#iefix') format('embedded-opentype');
    <?sm:if $render_data.font_weight && $render_data.font_weight != 'normal':?>font-weight: <?sm:$render_data.font_weight:?>;<?sm:/if:?>
    <?sm:if $render_data.is_italic == 'TRUE':?>font-style: italic, oblique;<?sm:else:?>font-style: normal;<?sm:/if:?>
}
#eot-preview p{
    font-size:3.2em;
    font-family: "sm-eot-preview";
}
</style>

<?sm:if $sm_user_agent.appname != "Explorer":?>
<div class="warning" style="margin-top:0px">
  The browser you are currently using is one that may not support the EOT font format, as this tends to only be supported by Microsoft browsers.
</div>
<?sm:/if:?>

<div id="eot-preview">
    <p>Aa Bb Cc Dd Ee Ff Gg Hh Ii Jj Kk Ll Mm<br />Nn Oo Pp Qq Rr Ss Tt Uu Vv Ww Xx Yy Zz</p>
    <p>0 1 2 3 4 5 6 7 8 9</p>
    <p>The Quick Brown Fox Jumped Over the Lazy Brown Dog</p>
</div>