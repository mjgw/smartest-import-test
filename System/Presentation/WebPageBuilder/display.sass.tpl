<?sm:if $render_data.use_style_tag == 'true' || $render_data.use_style_tag == '1' || $render_data.use_style_tag == 'on' :?><style type="text/css"<?sm:if strlen($render_data.media):?> media="<?sm:$render_data.media:?>"<?sm:/if:?>>
  @import url('<?sm:$domain:?><?sm:$sass_web_path:?>');
</style><?sm:else:?><link rel="stylesheet" href="<?sm:$domain:?><?sm:$sass_web_path:?>"<?sm:if strlen($render_data.media):?> media="<?sm:$render_data.media:?>"<?sm:/if:?> /><?sm:/if:?>