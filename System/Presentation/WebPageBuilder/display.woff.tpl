<style type="text/css">
@font-face {
    font-family: "<?sm:if $render_data.family_name:?><?sm:$render_data.family_name:?><?sm:else:?><?sm:$asset_info.stringid:?><?sm:/if:?>";
    src: url("<?sm:$domain:?>Resources/Fonts/<?sm:$asset_info.url:?>") format('woff');
    <?sm:if $render_data.font_weight && $render_data.font_weight != 'normal':?>font-weight: <?sm:$render_data.font_weight:?>;<?sm:/if:?>
    <?sm:if $render_data.is_italic == 'TRUE':?>font-style: italic, oblique;<?sm:else:?>font-style: normal;<?sm:/if:?>

}
</style>