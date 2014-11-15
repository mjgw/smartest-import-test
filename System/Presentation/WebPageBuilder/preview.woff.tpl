<style type="text/css">
@font-face {
    font-family: "sm-woff-preview";
    src: url("<?sm:$domain:?>Resources/Fonts/<?sm:$asset_info.url:?>") format('woff');
    <?sm:if $render_data.font_weight && $render_data.font_weight != 'normal':?>font-weight: <?sm:$render_data.font_weight:?>;<?sm:/if:?>
    <?sm:if $render_data.is_italic == 'TRUE':?>font-style: italic, oblique;<?sm:else:?>font-style: normal;<?sm:/if:?>

}
#woff-preview p{
    font-size:3.2em;
    font-family: "sm-woff-preview";
}
</style>

<div id="woff-preview">
    <p>Aa Bb Cc Dd Ee Ff Gg Hh Ii Jj Kk Ll Mm<br />Nn Oo Pp Qq Rr Ss Tt Uu Vv Ww Xx Yy Zz</p>
    <p>0 1 2 3 4 5 6 7 8 9</p>
    <p>The Quick Brown Fox Jumped Over the Lazy Brown Dog</p>
</div>