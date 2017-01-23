<?sm:if $sm_user_agent.is_unsupported_browser:?><?sm:/if:?>

<?sm:if !$_textattachment.float:?><div class="sm-attachment-outer <?sm:$_textattachment.alignment:?>"><?sm:/if:?>
<figure class="sm-attachment<?sm:if $_textattachment.float:?> float<?sm:else:?> nofloat<?sm:/if:?> <?sm:$_textattachment.alignment:?><?sm:if $_textattachment.border :?> sm-border<?sm:/if:?>" style="">

<?sm:* $_textattachment.asset.render_data_debug *:?>

<?sm:if $_textattachment.float :?>
  <?sm:if $_textattachment.allow_resize && $_textattachment.asset.is_binary_image:?>
  <img src="<?sm:$_textattachment.thumbnail.url:?>" style="display:block" alt="<?sm:$_textattachment.asset.render_data.alt_text:?>" />
  <?sm:else:?>
  <?sm:render_file asset=$_textattachment.asset style="display:block" manual_width=$_textattachment.div_width:?>
  <?sm:/if:?>
<?sm:else:?>
  <?sm:if $_textattachment.allow_resize && $_textattachment.asset.is_binary_image:?>
  <img src="<?sm:$_textattachment.thumbnail.url:?>" style="display:block" alt="<?sm:$_textattachment.asset.render_data.alt_text:?>" />
  <?sm:else:?>
  <?sm:render_file asset=$_textattachment.asset style="display:block" manual_width=$_textattachment.div_width:?>
  <?sm:/if:?>
<?sm:/if:?>

<?sm:if strlen($_textattachment.caption) || strlen($_textattachment.asset.credit) :?>
  <figcaption class="sm-attachment-caption" style="text-align:<?sm:$_textattachment.caption_alignment :?><?sm:if $_textattachment.alignment != 'center' :?>;max-width:<?sm:$_textattachment.div_width :?>px<?sm:/if:?>">
    <?sm:$_textattachment.caption:?>
    <?sm:if strlen($_textattachment.asset.credit):?>
    <?sm:if $_textattachment.asset.type == 'SM_ASSETTYPE_JPEG_IMAGE':?>Photo<?sm:else:?>Image<?sm:/if:?>: <?sm:$_textattachment.asset.credit:?>
    <?sm:/if:?>
    <?sm:$_textattachment.edit_link:?>
  </figcaption>
<?sm:else:?>
  <?sm:$_textattachment.edit_link:?>
<?sm:/if:?>

</figure>
<?sm:if !$_textattachment.float:?></div><?sm:/if:?>
<!SM_ATT_HB>