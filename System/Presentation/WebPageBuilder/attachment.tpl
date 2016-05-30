<!--Smartest Text Attachment: <?sm:$_textattachment._name:?>-->
<div class="sm-attachment" style="<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.div_width :?>px;<?sm:/if:?><?sm:if $_textattachment.border :?>border: 1px solid #ccc;padding:6px;<?sm:/if:?><?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>float: <?sm:else:?>text-align: <?sm:/if:?><?sm:$_textattachment.alignment:?>; margin:<?sm:if $_textattachment.alignment == "right" :?>0 0 10px 10px;<?sm:else if $_textattachment.alignment == "left" :?>0 10px 10px 0;<?sm:/if:?>">

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
<div class="sm-attachment-caption" style="text-align:<?sm:$_textattachment.caption_alignment :?>;display:block; margin:5px 0 0 0;font-size:0.8em;<?sm:if $_textattachment.float && $_textattachment.alignment != 'center' :?>width:<?sm:$_textattachment.div_width :?>px<?sm:/if:?>">
  <?sm:$_textattachment.caption:?>
  <?sm:if strlen($_textattachment.asset.credit):?>Image: <?sm:$_textattachment.asset.credit:?><?sm:/if:?>
  <?sm:$_textattachment.edit_link:?>
</div>
<?sm:else:?>
  <?sm:$_textattachment.edit_link:?>
<?sm:/if:?>

</div>